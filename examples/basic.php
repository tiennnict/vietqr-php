<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use VietQr\Payload;
use VietQr\QrCode;

$payload = Payload::build('970423', '2903678999', 500000, 'Chuyen khoan dang ky');

echo $payload, PHP_EOL;
file_put_contents(__DIR__ . '/qr.svg', QrCode::svg($payload));
