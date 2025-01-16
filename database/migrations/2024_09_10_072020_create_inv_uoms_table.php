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
        // جدول الوحدات
        Schema::create('inv_uoms', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->unique();
            $table->integer('com_code');
            $table->tinyInteger('is_master')->default(0)->comment('هل وحدة رئيسيه 0-1');
            $table->tinyInteger('active')->default(1);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_uoms');
    }
};
