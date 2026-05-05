<?php

namespace App\Modules\Core\Services;

use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;

class PackageCatalogService
{
    /**
     * @param  array{category?: string, package_type?: string, publisher_type?: string}  $filters
     * @return Collection<int, Package>
     */
    public function list(array $filters = []): Collection
    {
        $query = Package::query()
            ->where('status', 'active')
            ->where('publisher_type', $filters['publisher_type'] ?? 'official')
            ->orderBy('name');

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['package_type'])) {
            $query->where('package_type', $filters['package_type']);
        }

        return $query->get();
    }
}
