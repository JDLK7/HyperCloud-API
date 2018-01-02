<?php

namespace Tests\Unit;

use App\Folder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FolderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_folder_factory_real_folder() {
        $folder = factory(Folder::class)->create();

        $this->assertNotNull($folder);
        $this->assertTrue(file_exists(base_path($folder->path)));

        rmdir(base_path($folder->path));
    }

    public function test_folder_factory_database_folder() {
        $folder = factory(Folder::class)->create();

        $this->assertDatabaseHas('files', [
            'id' => $folder->id
        ]);

        rmdir(base_path($folder->path));
    }
}
