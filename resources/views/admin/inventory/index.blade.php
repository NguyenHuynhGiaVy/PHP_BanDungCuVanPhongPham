@extends('layouts.admin')

@section('title', 'Quản lý tồn kho | Stationery Store')
@section('page-heading', 'Tồn kho')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">VẬN HÀNH</span><h1>Tồn kho</h1><p>Mọi điều chỉnh đều được ghi vào sổ giao dịch.</p></div>
    </div>
    <div class="admin-panel mb-4">
        <div class="admin-panel-heading">
            <div><h2>Số lượng theo biến thể</h2><p>{{ $inventories->total() }} biến thể</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="inventory-search">Tìm SKU hoặc sản phẩm</label>
                <input class="form-control form-control-sm" id="inventory-search" name="q" value="{{ request('q') }}" placeholder="SKU hoặc tên sản phẩm">
                <button class="btn btn-outline-primary btn-sm" type="submit">Tìm</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Sản phẩm / SKU</th><th>Tồn kho</th><th>Đã giữ</th><th>Khả dụng</th><th>Điều chỉnh</th></tr></thead>
                <tbody>
                    @forelse ($inventories as $inventory)
                        <tr>
                            <td><strong>{{ $inventory->variant->product->name }}</strong><small class="d-block text-secondary">{{ $inventory->variant->sku }} · {{ $inventory->variant->variant_name }}</small></td>
                            <td>{{ number_format($inventory->quantity) }}</td>
                            <td>{{ number_format($inventory->reserved_quantity) }}</td>
                            <td>{{ number_format(max(0, $inventory->quantity - $inventory->reserved_quantity)) }}</td>
                            <td>
                                <form action="{{ route('admin.inventory.adjust', $inventory) }}" method="POST" class="admin-stock-adjust-form">
                                    @csrf
                                    <select class="form-select form-select-sm" name="transaction_type" aria-label="Loại giao dịch" required>
                                        <option value="IMPORT">Nhập kho</option>
                                        <option value="EXPORT">Xuất kho</option>
                                    </select>
                                    <input class="form-control form-control-sm" name="quantity" type="number" min="1" max="4294967295" placeholder="SL" aria-label="Số lượng" required>
                                    <select class="form-select form-select-sm" name="supplier_id" aria-label="Nhà cung cấp (khi nhập)">
                                        <option value="">Không chọn NCC</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control form-control-sm" name="note" maxlength="2000" placeholder="Ghi chú (không bắt buộc)" aria-label="Ghi chú">
                                    <button class="btn btn-primary btn-sm" type="submit">Ghi nhận</button>
                                </form>
                                @error('quantity')<small class="d-block text-danger mt-1">{{ $message }}</small>@enderror
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="admin-empty-note">Chưa có tồn kho để hiển thị.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $inventories->links() }}</div>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading"><div><h2>Giao dịch gần đây</h2><p>20 giao dịch mới nhất</p></div></div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Thời gian</th><th>Sản phẩm / SKU</th><th>Loại</th><th>Số lượng</th><th>Nhà cung cấp</th><th>Ghi chú</th></tr></thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $transaction->variant->product->name }}<small class="d-block text-secondary">{{ $transaction->variant->sku }}</small></td>
                            <td><span class="badge {{ $transaction->transaction_type === 'IMPORT' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $transaction->transaction_type }}</span></td>
                            <td>{{ number_format($transaction->quantity) }}</td>
                            <td>{{ $transaction->supplier?->name ?? '—' }}</td>
                            <td>{{ $transaction->note ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="admin-empty-note">Chưa có giao dịch tồn kho.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
