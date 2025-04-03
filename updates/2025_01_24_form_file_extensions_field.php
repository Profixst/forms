<?php namespace ProFixS\Forms\Updates;

use Db;
use ProFixS\Forms\Models\Field;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class FormFileExtensionsFieldsMigration extends Migration
{
    public function up()
    {
        Schema::table('profixs_forms_fields', function(Blueprint $table) {
            if (Schema::hasColumn('profixs_forms_fields', 'file_extensions')) {
                $table->renameColumn('file_extensions', 'file_extensions_list');
            }
            if (Schema::hasColumn('profixs_forms_fields', 'rules')) {
                $table->renameColumn('rules', 'field_rules');
            }
        });

        Db::table('profixs_forms_fields')->where('type', 'file')->get()->each(function ($item) {
            if (!$item->file_extensions_list) {
                return;
            }

            $extensions = collect(explode(',', $item->file_extensions_list))
                ->filter(function ($item) {
                    return in_array($item, Field::make()->getFileExtensionsListOptions());
                })
                ->toArray();

            Db::table('profixs_forms_fields')->where('id', $item->id)->update([
                'file_extensions_list' => $extensions
            ]);
        });
    }
}
