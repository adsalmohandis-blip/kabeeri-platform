<?php

namespace App\Modules\Core\Services;

class PackageManifestValidator
{
    /**
     * @param  array<string, mixed>  $manifest
     * @return array{valid: bool, errors: array<string, array<int, string>>}
     */
    public function validate(array $manifest): array
    {
        $errors = [];

        foreach (['key', 'name', 'version', 'package_type'] as $field) {
            if (! isset($manifest[$field]) || ! is_string($manifest[$field]) || trim($manifest[$field]) === '') {
                $errors[$field][] = 'This field is required and must be a non-empty string.';
            }
        }

        foreach (['permissions', 'dependencies', 'compatibility'] as $field) {
            if (isset($manifest[$field]) && ! is_array($manifest[$field])) {
                $errors[$field][] = 'This field must be an array when provided.';
            }
        }

        if (isset($manifest['key']) && is_string($manifest['key']) && ! preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $manifest['key'])) {
            $errors['key'][] = 'The key must use lowercase package identifier format.';
        }

        return [
            'valid' => $errors === [],
            'errors' => $errors,
        ];
    }
}
