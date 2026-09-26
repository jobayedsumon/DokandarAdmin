<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('success_topup_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('trxid')->unique();
            $table->string('service_type')->default('recharge'); // recharge, bill, drive
            $table->string('number')->nullable();
            $table->string('operator')->nullable();
            $table->decimal('amount', 24, 2)->default(0);
            $table->string('status')->default('pending'); // pending, processing, success, failed
            $table->text('gateway_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('success_topup_transactions');
    }
};
