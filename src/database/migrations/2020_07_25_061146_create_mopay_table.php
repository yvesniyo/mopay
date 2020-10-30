<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMopayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mopay_payments', function (Blueprint $table) {
            $table->id();
            $table->float("amount");
            $table->string("msisdn");
            $table->string("credit_number");
            $table->string("mopay_url")->nullable();
            $table->string("external_id");
            $table->string("post_back_url");
            $table->string("client_name");
            $table->string("email")->nullable();
            $table->string("token");
            $table->integer("status")->default(0);
            $table->string("message")->nullable();
            $table->string("reference_id")->nullable();
            $table->string("reason")->nullable();
            $table->string("context_model")->nullable();
            $table->string("context_model_id")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mopay_payments');
    }
}
