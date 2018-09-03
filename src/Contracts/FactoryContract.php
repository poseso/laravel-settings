<?php

namespace Poseso\Settings\Contracts;

interface FactoryContract
{
    /**
     * Get a settings repository instance by name.
     *
     * @param  string|null $name
     * @return \Poseso\Settings\Contracts\RepositoryContract
     */
    public function store($name = null);
}
