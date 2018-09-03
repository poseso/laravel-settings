<?php

namespace Poseso\Settings\Contracts;

interface RepositoryContract
{
    /**
     * Determine if an item exists in the settings store.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key);

    /**
     * Retrieve an item from the settings store by key.
     *
     * @param  string|iterable $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get all of the settings items.
     *
     * @return array
     */
    public function all();

    /**
     * Store an item in the settings store.
     *
     * @param  string|iterable $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value = null);

    /**
     * Remove an item from the settings store.
     *
     * @param  string|iterable $key
     * @return bool
     */
    public function forget($key);

    /**
     * Remove all items from the settings store.
     *
     * @return bool
     */
    public function flush();

    /**
     * Set the scope.
     *
     * @param mixed $scope
     * @return \Poseso\Settings\Contracts\RepositoryContract
     */
    public function scope($scope): self;
}
