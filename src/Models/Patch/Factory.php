<?php
namespace GreatOwl\Patches\Models\Patch;


class Factory
{
    /**
     * @param array $raw
     * @return Patch
     */
    public function createPatch(array $raw)
    {
        return new Patch($raw);
    }

    /**
     * @param array $patches
     * @return Collection
     */
    public function createCollection(array $patches)
    {
        return new Collection($patches);
    }
}