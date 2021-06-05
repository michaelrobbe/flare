<?php

namespace Tests\Unit\Flare\Events;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateRole;
use Tests\Traits\CreateUser;
use Tests\Traits\CreateUserSiteAccessStatistics;
use App\Flare\Events\SiteAccessedEvent;
use App\Flare\Models\User;
use App\Flare\Models\UserSiteAccessStatistics;

class SiteAccessedEventTest extends TestCase {

    use RefreshDatabase, CreateUser, CreateRole, CreateUserSiteAccessStatistics;


    public function setUp(): void {
        parent::setUp();

        $this->createAdmin([], $this->createAdminRole());
    }

    public function tearDown(): void {
        parent::tearDown();
    }

    public function testSetsRecord() {
        event(new SiteAccessedEvent(true, true));

        $this->assertTrue(!is_null(UserSiteAccessStatistics::first()));
    }

    public function testSetsRecordWhenOneExists() {
        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(true, true));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }

    public function testSetsRecordWhenJustSigningIn() {
        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(true, false));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }

    public function testSetsRecordWhenLoggingOut() {
        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(false, false, true));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }

    public function testSetsRecordWithOutAdmin() {
        User::first()->delete();

        event(new SiteAccessedEvent(true, true));

        $this->assertTrue(!is_null(UserSiteAccessStatistics::first()));
    }

    public function testSetsRecordWhenOneExistsWithOutAdmin() {
        User::first()->delete();

        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(true, true));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }

    public function testSetsRecordWhenJustSigningInWithOutAdmin() {
        User::first()->delete();

        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(true, false));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }

    public function testSetsRecordWhenLoggingOutWithOutAdmin() {
        User::first()->delete();

        $this->createUserSiteAccessStatistics();

        event(new SiteAccessedEvent(false, false, true));

        $this->assertTrue(UserSiteAccessStatistics::count() > 1);
    }
}