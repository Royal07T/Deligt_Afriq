<?php

namespace Tests\Feature\Access;

use App\Models\User;
use App\Services\Access\StaffAccountService;
use App\Support\Access\Permissions;
use App\Support\Access\RoleCatalog;
use App\Support\Access\Roles;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_default_roles_and_granular_permissions_are_seeded(): void
    {
        $this->assertCount(count(Roles::ALL), Role::all());
        $this->assertCount(count(Permissions::ALL), Permission::all());

        $superAdmin = Role::findByName(Roles::SUPER_ADMIN);
        $this->assertCount(count(Permissions::ALL), $superAdmin->permissions);

        $accountant = Role::findByName(Roles::ACCOUNTANT);
        $this->assertTrue($accountant->hasPermissionTo(Permissions::PAYMENTS_REFUND));
        $this->assertFalse($accountant->hasPermissionTo(Permissions::STAFF_MANAGE));
    }

    public function test_staff_login_accepts_staff_and_rejects_customers(): void
    {
        $staff = User::factory()->create(['password' => 'fashion12345']);
        $staff->assignRole(Roles::STORE_MANAGER);

        $this->post(route('admin.login'), ['email' => $staff->email, 'password' => 'fashion12345'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($staff);

        $this->post(route('logout'));
        $this->assertGuest();

        $customer = User::factory()->create(['password' => 'fashion12345']);
        $this->post(route('admin.login'), ['email' => $customer->email, 'password' => 'fashion12345'])
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_permission_middleware_denies_staff_without_permission(): void
    {
        Route::middleware(['auth', 'permission:'.Permissions::PRODUCTS_CREATE])
            ->get('/__test/products/create', fn () => response('allowed'));

        $processingStaff = User::factory()->create();
        $processingStaff->assignRole(Roles::ORDER_PROCESSING);

        $storeManager = User::factory()->create();
        $storeManager->assignRole(Roles::STORE_MANAGER);

        $this->actingAs($processingStaff)->get('/__test/products/create')->assertForbidden();
        $this->actingAs($storeManager)->get('/__test/products/create')->assertOk();
    }

    public function test_super_admin_role_policy_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::SUPER_ADMIN);
        $superAdminRole = Role::findByName(Roles::SUPER_ADMIN);

        $this->assertTrue(Gate::forUser($admin)->allows('delete', Role::findByName(Roles::CONTENT_MANAGER)));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', $superAdminRole));
    }

    public function test_staff_account_service_creates_staff_and_records_activity(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(Roles::SUPER_ADMIN);

        $staff = app(StaffAccountService::class)->create($actor, [
            'name' => 'New Staff Member',
            'email' => 'staff@example.com',
            'password' => 'staffpassword1',
            'roles' => [Roles::CUSTOMER_SUPPORT],
        ]);

        $this->assertTrue($staff->hasRole(Roles::CUSTOMER_SUPPORT));
        $this->assertDatabaseHas('users', ['email' => 'staff@example.com']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'staff.created', 'user_id' => $actor->id]);
    }

    public function test_non_super_admin_cannot_assign_super_admin_role(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(Roles::ADMINISTRATOR);
        $actor->givePermissionTo(Permissions::STAFF_MANAGE);

        $this->expectException(AuthorizationException::class);

        app(StaffAccountService::class)->create($actor, [
            'name' => 'Escalation Attempt',
            'email' => 'escalation@example.com',
            'password' => 'staffpassword1',
            'roles' => [Roles::SUPER_ADMIN],
        ]);
    }

    public function test_staff_cannot_change_their_own_roles(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(Roles::SUPER_ADMIN);

        $this->expectException(AuthorizationException::class);

        app(StaffAccountService::class)->update($actor, $actor, ['roles' => [Roles::ACCOUNTANT]]);
    }

    public function test_staff_cannot_suspend_their_own_account(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(Roles::SUPER_ADMIN);

        $this->expectException(AuthorizationException::class);

        app(StaffAccountService::class)->suspend($actor, $actor);
    }

    public function test_cannot_manage_super_admin_without_super_admin_role(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(Roles::ADMINISTRATOR);
        $actor->givePermissionTo(Permissions::STAFF_MANAGE);
        $target = User::factory()->create();
        $target->assignRole(Roles::SUPER_ADMIN);

        $this->expectException(AuthorizationException::class);

        app(StaffAccountService::class)->update($actor, $target, ['name' => 'Changed']);
    }

    public function test_suspended_staff_are_signed_out_by_active_middleware(): void
    {
        $staff = User::factory()->create([
            'is_active' => false,
            'suspended_at' => now(),
        ]);
        $staff->assignRole(Roles::STORE_MANAGER);

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_role_catalog_sync_is_idempotent(): void
    {
        RoleCatalog::sync();
        RoleCatalog::sync();

        $this->assertCount(count(Roles::ALL), Role::all());
        $this->assertCount(count(Permissions::ALL), Permission::all());
    }
}
