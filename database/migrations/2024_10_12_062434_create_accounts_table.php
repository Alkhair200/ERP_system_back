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
        // جدول الشجرة المحاسبيه العامه
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name',20);
            $table->unsignedBigInteger('parent_account_num')->nullable();
            $table->bigInteger('account_num'); // رقم  الحساب المالي
            $table->bigInteger('start_balance_status')->command('0- balance 1-credit 2-debit 3-balanced'); // حالة الرصيد اول المدة
            $table->decimal('start_balance',10,2)->command('دائن او مدين او متزن اول المده'); // الرصسد الافتتاحي اول المدة;
            $table->decimal('current_balance',10,2)->default(0)->command('رصيد الحساب لحظي');
            $table->bigInteger('other_lable_FK')->nullable();
            $table->date('date');
            $table->integer('com_code');
            $table->tinyInteger('is_archived')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->string('notes',250)->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->tinyInteger('is_parent')->default(2); // [1= نغم][2= لا]
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_account_num')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('account_type_id')->references('id')->on('account_types')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
