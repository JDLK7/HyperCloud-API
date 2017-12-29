<?php

namespace Tests\Unit;

use App\User;
use App\Account;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_uuid_generated_on_creation() {
        $user = factory(User::class)->create();

        $this->assertNotNull($user->id);
    }

    public function test_two_uuids_are_different() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        
        $this->assertNotEquals($user1->id, $user2->id);
    }

    public function test_user_is_attached_to_account() {
        $account = factory(Account::class)->create();

        $this->assertNotNull($account->id);
        $this->assertNotNull($account->user);
        $this->assertEquals($account->user_id, $account->user->id);
    }
}
