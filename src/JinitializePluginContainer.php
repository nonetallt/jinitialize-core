<?php

namespace Nonetallt\Jinitialize;

use Nonetallt\Jinitialize\Helpers\Strings;

class JinitializePluginContainer
{
    private $data;
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->data = [];
    }

    public function exportVariables(array $variables)
    {
        foreach($variables as $key => $value) {
            if(isset($this->data[$key])) {
                throw new \Exception("Variable export should not override an existing value $key");
            }
            $this->data[$key] = $value;
        }
    }

    public function get(string $key)
    {
        if(! isset($this->data[$key])) {
            throw new \Exception("Key '$key' is not set for plugin '$this->name'");
        }
        return $this->data[$key];
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
