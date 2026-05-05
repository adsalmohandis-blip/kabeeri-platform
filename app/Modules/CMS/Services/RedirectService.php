<?php

namespace App\Modules\CMS\Services;

use App\Models\Redirect;
use Illuminate\Validation\ValidationException;

class RedirectService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Redirect
    {
        $attributes['source_path'] = $this->normalizeSourcePath($attributes['source_path'] ?? '');

        $this->ensureSourcePathIsAvailable(
            organizationId: (int) $attributes['organization_id'],
            siteId: $attributes['site_id'] ?? null,
            sourcePath: $attributes['source_path'],
        );

        return Redirect::query()->create($attributes);
    }

    public function resolve(string $sourcePath, int $organizationId, ?int $siteId = null): ?Redirect
    {
        $query = Redirect::query()
            ->where('organization_id', $organizationId)
            ->where('source_path', $this->normalizeSourcePath($sourcePath))
            ->where('status', 'active');

        if ($siteId === null) {
            $query->whereNull('site_id');
        } else {
            $query->where('site_id', $siteId);
        }

        return $query->first();
    }

    public function recordHit(Redirect $redirect): Redirect
    {
        $redirect->increment('hit_count', 1, ['last_hit_at' => now()]);

        return $redirect->refresh();
    }

    public function normalizeSourcePath(string $sourcePath): string
    {
        $sourcePath = trim($sourcePath);

        if ($sourcePath === '') {
            throw ValidationException::withMessages([
                'source_path' => 'The source path is required.',
            ]);
        }

        if (! str_starts_with($sourcePath, '/')) {
            $sourcePath = '/'.$sourcePath;
        }

        return $sourcePath;
    }

    protected function ensureSourcePathIsAvailable(int $organizationId, mixed $siteId, string $sourcePath): void
    {
        $query = Redirect::query()
            ->where('organization_id', $organizationId)
            ->where('source_path', $sourcePath);

        if ($siteId === null) {
            $query->whereNull('site_id');
        } else {
            $query->where('site_id', $siteId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'source_path' => 'The source path already has a redirect in this site scope.',
            ]);
        }
    }
}
