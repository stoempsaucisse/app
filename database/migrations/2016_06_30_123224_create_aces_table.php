<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acl_id')->unsigned();
            $table->string('object', 255);
            $table->integer('object_id')
                  ->nullable();
            $table->string('field', 255)
                  ->nullable();
            $table->integer('mask')
                  ->nullable()
                  ->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('acl_id')
                  ->references('id')
                  ->on('acls')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('aces', function (Blueprint $table) {
            $table->dropForeign(['acl_id']);
        });
    }
}
