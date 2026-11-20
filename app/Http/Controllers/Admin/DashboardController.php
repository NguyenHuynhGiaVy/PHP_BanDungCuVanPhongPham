<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->hasPermission('view_dashboard')) {
            if ($user?->hasPermission('manage_products')) {
                return redirect()->route('admin.products.index');
            }
            if ($user?->hasPermission('manage_categories')) {
                return redirect()->route('admin.categories.index');
            }
            if ($user?->hasPermission('manage_inventory')) {
                return redirect()->route('admin.inventory.index');
            }
            if ($user?->hasPermission('manage_orders')) {
                return redirect()->route('admin.orders.index');
            }
            if ($user?->hasPermission('manage_customers')) {
                return redirect()->route('admin.customers.index');
            }
            if ($user?->hasPermission('manage_coupons')) {
                return redirect()->route('admin.coupons.index');
            }
            if ($user?->hasPermission('manage_reviews')) {
                return redirect()->route('admin.reviews.index');
            }
            if ($user?->hasPermission('manage_staff')) {
                return redirect()->route('admin.staff.index');
            }
            if ($user?->hasPermission('manage_permissions')) {
                return redirect()->route('admin.permissions.index');
            }

            abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }
        $monthlyRevenue = collect(range(5, 0))
            ->map(function (int $monthsAgo): array {
                $month = Carbon::now()->startOfMonth()->subMonths($monthsAgo);
                $revenue = Order::query()
                    ->where('payment_status', 'PAID')
                    ->where('order_status', '!=', 'CANCELLED')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount');

                $orders = Order::query()
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                return [
                    'label' => 'T'.$month->month,
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            });

        $orderStatusCounts = collect([
            'PENDING',
            'PROCESSING',
            'SHIPPED',
            'DELIVERED',
            'CANCELLED',
        ])->mapWithKeys(fn (string $status): array => [
            $status => Order::query()->where('order_status', $status)->count(),
        ]);

        return view('admin.dashboard', [
            'statistics' => [
                'products' => Product::query()->count(),
                'categories' => Category::query()->count(),
                'brands' => Brand::query()->count(),
                'suppliers' => Supplier::query()->count(),
                'customers' => User::query()
                    ->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))
                    ->count(),
                'orders' => Order::query()->count(),
                'revenue' => Order::query()
                    ->where('payment_status', 'PAID')
                    ->where('order_status', '!=', 'CANCELLED')
                    ->sum('total_amount'),
                'pendingReviews' => Review::query()->where('status', 0)->count(),
            ],
            'recentOrders' => Order::query()
                ->with('user:id,full_name,email')
                ->latest('id')
                ->limit(8)
                ->get(),
            'lowStock' => Inventory::query()
                ->with('variant.product')
                ->whereColumn('quantity', '<=', 'reserved_quantity')
                ->orderBy('quantity')
                ->limit(5)
                ->get(),
            'monthlyRevenue' => $monthlyRevenue,
            'orderStatusCounts' => $orderStatusCounts,
        ]);
    }

    public function exportExcel(): Response
    {
        $orders = Order::query()
            ->with('user:id,full_name,email')
            ->latest('id')
            ->get();

        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1">';
        $html .= '<tr><th colspan="7">BÁO CÁO THỐNG KÊ ĐƠN HÀNG</th></tr>';
        $html .= '<tr><th>Mã đơn</th><th>Khách hàng</th><th>Email</th><th>Thanh toán</th><th>Trạng thái</th><th>Tổng tiền</th><th>Ngày tạo</th></tr>';

        foreach ($orders as $order) {
            $html .= '<tr>';
            $html .= '<td>'.e($order->order_code).'</td>';
            $html .= '<td>'.e($order->user?->full_name ?? '').'</td>';
            $html .= '<td>'.e($order->user?->email ?? '').'</td>';
            $html .= '<td>'.e($order->payment_status).'</td>';
            $html .= '<td>'.e($order->order_status).'</td>';
            $html .= '<td>'.number_format((float) $order->total_amount, 0, ',', '.').' ₫</td>';
            $html .= '<td>'.$order->created_at->format('d/m/Y H:i').'</td>';
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bao-cao-don-hang-'.now()->format('Y-m-d').'.xls"',
        ]);
    }
}
