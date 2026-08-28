<?php

namespace App\Services\Pdf\Mapper;

class AboutMapper
{
    public function map(array $data): ?array
    {
        if (empty($data['about'])) {
            return null;
        }

        return ['title' => 'About', 'text' => $data['about']];
    }
}
