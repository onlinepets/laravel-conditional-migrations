<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Onlinepets\ConditionalMigrations\Contracts\ConditionalMigration;

class CreateConditionalUsersOneTable extends Migration implements ConditionalMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditional_users_one', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
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
        Schema::dropIfExists('conditional_users_one');
    }

    /**
     * @return bool
     */
    public function shouldRun(): bool
    {
        return true;
    }
}
