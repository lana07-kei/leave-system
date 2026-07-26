<?php

namespace App\Models;

use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'attachment_path',
        'approved_by',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'status' => LeaveStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === LeaveStatus::Pending;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === LeaveStatus::Pending;
    }

    public function scopePending($query)
    {
        return $query->where('status', LeaveStatus::Pending);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->whereHas('user', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }
}
