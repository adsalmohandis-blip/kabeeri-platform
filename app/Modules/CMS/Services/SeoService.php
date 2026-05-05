<?php

namespace App\Modules\CMS\Services;

use App\Models\ContentEntry;
use App\Modules\CMS\Data\SeoData;

class SeoService
{
    public function read(ContentEntry $entry): SeoData
    {
        return SeoData::fromContentEntry($entry);
    }

    /**
     * @param  array<string, mixed>|SeoData  $seoData
     */
    public function write(ContentEntry $entry, array|SeoData $seoData): ContentEntry
    {
        $seoData = is_array($seoData) ? SeoData::fromArray($seoData) : $seoData;

        $entry->forceFill($seoData->toContentEntryAttributes())->save();

        return $entry->refresh();
    }
}
