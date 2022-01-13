<?php

namespace AcMarche\Duobac\Service;

class ArrayUtils
{
    public static function initArraMonths(): array
    {
        return [1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    }

    public static function resetKeys(array $data): array
    {
        return array_values($data);
    }
}
