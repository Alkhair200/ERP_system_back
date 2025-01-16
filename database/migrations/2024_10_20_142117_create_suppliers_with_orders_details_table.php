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
        // تفاصيل اصناف فاتورة المشتريات و المرتجعات
        Schema::create('suppliers_with_orders_details', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->decimal('deliverd_qt',10,2)->command('الكمية المستلمه');
            $table->bigInteger('unit_id');
            $table->tinyInteger('is_parentuom')->command('واحد=> اليشراء بالوحدة الاساسيه اثنين=> الشراء بالوحدة الفرعيه');
            $table->decimal('unit_price',10,2);
            $table->decimal('total_price',10,2)->command('الاجمالي');
            $table->date('order_date');
            
            $table->decimal('batch_id',10,2)->nullable()->command('رقم الباتس بالمخزن التي تم تخزين الصنف بها');
            $table->date('production_date')->nullable();
            $table->date('expire_date')->nullable();
            
            $table->unsignedBigInteger('supplier_with_order_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->timestamps();

            $table->foreign('supplier_with_order_id')->references('id')->on('suppliers_with_orders')->onDelete('cascade');
            $table->foreign('uom_id')->references('id')->on('inv_uoms')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inv_item_cards')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers_with_orders_details');
    }
};
