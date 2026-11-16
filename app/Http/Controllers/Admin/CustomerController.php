<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCustomerStatusRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:0,1'],
        ]);

        return view('admin.customers.index', [
            'customers' => User::query()
                ->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))
                ->withCount('orders')
                ->when($filters['q'] ?? null, static fn (Builder $query, string $term): Builder => $query
                    ->where(static fn (Builder $customerQuery): Builder => $customerQuery
                        ->where('full_name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%')
                        ->orWhere('phone', 'like', '%'.$term.'%')))
                ->when(isset($filters['status']), static fn (Builder $query): Builder => $query->where('status', $filters['status']))
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(User $customer): View
    {
        $this->ensureCustomer($customer);

        return view('admin.customers.show', [
            'customer' => $customer->load('role:id,name'),
            'orders' => $customer->orders()->latest('id')->paginate(10),
        ]);
    }

    public function update(UpdateCustomerStatusRequest $request, User $customer): RedirectResponse
    {
        $this->ensureCustomer($customer);
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Trạng thái tài khoản khách hàng đã được cập nhật.');
    }

    private function ensureCustomer(User $customer): void
    {
        abort_unless($customer->role()->where('name', 'CUSTOMER')->exists(), 404);
    }
}
