<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Đảm bảo có đủ 3 vai trò: ADMIN (Quản lý), STAFF (Thành viên), CUSTOMER (Khách)
        $roles = [
            ['name' => 'ADMIN', 'description' => 'Quản trị viên hệ thống (Toàn quyền)'],
            ['name' => 'STAFF', 'description' => 'Thành viên / Nhân viên vận hành'],
            ['name' => 'CUSTOMER', 'description' => 'Khách mua sắm'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description'], 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // 2. Danh mục phân quyền sử dụng chi tiết
        $permissions = [
            // Nhóm 1: Công việc & Tổng quan
            [
                'code' => 'view_dashboard',
                'name' => 'Quyền xem báo cáo tổng thể',
                'description' => 'Quyền xem báo cáo tổng hợp kết quả hoạt động kinh doanh',
                'group' => 'Báo cáo & Tổng quan',
                'sort_order' => 1,
            ],
            [
                'code' => 'view_staff_tasks',
                'name' => 'Xem công việc của người khác',
                'description' => 'Xem công việc và đơn hàng giao cho người khác',
                'group' => 'Báo cáo & Tổng quan',
                'sort_order' => 2,
            ],
            [
                'code' => 'assign_staff_tasks',
                'name' => 'Quyền xem - giao công việc cho người khác',
                'description' => 'Quyền xem các công việc và phân công nhiệm vụ cho nhân viên',
                'group' => 'Báo cáo & Tổng quan',
                'sort_order' => 3,
            ],

            // Nhóm 2: Sản phẩm & Hàng hóa
            [
                'code' => 'manage_products',
                'name' => 'Tạo và chỉnh sửa sản phẩm mới',
                'description' => 'Quyền thêm sản phẩm, cập nhật giá bán và biến thể văn phòng phẩm',
                'group' => 'Sản phẩm & Danh mục',
                'sort_order' => 4,
            ],
            [
                'code' => 'manage_categories',
                'name' => 'Tạo nhóm danh mục & thương hiệu mới',
                'description' => 'Quyền tạo nhóm danh mục, nhãn hàng văn phòng phẩm mới',
                'group' => 'Sản phẩm & Danh mục',
                'sort_order' => 5,
            ],

            // Nhóm 3: Đơn hàng & Giao dịch
            [
                'code' => 'manage_orders',
                'name' => 'Xử lý và duyệt đơn hàng',
                'description' => 'Quyền xác nhận đơn, hủy đơn và cập nhật trạng thái đơn hàng',
                'group' => 'Đơn hàng & Giao dịch',
                'sort_order' => 6,
            ],
            [
                'code' => 'manage_inventory',
                'name' => 'Tạo phiếu nhập xuất kho hàng',
                'description' => 'Quyền lập phiếu nhập hàng mới từ nhà cung cấp và xuất kho hàng',
                'group' => 'Đơn hàng & Giao dịch',
                'sort_order' => 7,
            ],

            // Nhóm 4: Khách hàng & Hỗ trợ
            [
                'code' => 'manage_customers',
                'name' => 'Xem và quản lý khách hàng',
                'description' => 'Quyền xem danh sách thông tin và lịch sử mua sắm của khách hàng',
                'group' => 'Khách hàng & Hỗ trợ',
                'sort_order' => 8,
            ],
            [
                'code' => 'manage_reviews',
                'name' => 'Quyền kiểm duyệt đánh giá & thảo luận',
                'description' => 'Tạo thảo luận và bình luận, kiểm duyệt phản hồi của khách hàng',
                'group' => 'Khách hàng & Hỗ trợ',
                'sort_order' => 9,
            ],
            [
                'code' => 'manage_coupons',
                'name' => 'Tạo mã ưu đãi & khuyến mãi',
                'description' => 'Quyền thiết lập mã giảm giá và chiến dịch khuyến mãi bán hàng',
                'group' => 'Khách hàng & Hỗ trợ',
                'sort_order' => 10,
            ],

            // Nhóm 5: Hệ thống & Phân quyền
            [
                'code' => 'manage_staff',
                'name' => 'Quản lý thành viên & nhân viên',
                'description' => 'Quyền thêm mới, cấp tài khoản và chỉnh sửa nhân sự hệ thống',
                'group' => 'Hệ thống & Cài đặt',
                'sort_order' => 11,
            ],
            [
                'code' => 'manage_permissions',
                'name' => 'Phân quyền sử dụng hệ thống',
                'description' => 'Quyền thay đổi và cấu hình ma trận phân quyền các loại tài khoản',
                'group' => 'Hệ thống & Cài đặt',
                'sort_order' => 12,
            ],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name' => $p['name'],
                    'description' => $p['description'],
                    'group' => $p['group'],
                    'sort_order' => $p['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // 3. Gán quyền mặc định
        $adminRole = Role::where('name', 'ADMIN')->first();
        $staffRole = Role::where('name', 'STAFF')->first();
        $customerRole = Role::where('name', 'CUSTOMER')->first();

        $allPermissionIds = Permission::pluck('id')->toArray();

        // Admin: có tất cả các quyền
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissionIds);
        }

        // Staff (Thành viên): mặc định có các quyền vận hành (như trong ảnh mẫu)
        if ($staffRole) {
            $staffPermCodes = [
                'view_dashboard',
                'assign_staff_tasks',
                'manage_products',
                'manage_orders',
                'manage_inventory',
                'manage_customers',
                'manage_reviews',
            ];
            $staffPermIds = Permission::whereIn('code', $staffPermCodes)->pluck('id')->toArray();
            $staffRole->permissions()->sync($staffPermIds);
        }

        // Customer (Khách): có thể xem báo cáo tổng thể / thảo luận nếu cho phép
        if ($customerRole) {
            $customerPermCodes = [
                'view_dashboard',
            ];
            $customerPermIds = Permission::whereIn('code', $customerPermCodes)->pluck('id')->toArray();
            $customerRole->permissions()->sync($customerPermIds);
        }
    }
}
