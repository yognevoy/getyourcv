<?php

namespace App\Services\Pdf\Mapper;

class CertificationsMapper
{
    public function map(array $data): ?array
    {
        $entries = [];

        foreach ($data['certifications'] ?? [] as $certification) {
            if (empty($certification['title'])) {
                continue;
            }

            $entries[] = ['title' => $certification['title'], 'subtitle' => (string) ($certification['provider'] ?? '')];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Certifications', 'entries' => $entries];
    }
}
