<?php

namespace App\Interfaces;

interface RepositoryInterface
{
    /**
     * Get All Data
     *
     * @return array All Data
     */
    public function getAll(string $preference);

    /**
     * Update Preference By Id and Data
     *
     * @param int $id
     * @param array $data
     * @return object Updated Preference Information
     */
    public function create(array $value);
}