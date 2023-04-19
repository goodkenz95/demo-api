<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(env('WRITER_DB_CONNECTION'))->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();

            $table->string('type')->nullable()->default('user');

            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('password');

            $table->text('directory')->nullable();
            $table->text('path')->nullable();
            $table->string('filename')->nullable();
            $table->string('source')->default("file")->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(env('WRITER_DB_CONNECTION'))->dropIfExists('users');
    }
};
