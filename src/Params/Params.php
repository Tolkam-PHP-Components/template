<?php declare(strict_types=1);

namespace Tolkam\Template\Params;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use Tolkam\Utils\Arr;

/**
 * Extendable params object with array access and more
 *
 * @package Tolkam\Template\Params
 */
class Params implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Array path separator
     */
    public const PATH_SEPARATOR = '.';

    /**
     * @var array
     */
    private array $items = [];

    /**
     * @var array
     */
    private array $options = [
        // whether to use closure return value as offset value
        'resolveClosures' => true,
    ];

    /**
     * @var ParamsExtensionInterface[]
     */
    private array $extensions = [];

    /**
     * @param array $input
     * @param array $options
     */
    public function __construct(array $input = [], array $options = [])
    {
        $this->options = array_replace($this->options, $options);
        foreach ($input as $k => $v) {
            $this->items[$k] = $this->convertValue($v);
        }
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    public function set(string $key, $value): self
    {
        Arr::set($this->items, $key, $this->convertValue($value), self::PATH_SEPARATOR);

        return $this;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return array|ArrayAccess|mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->items, $key, $default, self::PATH_SEPARATOR);
    }

    /**
     * @param mixed ...$values
     *
     * @return $this
     */
    public function push(...$values): self
    {
        array_push($this->items, ...$values);

        return $this;
    }

    /**
     * @param mixed ...$values
     *
     * @return $this
     */
    public function unshift(...$values): self
    {
        array_unshift($this->items, ...$values);

        return $this;
    }

    /**
     * Registers extension
     *
     * @param ParamsExtensionInterface $extension
     *
     * @return self
     */
    public function addExtension(ParamsExtensionInterface $extension): self
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * Returns a copy of self with extensions applied
     *
     * @return self
     */
    public function withExtensions(): self
    {
        $extended = clone $this;

        foreach ($this->extensions as $extension) {
            $path = $extension->getPath();

            $currentValue = $extended->get($path);
            $newValue = $extension->extend($currentValue);

            if ($newValue !== $currentValue) {
                $extended->set($path, $newValue);
            }
        }

        return $extended;
    }

    /**
     * Gets params as array
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [];

        foreach ($this->items as $k => $v) {
            $arr[$k] = $v instanceof static ? $v->toArray() : $v;
        }

        return $arr;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $this->convertValue($value);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->resolveValue($this->items[$offset] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Converts value to self instance
     *
     * @param $value
     *
     * @return mixed
     */
    private function convertValue($value)
    {
        if (is_array($value)) {
            $value = new static($value, $this->options);
        }

        return $value;
    }

    /**
     * Resolves value
     *
     * @param $value
     *
     * @return mixed
     */
    private function resolveValue($value)
    {
        if (!!$this->options['resolveClosures'] && $value instanceof Closure) {
            return $value();
        }

        return $value;
    }
}
