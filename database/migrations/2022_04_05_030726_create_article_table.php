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
        Schema::connection(env('WRITER_DB_CONNECTION'))->create('article', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->text('directory')->nullable();
            $table->text('path')->nullable();
            $table->string('filename')->nullable();
            $table->string('source')->default("file")->nullable();

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
        Schema::connection(env('WRITER_DB_CONNECTION'))->dropIfExists('article');
    }
};
