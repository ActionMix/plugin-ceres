<?php

namespace Ceres\Widgets\Helper\Factories\Settings;

/**
 * Class BaseSettingFactory
 *
 * Base factory class to generate widget settings.
 * Contains interfaces for all common properties of a setting.
 *
 * @package Ceres\Widgets\Helper\Factories\Settings
 */
class BaseSettingFactory
{
    protected $data = [];

    /**
     * Set the type of the setting.
     *
     * @param string    $type
     * @return $this
     */
    protected function withType($type)
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * Set an option for the setting.
     *
     * @param string    $key        The option key
     * @param mixed     $value      The option value
     * @return $this
     */
    protected function withOption($key, $value)
    {
        $this->data['options'] = $this->data['options'] ?? [];
        $this->data['options'][$key] = $value;
        return $this;
    }

    /**
     * Set the default value for the setting.
     *
     * @param mixed     $defaultValue   The default value
     * @return $this
     */
    public function withDefaultValue($defaultValue)
    {
        $this->data['defaultValue'] = $defaultValue;
        return $this;
    }

    /**
     * Set a condition if the setting should be visible or not.
     *
     * @param string    $condition  Condition if the related form element should be visible or not.
     * @return $this
     */
    public function withCondition($condition)
    {
        $this->data['isVisible'] = $condition;
        return $this;
    }

    /**
     * Set the name of the setting.
     *
     * @param $name
     * @return $this
     */
    public function withName($name)
    {
        return $this->withOption("name", $name);
    }

    /**
     * Determines whether the declaration is used to render a list of the specified form field.
     *
     * @param string|int $min
     * @param string|int $max
     * @return $this
     */
    public function withList($min, $max = "")
    {
        $this->data['isList'] = "[{$min}, {$max}]";
        return $this;
    }

    /**
     * Get all data as a native array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}