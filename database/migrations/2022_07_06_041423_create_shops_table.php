<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('shops', function (Blueprint $table) {
                // $table->id();
                $table->bigInteger('id')->unsigned()->primary();
                $table->string('name');
                $table->string('domain');
                $table->string('email');
                $table->string('shopify_domain');
                $table->string('access_token');
                $table->string('plan');
                $table->date('created_at');
                // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
