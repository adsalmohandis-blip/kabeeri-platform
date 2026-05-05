<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_notification(): void
    {
        $recipient = User::factory()->create();
        $organization = Organization::factory()->create();
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->forCompany($company)->create();

        $notification = app(NotificationService::class)->create(
            recipientUserId: $recipient->id,
            type: 'organization.invitation',
            title: 'You were invited',
            body: 'Join the organization workspace.',
            actionUrl: '/invitations/accept',
            organizationId: $organization->id,
            companyId: $company->id,
            siteId: $site->id,
            priority: 'normal',
            metadata: ['source' => 'test'],
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'recipient_user_id' => $recipient->id,
            'type' => 'organization.invitation',
            'title' => 'You were invited',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_can_mark_notification_as_read(): void
    {
        $recipient = User::factory()->create();

        $notification = app(NotificationService::class)->create(
            recipientUserId: $recipient->id,
            type: 'content.review',
            title: 'Content needs your review',
        );

        $this->assertNull($notification->read_at);

        $updated = app(NotificationService::class)->markAsRead($notification);

        $this->assertNotNull($updated->read_at);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'recipient_user_id' => $recipient->id,
        ]);
    }
}
