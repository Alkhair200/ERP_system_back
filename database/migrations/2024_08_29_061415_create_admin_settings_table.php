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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name',100);
            $table->string('address',250);
            $table->string('phone',30);
            $table->string('logo',200)->default('default.png');
            $table->tinyInteger('active')->default(1);
            $table->string('general_alert',150)->nullable();
            $table->integer('com_code');
            $table->unsignedBigInteger('customer_parent_account_num')
            ->nullable()->command('رقم الحساب الاب للعملاء');
            $table->unsignedBigInteger('supplier_parent_account_num')
            ->nullable()->command('رقم الحساب الاب للموردين');
            $table->unsignedBigInteger('em_parent_account_num')
            ->nullable()->command('رقم الحساب الاب للموظفين');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('customer_parent_account_num')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('supplier_parent_account_num')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('em_parent_account_num')->references('id')->on('emplyees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
