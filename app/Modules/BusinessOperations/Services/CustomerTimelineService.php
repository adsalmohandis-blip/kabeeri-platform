<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contact;
use Illuminate\Support\Collection;

class CustomerTimelineService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function forContact(Contact $contact): Collection
    {
        $items = collect([
            [
                'type' => 'contact.created',
                'occurred_at' => $contact->created_at,
                'subject' => $contact->display_name,
                'record_type' => Contact::class,
                'record_id' => $contact->id,
            ],
        ]);

        $contact->crmActivities()
            ->get()
            ->each(function ($activity) use ($items): void {
                $items->push([
                    'type' => 'crm_activity.'.$activity->activity_type,
                    'occurred_at' => $activity->completed_at ?? $activity->created_at,
                    'subject' => $activity->subject,
                    'record_type' => $activity::class,
                    'record_id' => $activity->id,
                    'status' => $activity->status,
                ]);
            });

        $contact->organization->leads()
            ->where('contact_id', $contact->id)
            ->get()
            ->each(function ($lead) use ($items): void {
                $items->push([
                    'type' => 'lead.created',
                    'occurred_at' => $lead->created_at,
                    'subject' => $lead->title ?? $lead->name,
                    'record_type' => $lead::class,
                    'record_id' => $lead->id,
                    'status' => $lead->status,
                ]);

                if ($lead->stage_changed_at !== null) {
                    $items->push([
                        'type' => 'lead.stage_changed',
                        'occurred_at' => $lead->stage_changed_at,
                        'subject' => $lead->salesPipelineStage?->name,
                        'record_type' => $lead::class,
                        'record_id' => $lead->id,
                        'status' => $lead->status,
                    ]);
                }
            });

        return $items
            ->sortByDesc('occurred_at')
            ->values();
    }
}
