<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, 7)) . '.php';
    if (strpos($class, 'VietQr\\') === 0 && is_file($file)) {
        require $file;
    }
});

use VietQr\Banks;
use VietQr\Payload;
use VietQr\QrCode;
use VietQr\Text;

$failed = 0;
$check = static function (string $name, bool $ok) use (&$failed): void {
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . PHP_EOL;
    $failed += $ok ? 0 : 1;
};

$check('crc16 check value', Payload::crc16('123456789') === '29B1');
$check('toAscii', Text::toAscii('Nguyễn Ngọc Tiến Đặng') === 'Nguyen Ngoc Tien Dang');

$payload = Payload::build('970423', '2903678999', 500000, 'DHM2026 0903402903 42km Nguyễn Ngọc Tiến');
$check('payload prefix', strpos($payload, '000201010212') === 0);
$check('payload has amount', strpos($payload, '5406500000') !== false);
$check('payload crc', substr($payload, -4) === Payload::crc16(substr($payload, 0, -4)));
$check('static payload', strpos(Payload::build('970423', '123456'), '010211') !== false);

try {
    Payload::build('12', '123');
    $check('invalid bin rejected', false);
} catch (InvalidArgumentException $e) {
    $check('invalid bin rejected', true);
}

$svg = QrCode::svg($payload);
$check('svg output', strpos($svg, '<svg') === 0 && strpos($svg, '<path') !== false);
$check('data uri', strpos(QrCode::dataUri($payload), 'data:image/svg+xml;base64,') === 0);

$check('bank code', Banks::codeByBin('970423') === 'TPB');

exit($failed > 0 ? 1 : 0);
