<?php

namespace App\Services\Access;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditService
{
    public function __construct(private readonly Request $request) {}

    public function record(
        string $action,
        ?User $user,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $properties = [],
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
