<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use OtimOtim\PesapalIntegrationPackage\Enums\TransactionStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesapal_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usable_id')->unsigned()->nullable();
            $table->string('usable_type')->nullable();
            $table->string('modelable_type')->nullable();
            $table->bigInteger('modelable_id')->unsigned()->nullable();
            $table->string('order_tracking_id')->nullable();
            $table->string('merchant_reference')->nullable();
            $table->unsignedFloat('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', TransactionStatusEnum::toArray())->default(TransactionStatusEnum::PENDING->name);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesapal_transactions');
    }
};
