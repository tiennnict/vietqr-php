<?php

declare(strict_types=1);

namespace VietQr;

use InvalidArgumentException;

final class Payload
{
    public const MAX_MESSAGE_LENGTH = 95;

    public static function build(string $bin, string $account, int $amount = 0, string $message = ''): string
    {
        if (!\preg_match('/^[0-9]{6}$/', $bin)) {
            throw new InvalidArgumentException('Bank BIN must be exactly 6 digits.');
        }
        if (!\preg_match('/^[A-Za-z0-9]{1,19}$/', $account)) {
            throw new InvalidArgumentException('Account number must be 1-19 alphanumeric characters.');
        }
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must not be negative.');
        }

        $beneficiary = self::field('00', $bin) . self::field('01', $account);
        $merchantInfo = self::field('00', 'A000000727')
            . self::field('01', $beneficiary)
            . self::field('02', 'QRIBFTTA');

        $payload = self::field('00', '01')
            . self::field('01', $amount > 0 ? '12' : '11')
            . self::field('38', $merchantInfo)
            . self::field('53', '704');

        if ($amount > 0) {
            $payload .= self::field('54', (string) $amount);
        }

        $payload .= self::field('58', 'VN');

        $message = Text::message($message, self::MAX_MESSAGE_LENGTH);
        if ($message !== '') {
            $payload .= self::field('62', self::field('08', $message));
        }

        $payload .= '6304';

        return $payload . self::crc16($payload);
    }

    public static function crc16(string $data): string
    {
        $crc = 0xFFFF;
        $length = \strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= \ord($data[$i]) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) & 0xFFFF : ($crc << 1) & 0xFFFF;
            }
        }

        return \sprintf('%04X', $crc);
    }

    private static function field(string $id, string $value): string
    {
        return $id . \sprintf('%02d', \strlen($value)) . $value;
    }
}
