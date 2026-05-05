<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\VerificationDocument;
use App\Models\VerificationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VerificationDocument>
 */
class VerificationDocumentFactory extends Factory
{
    protected $model = VerificationDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'verification_request_id' => VerificationRequest::factory(),
            'media_asset_id' => function (array $attributes): int {
                $request = VerificationRequest::query()->findOrFail($attributes['verification_request_id']);
                $filename = Str::slug(fake()->words(2, true)).'.pdf';

                return MediaAsset::query()->create([
                    'organization_id' => $request->organization_id,
                    'site_id' => null,
                    'company_id' => $request->company_id,
                    'uploaded_by' => $request->requested_by,
                    'disk' => 'private',
                    'path' => 'organizations/'.$request->organization_id.'/verification/'.$filename,
                    'relative_path' => 'verification/'.$filename,
                    'filename' => $filename,
                    'original_filename' => $filename,
                    'mime_type' => 'application/pdf',
                    'extension' => 'pdf',
                    'size_bytes' => fake()->numberBetween(10000, 500000),
                    'width' => null,
                    'height' => null,
                    'visibility' => 'private',
                    'alt_text' => null,
                    'caption' => null,
                    'checksum' => Str::random(16),
                    'metadata' => ['source' => 'verification_document_factory'],
                ])->id;
            },
            'document_type' => fake()->randomElement([
                'commercial_registration',
                'tax_certificate',
                'identity_proof',
            ]),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
