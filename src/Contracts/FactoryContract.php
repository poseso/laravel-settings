<?php

declare(strict_types=1);

namespace Poseso\Settings\Contracts;

interface FactoryContract
{
    /**
     * Get a settings repository instance by name.
     *
     * @param  string|null $name
     * @return \Poseso\Settings\Contracts\RepositoryContract
     */
    public function store(string $name = null);
}