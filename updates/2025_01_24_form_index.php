<?php namespace ProFixS\Forms\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class FormIndicesMigration extends Migration
{
    public function up()
    {
        Schema::table('profixs_forms_fields', function (Blueprint $table) {
            $table->index('form_id');
        });

        Schema::table('profixs_forms_inbox', function (Blueprint $table) {
            $table->index('form_id');
        });
    }
}
