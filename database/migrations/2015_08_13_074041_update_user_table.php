<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('username',40);
            $table->string('last_name',50);
            $table->string('middle_name',50);
            $table->string('first_name',50);
            $table->string('extension_name',20);
            $table->string('gender',2);
            $table->string('address',255);
            $table->string('country',2);
            $table->string('skype_account',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('username',40);
            $table->dropColumn('last_name',50);
            $table->dropColumn('middle_name',50);
            $table->dropColumn('first_name',50);
            $table->dropColumn('extension_name',20);
            $table->dropColumn('gender',2);
            $table->dropColumn('address',255);
            $table->dropColumn('country',2);
            $table->dropColumn('skype_account',100);

        });
    }
}
