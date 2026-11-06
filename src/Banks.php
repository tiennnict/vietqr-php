<?php

declare(strict_types=1);

namespace VietQr;

final class Banks
{
    private static ?array $banks = null;

    public static function all(): array
    {
        if (self::$banks !== null) {
            return self::$banks;
        }

        self::$banks = [];
        $raw = @\file_get_contents(__DIR__ . '/../data/banks.json');
        $data = $raw === false ? [] : \json_decode($raw, true);

        foreach (($data['banks'] ?? []) as $bank) {
            $bin = (string) ($bank['bin'] ?? '');
            if ($bin === '') {
                continue;
            }
            self::$banks[$bin] = [
                'bin' => $bin,
                'code' => (string) ($bank['code'] ?? ''),
                'short_name' => (string) ($bank['short_name'] ?? ''),
                'name' => (string) ($bank['name'] ?? ''),
                'transfer' => !empty($bank['transfer']),
            ];
        }

        return self::$banks;
    }

    public static function find(string $bin): ?array
    {
        return self::all()[$bin] ?? null;
    }

    public static function codeByBin(string $bin): ?string
    {
        $code = self::find($bin)['code'] ?? '';

        return $code === '' ? null : $code;
    }

    public static function nameByBin(string $bin): string
    {
        return self::find($bin)['short_name'] ?? $bin;
    }
}
