<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run()
    {
        $phones = Category::query()->firstOrCreate(
            ['name' => 'Mobile phones'],
            ['description' => 'Smartphones and devices']
        );
        $acc = Category::query()->firstOrCreate(
            ['name' => 'Accessories'],
            ['description' => 'Cases, chargers, cables']
        );

        $products = [
            ['name' => 'Phone Model A', 'sku' => 'PHONE-A', 'category_id' => $phones->id, 'retail' => 599, 'wholesale' => 520, 'cost' => 400, 'stock' => 25, 'min' => 3],
            ['name' => 'Phone Model B', 'sku' => 'PHONE-B', 'category_id' => $phones->id, 'retail' => 799, 'wholesale' => 690, 'cost' => 550, 'stock' => 12, 'min' => 2],
            ['name' => 'USB-C Cable', 'sku' => 'CAB-USBC', 'category_id' => $acc->id, 'retail' => 15, 'wholesale' => 8, 'cost' => 4, 'stock' => 200, 'min' => 30],
            ['name' => 'Glass protector', 'sku' => 'PROT-GL', 'category_id' => $acc->id, 'retail' => 12, 'wholesale' => 6, 'cost' => 3, 'stock' => 150, 'min' => 20],
        ];

        foreach ($products as $p) {
            Product::query()->updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'name' => $p['name'],
                    'category_id' => $p['category_id'],
                    'description' => null,
                    'wholesale_price' => $p['wholesale'],
                    'retail_price' => $p['retail'],
                    'purchase_price' => $p['cost'],
                    'stock_quantity' => $p['stock'],
                    'minimum_stock_alert' => $p['min'],
                    'status' => Product::STATUS_ACTIVE,
                ]
            );
        }

        Customer::query()->firstOrCreate(
            ['name' => 'Walk-in customer'],
            [
                'phone' => '0500000000',
                'address' => null,
                'notes' => 'Default retail customer',
                'total_debt' => 0,
            ]
        );
    }
}
