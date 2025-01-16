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
        Schema::create('treasuries__deliveries', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->string('treasuries_can_delivery_id',100)->comment('الخزنة التي يتم تسليمها');
            $table->unsignedBigInteger('treasury_id')->nullable()->comment('الخزنة التي تستلم');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasuries__deliveries');
    }
};
