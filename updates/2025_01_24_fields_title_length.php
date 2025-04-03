<?php namespace ProFixS\Forms\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class FieldsTitleLength extends Migration
{
    public function up()
    {
        Schema::table('profixs_forms_fields', function(Blueprint $table) {
            $table->string('title', 1000)->nullable()->change();
        });
    }

    public function down()
    {

    }
}
