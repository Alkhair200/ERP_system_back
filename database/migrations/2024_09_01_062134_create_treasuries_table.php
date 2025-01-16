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
        Schema::create('treasuries', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            // هل خزنه رئيسيه 0-1
            $table->tinyInteger('is_master')->default(0)->comment('هل خزنه رئيسيه 0-1');
            //  اخر ايصال للتحصيل
            $table->bigInteger('last_isal_exchange')->comment('رقم اخر ايصال للصرف');
            $table->bigInteger('last_isal_collect')->comment(' اخر ايصال للتحصيل');
            // $table->integer('added_by');
            $table->integer('com_code');
            $table->date('date');
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
        Schema::dropIfExists('treasuries');
    }
};
