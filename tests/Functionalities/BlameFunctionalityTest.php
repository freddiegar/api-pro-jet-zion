<?php

use App\Constants\BlameEvent;
use App\Entities\UserEntity;
use App\Managers\UserManager;
use App\Models\User;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class BlameFunctionalityTest
 * This test are from manager, in request user is authenticate :P
 */
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

    private function request(array $excludeKeys = [], array $includeKeys = [])
    {
        return $this->applyKeys([
            'username' => 'freddie@gar.com',
            'password' => 'password_easy',
            UserEntity::KEY_API_TOKEN => $this->apiToken(),
        ], $excludeKeys, $includeKeys);
    }

    private function manager()
    {
        return new UserManager(new Request($this->request()), new EloquentUserRepository());
    }

    private function message($event)
    {
        $model = $this->model();
        return trans('exceptions.unauthorized', compact('event', 'model'));
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

    public function testBlameFunctionalityCreateError()
    {
        try {
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityUpdateError()
    {
        try {
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityDeleteError()
    {
        try {
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityCreateDisableCreatedByOK()
    {
        try {
            User::disableCreatedBy();
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityCreateDisableUpdatedByError()
    {
        try {
            User::disableUpdatedBy();
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityCreateDisableDeletedByError()
    {
        try {
            User::disableDeletedBy();
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnCreating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityUpdateDisableCreatedByError()
    {
        try {
            User::disableCreatedBy();
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityUpdateDisableUpdatedByOk()
    {
        try {
            User::disableUpdatedBy();
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityUpdateDisableDeletedByError()
    {
        try {
            User::disableDeletedBy();
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnUpdating(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityDeleteDisableCreatedByError()
    {
        try {
            User::disableCreatedBy();
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityDeleteDisableUpdateByError()
    {
        try {
            User::disableUpdatedBy();
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $noFail = true;
            $this->assertEquals($this->messageOnDeleting(), $e->getMessage());
        }
        if (!isset($noFail)) {
            $this->assertEquals(true, false);
        }
    }

    public function testBlameFunctionalityDeleteDisableUDeletedByOk()
    {
        try {
            User::disableDeletedBy();
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityGetCurrentUserLoged()
    {
        Auth::setUser(User::find($this->userId()));
        $this->assertEquals($this->userId(), User::getCurrentUserAuthenticated('test', $this->model()));
    }

    public function testBlameFunctionalityDeleteSetCurrentUserAuthenticatedOK()
    {
        try {
            User::setCurrentUserAuthenticated($this->userId());
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityCreateDisableBlameOK()
    {
        try {
            User::disableBlame();
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityUpdateDisableBlameOK()
    {
        try {
            User::disableBlame();
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityDeleteDisableBlameOK()
    {
        try {
            User::disableBlame();
            $this->manager()->delete($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityCreateSetCurrentUserAuthenticatedOK()
    {
        try {
            User::setCurrentUserAuthenticated($this->userId());
            $this->manager()->create();
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }

    public function testBlameFunctionalityUpdateSetCurrentUserAuthenticatedOK()
    {
        try {
            User::setCurrentUserAuthenticated($this->userId());
            $this->manager()->update($this->userId());
        } catch (UnauthorizedException $e) {
            $this->assertEquals(true, false, $e->getMessage());
        }
        $this->assertEquals(true, true);
    }
}
