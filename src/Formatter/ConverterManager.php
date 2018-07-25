<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\Converters\ArrayConverter;
use CollabCorp\Formatter\Converters\DateConverter;
use CollabCorp\Formatter\Converters\MathConverter;
use CollabCorp\Formatter\Converters\StringConverter;
use CollabCorp\Formatter\Formatter;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use ReflectionClass;

class ConverterManager extends Manager
{
    /**
     * Array of drivers available by method.
     *
     * @var array
     */
    protected $availableByMethod = [];

    /**
     * Get the names of all the available drivers.
     *
     * @return array
     */
    public function available()
    {
        if (empty($this->availableByMethod)) {
            $this->buildAvailableByMethod();
        }

        return array_merge(
            array_keys($this->customCreators),
            $this->availableByMethod
        );
    }

    /**
     * Build the array of drivers available by method
     *
     * @return array
     */
    protected function buildAvailableByMethod()
    {
        return $this->availableByMethod = collect((new ReflectionClass($this))->getMethods())
            ->map->name
            ->filter(function ($method) {
                return Str::startsWith($method, 'create') && Str::endsWith($method, 'Driver');
            })
            ->map(function ($method) {
                return Str::replaceLast(
                    'Driver',
                    '',
                    Str::after($method, 'create')
                );
            })
            ->toArray();
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'default';
    }

    /**
     * Return the default formatter driver
     * @return CollabCorp\Formatter\Formatter
     */
    public function createDefaultDriver()
    {
        return new Formatter;
    }

    /**
     * Return the math formatter driver
     * @return CollabCorp\Formatter\MathConverter
     */
    public function createMathDriver()
    {
        return new MathConverter;
    }


    /**
     * Return the string formatter driver
     * @return CollabCorp\Formatter\StringConverter
     */
    public function createStringDriver()
    {
        return new StringConverter;
    }

    /**
     * Return the date formatter driver
     * @return CollabCorp\Formatter\DateConverter
     */
    public function createDateDriver()
    {
        return new DateConverter;
    }
}
