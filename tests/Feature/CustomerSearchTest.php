<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @requires extension pdo_sqlite
 */
class CustomerSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::SLUG_ADMIN]);

        return User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_empty_query_returns_empty_json()
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->getJson(route('admin.customers.search', ['q' => '']))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_search_returns_matching_customers()
    {
        $user = $this->adminUser();
        Customer::query()->create([
            'name' => 'Acme Wholesale',
            'phone' => '0501111111',
            'address' => null,
            'notes' => null,
            'total_debt' => 0,
        ]);
        Customer::query()->create([
            'name' => 'Other Shop',
            'phone' => '0502222222',
            'address' => null,
            'notes' => null,
            'total_debt' => 0,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('admin.customers.search', ['q' => 'Acme']))
            ->assertOk();

        $response->assertJsonFragment(['name' => 'Acme Wholesale']);
        $this->assertCount(1, $response->json());
    }
}
