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
        Schema::create('inv_item_cards', function (Blueprint $table) {
        $table->id();
        $table->string('name',100)->unique();
        $table->integer('com_code');
        $table->integer('item_code');
        $table->string('barcode',50);
        $table->tinyInteger('active')->default(1);
        $table->tinyInteger('has_fixced_price')->default(1)->comment('هل للصنف سعر ثابت بالفواتير او قابل للتغيير بالفواتير');
        $table->tinyInteger('item_type')->default(0)->comment('واحد مخزني , اثنين استهلاكي , ثلاثه عهدة');
        $table->unsignedBigInteger('category_id')->nullable();
        $table->bigInteger('parent_inv_itemCard_id')->comment('كود الصنف الاب له');
        $table->tinyInteger('does_has_reta_unit')->default(0)->comment('هل للصنف وحدة تجزئه');
        
        $table->unsignedBigInteger('uom_id')->nullable()->comment('كود وحدة قياس الاب');
        
        $table->integer('retal_uom_id')->nullable()->comment('كود وحدة قياس التجزئه');
        $table->decimal('retal_qt_to_parent',10,2)->nullable()->comment('كود وحدة قياس الاب');

        $table->decimal('price',10,2)->comment('سعر القطاعي بوحدة القياس الاساسيه');
        $table->decimal('nos_gomla_price',10,2)->comment('سعر نص الجمله مع وحدة الاب');
        $table->decimal('gomla_price',10,2)->comment('سعر الجمله بوحدة القياس الاساسيه');
       
        $table->decimal('price_retal',10,2)->nullable()->comment('سعر القطاعي بوحدة قياس التجزئه');
        $table->decimal('nos_gomla_price_retal',10,2)->nullable()->comment('سعر النص جمله قطاعي مع الوحدة التجزئه');
        $table->decimal('gomla_price_retal',10,2)->nullable()->comment('سعر الجمله بوحدة القياس التجزئه');
        $table->decimal('cost_price_retal',10,2)->nullable()->comment('متوسط تكلفة الصنف بالوحدة قياس التجزئه');
        
        $table->decimal('post_price',10,2)->comment('متوسط تكلفة الصنف بالوحدة الاساسيه');

        $table->decimal('quantity',10,3)->nullable()->comment('الكمية بالوحده الاب');
        $table->decimal('quantity_retal',10,3)->nullable()->comment('كمية التجزئه المتبقيه من الوحدة الاب في حالة وجود وحدة تجزئه للصنف');
        $table->decimal('quantity_all_retals',10,3)->nullable()->comment('كل الكمية محوله بوحدة التجزئه ');
                
        $table->string('image',230)->nullable();
        // $table->tinyInteger('has_fixed_price',50)->default(0)->comment('هل لديه سعر ثابت');
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->date('date');
        $table->timestamps();
        
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        $table->foreign('uom_id')->references('id')->on('inv_uoms')->onDelete('cascade');
        $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }


    // $table->id(); 
 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_item_cards');
    }
};
