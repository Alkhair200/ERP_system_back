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
        Schema::create('shifts_types', function (Blueprint $table) {
            $table->id();
            $table->integer('com_code');
            $table->string('name',10);
            $table->tinyInteger('type')->command('واحد صباحي اثنين مسائي ثلاثه يوم كامل');
            $table->time('from_time');
            $table->time('to_time');
            $table->decimal('total_hours',10,2);
            $table->tinyInteger('active')->default(1);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts_types');
    }
};
