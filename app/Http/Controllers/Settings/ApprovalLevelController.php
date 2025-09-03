<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLevel;
use App\Models\User;
use App\Models\mak_hrd\Unit;
use App\Models\mak_hrd\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalLevelController extends Controller
{
    /**
     * Display approval levels management
     */
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Approval Level',
            'slug' => 'approval-levels',
            'scripts' => ['js/approval-levels.js'],
            'csses' => ['css/approval-levels.css']
        ];

        return view('settings.approval-levels', $data);
    }

    /**
     * Get datatable data
     */
    public function datatable()
    {
        $approvalLevels = ApprovalLevel::with(['supervisor', 'unit', 'divisi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables($approvalLevels)
            ->addColumn('supervisor_name', function ($row) {
                return $row->supervisor ? $row->supervisor->nama : '-';
            })
            ->addColumn('scope', function ($row) {
                if ($row->approval_type === 'unit' && $row->unit) {
                    return 'Unit: ' . $row->unit->nama;
                } elseif ($row->approval_type === 'divisi' && $row->divisi) {
                    return 'Divisi: ' . $row->divisi->nama;
                }
                return '-';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->is_active ? 'badge-success' : 'badge-secondary';
                $status = $row->is_active ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $editBtn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                    <i class="fas fa-edit"></i> Edit
                </button>';
                
                $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                    <i class="fas fa-trash"></i> Hapus
                </button>';
                
                return $editBtn . ' ' . $deleteBtn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Store new approval level
     */
    public function store(Request $request)
    {
        $request->validate([
            'supervisor_user_id' => 'required|exists:users,id',
            'approval_type' => 'required|in:unit,divisi',
            'unit_id' => 'required_if:approval_type,unit|exists:hrd_units,id',
            'divisi_id' => 'required_if:approval_type,divisi|exists:hrd_divisis,id',
        ]);

        try {
            DB::beginTransaction();

            // Check if approval level already exists
            $existing = ApprovalLevel::where('approval_type', $request->approval_type)
                ->where(function ($query) use ($request) {
                    if ($request->approval_type === 'unit') {
                        $query->where('unit_id', $request->unit_id);
                    } else {
                        $query->where('divisi_id', $request->divisi_id);
                    }
                })
                ->where('is_active', true)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval level untuk scope ini sudah ada'
                ], 400);
            }

            $approvalLevel = ApprovalLevel::create([
                'supervisor_user_id' => $request->supervisor_user_id,
                'approval_type' => $request->approval_type,
                'unit_id' => $request->approval_type === 'unit' ? $request->unit_id : null,
                'divisi_id' => $request->approval_type === 'divisi' ? $request->divisi_id : null,
                'is_active' => true,
            ]);

            // Update user sebagai supervisor
            $user = User::find($request->supervisor_user_id);
            $user->is_supervisor = true;
            
            if ($request->approval_type === 'unit') {
                $supervisedUnits = $user->supervised_units ?? [];
                if (!in_array($request->unit_id, $supervisedUnits)) {
                    $supervisedUnits[] = $request->unit_id;
                    $user->supervised_units = $supervisedUnits;
                }
            } else {
                $supervisedDivisis = $user->supervised_divisis ?? [];
                if (!in_array($request->divisi_id, $supervisedDivisis)) {
                    $supervisedDivisis[] = $request->divisi_id;
                    $user->supervised_divisis = $supervisedDivisis;
                }
            }
            
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval level berhasil ditambahkan',
                'data' => $approvalLevel
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval level data
     */
    public function show($id)
    {
        $approvalLevel = ApprovalLevel::with(['supervisor', 'unit', 'divisi'])->find($id);
        
        if (!$approvalLevel) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $approvalLevel
        ]);
    }

    /**
     * Update approval level
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supervisor_user_id' => 'required|exists:users,id',
            'approval_type' => 'required|in:unit,divisi',
            'unit_id' => 'required_if:approval_type,unit|exists:hrd_units,id',
            'divisi_id' => 'required_if:approval_type,divisi|exists:hrd_divisis,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $approvalLevel = ApprovalLevel::find($id);
            
            if (!$approvalLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Check if approval level already exists (exclude current)
            $existing = ApprovalLevel::where('approval_type', $request->approval_type)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($request) {
                    if ($request->approval_type === 'unit') {
                        $query->where('unit_id', $request->unit_id);
                    } else {
                        $query->where('divisi_id', $request->divisi_id);
                    }
                })
                ->where('is_active', true)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval level untuk scope ini sudah ada'
                ], 400);
            }

            $approvalLevel->update([
                'supervisor_user_id' => $request->supervisor_user_id,
                'approval_type' => $request->approval_type,
                'unit_id' => $request->approval_type === 'unit' ? $request->unit_id : null,
                'divisi_id' => $request->approval_type === 'divisi' ? $request->divisi_id : null,
                'is_active' => $request->get('is_active', true),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval level berhasil diupdate',
                'data' => $approvalLevel
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete approval level
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $approvalLevel = ApprovalLevel::find($id);
            
            if (!$approvalLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $approvalLevel->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval level berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supervisors list
     */
    public function getSupervisors()
    {
        $supervisors = User::where('role', '!=', 'pic')
            ->select('id', 'nama', 'username')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $supervisors
        ]);
    }

    /**
     * Get units list
     */
    public function getUnits()
    {
        $units = Unit::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }

    /**
     * Get divisis list
     */
    public function getDivisis()
    {
        $divisis = Divisi::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $divisis
        ]);
    }
}
