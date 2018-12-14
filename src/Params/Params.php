<?php declare(strict_types=1);

namespace Tolkam\Template\Params;

use ArrayObject;
use Closure;
use Tolkam\Utils\Arr;

/**
 * Extendable params object with array access and more
 *
 * @package Tolkam\Template\Params
 */
class Params extends ArrayObject implements ParamsInterface
{
    /**
     * Array path separator
     */
    const PATH_SEPARATOR = '.';
    
    /**
     * @var array
     */
    private $options = [
    
        // whether to use closure return value as offset value
        'resolveClosures' => true,
    
        // Iterator implementation class
        'iteratorClass' => 'ArrayIterator',
    ];
    
    /**
     * @var ParamsExtensionInterface[]
     */
    private $extensions = [];
    
    /**
     * @param array $input
     * @param array $options
     */
    public function __construct(array $input = [], array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        
        // convert arrays to Params
        foreach ($input as $k => $v) {
            if (is_array($v)) {
                $input[$k] = new static($v, $this->options);
            }
        }
        
        parent::__construct($input, self::ARRAY_AS_PROPS, $this->options['iteratorClass']);
    }
    
    /**
     * @inheritDoc
     */
    public function set(string $key, $value): void
    {
        Arr::set($this, $key, $value, self::PATH_SEPARATOR);
    }
    
    /**
     * @inheritDoc
     */
    public function add(string $key, $value): void
    {
        Arr::add($this, $key, $value, self::PATH_SEPARATOR);
    }
    
    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this, $key, $default, self::PATH_SEPARATOR);
    }
    
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
    
    /**
     * @inheritDoc
     */
    public function offsetSet($index, $value)
    {
        if (is_array($value)) {
            $value = new static($value, $this->options);
        }
        
        parent::offsetSet($index, $value);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet($index)
    {
        return $this->resolveValue(parent::offsetGet($index));
    }
    
    /**
     * @inheritDoc
     */
    public function addExtension(ParamsExtensionInterface $extension): ParamsInterface
    {
        $this->extensions[] = $extension;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function withExtensions(): ParamsInterface
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
