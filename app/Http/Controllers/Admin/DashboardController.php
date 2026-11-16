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
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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
        ]);
    }
}
