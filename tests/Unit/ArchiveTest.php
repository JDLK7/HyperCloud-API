<?php

namespace Tests\Unit;

use App\Archive;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArchiveTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @after
     */
    public function tearDownFixtures() {
        array_map('unlink', glob("files/test/*"));
        rmdir('files/test/');
    }

    public function test_archive_factory_real_archive() {
        $archive = factory(Archive::class)->make();

        $this->assertNotNull($archive);
        $this->assertTrue(file_exists(base_path($archive->path)));
        $this->assertDatabaseMissing('files', [
            'path' => $archive->path
        ]);
    }

    public function test_archive_factory_database_archive() {
        $archive = factory(Archive::class)->create();

        $this->assertDatabaseHas('files', [
            'id' => $archive->id
        ]);
    }
}
