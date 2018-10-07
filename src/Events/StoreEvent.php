<?php

namespace Poseso\Settings\Events;

use Poseso\Settings\Scopes\Scope;

class StoreEvent
{
    /**
     * The store name.
     *
     * @var string
     */
    protected $storeName;

    /**
     * The scope.
     *
     * @var \Poseso\Settings\Scopes\Scope
     */
    protected $scope;

    /**
     * Get the store name.
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * Set the store name.
     *
     * @param string $storeName
     */
    public function setStoreName($storeName)
    {
        $this->storeName = $storeName;
    }

    /**
     * Get the scope.
     *
     * \Poseso\Settings\Scopes\Scope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set the scope.
     *
     * \Poseso\Settings\Scopes\Scope $scope
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
    }
}
