<?php

namespace App\Contracts\Interfaces;

interface BlameInterface
{
    public function createdBy();

    public function updatedBy();

    public function deletedBy();

    public function createdAt();

    public function updatedAt();

    public function deletedAt();
}
