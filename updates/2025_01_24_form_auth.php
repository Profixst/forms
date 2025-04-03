<?php namespace ProFixS\Forms\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class FormAuth extends Migration
{
    public function up()
    {
        Schema::table('profixs_forms_forms', function(Blueprint $table) {
            $table->boolean('is_auth_required')->default(false);
        });
    }

    public function down()
    {

    }
}
