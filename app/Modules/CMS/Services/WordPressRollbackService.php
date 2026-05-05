<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use App\Models\ImportRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WordPressRollbackService
{
    /**
     * @return array{rolled_back: int, skipped: array<int, array<string, mixed>>}
     */
    public function rollback(ImportJob $job): array
    {
        $rolledBack = 0;
        $skipped = [];

        $job->records()
            ->whereNotNull('target_type')
            ->whereNotNull('target_id')
            ->get()
            ->each(function (ImportRecord $record) use (&$rolledBack, &$skipped): void {
                $target = $this->resolveTarget($record);

                if (! $target instanceof Model) {
                    $skipped[] = ['record_id' => $record->id, 'reason' => 'Target record not found.'];

                    return;
                }

                if (! in_array(SoftDeletes::class, class_uses_recursive($target), true)) {
                    $skipped[] = ['record_id' => $record->id, 'reason' => 'Target does not support soft deletes.'];

                    return;
                }

                if ($target->updated_at !== null && $record->created_at !== null && $target->updated_at->greaterThan($record->created_at)) {
                    $skipped[] = ['record_id' => $record->id, 'reason' => 'Target was edited after import.'];

                    return;
                }

                $target->delete();
                $record->forceFill(['status' => 'rolled_back'])->save();
                $rolledBack++;
            });

        $job->forceFill([
            'status' => 'rolled_back',
            'summary' => array_merge(is_array($job->summary) ? $job->summary : [], [
                'rollback' => [
                    'rolled_back' => $rolledBack,
                    'skipped' => $skipped,
                ],
            ]),
        ])->save();

        return ['rolled_back' => $rolledBack, 'skipped' => $skipped];
    }

    protected function resolveTarget(ImportRecord $record): ?Model
    {
        $class = $record->target_type;

        if (! is_string($class) || ! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            return null;
        }

        return $class::query()->whereKey($record->target_id)->first();
    }
}
