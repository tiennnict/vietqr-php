# vietqr-php

**EN:** Pure-PHP library that builds VietQR (NAPAS 247 interbank) payloads and renders QR codes **offline** as SVG.

**VI:** Thư viện PHP thuần giúp dựng payload VietQR (chuyển nhanh NAPAS 247) và vẽ mã QR dạng SVG **hoàn toàn cục bộ**.

---

## Features / Tính năng

| EN | VI |
|---|---|
| EMV payload with CRC-16/CCITT-FALSE (dynamic with amount, or static without) | Payload EMV kèm CRC-16/CCITT-FALSE (động có số tiền, hoặc tĩnh không số tiền) |
| QR as SVG string or `data:` URI, no external API, no GD/mbstring/Composer dependency | QR dạng chuỗi SVG hoặc `data:` URI, không gọi API ngoài, không cần GD/mbstring/Composer |
| Vietnamese text to ASCII (diacritics, `đ/Đ`) for the transfer message | Bỏ dấu tiếng Việt (kể cả `đ/Đ`) cho nội dung chuyển khoản |
| Bank list by BIN (`data/banks.json`) | Danh sách ngân hàng theo mã BIN (`data/banks.json`) |

Requirements / Yêu cầu: PHP >= 7.4. The `intl` extension is optional (Unicode normalization). / Extension `intl` là tùy chọn (chuẩn hóa Unicode).

## Install / Cài đặt

```bash
composer require your-vendor/vietqr-php
```

Without Composer / Không dùng Composer: copy the repository and `require` the files in `src/` (or register a PSR-4 autoloader for the `VietQr\` namespace → `src/`).

## Usage / Sử dụng

```php
<?php

use VietQr\Banks;
use VietQr\Payload;
use VietQr\QrCode;

$bin     = '970423';
$account = '2903678999';

$payload = Payload::build($bin, $account, 500000, 'Chuyển khoản đăng ký');

echo QrCode::svg($payload);

echo '<img src="' . QrCode::dataUri($payload) . '" alt="VietQR">';

echo Banks::nameByBin($bin);
```

EN: Omit the amount (or pass `0`) to get a static QR where the payer types the amount.
VI: Bỏ qua số tiền (hoặc truyền `0`) để có QR tĩnh, người chuyển tự nhập số tiền.

## API

| Class | Method | EN | VI |
|---|---|---|---|
| `Payload` | `build($bin, $account, $amount = 0, $message = '')` | Build the EMV string. Throws `InvalidArgumentException` on invalid input. | Dựng chuỗi EMV. Ném `InvalidArgumentException` khi dữ liệu sai. |
| `Payload` | `crc16($data)` | CRC-16/CCITT-FALSE, 4 hex chars. | CRC-16/CCITT-FALSE, 4 ký tự hex. |
| `QrCode` | `svg($text, $errorCorrection = 'L', $moduleSize = 8, $margin = 32, $dark, $light)` | SVG string. Levels `L`, `M`, `Q`, `H`. | Chuỗi SVG. Mức sửa lỗi `L`, `M`, `Q`, `H`. |
| `QrCode` | `dataUri(...)` | Base64 SVG data URI for `<img src>`. | Data URI SVG base64 cho `<img src>`. |
| `Banks` | `all()`, `find()`, `codeByBin()`, `nameByBin()` | Bank lookup by BIN. | Tra cứu ngân hàng theo BIN. |
| `Text` | `toAscii()`, `message()` | Strip diacritics; message capped at 95 chars. | Bỏ dấu; nội dung cắt tối đa 95 ký tự. |

## Test / Kiểm thử

```bash
php tests/run.php
```

## Trademarks and disclaimer / Thương hiệu và miễn trừ trách nhiệm

**EN**

- **VietQR** is a name and brand of its respective owner; see <https://vietqr.net/> for the official specification and brand rules. This project is an **independent, unofficial, community** implementation. It is **not** affiliated with, endorsed by, or sponsored by VietQR, NAPAS, or any bank.
- **QR Code** is a registered trademark of DENSO WAVE INCORPORATED.
- All other product, bank and company names are trademarks of their owners and are used only to identify compatibility.
- The bank list source (`https://api.vietqr.io/v2/banks`) is operated by a third party and may change without notice. Check its terms of use before commercial use.
- The software is provided "as is" (see [LICENSE](LICENSE)). Always test with a small real transfer and confirm with your bank before using it in production. The MIT license applies to the code only and grants no rights to any trademark.

**VI**

- **VietQR** là tên và thương hiệu của chủ sở hữu tương ứng; tham khảo đặc tả và quy định sử dụng thương hiệu chính thức tại <https://vietqr.net/>. Dự án này là bản triển khai **độc lập, không chính thức, do cộng đồng đóng góp**, **không** liên kết, không được bảo trợ hay xác nhận bởi VietQR, NAPAS hoặc bất kỳ ngân hàng nào.
- **QR Code** là nhãn hiệu đã đăng ký của DENSO WAVE INCORPORATED.
- Mọi tên sản phẩm, ngân hàng, công ty khác là thương hiệu của chủ sở hữu và chỉ dùng để chỉ khả năng tương thích.
- Nguồn danh sách ngân hàng (`https://api.vietqr.io/v2/banks`) do bên thứ ba vận hành, có thể thay đổi mà không báo trước. Hãy kiểm tra điều khoản sử dụng trước khi dùng thương mại.
- Phần mềm được cung cấp "nguyên trạng" (xem [LICENSE](LICENSE)). Luôn thử bằng một giao dịch thật số tiền nhỏ và xác nhận với ngân hàng trước khi dùng thực tế. Giấy phép MIT chỉ áp dụng cho mã nguồn, không cấp quyền nào đối với bất kỳ thương hiệu nào.

## License / Giấy phép

MIT — see [LICENSE](LICENSE). The bundled QR encoder (`src/Vendor/qrcode.php`) is by Kazuhiko Arase, MIT; see [NOTICE](NOTICE). / Bộ mã hóa QR đi kèm (`src/Vendor/qrcode.php`) của Kazuhiko Arase, MIT; xem [NOTICE](NOTICE).
