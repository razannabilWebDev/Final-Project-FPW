<?php

namespace App\Actions\Audit;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class LogActivityAction
{
    /**
     * Catat aktivitas sensitif secara append-only ke tabel activity_logs.
     * Satu jalur ini dipakai seluruh modul agar format action/metadata konsisten.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function execute(
        ?User $actor,
        string $action,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $metadata = [],
    ): void {
        $metadata['ip'] = request()->ip();
        $metadata['user_agent'] = request()->userAgent();

        DB::table('activity_logs')->insert([
            'user_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => json_encode($metadata, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }
}
