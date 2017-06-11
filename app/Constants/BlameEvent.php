<?php

namespace App\Constants;

interface BlameEvent
{
    const CREATING = 'creating';
    const UPDATING = 'updating';
    const DELETING = 'deleting';
    const SAVED = 'saved';
}
