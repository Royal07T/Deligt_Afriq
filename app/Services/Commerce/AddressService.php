<?php

namespace App\Services\Commerce;

use App\Models\Address;
use App\Models\User;

class AddressService
{
    public function save(User $user, array $data, ?Address $address = null): Address
    {
        if ($data['is_default'] ?? false) {
            $user->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        if (! $address) {
            return $user->addresses()->create($data);
        }

        $address->update($data);

        return $address->refresh();
    }

    public function makeDefault(User $user, Address $address): Address
    {
        $user->addresses()->where('is_default', true)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return $address->refresh();
    }

    public function delete(Address $address): void
    {
        $address->delete();
    }
}
