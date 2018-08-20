<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word');
            $table->json('senses'); // 意思
            $table->json('attrs'); // 属性
            $table->unsignedTinyInteger('star'); // 星级
            $table->boolean('if_grasp')->default(false);         // 是否掌握
            $table->boolean('if_recite')->default(false);        // 是否背诵
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('words');
    }
}
