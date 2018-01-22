<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtensionIconSeeder extends Seeder
{
    protected $icons = [
        'file-video-o'      =>  ['avi', 'mkv', 'wmv', 'mp4', 'flv', '3gp'],
        'file-text-o'       =>  ['doc', 'docx', 'txt', 'csv', 'odt', 'xlsx'],
        'file-image-o'      =>  ['jpg', 'jpeg', 'gif', 'bmp', 'png', 'svg', 'psd'],
        'file-pdf-o'        =>  ['pdf', 'PDF'],
        'file-archive-o'    =>  ['zip', 'tar', 'tar.gz', 'rar'],
        'file-code-o'       =>  ['cc', 'cpp', 'py', 'cs', 'php', 'html', 'css', 'java'],
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
