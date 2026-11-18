<?php

namespace App\Services\Access;

use App\Models\User;
use App\Support\Access\Permissions;
use App\Support\Access\Roles;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

class StaffAccountService
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function create(User $actor, array $data): User
    {
        $this->ensureCanManage($actor);
        $roles = $this->validateRoles($data['roles'] ?? [], $actor);

        $staff = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $staff->syncRoles($roles);
        $this->audit->record('staff.created', $actor, User::class, $staff->id, ['roles' => $roles]);

        return $staff;
    }

    public function update(User $actor, User $staff, array $data): User
    {
        $this->ensureCanManage($actor);
        $this->ensureTargetIsManageable($actor, $staff);

        $changes = array_intersect_key($data, array_flip(['name', 'email', 'phone']));

        if (array_key_exists('is_active', $data)) {
            if ($staff->is($actor)) {
                throw new AuthorizationException('You cannot change your own account status.');
            }

            if (! $data['is_active'] && $staff->isSuperAdmin() && $this->activeSuperAdminCount() <= 1) {
                throw new AuthorizationException('The last active Super Administrator cannot be suspended.');
            }

            $changes['is_active'] = (bool) $data['is_active'];
            $changes['suspended_at'] = $data['is_active'] ? null : now();
        }

        if (array_key_exists('password', $data) && filled($data['password'])) {
            $changes['password'] = Hash::make($data['password']);
        }

        if (! empty($changes)) {
            $staff->update($changes);
        }

        if (array_key_exists('roles', $data)) {
            if ($staff->is($actor)) {
                throw new AuthorizationException('You cannot change your own roles.');
            }

            $roles = $this->validateRoles($data['roles'], $actor);

            if ($staff->isSuperAdmin() && ! in_array(Roles::SUPER_ADMIN, $roles, true) && $this->activeSuperAdminCount() <= 1) {
                throw new AuthorizationException('The last Super Administrator role cannot be removed.');
            }

            $staff->syncRoles($roles);
            $this->audit->record('staff.roles_updated', $actor, User::class, $staff->id, ['roles' => $roles]);
        }

        $this->audit->record('staff.updated', $actor, User::class, $staff->id, ['fields' => array_keys($changes)]);

        return $staff->refresh();
    }

    public function suspend(User $actor, User $staff): User
    {
        return $this->update($actor, $staff, ['is_active' => false]);
    }

    public function restore(User $actor, User $staff): User
    {
        return $this->update($actor, $staff, ['is_active' => true]);
    }

    public function availableRoles(): Collection
    {
        return Role::query()->orderBy('name')->get();
    }

    private function ensureCanManage(User $actor): void
    {
        if (! $actor->can(Permissions::STAFF_MANAGE)) {
            throw new AuthorizationException('You do not have permission to manage staff accounts.');
        }
    }

    private function ensureTargetIsManageable(User $actor, User $staff): void
    {
        if ($staff->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Administrator can manage another Super Administrator.');
        }
    }

    private function validateRoles(array $roles, User $actor): array
    {
        $roles = array_values(array_unique(array_filter($roles, 'is_string')));

        if ($roles === []) {
            throw new InvalidArgumentException('At least one role is required.');
        }

        $valid = Role::query()->whereIn('name', $roles)->pluck('name')->all();

        if (count($valid) !== count($roles)) {
            throw new InvalidArgumentException('One or more selected roles do not exist.');
        }

        if (in_array(Roles::SUPER_ADMIN, $roles, true) && ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Administrator can assign the Super Administrator role.');
        }

        return $roles;
    }

    private function activeSuperAdminCount(): int
    {
        return User::role(Roles::SUPER_ADMIN)
            ->where('is_active', true)
            ->whereNull('suspended_at')
            ->count();
    }
}
