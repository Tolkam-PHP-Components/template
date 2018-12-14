<?php declare(strict_types=1);

namespace Tolkam\Template\Params;

interface ParamsExtensionInterface
{
    /**
     * Returns path to value to extend
     *
     * @return string
     */
    public function getPath(): string;
    
    /**
     * Returns new value
     *
     * @param $previousValue
     *
     * @return mixed
     */
    public function extend($previousValue);
}
