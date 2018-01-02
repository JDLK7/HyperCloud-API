<?php

namespace Tests\Unit;

use App\Archive;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_archive_factory_real_archive() {
        $archive = factory(Archive::class)->make();

        $this->assertNotNull($archive);
        $this->assertTrue(file_exists(base_path($archive->path)));
        $this->assertDatabaseMissing('files', [
            'path' => $archive->path
        ]);

        unlink($archive->path);
    }

    public function test_archive_factory_database_archive() {
        $archive = factory(Archive::class)->create();

        $this->assertDatabaseHas('files', [
            'id' => $archive->id
        ]);

        unlink($archive->path);
    }
}
