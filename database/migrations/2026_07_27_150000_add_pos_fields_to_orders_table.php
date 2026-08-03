<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('dining_table_id')
                ->nullable()
                ->after('user_id')
                ->constrained('dining_tables')
                ->nullOnDelete();

            $table->string('order_source')->default('online')->after('status');
            $table->string('fulfillment_type')->nullable()->after('order_source');
            $table->foreignId('created_by')
                ->nullable()
                ->after('fulfillment_type')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dining_table_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['order_source', 'fulfillment_type']);
        });
    }
};
