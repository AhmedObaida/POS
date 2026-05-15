<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @requires extension pdo_sqlite
 */
class InvoiceStockTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser()
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::SLUG_ADMIN]);

        return User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_cannot_sell_more_than_stock()
    {
        $user = $this->adminUser();
        $cat = Category::query()->create(['name' => 'C', 'description' => null]);
        $product = Product::query()->create([
            'name' => 'P',
            'sku' => 'SKU1',
            'category_id' => $cat->id,
            'wholesale_price' => 10,
            'retail_price' => 12,
            'purchase_price' => 5,
            'stock_quantity' => 3,
            'minimum_stock_alert' => 0,
            'status' => Product::STATUS_ACTIVE,
        ]);
        $customer = Customer::query()->create([
            'name' => 'Cust',
            'phone' => null,
            'address' => null,
            'notes' => null,
            'total_debt' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('admin.invoices.store'), [
            'customer_id' => $customer->id,
            'pricing_type' => 'retail',
            'discount_amount' => 0,
            'tax_rate' => null,
            'paid_amount' => 0,
            'notes' => null,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10],
            ],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertSame(3, (int) $product->fresh()->stock_quantity);
    }

    public function test_sale_reduces_stock()
    {
        $user = $this->adminUser();
        $cat = Category::query()->create(['name' => 'C', 'description' => null]);
        $product = Product::query()->create([
            'name' => 'P',
            'sku' => 'SKU2',
            'category_id' => $cat->id,
            'wholesale_price' => 10,
            'retail_price' => 12,
            'purchase_price' => 5,
            'stock_quantity' => 5,
            'minimum_stock_alert' => 0,
            'status' => Product::STATUS_ACTIVE,
        ]);
        $customer = Customer::query()->create([
            'name' => 'Cust',
            'phone' => null,
            'address' => null,
            'notes' => null,
            'total_debt' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('admin.invoices.store'), [
            'customer_id' => $customer->id,
            'pricing_type' => 'retail',
            'discount_amount' => 0,
            'tax_rate' => null,
            'paid_amount' => 0,
            'notes' => null,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        $this->assertSame(3, (int) $product->fresh()->stock_quantity);
    }
}
