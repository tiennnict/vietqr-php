<?php

declare(strict_types=1);

namespace VietQr;

use InvalidArgumentException;

require_once __DIR__ . '/Vendor/qrcode.php';

final class QrCode
{
    public static function svg(
        string $text,
        string $errorCorrection = 'L',
        int $moduleSize = 8,
        int $margin = 32,
        string $darkColor = '#000000',
        string $lightColor = '#ffffff'
    ): string {
        if ($moduleSize < 1 || $margin < 0) {
            throw new InvalidArgumentException('Module size must be >= 1 and margin >= 0.');
        }

        $qr = \QRCode::getMinimumQRCode($text, self::level($errorCorrection));
        $count = $qr->getModuleCount();
        $size = $count * $moduleSize + $margin * 2;

        $path = '';
        for ($row = 0; $row < $count; $row++) {
            for ($col = 0; $col < $count; $col++) {
                if ($qr->isDark($row, $col)) {
                    $path .= 'M' . ($margin + $col * $moduleSize) . ' ' . ($margin + $row * $moduleSize)
                        . 'h' . $moduleSize . 'v' . $moduleSize . 'h-' . $moduleSize . 'z';
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '"'
            . ' width="' . $size . '" height="' . $size . '" shape-rendering="crispEdges">'
            . '<rect width="' . $size . '" height="' . $size . '" fill="' . \htmlspecialchars($lightColor, ENT_QUOTES) . '"/>'
            . '<path d="' . $path . '" fill="' . \htmlspecialchars($darkColor, ENT_QUOTES) . '"/>'
            . '</svg>';
    }

    public static function dataUri(
        string $text,
        string $errorCorrection = 'L',
        int $moduleSize = 8,
        int $margin = 32
    ): string {
        return 'data:image/svg+xml;base64,' . \base64_encode(self::svg($text, $errorCorrection, $moduleSize, $margin));
    }

    private static function level(string $level): int
    {
        switch (\strtoupper($level)) {
            case 'M':
                return QR_ERROR_CORRECT_LEVEL_M;
            case 'Q':
                return QR_ERROR_CORRECT_LEVEL_Q;
            case 'H':
                return QR_ERROR_CORRECT_LEVEL_H;
            default:
                return QR_ERROR_CORRECT_LEVEL_L;
        }
    }
}
