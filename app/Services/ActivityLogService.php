<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function log(
        int $userId,
        string $logableType,
        int $logableId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'logable_type' => $logableType,
            'logable_id' => $logableId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
