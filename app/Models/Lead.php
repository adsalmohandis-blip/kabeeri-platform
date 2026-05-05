<?php

namespace App\Models;

use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'company_id',
    'form_submission_id',
    'contact_id',
    'title',
    'company_name',
    'name',
    'email',
    'phone',
    'source',
    'lead_source_id',
    'sales_pipeline_id',
    'sales_pipeline_stage_id',
    'status',
    'priority',
    'expected_value',
    'currency_code',
    'score',
    'score_breakdown',
    'scored_at',
    'stage_changed_at',
    'assigned_to',
    'qualified_at',
    'converted_at',
    'lost_at',
    'lost_reason',
    'message',
    'metadata',
])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $lead): void {
            if (blank($lead->ulid)) {
                $lead->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'score' => 'integer',
            'score_breakdown' => 'array',
            'scored_at' => 'datetime',
            'stage_changed_at' => 'datetime',
            'expected_value' => 'decimal:2',
            'qualified_at' => 'datetime',
            'converted_at' => 'datetime',
            'lost_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function formSubmission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function salesPipeline(): BelongsTo
    {
        return $this->belongsTo(SalesPipeline::class);
    }

    public function salesPipelineStage(): BelongsTo
    {
        return $this->belongsTo(SalesPipelineStage::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
