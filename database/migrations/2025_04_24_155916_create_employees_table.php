<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS public;');

        Schema::connection('pgsql')->create('public.employees', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('position');
            $table->timestamps();
        });

        Schema::connection('pgsql')->table('public.employees', function ($table) {
            $table->foreign('user_id')->references('id')->on('access.users')->onDelete('cascade');
        });

        Schema::connection('pgsql')->create('public.tasks', function ($table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->date('due_date');
            $table->date('deadline_date');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        Schema::connection('pgsql')->create('public.employee_task', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->uuid('employee_id');
            $table->text('note')->nullable();
            $table->decimal('hours_spent', 8, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('additional_charges', 10, 2)->nullable();
            $table->decimal('total_remuneration', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::connection('pgsql')->table('public.employee_task', function ($table) {
            $table->foreign('task_id')->references('id')->on('public.tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('public.employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.employee_task');
        Schema::dropIfExists('public.tasks');
        Schema::dropIfExists('public.employees');
    }
};
