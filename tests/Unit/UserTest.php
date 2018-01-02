<?php

namespace Tests\Unit;

use App\User;
use App\Folder;
use App\Archive;
use App\Account;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @after
     */
    public function tearDownFixtures() {
        @rmdir('files/test/');
    }

    public function test_uuid_generated_on_creation() {
        $user = factory(User::class)->create();

        $this->assertNotNull($user->id);
    }

    public function test_two_uuids_are_different() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        
        $this->assertNotEquals($user1->id, $user2->id);
    }

    public function test_it_creates_user_folder_on_account_creation() {
        $account = factory(Account::class)->create();

        $this->assertTrue(file_exists(base_path($account->path)));

        rmdir($account->path);
    }

    public function test_user_is_attached_to_account() {
        $account = factory(Account::class)->create();

        $this->assertNotNull($account->id);
        $this->assertNotNull($account->user);
        $this->assertEquals($account->user_id, $account->user->id);

        rmdir($account->path);
    }

    public function test_user_account_has_folders() {
        $account = factory(Account::class)->create();
        $folder = factory(Folder::class)->create();

        $folder->account()->associate($account);
        $folder->save();

        $this->assertTrue($account->folders->contains($folder));
        $this->assertTrue($account->files()->contains($folder));

        rmdir($folder->path);
    }

    public function test_user_account_has_archives() {
        $account = factory(Account::class)->create();
        $archive = factory(Archive::class)->create();

        $archive->account()->associate($account);
        $archive->save();

        $this->assertTrue($account->archives->contains($archive));
        $this->assertTrue($account->files()->contains($archive));

        unlink($archive->path);
    }

    public function test_it_returns_only_associated_folders() {
        $account = factory(Account::class)->create();
        $folder = factory(Folder::class)->create();
        $notAssociatedFolder = factory(Folder::class)->create();

        $folder->account()->associate($account);
        $folder->save();

        $this->assertFalse($account->folders->contains($notAssociatedFolder));        
        $this->assertFalse($account->files()->contains($notAssociatedFolder));   
        
        rmdir($folder->path);
        rmdir($notAssociatedFolder->path);
    }

    public function test_it_returns_only_associated_archives() {
        $account = factory(Account::class)->create();
        $archive = factory(Archive::class)->create();
        $notAssociatedArchive = factory(Archive::class)->create();

        $archive->account()->associate($account);
        $archive->save();

        $this->assertFalse($account->archives->contains($notAssociatedArchive));        
        $this->assertFalse($account->files()->contains($notAssociatedArchive));
        
        unlink($archive->path);
        unlink($notAssociatedArchive->path);
    }
}
