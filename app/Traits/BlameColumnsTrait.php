<?php

namespace App\Traits;

use App\Constants\BlameColumns;

trait BlameColumnsTrait
{
    /**
     * @return string
     */
    public function createdBy()
    {
        return $this->{BlameColumns::CREATED_BY};
    }

    /**
     * @return string
     */
    public function updatedBy()
    {
        return $this->{BlameColumns::UPDATED_BY};
    }

    /**
     * @return string
     */
    public function deletedBy()
    {
        return $this->{BlameColumns::DELETED_BY};
    }

    /**
     * @return string
     */
    public function createdAt()
    {
        return $this->{BlameColumns::CREATED_AT};
    }

    /**
     * @return string
     */
    public function updatedAt()
    {
        return $this->$this->{BlameColumns::UPDATED_AT};
    }

    /**
     * @return string
     */
    public function deletedAt()
    {
        return $this->$this->{BlameColumns::DELETED_AT};
    }
}
