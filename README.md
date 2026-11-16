# Stationery Store

Ứng dụng thương mại điện tử bán văn phòng phẩm, xây dựng bằng Laravel. Dự án có giao diện cửa hàng cho khách hàng và khu vực quản trị để quản lý sản phẩm, đơn hàng, kho hàng cùng các dữ liệu liên quan.

## Tính năng

### Cửa hàng

- Xem, tìm kiếm và lọc sản phẩm theo danh mục.
- Đăng ký, đăng nhập và quản lý thông tin tài khoản.
- Quản lý nhiều địa chỉ giao hàng.
- Thêm sản phẩm vào giỏ hàng, cập nhật số lượng và thực hiện đặt hàng.
- Xem đơn hàng đã đặt và lưu sản phẩm vào danh sách yêu thích.
- Áp dụng mã giảm giá khi đặt hàng.

### Quản trị

Khu vực quản trị dành cho tài khoản có quyền `ADMIN`, tại đường dẫn `/admin`:

- Theo dõi tổng quan cửa hàng.
- Quản lý sản phẩm, biến thể, hình ảnh, danh mục, thương hiệu và nhà cung cấp.
- Theo dõi tồn kho và ghi nhận điều chỉnh kho.
- Quản lý đơn hàng, trạng thái đơn, khách hàng, mã giảm giá và đánh giá.

## Công nghệ

- PHP 8.3 trở lên và Laravel 13.
- Composer 2.
- Node.js và npm (dùng Vite 8 để biên dịch tài nguyên giao diện).
- SQLite mặc định; có thể cấu hình thêm cơ sở dữ liệu khác Laravel hỗ trợ.
- Bootstrap 5 và Bootstrap Icons.

## Cài đặt và chạy trên Windows

Mở PowerShell tại thư mục gốc của dự án. Cần cài PHP, Composer, Node.js và npm trước khi thực hiện.

### 1. Cài các thư viện PHP và tạo file môi trường

```powershell
if (-not (Test-Path '.env')) {
    Copy-Item .env.example .env
}
```

Không chép đè `.env` đang có vì file có thể chứa cấu hình riêng của môi trường. Không đưa `.env` lên Git.

### 2. Chuẩn bị SQLite

Mặc định ứng dụng dùng SQLite. Tạo file cơ sở dữ liệu nếu file chưa tồn tại:

```powershell
if (-not (Test-Path 'database\database.sqlite')) {
    New-Item -ItemType File -Path 'database\database.sqlite' | Out-Null
}
```

Trong `.env`, kiểm tra `DB_CONNECTION=sqlite`. Để chạy gọn trên máy cá nhân, đặt thêm các driver sau:

```dotenv
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Các migration hiện tại chưa tạo bảng `sessions` hoặc `jobs`, vì vậy không dùng driver `database` cho session/queue nếu chưa tự bổ sung các bảng tương ứng.

### 3. Cài đặt dependency và khởi tạo ứng dụng

```powershell
composer run setup
```

Script `setup` cài thư viện PHP/JavaScript, tạo `APP_KEY`, chạy migration và build giao diện. Script không tự nạp dữ liệu mẫu.

### 4. Nạp dữ liệu mẫu

```powershell
php artisan db:seed
```

Seeder tạo dữ liệu khởi tạo gồm vai trò, tài khoản mẫu, danh mục, thương hiệu, nhà cung cấp, sản phẩm và mã giảm giá. Chỉ chạy seed trên cơ sở dữ liệu phát triển/trống; các seeder tạo dữ liệu mới và có thể bị trùng nếu chạy lại.

Tài khoản quản trị mẫu sau khi seed:

| Email | Mật khẩu |
| --- | --- |
| `admin@stationery.com` | `Admin@123456789` |

Đây là thông tin đăng nhập **chỉ dùng để phát triển cục bộ**. Hãy thay đổi hoặc xóa tài khoản mẫu trước khi đưa ứng dụng lên môi trường dùng thật.

### 5. Khởi chạy ứng dụng

```powershell
php artisan serve
```

Mở [http://127.0.0.1:8000](http://127.0.0.1:8000) trong trình duyệt. Khi đang phát triển giao diện, chạy thêm lệnh sau trong một cửa sổ terminal khác để Vite tự cập nhật tài nguyên:

```powershell
npm run dev
```

## Cấu hình cơ sở dữ liệu khác

Để dùng MySQL hoặc MariaDB, tạo cơ sở dữ liệu trước, sau đó cấu hình kết nối trong `.env`, ví dụ:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stationery_store
DB_USERNAME=root
DB_PASSWORD=
```

Sau khi cập nhật cấu hình, chạy lại:

```powershell
php artisan config:clear
php artisan migrate --seed
```

Không sử dụng tài khoản/mật khẩu ví dụ phía trên làm thông tin production. Với môi trường thật, hãy cấu hình thông tin xác thực an toàn và dùng các dịch vụ session, cache, queue phù hợp.

## Chạy kiểm thử

```powershell
php artisan test
```

Hoặc dùng script Composer đã khai báo trong dự án:

```powershell
composer run test
```

Các kiểm thử dùng SQLite trong bộ nhớ và không cần nạp dữ liệu mẫu vào cơ sở dữ liệu đang chạy.

## Lệnh hữu ích

```powershell
# Xem các route
php artisan route:list

# Chạy migration mới
php artisan migrate

# Chỉ nạp dữ liệu mẫu (chạy một lần trên database phù hợp)
php artisan db:seed

# Biên dịch tài nguyên để chạy ứng dụng
npm run build
```

`php artisan migrate:fresh --seed` sẽ xóa toàn bộ bảng hiện tại trước khi tạo lại và seed dữ liệu. Chỉ dùng lệnh này khi chấp nhận mất dữ liệu trong database đang chọn.

## Cấu trúc thư mục

```text
app/
  Actions/       Các thao tác nghiệp vụ
  Http/          Controllers, middleware và xử lý HTTP
  Models/        Eloquent models
database/
  migrations/    Cấu trúc cơ sở dữ liệu
  seeders/       Dữ liệu khởi tạo/mẫu
resources/
  views/         Giao diện Blade
  js/, css/      Tài nguyên frontend
routes/
  web.php        Các route của cửa hàng và quản trị
tests/
  Feature/       Kiểm thử chức năng
  Unit/          Kiểm thử đơn vị
```

## Khắc phục sự cố

- **Không kết nối được SQLite:** kiểm tra đã tạo `database\database.sqlite`, `DB_CONNECTION=sqlite` và đã chạy `php artisan migrate`.
- **Lỗi thiếu bảng `sessions` hoặc `jobs`:** đặt `SESSION_DRIVER=file` và `QUEUE_CONNECTION=sync` trong `.env`, hoặc tạo các migration/bảng tương ứng trước khi dùng driver database.
- **Thay đổi `.env` nhưng cấu hình chưa cập nhật:** chạy `php artisan config:clear`.
- **Trang báo không tìm thấy tài nguyên Vite:** chạy `npm install` rồi `npm run build`; khi phát triển, có thể chạy `npm run dev`.

