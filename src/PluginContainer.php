<?php

namespace Nonetallt\Jinitialize\Plugin;

use Nonetallt\Jinitialize\Helpers\Strings;

class PluginContainer
{
    private $data;
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->data = [];
    }

    public function setArray(array $variables)
    {
        foreach($variables as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get(string $key)
    {
        if(! isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function exportData()
    {
        $out = [];
        foreach($this->data as $key => $data) {
            $key = self::transformForStub($key);
            $out[$key] = $data;
        }
        return $out;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Example: example value -> [EXAMPLE_VALUE]
     *
     */
    public static function transformForStub(string $value)
    {
        $value = Strings::toSnakeCase($value);
        return '[' . str_replace(' ', '_', strtoupper($value)) . ']';
    }
}
