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
        Schema::create('treasuries_admins', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasuries_admins');
    }
};
