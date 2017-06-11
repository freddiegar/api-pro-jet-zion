<?php

namespace App\Traits;

use App\Constants\BlameColumn;

trait BlameColumnsTrait
{
    /**
     * @param int $created_by
     * @return string
     */
    public function createdBy($created_by = null)
    {
        if (!is_null($created_by)) {
            $this->{BlameColumn::CREATED_BY} = $created_by;
            return $this;
        }
        return $this->{BlameColumn::CREATED_BY};
    }

    /**
     * @param int $updated_by
     * @return string
     */
    public function updatedBy($updated_by = null)
    {
        if (!is_null($updated_by)) {
            $this->{BlameColumn::UPDATED_BY} = $updated_by;
            return $this;
        }
        return $this->{BlameColumn::UPDATED_BY};
    }

    /**
     * @param int $deleted_by
     * @return string
     */
    public function deletedBy($deleted_by = null)
    {
        if (!is_null($deleted_by)) {
            $this->{BlameColumn::DELETED_BY} = $deleted_by;
            return $this;
        }
        return $this->{BlameColumn::DELETED_BY};
    }

    /**
     * @param string $created_at
     * @return string
     */
    public function createdAt($created_at = null)
    {
        if (!is_null($created_at)) {
            $this->{BlameColumn::CREATED_AT} = $created_at;
            return $this;
        }
        return $this->{BlameColumn::CREATED_AT};
    }

    /**
     * @param string $updated_at
     * @return string
     */
    public function updatedAt($updated_at = null)
    {
        if (!is_null($updated_at)) {
            $this->{BlameColumn::UPDATED_AT} = $updated_at;
            return $this;
        }
        return $this->{BlameColumn::UPDATED_AT};
    }

    /**
     * @param string $deleted_at
     * @return string
     */
    public function deletedAt($deleted_at = null)
    {
        if (!is_null($deleted_at)) {
            $this->{BlameColumn::DELETED_AT} = $deleted_at;
            return $this;
        }
        return $this->{BlameColumn::DELETED_AT};
    }
}
