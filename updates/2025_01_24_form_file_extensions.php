<?php namespace ProFixS\Forms\Updates;

use Db;
use ProFixS\Forms\Models\Field;
use October\Rain\Database\Updates\Migration;

class FormFileExtensionsMigration extends Migration
{
    public function up()
    {
        Db::table('profixs_forms_fields')->where('type', 'file')->get()->each(function ($item) {
            if (!$item->file_extensions_list) {
                return;
            }

            $extensions = collect(json_decode($item->file_extensions_list))
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
