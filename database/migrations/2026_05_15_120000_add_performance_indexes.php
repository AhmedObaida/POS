<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('payment_status');
            $table->index(['customer_id', 'created_at']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
            $table->index('category_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('total_debt');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_date');
            $table->index(['customer_id', 'payment_date']);
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'payment_date']);
            $table->dropIndex(['payment_date']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['total_debt']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'created_at']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
        });
    }
}
