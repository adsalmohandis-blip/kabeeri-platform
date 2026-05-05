<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Lead;

class LeadScoringService
{
    public function score(Lead $lead): Lead
    {
        $breakdown = [
            'source' => $lead->leadSource?->default_score ?? 0,
            'priority' => match ($lead->priority) {
                'high' => 20,
                'urgent' => 30,
                'low' => -5,
                default => 0,
            },
            'expected_value' => $this->valueScore($lead),
            'contact_details' => $this->contactDetailsScore($lead),
        ];

        $lead->forceFill([
            'score' => max(0, min(100, array_sum($breakdown))),
            'score_breakdown' => $breakdown,
            'scored_at' => now(),
        ])->save();

        return $lead->refresh();
    }

    protected function valueScore(Lead $lead): int
    {
        $value = (float) ($lead->expected_value ?? 0);

        return match (true) {
            $value >= 50000 => 25,
            $value >= 10000 => 15,
            $value > 0 => 5,
            default => 0,
        };
    }

    protected function contactDetailsScore(Lead $lead): int
    {
        $score = 0;

        if (filled($lead->email)) {
            $score += 10;
        }

        if (filled($lead->phone)) {
            $score += 10;
        }

        return $score;
    }
}
