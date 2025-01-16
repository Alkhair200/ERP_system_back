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
        Schema::create('treasuries_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->bigInteger('auto_serial');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->unsignedBigInteger('admin_shift_id')->nullable();
            $table->unsignedBigInteger('move_type_id')->nullable()->command('نوع حركة النقديه');
            $table->bigInteger('the_foregin_key')->nullable()->command('كود الجدول الاخر المرتبط بالحركة');
            $table->bigInteger('account_num')->nullable();
            $table->tinyInteger('is_account')->default(1)->command('هل هو حساب مالي');
            $table->tinyInteger('is_approved')->default(1);
            $table->decimal('money',10,2)->default(0)->command('قيمة المبلغ المصروف او المحصل للخزنه');
            $table->decimal('money_for_account',10,2)->default(0)->command('قيمة المبلغ المستحق للحساب او علي الحساب');
            $table->date('date');
            $table->string('byan',230)->nullable();
            $table->bigInteger('isal_num')->command('كود صرف النقديه');

            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('cascade');
            $table->foreign('admin_shift_id')->references('id')->on('admins_shifts')->onDelete('cascade');
            $table->foreign('move_type_id')->references('id')->on('move_types')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasuries_transactions');
    }
};
