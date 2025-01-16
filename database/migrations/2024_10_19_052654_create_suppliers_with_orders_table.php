<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // جدول مشتريات و مرتجعات الموردين
        Schema::create('suppliers_with_orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order_type')
            ->command('1=>Barshase 2=>return on same pill 3=>return on geniral');
            // واحد مشتريات اثنين مرتجع باصل الفاتورة ثلاثه مرتجع بدون اصل الفاتورة

            $table->bigInteger('auto_serial');
            $table->string('doc_no',30); // رقم الفاتورة
            $table->bigInteger('account_num');  
            $table->date('order_date'); // تاريخ  الفاتورة
            $table->tinyInteger('is_approved')->default(0); // الفاتورة معتمده غير معتمده
            $table->integer('com_code');
            $table->tinyInteger('discount_type')->nullable()->command('واحد نوع الخسم نسبه || اثنين نوع الخصم يدوي');
            $table->decimal('discount_percent',10,2)->default(0)->nullable()->command('قيمة نسبة الخصم');
            $table->decimal('discount_value',10,2)->default(0)->command('قيمة الخصم');
            $table->decimal('tax_percent',10,2)->default(0)->nullable()->command('نسبة الضريبه');
            $table->decimal('tax_value',10,2)->default(0)->nullable()->command('قيمة الضريبه القيمه المضافه');
            $table->decimal('total_cost_items',10,2)->default(0)->command('إجمالي الاصناف فقط');
            
            $table->decimal('total_befor_discount',10,2)->default(0)->command('اجمالي الفاتورة قبل الخصم');
            $table->decimal('total_cost',10,2)->nullable()->default(0)->command('اجمالي القيمه النهائية للفاتوره');
            $table->decimal('money_for_account',10,2)->nullable();
            $table->tinyInteger('pill_type')->command('نوع الفاتورة اجل او كاش');
            $table->decimal('what_paid',10,2)->default(0)->nullable();
            $table->decimal('what_remain',10,2)->default(0)->nullable();
            $table->decimal('supplier_balance_befor',10,2)->default(0)->nullable()->command('حاله رصيد المورد قبل الفاتورة');
            $table->decimal('supplier_balance_after',10,2)->default(0)->nullable()->command('حاله رصيد المورد بعد الفاتورة');
            $table->string('notes',250)->nullable();

            $table->unsignedBigInteger('treasuries_transaction_id')->nullable();
            $table->bigInteger('supplier_code')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable()->command('كود المخزن المستلم للفاتورة');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();

            $table->foreign('treasuries_transaction_id')->references('id')->on('treasuries')->onDelete('cascade');
            // $table->foreign('supplier_code')->references('supplier_code')->on('suppliers')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('suppliers_with_orders');
    }
};
