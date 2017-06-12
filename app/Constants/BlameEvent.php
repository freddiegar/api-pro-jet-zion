<?php

namespace App\Constants;

/**
 * Interface BlameEvent
 * @package App\Constants
 */
interface BlameEvent
{
    const SAVING = 'saving';
    const CREATING = 'creating';
    const CREATED = 'created';
    const UPDATING = 'updating';
    const UPDATED = 'updated';
    const DELETING = 'deleting';
    const DELETED = 'deleted';
    const RESTORING = 'restoring';
    const RESTORED = 'restored';
    const SAVED = 'saved';
}
