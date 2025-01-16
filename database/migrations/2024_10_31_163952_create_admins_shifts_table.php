<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->decimal('treasuries_balance_in_shift_start',10,2)->default(0)->command('رصيد الخزنه في بداية اسلام الشفت للمستخدم');
            $table->dateTime('start_date')->command('تاريخ بداية الشفت');
            $table->dateTime('end_date')->command('تاريخ نهاية الشفت')->nullable();
            $table->date('date');
            $table->tinyInteger('is_finished')->default(0)->command('هل تم انتهاء الشفت');
            $table->tinyInteger('is_delivered_and_review')->default(0)->command('هل تم مراجعة واستلام شفت الخزنة');
            
            $table->integer('delivered_to_admin_id')->nullable()->command('كود المستخدم الذي تسلم هذا الشفت وراجعه');
            $table->bigInteger('delivered_to_admin_shift_id')->nullable()->command('كود الشفت الذي تسلم هذا الشفت وراجعه');
            $table->integer('delivered_to_treasuries_id')->nullable()->command('كود الخزنه التي راجعت واستلمت هذا الشفت');
            $table->decimal('mony_should_delivered',10,2)->nullable()->command('النقديه الفعليه التي يفترض ان تسلم');
            $table->decimal('what_realy_delivered',10,2)->nullable()->command('المبلغ الفعلي الذي تم تسلمه');
            $table->tinyInteger('mony_state')->nullable()->default(1)->command('حالة النقدية [0=>balanced,1=>inability,2=>extray] صفر متزن-واحد يوجد عجز-اثنين يوجد زيادة');
            $table->decimal('mony_state_value',10,2)->nullable()->command('قيمة العجز او اليادة ان وجدت');
            $table->tinyInteger('receive_type')->nullable()->command('واحد استلام علي نفس الخزن - اثنين استلام علي خزنه اخري');
            $table->date('review_receive_date')->nullable()->command('تاريخ مراجعه واستلام هذا الشفت');
            $table->bigInteger('treasuries_transaction_id')->nullable()->command('رقم الايصال بجدول تحصيل النقدية لحركة الخزن');
            $table->string('notes',250)->nullable();
            
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins_shifts');
    }
};
