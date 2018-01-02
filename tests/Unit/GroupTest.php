<?php

namespace Tests\Unit;

use App\Group;
use App\Folder;
use App\Archive;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GroupTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * @after
     */
    public function tearDownFixtures() {
        @rmdir('files/test/');
    }

    public function test_group_has_folders() {
        $group = factory(Group::class)->create();
        $folder = factory(Folder::class)->create();

        $folder->group()->associate($group);
        $folder->save();

        $this->assertTrue($group->folders->contains($folder));
        $this->assertTrue($group->files()->contains($folder));

        rmdir($folder->path);
    }

    public function test_group_has_archives() {
        $group = factory(Group::class)->create();
        $archive = factory(Archive::class)->create();

        $archive->group()->associate($group);
        $archive->save();

        $this->assertTrue($group->archives->contains($archive));
        $this->assertTrue($group->files()->contains($archive));

        unlink($archive->path);
    }

    public function test_it_returns_only_associated_folders() {
        $group = factory(Group::class)->create();
        $folder = factory(Folder::class)->create();
        $notAssociatedFolder = factory(Folder::class)->create();

        $folder->group()->associate($group);
        $folder->save();

        $this->assertFalse($group->folders->contains($notAssociatedFolder));        
        $this->assertFalse($group->files()->contains($notAssociatedFolder));   
        
        rmdir($folder->path);
        rmdir($notAssociatedFolder->path);
    }

    public function test_it_returns_only_associated_archives() {
        $group = factory(Group::class)->create();
        $archive = factory(Archive::class)->create();
        $notAssociatedArchive = factory(Archive::class)->create();

        $archive->group()->associate($group);
        $archive->save();

        $this->assertFalse($group->archives->contains($notAssociatedArchive));        
        $this->assertFalse($group->files()->contains($notAssociatedArchive));
        
        unlink($archive->path);
        unlink($notAssociatedArchive->path);
    }
}
