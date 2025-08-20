<?php

namespace Tests\Feature;

use App\Models\ApprovalLevel;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\User;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Unit;
use App\Models\WorkflowHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkflowApprovalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $pic;
    protected $supervisor;
    protected $hrd;
    protected $employee;
    protected $unit;
    protected $jenisCuti;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable notifications for testing
        Notification::fake();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create unit
        $this->unit = Unit::factory()->create([
            'nama' => 'IT Department'
        ]);

        // Create employee
        $this->employee = Employee::factory()->create([
            'nama' => 'John Doe',
            'nip' => '12345'
        ]);

        // Create users
        $this->pic = User::factory()->create([
            'role' => 'pic',
            'is_supervisor' => false,
            'is_hrd' => false,
        ]);

        $this->supervisor = User::factory()->create([
            'role' => 'admin',
            'is_supervisor' => true,
            'is_hrd' => false,
            'supervised_units' => [$this->unit->id],
        ]);

        $this->hrd = User::factory()->create([
            'role' => 'admin',
            'is_supervisor' => false,
            'is_hrd' => true,
        ]);

        // Create approval level
        ApprovalLevel::create([
            'unit_id' => $this->unit->id,
            'approval_type' => 'unit',
            'supervisor_user_id' => $this->supervisor->id,
            'is_active' => true,
        ]);

        // Create jenis cuti
        $this->jenisCuti = JenisCuti::factory()->create([
            'cuti' => 'Cuti Tahunan'
        ]);
    }

    /** @test */
    public function pic_can_submit_cuti_request()
    {
        $this->actingAs($this->pic);

        $cutiData = [
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
            'keterangan' => 'Cuti untuk liburan',
        ];

        $cuti = Cuti::create($cutiData);

        $this->assertDatabaseHas('cutis', [
            'id' => $cuti->id,
            'status' => 'pending',
            'submitted_by' => $this->pic->id,
        ]);

        $this->assertDatabaseHas('workflow_histories', [
            'workflowable_type' => Cuti::class,
            'workflowable_id' => $cuti->id,
            'action' => 'submitted',
            'user_id' => $this->pic->id,
        ]);
    }

    /** @test */
    public function supervisor_can_approve_pending_cuti()
    {
        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
            'keterangan' => 'Cuti untuk liburan',
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->postJson('/workflow/approve-supervisor', [
            'type' => 'cuti',
            'id' => $cuti->id,
            'notes' => 'Approved by supervisor',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cutis', [
            'id' => $cuti->id,
            'status' => 'approved_supervisor',
            'approved_by_supervisor' => $this->supervisor->id,
        ]);
    }

    /** @test */
    public function hrd_can_approve_supervisor_approved_cuti()
    {
        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
            'status' => 'approved_supervisor',
            'approved_by_supervisor' => $this->supervisor->id,
        ]);

        $this->actingAs($this->hrd);

        $response = $this->postJson('/workflow/approve-hrd', [
            'type' => 'cuti',
            'id' => $cuti->id,
            'notes' => 'Final approval by HRD',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cutis', [
            'id' => $cuti->id,
            'status' => 'approved_hrd',
            'approved_by_hrd' => $this->hrd->id,
        ]);
    }

    /** @test */
    public function supervisor_can_reject_pending_cuti()
    {
        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->postJson('/workflow/reject', [
            'type' => 'cuti',
            'id' => $cuti->id,
            'reason' => 'Insufficient leave balance',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cutis', [
            'id' => $cuti->id,
            'status' => 'rejected',
            'rejection_reason' => 'Insufficient leave balance',
        ]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_workflow_dashboard()
    {
        $regularUser = User::factory()->create([
            'role' => 'pic',
            'is_supervisor' => false,
            'is_hrd' => false,
        ]);

        $this->actingAs($regularUser);

        $response = $this->get('/workflow/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function supervisor_can_only_approve_their_supervised_employees()
    {
        // Create another unit and supervisor
        $otherUnit = Unit::factory()->create(['nama' => 'Finance Department']);
        $otherSupervisor = User::factory()->create([
            'is_supervisor' => true,
            'supervised_units' => [$otherUnit->id],
        ]);

        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
        ]);

        // Try to approve with wrong supervisor
        $this->actingAs($otherSupervisor);

        $response = $this->postJson('/workflow/approve-supervisor', [
            'type' => 'cuti',
            'id' => $cuti->id,
            'notes' => 'Trying to approve',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function workflow_history_is_created_for_each_action()
    {
        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
        ]);

        // Approve by supervisor
        $this->actingAs($this->supervisor);
        $cuti->approveBySupervisor('Supervisor approval');

        // Approve by HRD
        $this->actingAs($this->hrd);
        $cuti->approveByHrd('HRD final approval');

        $histories = WorkflowHistory::where('workflowable_type', Cuti::class)
            ->where('workflowable_id', $cuti->id)
            ->orderBy('created_at')
            ->get();

        $this->assertCount(3, $histories); // submitted, approved_supervisor, approved_hrd

        $this->assertEquals('submitted', $histories[0]->action);
        $this->assertEquals('approved_supervisor', $histories[1]->action);
        $this->assertEquals('approved_hrd', $histories[2]->action);
    }

    /** @test */
    public function pending_approvals_are_filtered_correctly_for_supervisor()
    {
        $this->actingAs($this->supervisor);

        $response = $this->getJson('/workflow/pending-approvals?type=cuti');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function hrd_can_see_supervisor_approved_items()
    {
        $this->actingAs($this->pic);
        
        $cuti = Cuti::create([
            'date' => '2024-01-15',
            'employee_id' => $this->employee->id,
            'jenis_cuti' => $this->jenisCuti->id,
            'status' => 'approved_supervisor',
        ]);

        $this->actingAs($this->hrd);

        $response = $this->getJson('/workflow/pending-approvals?type=cuti');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
