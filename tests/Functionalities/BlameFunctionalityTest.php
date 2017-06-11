<?php

use App\Constants\BlameEvent;
use App\Constants\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class BlameFunctionalityTest extends DBTestCase
{
    private function userId()
    {
        return 1;
    }
    
    private function model()
    {
        return class_basename(User::class);
    }

    private function message($event)
    {
        $model = $this->model();
        return trans('login.error.not_authenticated', compact('event', 'model'));
    }

    private function messageOnCreating()
    {
        return $this->message(BlameEvent::CREATING);

    }

    private function messageOnUpdating()
    {
        return $this->message(BlameEvent::UPDATING);

    }

    private function messageOnDeleting()
    {
        return $this->message(BlameEvent::DELETING);

    }

    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys([
            'status' => UserStatus::ACTIVE,
            'username' => 'freddie@gar.com',
            'password' => hashing('Admin$this->userId()234/'),
            'type' => User::class,
        ], $excludeKeys, $includeKeys);
    }

    public function testCreateError()
    {
        try {
            User::create($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
    }

    public function testUpdateError()
    {
        try {
            User::findOrFail($this->userId())->update($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
    }

    public function testDeleteError()
    {
        try {
            User::findOrFail($this->userId())->delete();
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
    }

    public function testCreateDisableCreatedByOK()
    {
        try {
            User::disableCreatedBy();
            User::create($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testCreateDisableUpdatedByError()
    {
        try {
            User::disableUpdatedBy();
            User::create($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
    }

    public function testCreateDisableDeletedByError()
    {
        try {
            User::disableDeletedBy();
            User::create($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
    }

    public function testUpdateDisableCreatedByError()
    {
        try {
            User::disableCreatedBy();
            User::findOrFail($this->userId())->update($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
    }

    public function testUpdateDisableUpdatedByOk()
    {
        try {
            User::disableUpdatedBy();
            User::findOrFail($this->userId())->update($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testUpdateDisableDeletedByError()
    {
        try {
            User::disableDeletedBy();
            User::findOrFail($this->userId())->update($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
    }

    public function testDeleteDisableCreatedByError()
    {
        try {
            User::disableCreatedBy();
            User::findOrFail($this->userId())->delete();
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
    }

    public function testDeleteDisableUpdateByError()
    {
        try {
            User::disableUpdatedBy();
            User::findOrFail($this->userId())->delete();
        } catch (UnauthorizedException $e) {
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
    }

    public function testDeleteDisableUDeletedByOk()
    {
        try {
            User::disableDeletedBy();
            User::findOrFail($this->userId())->delete();
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testCreateDisableBlameOK()
    {
        try {
            User::disableBlame();
            User::create($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testUpdateDisableBlameOK()
    {
        try {
            User::disableBlame();
            User::findOrFail($this->userId())->update($this->request());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testDeleteDisableBlameOK()
    {
        try {
            User::disableBlame();
            User::findOrFail($this->userId())->delete();
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testGetCurrentUserAuthenticate()
    {
        Auth::setUser(User::findOrfail($this->userId()));
        $this->assertEquals($this->userId(), User::getCurrentUserAuthenticated('test', $this->model()));
    }
}
