<?php

namespace Tests\Feature;

use App\Enums\LeaveStatus;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $employee;
    private User $manager;
    private LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::create(['name' => 'IT']);

        $this->manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'department_id' => $department->id,
        ]);

        $this->employee = User::create([
            'name' => 'Employee',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'department_id' => $department->id,
        ]);

        $this->leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'days_allowed' => 12,
        ]);

        LeaveBalance::create([
            'user_id' => $this->employee->id,
            'leave_type_id' => $this->leaveType->id,
            'year' => now()->year,
            'total_days' => 12,
            'used_days' => 0,
            'remaining_days' => 12,
        ]);
    }

    private function getNextMonday(int $offset = 0): string
    {
        $date = now()->next('monday')->addDays($offset * 7);
        return $date->format('Y-m-d');
    }

    public function test_employee_can_submit_leave_request(): void
    {
        $service = new \App\Services\LeaveRequestService();

        $monday = $this->getNextMonday();
        $wednesday = (new \DateTime($monday))->modify('+2 days')->format('Y-m-d');

        $result = $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $wednesday,
            'reason' => 'Liburan keluarga',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals(LeaveStatus::Pending, $result->status);
        $this->assertEquals(3, $result->total_days);

        $balance = LeaveBalance::where('user_id', $this->employee->id)->first();
        $this->assertEquals(9, $balance->remaining_days);
    }

    public function test_employee_cannot_submit_with_insufficient_balance(): void
    {
        $this->expectException(\App\Exceptions\LeaveException::class);

        $service = new \App\Services\LeaveRequestService();

        $monday = $this->getNextMonday();
        $fiveWeeksLater = (new \DateTime($monday))->modify('+34 days')->format('Y-m-d');

        $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $fiveWeeksLater,
            'reason' => 'Cut panjang',
        ]);
    }

    public function test_manager_can_approve_leave_request(): void
    {
        $monday = $this->getNextMonday();
        $wednesday = (new \DateTime($monday))->modify('+2 days')->format('Y-m-d');

        $service = new \App\Services\LeaveRequestService();
        $leaveRequest = $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $wednesday,
            'reason' => 'Test',
        ]);

        $approvalService = new \App\Services\LeaveApprovalService();
        $result = $approvalService->approve($leaveRequest, $this->manager);

        $this->assertEquals(LeaveStatus::Approved, $result->status);
        $this->assertEquals($this->manager->id, $result->approved_by);
    }

    public function test_manager_can_reject_leave_request(): void
    {
        $monday = $this->getNextMonday();
        $wednesday = (new \DateTime($monday))->modify('+2 days')->format('Y-m-d');

        $service = new \App\Services\LeaveRequestService();
        $leaveRequest = $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $wednesday,
            'reason' => 'Test',
        ]);

        $approvalService = new \App\Services\LeaveApprovalService();
        $result = $approvalService->reject($leaveRequest, $this->manager, 'Tidak disetujui');

        $this->assertEquals(LeaveStatus::Rejected, $result->status);
        $this->assertEquals('Tidak disetujui', $result->rejection_reason);

        $balance = LeaveBalance::where('user_id', $this->employee->id)->first();
        $this->assertEquals(12, $balance->remaining_days);
    }

    public function test_employee_can_cancel_pending_request(): void
    {
        $monday = $this->getNextMonday();
        $wednesday = (new \DateTime($monday))->modify('+2 days')->format('Y-m-d');

        $service = new \App\Services\LeaveRequestService();
        $leaveRequest = $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $wednesday,
            'reason' => 'Test',
        ]);

        $service->cancelLeaveRequest($this->employee, $leaveRequest);

        $leaveRequest->refresh();
        $this->assertEquals(LeaveStatus::Cancelled, $leaveRequest->status);

        $balance = LeaveBalance::where('user_id', $this->employee->id)->first();
        $this->assertEquals(12, $balance->remaining_days);
    }

    public function test_cannot_submit_two_pending_requests(): void
    {
        $this->expectException(\App\Exceptions\LeaveException::class);

        $monday = $this->getNextMonday();
        $wednesday = (new \DateTime($monday))->modify('+2 days')->format('Y-m-d');

        $service = new \App\Services\LeaveRequestService();
        $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $monday,
            'end_date' => $wednesday,
            'reason' => 'Pertama',
        ]);

        $nextMonday = $this->getNextMonday(1);
        $nextWednesday = (new \DateTime($nextMonday))->modify('+2 days')->format('Y-m-d');

        $service->createLeaveRequest($this->employee, [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => $nextMonday,
            'end_date' => $nextWednesday,
            'reason' => 'Kedua',
        ]);
    }

    public function test_working_days_exclude_weekends(): void
    {
        $service = new \App\Services\LeaveRequestService();

        $monday = $this->getNextMonday();
        $friday = (new \DateTime($monday))->modify('+4 days')->format('Y-m-d');

        $days = $service->calculateWorkingDays($monday, $friday);
        $this->assertEquals(5, $days);
    }
}
