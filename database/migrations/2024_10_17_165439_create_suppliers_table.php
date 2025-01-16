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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name',20);
            $table->bigInteger('account_num'); // رقم  الحساب المالي
            $table->bigInteger('supplier_code'); // رقم المعميل
            $table->bigInteger('start_balance_status')->command('0- balance 1-credit 2-debit 3-balanced'); // حالة الرصيد اول المدة
            $table->decimal('start_balance',10,2)->command('دائن او مدين او متزن اول المده'); // الرصسد الافتتاحي اول المدة;
            $table->decimal('current_balance',10,2)->default(0)->command('رصيد الحساب لحظي');
            $table->date('date');
            $table->integer('com_code');
            $table->tinyInteger('active')->default(1);
            $table->string('notes',250)->nullable();
            $table->string('address',250)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('supplier_category_id')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('supplier_category_id')->references('id')->on('suppliers_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
