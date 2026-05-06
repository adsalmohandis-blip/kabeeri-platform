<?php

namespace App\Models;

use Database\Factories\DashboardWidgetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'organization_id', 'report_definition_id', 'key', 'title', 'widget_type', 'status', 'sort_order', 'settings', 'metadata'])]
class DashboardWidget extends Model
{
    /** @use HasFactory<DashboardWidgetFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $widget): void {
            if (blank($widget->ulid)) {
                $widget->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function reportDefinition(): BelongsTo
    {
        return $this->belongsTo(ReportDefinition::class);
    }
}
