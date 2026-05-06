<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'name',
    'slug',
    'owner_user_id',
    'account_type',
    'status',
    'plan_code',
    'country_id',
    'locale',
    'timezone',
    'settings',
    'metadata',
])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $organization): void {
            if (blank($organization->ulid)) {
                $organization->ulid = (string) Str::ulid();
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
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function moduleInstallations(): HasMany
    {
        return $this->hasMany(ModuleInstallation::class);
    }

    public function contentTypes(): HasMany
    {
        return $this->hasMany(ContentType::class);
    }

    public function contentEntries(): HasMany
    {
        return $this->hasMany(ContentEntry::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(Redirect::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function leadSources(): HasMany
    {
        return $this->hasMany(LeadSource::class);
    }

    public function salesPipelines(): HasMany
    {
        return $this->hasMany(SalesPipeline::class);
    }

    public function crmActivities(): HasMany
    {
        return $this->hasMany(CrmActivity::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function employeeEvaluations(): HasMany
    {
        return $this->hasMany(EmployeeEvaluation::class);
    }

    public function businessProjects(): HasMany
    {
        return $this->hasMany(BusinessProject::class);
    }

    public function businessTasks(): HasMany
    {
        return $this->hasMany(BusinessTask::class);
    }

    public function workflowDefinitions(): HasMany
    {
        return $this->hasMany(WorkflowDefinition::class);
    }

    public function workflowRuns(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }

    public function approvalRequests(): HasMany
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    public function reportDefinitions(): HasMany
    {
        return $this->hasMany(ReportDefinition::class);
    }

    public function reportSnapshots(): HasMany
    {
        return $this->hasMany(ReportSnapshot::class);
    }

    public function dashboardWidgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function operatingModes(): HasMany
    {
        return $this->hasMany(OrganizationOperatingMode::class);
    }

    public function cloudSites(): HasMany
    {
        return $this->hasMany(CloudSite::class);
    }

    public function cloudDomains(): HasMany
    {
        return $this->hasMany(CloudDomain::class);
    }

    public function cloudBackups(): HasMany
    {
        return $this->hasMany(CloudBackup::class);
    }

    public function cloudHealthChecks(): HasMany
    {
        return $this->hasMany(CloudHealthCheck::class);
    }

    public function mallPublicationConsents(): HasMany
    {
        return $this->hasMany(MallPublicationConsent::class);
    }

    public function mallSyncSources(): HasMany
    {
        return $this->hasMany(MallSyncSource::class);
    }

    public function mallSyncEvents(): HasMany
    {
        return $this->hasMany(MallSyncEvent::class);
    }

    public function backofficeWorkspaces(): HasMany
    {
        return $this->hasMany(BackofficeWorkspace::class);
    }

    public function moderationCases(): HasMany
    {
        return $this->hasMany(ModerationCase::class);
    }

    public function moderationFlags(): HasMany
    {
        return $this->hasMany(ModerationFlag::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reputationSnapshots(): HasMany
    {
        return $this->hasMany(ReputationSnapshot::class);
    }

    public function mallMirrorBusinesses(): HasMany
    {
        return $this->hasMany(MallMirrorBusiness::class);
    }

    public function mallMirrorServices(): HasMany
    {
        return $this->hasMany(MallMirrorService::class);
    }

    public function mallMirrorCourses(): HasMany
    {
        return $this->hasMany(MallMirrorCourse::class);
    }

    public function mallMirrorTalent(): HasMany
    {
        return $this->hasMany(MallMirrorTalent::class);
    }

    public function travelTourismMallListings(): HasMany
    {
        return $this->hasMany(TravelTourismMallListing::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function importJobs(): HasMany
    {
        return $this->hasMany(ImportJob::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function taxonomies(): HasMany
    {
        return $this->hasMany(Taxonomy::class);
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function legalPartnerProfiles(): HasMany
    {
        return $this->hasMany(LegalPartnerProfile::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_memberships')
            ->withPivot([
                'membership_type',
                'status',
                'job_title',
                'invited_by',
                'invited_at',
                'accepted_at',
                'start_date',
                'end_date',
                'visibility',
                'metadata',
                'deleted_at',
            ])
            ->withTimestamps();
    }
}
