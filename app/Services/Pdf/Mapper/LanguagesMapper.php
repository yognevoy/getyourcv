<?php

namespace App\Services\Pdf\Mapper;

class LanguagesMapper
{
    public function map(array $data): ?array
    {
        $entries = [];

        foreach ($data['languages'] ?? [] as $language) {
            if (empty($language['language'])) {
                continue;
            }

            $entries[] = ['title' => $language['language'], 'subtitle' => (string) ($language['level'] ?? '')];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Languages', 'entries' => $entries];
    }
}
