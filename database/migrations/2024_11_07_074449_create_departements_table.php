<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // جدول إدارات المنشأة
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->string('name',100);
            $table->tinyInteger('active')->default(1);
            $table->string('address',240)->nullable();
            $table->string('phone',20)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departements');
    }
};
