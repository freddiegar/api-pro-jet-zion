<?php

namespace FreddieGar\Base\Traits;

use App\Models\User;

trait BlameRelationshipTrait
{

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
