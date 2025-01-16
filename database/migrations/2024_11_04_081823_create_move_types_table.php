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
        // الحركة علي الخزينه
        Schema::create('move_types', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            // 1- صرف نقدية
            // 2- تحصيل نقديه
            $table->tinyInteger('in_screen')->command('1-dissmissal 2-collect');
            $table->tinyInteger('is_private_internal')->default(0);
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('move_types');
    }
};
