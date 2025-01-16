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
        Schema::create('emplyees', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->bigInteger('employee_code'); // رقم الموظف
            $table->string('name',30);
            $table->bigInteger('account_num'); // رقم  الحساب المالي
            $table->unsignedBigInteger('departement_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->tinyInteger('do_has_shift')->command('هل للموظف شفت يومي ثابت');
            $table->unsignedBigInteger('shift_type_id')->nullable();
            $table->decimal('total_hours',10,2)->nullable();
            $table->string('phone',20);
            $table->string('address',240);

            $table->tinyInteger('does_has_social_insurance')->command('هل للموظف تأمين اجتماعي');
            $table->decimal('social_insurance_value',10,2)->nullable();
            $table->string('social_insurance_num',30)->nullable();

            $table->tinyInteger('do_has_social_motivation')->command('هل للموظف حافز شهري ثابت');
            $table->decimal('motivation_value',10,2)->nullable();

            $table->tinyInteger('does_has_allowances')->command('هل للموظف بدلات ثابته');
            $table->decimal('allowances_value',10,2)->nullable();

            $table->decimal('salary',10,2);
            $table->decimal('current_balance',10,2)->default(0)->command('رصيد الحساب لحظي');
            $table->decimal('day_price',10,2)->command('اجر اليوم');
            $table->bigInteger('start_balance_status')->command('0- balance 1-credit 2-debit 3-balanced'); // حالة الرصيد اول المدة
            $table->decimal('start_balance',10,2)->command('دائن او مدين او متزن اول المده'); // الرصسد الافتتاحي اول المدة;
            $table->tinyInteger('active')->default(1);
            $table->string('notes',250)->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('departement_id')->references('id')->on('departements')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('shift_type_id')->references('id')->on('shifts_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplyees');
    }
};
