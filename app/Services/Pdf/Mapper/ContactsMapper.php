<?php

namespace App\Services\Pdf\Mapper;

class ContactsMapper
{
    public function map(array $data): array
    {
        $contacts = [];

        if (! empty($data['email'])) {
            $contacts[] = ['label' => '', 'value' => $data['email'], 'url' => 'mailto:'.$data['email']];
        }

        foreach ($data['links'] ?? [] as $link) {
            if (empty($link['url']) || empty($link['label'])) {
                continue;
            }

            $contacts[] = ['label' => $link['label'], 'value' => '', 'url' => $link['url']];
        }

        return $contacts;
    }
}
