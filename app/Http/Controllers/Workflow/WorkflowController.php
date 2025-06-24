<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLevel;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Lembur;
use App\Models\VerifikasiAbsen;
use App\Models\WorkflowHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    /**
     * Dashboard workflow untuk supervisor dan HRD
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $data = [
            'title' => 'Workflow Approval',
            'slug' => 'workflow',
            'scripts' => ['js/workflow.js'],
            'csses' => ['css/workflow.css']
        ];

        // Get pending approvals berdasarkan role
        if ($user->is_hrd) {
            // HRD melihat semua yang sudah approved supervisor
            $data['pending_cuti'] = Cuti::byStatus('approved_supervisor')->count();
            $data['pending_izin'] = Izin::byStatus('approved_supervisor')->count();
            $data['pending_lembur'] = Lembur::byStatus('approved_supervisor')->count();
            $data['pending_verifikasi'] = VerifikasiAbsen::byStatus('approved_supervisor')->count();
        } elseif ($user->is_supervisor) {
            // Supervisor melihat yang pending untuk employee yang dia supervisi
            $data['pending_cuti'] = $this->getPendingForSupervisor(Cuti::class);
            $data['pending_izin'] = $this->getPendingForSupervisor(Izin::class);
            $data['pending_lembur'] = $this->getPendingForSupervisor(Lembur::class);
            $data['pending_verifikasi'] = $this->getPendingForSupervisor(VerifikasiAbsen::class);
        }

        return view('workflow.dashboard', $data);
    }

    /**
     * Get pending items untuk supervisor
     */
    private function getPendingForSupervisor($modelClass)
    {
        $user = Auth::user();
        
        return $modelClass::byStatus('pending')
            ->whereHas('employee', function ($query) use ($user) {
                $query->whereHas('unit', function ($q) use ($user) {
                    $q->whereIn('id', $user->supervised_units ?? []);
                })->orWhereHas('divisi', function ($q) use ($user) {
                    $q->whereIn('id', $user->supervised_divisis ?? []);
                });
            })
            ->count();
    }

    /**
     * List pending approvals
     */
    public function pendingApprovals(Request $request)
    {
        $type = $request->get('type', 'all'); // cuti, izin, lembur, verifikasi, all
        $user = Auth::user();
        
        $data = [];
        
        if ($user->is_hrd) {
            // HRD melihat yang approved_supervisor
            if (in_array($type, ['cuti', 'all'])) {
                $data['cuti'] = Cuti::with(['employee', 'jenisCuti', 'submittedBy', 'approvedBySupervisor'])
                    ->byStatus('approved_supervisor')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['izin', 'all'])) {
                $data['izin'] = Izin::with(['employee', 'jenisIzin', 'submittedBy', 'approvedBySupervisor'])
                    ->byStatus('approved_supervisor')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['lembur', 'all'])) {
                $data['lembur'] = Lembur::with(['employee', 'submittedBy', 'approvedBySupervisor'])
                    ->byStatus('approved_supervisor')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['verifikasi', 'all'])) {
                $data['verifikasi'] = VerifikasiAbsen::with(['employee', 'submittedBy', 'approvedBySupervisor'])
                    ->byStatus('approved_supervisor')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } elseif ($user->is_supervisor) {
            // Supervisor melihat yang pending untuk employee yang dia supervisi
            $employeeQuery = function ($query) use ($user) {
                $query->whereHas('unit', function ($q) use ($user) {
                    $q->whereIn('id', $user->supervised_units ?? []);
                })->orWhereHas('divisi', function ($q) use ($user) {
                    $q->whereIn('id', $user->supervised_divisis ?? []);
                });
            };
            
            if (in_array($type, ['cuti', 'all'])) {
                $data['cuti'] = Cuti::with(['employee', 'jenisCuti', 'submittedBy'])
                    ->byStatus('pending')
                    ->whereHas('employee', $employeeQuery)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['izin', 'all'])) {
                $data['izin'] = Izin::with(['employee', 'jenisIzin', 'submittedBy'])
                    ->byStatus('pending')
                    ->whereHas('employee', $employeeQuery)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['lembur', 'all'])) {
                $data['lembur'] = Lembur::with(['employee', 'submittedBy'])
                    ->byStatus('pending')
                    ->whereHas('employee', $employeeQuery)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            if (in_array($type, ['verifikasi', 'all'])) {
                $data['verifikasi'] = VerifikasiAbsen::with(['employee', 'submittedBy'])
                    ->byStatus('pending')
                    ->whereHas('employee', $employeeQuery)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Approve by supervisor
     */
    public function approveBySupervisor(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,lembur,verifikasi',
            'id' => 'required|integer',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $model = $this->getModel($request->type, $request->id);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $model->approveBySupervisor($request->notes);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil disetujui oleh supervisor'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Approve by HRD
     */
    public function approveByHrd(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,lembur,verifikasi',
            'id' => 'required|integer',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $model = $this->getModel($request->type, $request->id);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $model->approveByHrd($request->notes);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil disetujui oleh HRD'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reject request
     */
    public function reject(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,lembur,verifikasi',
            'id' => 'required|integer',
            'reason' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $model = $this->getModel($request->type, $request->id);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $model->reject($request->reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get model instance
     */
    private function getModel($type, $id)
    {
        switch ($type) {
            case 'cuti':
                return Cuti::find($id);
            case 'izin':
                return Izin::find($id);
            case 'lembur':
                return Lembur::find($id);
            case 'verifikasi':
                return VerifikasiAbsen::find($id);
            default:
                return null;
        }
    }
}
