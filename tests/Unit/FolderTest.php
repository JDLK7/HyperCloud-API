<?php

namespace Tests\Unit;

use App\Folder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FolderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @after
     */
    public function tearDownFixtures() {
        array_map('rmdir', glob("files/test/*"));
        rmdir('files/test/');
    }

    public function test_archive_factory_real_archive() {
        $folder = factory(Folder::class)->make();

        $this->assertNotNull($folder);
        $this->assertTrue(file_exists(base_path($folder->path)));
        $this->assertDatabaseMissing('files', [
            'path' => $folder->path
        ]);
    }

    public function test_archive_factory_database_archive() {
        $folder = factory(Folder::class)->create();

        $this->assertDatabaseHas('files', [
            'id' => $folder->id
        ]);
    }
}
