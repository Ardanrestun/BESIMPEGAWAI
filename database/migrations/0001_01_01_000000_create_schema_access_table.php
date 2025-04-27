<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS access;');

        Schema::connection('pgsql')->create('access.roles', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::connection('pgsql')->create('access.users', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->uuid('role_id');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('access.roles')->onDelete('cascade');
        });

        Schema::connection('pgsql')->create('access.detail_users', function ($table) {
            $table->uuid('user_id');
            $table->json('alamat')->nullable();
            $table->string('nohp');
            $table->enum('jeniskelamin', ['laki-laki', 'perempuan']);
            $table->string('image');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('access.users')->onDelete('cascade');
        });


        Schema::connection('pgsql')->create('access.menus', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('route')->nullable();
            $table->json('roles');
            $table->uuid('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();
        });


        Schema::connection('pgsql')->table('access.menus', function ($table) {
            $table->foreign('parent_id')->references('id')->on('access.menus')->onDelete('cascade');
        });

        Schema::connection('pgsql')->create(
            'access.password_reset_tokens',
            function ($table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            }

        );

        Schema::connection('pgsql')->create('access.sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::connection('pgsql')->create('access.personal_access_tokens', function ($table) {
            $table->id();
            $table->uuidMorphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        Schema::dropIfExists('access.detail_users');
        Schema::dropIfExists('access.password_reset_tokens');
        Schema::dropIfExists('access.sessions');
        Schema::dropIfExists('access.menus');
        Schema::dropIfExists('access.users');
        Schema::dropIfExists('access.personal_access_tokens');
        Schema::dropIfExists('access.roles');



        DB::statement('DROP SCHEMA IF EXISTS access;');
    }
};
