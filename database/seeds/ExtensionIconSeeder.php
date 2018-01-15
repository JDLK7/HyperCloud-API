<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtensionIconSeeder extends Seeder
{
    protected $icons = [
        'video'     =>  ['avi', 'mkv', 'wmv', 'mp4', 'flv', '3gp'],
        'document'  =>  ['doc', 'docx', 'txt', 'csv', 'odt', 'xlsx'],
        'image'     =>  ['jpg', 'jpeg', 'gif', 'bmp', 'png', 'svg', 'psd'],
        'pdf'       =>  ['pdf', 'PDF'],
        'compress'  =>  ['zip', 'tar', 'tar.gz', 'rar'],
        'code'      =>  ['cc', 'cpp', 'py', 'cs', 'php', 'html', 'css', 'java'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->icons as $icon => $extensions) {
            foreach($extensions as $extension) {
                DB::table('extension_icon')->insert([
                    'extension' => $extension,
                    'icon'      => "fa-$icon",
                ]);
            }
        }
    }
}
