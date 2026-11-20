# Việt Thịnh — Website bán văn phòng phẩm

Hệ thống thương mại điện tử bán văn phòng phẩm và dụng cụ văn phòng, xây dựng bằng **Laravel 13**. Gồm giao diện cửa hàng cho khách hàng và khu vực quản trị cho quản trị viên, nhân viên.

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)

## Mục lục

- [Tính năng](#tính-năng)
- [Công nghệ](#công-nghệ)
- [Cài đặt](#cài-đặt)
- [Cấu hình](#cấu-hình)
- [Tài khoản mẫu](#tài-khoản-mẫu)
- [Phân quyền](#phân-quyền)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Các route chính](#các-route-chính)
- [Lệnh thường dùng](#lệnh-thường-dùng)
- [Khắc phục sự cố](#khắc-phục-sự-cố)
- [Bảo mật khi triển khai](#bảo-mật-khi-triển-khai)
- [Tác giả](#tác-giả)

## Tính năng

### Cửa hàng (khách hàng)

| Chức năng | Mô tả |
|-----------|-------|
| Duyệt và tìm kiếm sản phẩm | Lọc theo danh mục, thương hiệu, khoảng giá; sắp xếp theo giá, mới nhất |
| Giỏ hàng | Thêm, cập nhật số lượng, xóa sản phẩm |
| Yêu thích | Lưu sản phẩm để mua sau |
| Thanh toán | COD, VNPay (sandbox) và Mock để kiểm thử |
| Mã giảm giá | Nhập coupon khi thanh toán |
| Đơn hàng | Theo dõi lịch sử và trạng thái đơn |
| Tài khoản | Đăng ký, đăng nhập, quên mật khẩu, quản lý thông tin và địa chỉ giao hàng |
| Đánh giá | Khách hàng đánh giá sản phẩm, quản trị viên duyệt |
| Trang thông tin | Giới thiệu (`/gioi-thieu`), Liên hệ kèm FAQ và bản đồ (`/lien-he`) |

### Quản trị (`/admin`)

Yêu cầu tài khoản có vai trò `ADMIN` hoặc `STAFF`.

| Module | Chức năng |
|--------|-----------|
| Bảng điều khiển | Tổng quan doanh thu, đơn hàng, sản phẩm, cảnh báo tồn kho |
| Sản phẩm | Thêm, sửa, xóa sản phẩm; biến thể (màu, kích thước); hình ảnh |
| Danh mục, thương hiệu, nhà cung cấp | Quản lý danh mục dạng cây, thương hiệu và nhà cung cấp |
| Kho hàng | Theo dõi tồn kho, nhập và xuất kho |
| Đơn hàng | Xem chi tiết, cập nhật trạng thái |
| Khách hàng | Danh sách tài khoản khách hàng |
| Mã giảm giá | Tạo và quản lý coupon |
| Đánh giá | Duyệt và quản lý nhận xét |
| Nhân viên | Thêm và quản lý tài khoản nhân viên |
| Phân quyền | Ma trận phân quyền cho vai trò `STAFF` |

## Công nghệ

| Thành phần | Chi tiết |
|------------|----------|
| Backend | PHP 8.3+, Laravel 13, Composer 2 |
| Frontend | Blade, Bootstrap 5, Bootstrap Icons, Vite 8, font Be Vietnam Pro |
| Cơ sở dữ liệu | SQLite (mặc định) hoặc MySQL |
| Thanh toán | VNPay (môi trường sandbox) |
| Lưu trữ hình ảnh | Cloudinary (`cloudinary/cloudinary_php`) |

## Cài đặt

### Yêu cầu

- PHP 8.3 trở lên
- Composer 2
- Node.js (bản LTS) và npm
- Tài khoản [Cloudinary](https://cloudinary.com) và tài khoản [VNPay Sandbox](https://sandbox.vnpayment.vn) để dùng đầy đủ tính năng

### Các bước

**1. Lấy mã nguồn và tạo file môi trường**

```bash
git clone <url-repository>
cd <thu-muc-du-an>
cp .env.example .env          # Windows PowerShell: Copy-Item .env.example .env
```

**2. Cài dependencies**

```bash
composer update
npm install
```

> Dùng `composer update` thay vì `composer install` để đảm bảo tương thích với PHP 8.3.

**3. Cấu hình `.env`**: điền khóa Cloudinary và VNPay, xem mục [Cấu hình](#cấu-hình).

**4. Tạo file SQLite** (bỏ qua nếu dùng MySQL)

```bash
touch database/database.sqlite   # Windows PowerShell: New-Item database\database.sqlite -Force
```

**5. Khởi tạo ứng dụng**

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

**6. Chạy server**

```bash
php artisan serve
```

Truy cập [http://localhost:8000](http://localhost:8000). Khi phát triển giao diện, chạy thêm `npm run dev` để tự động cập nhật.

## Cấu hình

Sao chép `.env.example` thành `.env` rồi điền các biến sau (các biến còn lại giữ mặc định khi chạy local):

```dotenv
APP_NAME="Việt Thịnh"
APP_URL=http://localhost:8000
APP_LOCALE=vi

# Cơ sở dữ liệu (SQLite mặc định)
DB_CONNECTION=sqlite
# MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=<ten-database>
# DB_USERNAME=root
# DB_PASSWORD=

# Session, cache, queue lưu trong cơ sở dữ liệu (cần chạy migrate)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Thư: mặc định ghi vào storage/logs/laravel.log
MAIL_MAILER=log

# VNPay
VNPAY_TMN_CODE=
VNPAY_HASH_SECRET=
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/checkout/vnpay-return

# Cloudinary: dùng CLOUDINARY_URL hoặc bộ ba CLOUD_NAME / API_KEY / API_SECRET
CLOUDINARY_URL=
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_FOLDER=viet-thinh
```

> Với `SESSION_DRIVER`, `CACHE_STORE` và `QUEUE_CONNECTION` là `database`, bảng tương ứng được tạo khi chạy `php artisan migrate`. Nếu dùng hàng đợi, chạy thêm `php artisan queue:work`.

### Cloudinary

Ảnh sản phẩm và các ảnh tải lên từ khu vực quản trị được lưu trên Cloudinary thông qua `App\Services\CloudinaryStorage`.

1. Đăng nhập [Cloudinary Console](https://console.cloudinary.com), mở **Settings → API Keys**.
2. Điền **một trong hai** cách:
   - `CLOUDINARY_URL=cloudinary://<API_KEY>:<API_SECRET>@<CLOUD_NAME>`, hoặc
   - `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`.
3. `CLOUDINARY_FOLDER` là thư mục gốc chứa ảnh của dự án trên Cloudinary (mặc định `viet-thinh`), ảnh được chia tiếp thư mục con theo loại.
4. Cơ sở dữ liệu lưu ảnh dưới dạng `cloudinary://<public_id>`; khi xóa hoặc thay ảnh, hệ thống tự xóa tệp tương ứng trên Cloudinary.

Nếu chưa cấu hình, chức năng tải ảnh sẽ báo lỗi yêu cầu điền biến môi trường.

### VNPay

Thanh toán trực tuyến qua `App\Services\VNPayService` (chữ ký HMAC SHA512, phiên bản API 2.1.0).

1. Đăng ký merchant sandbox tại [sandbox.vnpayment.vn](https://sandbox.vnpayment.vn) để nhận `TMN Code` và `Hash Secret`, điền vào `VNPAY_TMN_CODE` và `VNPAY_HASH_SECRET`.
2. `VNPAY_RETURN_URL` phải trỏ tới route `/checkout/vnpay-return` của ứng dụng và truy cập được từ trình duyệt của người thanh toán.
3. Sandbox không cần thẻ thật. Khi lên production, đổi `VNPAY_URL` sang địa chỉ chính thức do VNPay cung cấp.

## Tài khoản mẫu

Sau khi chạy `php artisan migrate --seed` (chỉ dùng khi phát triển):

| Vai trò | Email | Mật khẩu | Truy cập |
|---------|-------|----------|----------|
| Admin | `admin@stationery.com` | `Admin@123` | `/admin` |
| Khách hàng | `customer1@gmail.com` | `Customer@123` | Cửa hàng |

> Chỉ dùng cho môi trường phát triển. Đổi hoặc xóa các tài khoản này trước khi triển khai.

## Phân quyền

Hệ thống Role–Permission tùy chỉnh với ba vai trò:

| Vai trò | Mô tả | Phạm vi |
|---------|-------|---------|
| `ADMIN` | Quản trị viên | Toàn quyền trong `/admin` |
| `STAFF` | Nhân viên | `/admin` với các quyền được cấp |
| `CUSTOMER` | Khách hàng | Chỉ giao diện cửa hàng |

Admin vào **Quản trị → Phân quyền** để bật hoặc tắt từng quyền của `STAFF` bằng ma trận checkbox.

## Cấu trúc thư mục

```text
app/
├── Actions/          Nghiệp vụ (CreateOrder, CreateMockOrder, ...)
├── Http/
│   ├── Controllers/  Controller (Auth, Admin, Store, Checkout, ...)
│   ├── Middleware/   Phân quyền (EnsureUserIsAdmin, EnsureUserHasPermission)
│   └── Requests/     Validation
├── Models/           Eloquent models (User, Product, Order, Role, Permission, ...)
└── Services/         Dịch vụ bên ngoài (VNPayService, ...)

database/
├── migrations/       Cấu trúc cơ sở dữ liệu
└── seeders/          Dữ liệu mẫu

resources/
├── views/
│   ├── admin/        Giao diện quản trị
│   ├── auth/         Đăng nhập, đăng ký, quên mật khẩu
│   ├── checkout/     Đặt hàng và thanh toán
│   ├── layouts/      Layout (topbar, navigation, footer)
│   └── pages/        Trang tĩnh (giới thiệu, liên hệ)
├── css/              CSS tùy chỉnh
└── js/               JavaScript

routes/
└── web.php           Toàn bộ route (store, admin, auth, checkout, pages)
```

## Các route chính

| Route | Mô tả |
|-------|-------|
| `/` | Trang chủ |
| `/products` | Danh sách sản phẩm |
| `/cart` | Giỏ hàng |
| `/checkout` | Thanh toán (COD, VNPay, Mock) |
| `/checkout/vnpay-return` | Callback VNPay |
| `/orders` | Lịch sử đơn hàng |
| `/wishlist` | Sản phẩm yêu thích |
| `/login`, `/register`, `/forgot-password` | Xác thực |
| `/gioi-thieu`, `/lien-he` | Giới thiệu, liên hệ |
| `/admin` | Bảng điều khiển quản trị |
| `/admin/products` | Quản lý sản phẩm |
| `/admin/staff` | Quản lý nhân viên |
| `/admin/permissions` | Ma trận phân quyền |

## Lệnh thường dùng

```bash
php artisan migrate                  # chạy migration mới
php artisan migrate:fresh --seed     # reset và seed lại (XÓA TOÀN BỘ DỮ LIỆU)
php artisan db:seed                  # chỉ chạy seeder
php artisan route:list               # xem danh sách route
php artisan view:clear               # xóa cache view
php artisan config:clear             # xóa cache cấu hình
php artisan test                     # chạy kiểm thử
npm run dev                          # chạy Vite khi phát triển
npm run build                        # build tài nguyên production
```

## Khắc phục sự cố

| Lỗi | Cách xử lý |
|-----|-----------|
| `No such table: sessions` (hoặc `cache`, `jobs`) | Chưa chạy migration: `php artisan migrate`. Hoặc tạm đặt `SESSION_DRIVER=file` rồi `php artisan config:clear` |
| Lỗi phiên bản `symfony/yaml` | Dùng `composer update` thay cho `composer install` |
| Vite không tìm thấy tài nguyên | `npm install && npm run build` |
| Cấu hình không được cập nhật | `php artisan config:clear && php artisan cache:clear` |
| SQLite không kết nối được | Kiểm tra file `database/database.sqlite` tồn tại và `DB_CONNECTION=sqlite` |
| Lỗi trùng dữ liệu khi seed | `php artisan migrate:fresh --seed` |
| Giao diện không đổi sau khi sửa Blade | `php artisan view:clear` |
| Không tải được ảnh lên | Kiểm tra `CLOUDINARY_URL` (hoặc bộ ba `CLOUD_NAME`, `API_KEY`, `API_SECRET`), sau đó `php artisan config:clear` |
| Không nhận được email đặt lại mật khẩu | Với `MAIL_MAILER=log`, nội dung thư và link đặt lại nằm trong `storage/logs/laravel.log`. Cấu hình SMTP thật khi triển khai |
| VNPay báo sai chữ ký | Kiểm tra `VNPAY_TMN_CODE`, `VNPAY_HASH_SECRET` và `VNPAY_RETURN_URL` |

## Bảo mật khi triển khai

- Không commit file `.env`. Không ghi khóa Cloudinary hoặc VNPay vào README hay mã nguồn.
- Đổi hoặc xóa toàn bộ tài khoản mẫu; dùng khóa Cloudinary và VNPay của môi trường production.
- Đặt `APP_ENV=production`, `APP_DEBUG=false` và `APP_URL` đúng tên miền; bật HTTPS.
- Cân nhắc chuyển `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` sang Redis khi lưu lượng lớn.
- Cấu hình `MAIL_*` bằng dịch vụ gửi thư thật để chức năng quên mật khẩu hoạt động.

## Tác giả

**Thịnh Vũ**

Xây dựng với Laravel, Bootstrap, Cloudinary và VNPay.