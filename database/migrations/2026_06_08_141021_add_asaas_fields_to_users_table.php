<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->after('password');
            $table->string('asaas_subscription_id')->nullable()->after('asaas_customer_id');
            $table->string('plan_type')->default('free')->after('asaas_subscription_id'); // free, pro, max
            $table->string('subscription_status')->nullable()->after('plan_type'); // ACTIVE, OVERDUE, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'asaas_customer_id',
                'asaas_subscription_id',
                'plan_type',
                'subscription_status'
            ]);
        });
    }
};
