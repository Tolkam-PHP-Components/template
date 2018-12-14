<?php declare(strict_types=1);

namespace Tolkam\Template\Params;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Serializable;

interface ParamsInterface extends IteratorAggregate, ArrayAccess, Serializable, Countable
{
    /**
     * Sets param value
     *
     * @param string $key
     * @param        $value
     *
     * @return void
     */
    public function set(string $key, $value): void;
    
    /**
     * Adds param value if it doesn't exist
     *
     * @param string $key
     * @param        $value
     *
     * @return void
     */
    public function add(string $key, $value): void;
    
    /**
     * Gets param value
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);
    
    /**
     * Registers extension
     *
     * @param ParamsExtensionInterface $extension
     *
     * @return ParamsInterface
     */
    public function addExtension(ParamsExtensionInterface $extension): ParamsInterface;
    
    /**
     * Returns a copy of self with extensions applied
     *
     * @return ParamsInterface
     */
    public function withExtensions(): ParamsInterface;
    
    /**
     * Gets params as array
     *
     * @return array
     */
    public function toArray(): array;
}
