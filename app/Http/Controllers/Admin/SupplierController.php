<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);

        return view('admin.suppliers.index', [
            'suppliers' => Supplier::query()
                ->when($filters['q'] ?? null, static fn ($query, string $term) => $query
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%'))
                ->withCount(['supplierProducts', 'inventoryTransactions'])
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier(['status' => 1])]);
    }

    public function store(SaveSupplierRequest $request): RedirectResponse
    {
        Supplier::query()->create($request->validated());

        return redirect()->route('admin.suppliers.index')->with('status', 'Nhà cung cấp đã được tạo.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.form', ['supplier' => $supplier]);
    }

    public function update(SaveSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.index')->with('status', 'Nhà cung cấp đã được cập nhật.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->supplierProducts()->exists() || $supplier->inventoryTransactions()->exists()) {
            return back()->withErrors([
                'supplier' => 'Nhà cung cấp đang gắn với sản phẩm hoặc lịch sử nhập kho. Hãy ngừng hoạt động thay vì xóa.',
            ]);
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('status', 'Nhà cung cấp đã được xóa.');
    }
}
