<?php

namespace App\Modules\CMS\Data;

use App\Models\ContentEntry;

class SeoData
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $canonicalUrl = null,
        public readonly ?string $ogTitle = null,
        public readonly ?string $ogDescription = null,
        public readonly ?int $ogImageMediaId = null,
        public readonly bool $noindex = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? $data['seo_title'] ?? null,
            description: $data['description'] ?? $data['seo_description'] ?? null,
            canonicalUrl: $data['canonical_url'] ?? null,
            ogTitle: $data['og_title'] ?? null,
            ogDescription: $data['og_description'] ?? null,
            ogImageMediaId: isset($data['og_image_media_id']) ? (int) $data['og_image_media_id'] : null,
            noindex: (bool) ($data['noindex'] ?? false),
        );
    }

    public static function fromContentEntry(ContentEntry $entry): self
    {
        $legacySeo = is_array($entry->seo) ? $entry->seo : [];

        return new self(
            title: $entry->seo_title ?? $legacySeo['title'] ?? null,
            description: $entry->seo_description ?? $legacySeo['description'] ?? null,
            canonicalUrl: $entry->canonical_url ?? $legacySeo['canonical_url'] ?? null,
            ogTitle: $entry->og_title ?? $legacySeo['og_title'] ?? null,
            ogDescription: $entry->og_description ?? $legacySeo['og_description'] ?? null,
            ogImageMediaId: $entry->og_image_media_id ?? $legacySeo['og_image_media_id'] ?? null,
            noindex: $entry->noindex || (bool) ($legacySeo['noindex'] ?? false),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toContentEntryAttributes(): array
    {
        return [
            'seo_title' => $this->title,
            'seo_description' => $this->description,
            'canonical_url' => $this->canonicalUrl,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image_media_id' => $this->ogImageMediaId,
            'noindex' => $this->noindex,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'canonical_url' => $this->canonicalUrl,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image_media_id' => $this->ogImageMediaId,
            'noindex' => $this->noindex,
        ];
    }
}
