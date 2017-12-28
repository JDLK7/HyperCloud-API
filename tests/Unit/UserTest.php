<?php

namespace Tests\Unit;

use App\User;
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
}
