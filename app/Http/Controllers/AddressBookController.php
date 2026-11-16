<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAddressRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressBookController extends Controller
{
    public function store(SaveAddressRequest $request): RedirectResponse
    {
        $validated = $request->safe()->only([
            'receiver_name',
            'phone',
            'province',
            'district',
            'ward',
            'detail_address',
        ]);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($request, $validated, $makeDefault): void {
            $user = $this->lockedUser($request);
            $hasDefaultAddress = $user->addresses()->where('is_default', true)->exists();

            if ($makeDefault || ! $hasDefaultAddress) {
                $user->addresses()->where('is_default', true)->update(['is_default' => false]);
            }

            $user->addresses()->create([
                ...$validated,
                'is_default' => $makeDefault || ! $hasDefaultAddress,
            ]);
        });

        return back()->with('status', 'Địa chỉ đã được thêm vào sổ địa chỉ.');
    }

    public function update(SaveAddressRequest $request, int $address): RedirectResponse
    {
        $validated = $request->safe()->only([
            'receiver_name',
            'phone',
            'province',
            'district',
            'ward',
            'detail_address',
        ]);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($request, $address, $validated, $makeDefault): void {
            $user = $this->lockedUser($request);
            $savedAddress = $user->addresses()->lockForUpdate()->findOrFail($address);

            if ($makeDefault) {
                $user->addresses()->where('is_default', true)->update(['is_default' => false]);
                $validated['is_default'] = true;
            }

            $savedAddress->update($validated);
        });

        return back()->with('status', 'Thông tin địa chỉ đã được cập nhật.');
    }

    public function setDefault(Request $request, int $address): RedirectResponse
    {
        DB::transaction(function () use ($request, $address): void {
            $user = $this->lockedUser($request);
            $savedAddress = $user->addresses()->lockForUpdate()->findOrFail($address);

            $user->addresses()->where('is_default', true)->update(['is_default' => false]);
            $savedAddress->update(['is_default' => true]);
        });

        return back()->with('status', 'Địa chỉ mặc định đã được cập nhật.');
    }

    public function destroy(Request $request, int $address): RedirectResponse
    {
        DB::transaction(function () use ($request, $address): void {
            $user = $this->lockedUser($request);
            $savedAddress = $user->addresses()->lockForUpdate()->findOrFail($address);
            $wasDefault = $savedAddress->is_default;

            $savedAddress->delete();

            if ($wasDefault) {
                $user->addresses()
                    ->latest('id')
                    ->first()
                    ?->update(['is_default' => true]);
            }
        });

        return back()->with('status', 'Địa chỉ đã được xóa khỏi sổ địa chỉ.');
    }

    private function lockedUser(Request $request): User
    {
        return User::query()
            ->whereKey($request->user()->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }
}
