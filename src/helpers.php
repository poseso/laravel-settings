<?php

if (! function_exists('settings')) {
    /**
     * Get / set the specified settings value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  string|iterable $key
     * @param  mixed|null $default
     * @return mixed|\Poseso\Settings\SettingsManager
     */
    function settings($key = null, $default = null)
    {
        $usuario = auth()->user()->settings($key);
        if ($usuario != null) {
            return $usuario;
        } else {
            $global = app('settings')->get($key);
            return $global;
        }
    }
}
