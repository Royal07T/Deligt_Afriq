<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Address;
use App\Services\Commerce\AddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(private readonly AddressService $addressService) {}

    public function index(): View
    {
        return view('customer.addresses.index', [
            'addresses' => auth()->user()->addresses()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Address::class);

        return view('customer.addresses.create', ['address' => new Address]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $this->authorize('create', Address::class);
        $this->addressService->save($request->user(), $request->validated());

        return redirect()->route('customer.addresses.index')->with('status', 'Address saved.');
    }

    public function edit(Address $address): View
    {
        $this->authorize('update', $address);

        return view('customer.addresses.edit', ['address' => $address]);
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        $this->addressService->save($request->user(), $request->validated(), $address);

        return redirect()->route('customer.addresses.index')->with('status', 'Address updated.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        $this->addressService->delete($address);

        return redirect()->route('customer.addresses.index')->with('status', 'Address removed.');
    }

    public function default(Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        $this->addressService->makeDefault(auth()->user(), $address);

        return redirect()->route('customer.addresses.index')->with('status', 'Default address updated.');
    }
}
