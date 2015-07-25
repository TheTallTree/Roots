<?php
namespace GreatOwl\Patches\Models\Patch;


class Repository
{

    private $map;
    /**
     * @var Factory $factory
     */
    private $factory;

    public function __construct(Map $patchMap, Factory $factory)
    {
        $this->map = $patchMap;
        $this->factory = $factory;
    }

    public function buildPatchesFromDatabase($table)
    {
        $rawPatches = $this->map->getPatchesFromDB($table);

        return $this->buildPatches($rawPatches);

    }

    public function buildPatchesFromFile($table)
    {
        $rawPatches = $this->map->getPatchesFromFile($table);

        return $this->buildPatches($rawPatches);
    }

    /**
     * @param $rawPatches
     * @return Collection
     */
    protected function buildPatches($rawPatches)
    {
        $patches = [];
        foreach ($rawPatches as $patch) {
            $patches[] = $this->getFactory()->createPatch($patch);
        }

        return $this->factory->createCollection($patches);
    }

    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function getFactory()
    {
        if (is_null($this->factory)) {
            $this->setFactory($this->createFactory());
        }

        return $this->factory;
    }

    protected function createFactory()
    {
        return new Factory();
    }
}