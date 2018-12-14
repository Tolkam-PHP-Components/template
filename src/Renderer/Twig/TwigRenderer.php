<?php declare(strict_types=1);

namespace Tolkam\Template\Renderer\Twig;

use Tolkam\Template\RendererInterface;
use Traversable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TwigRenderer implements RendererInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var string
     */
    protected $ext = 'twig';

    /**
     * @param Environment|null $environment
     * @param string|null      $ext
     * @param array            $options     Environment options
     */
    public function __construct(Environment $environment = null, string $ext = null, array $options = [])
    {
        if ($environment !== null) {
            $this->environment = $environment;
        } else {
            $this->loader = $this->createDefaultLoader();
            $this->environment = $this->createEnvironment($this->loader, $options);
        }

        if ($ext !== null) {
            $this->ext = $ext;
        }
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, array $params = []): string
    {
        return $this->environment->render($this->normalizeName($name), $this->normalizeParams($params));
    }

    /**
     * @inheritDoc
     */
    public function addPath(string $path, string $namespace = null): void
    {
        if ($this->loader instanceof FilesystemLoader) {
            $this->loader->addPath($path, $namespace ?: FilesystemLoader::MAIN_NAMESPACE);
        }
    }

    /**
     * Gets the environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Creates the environment
     *
     * @param  array $options
     *
     * @return Environment
     */
    protected function createEnvironment(LoaderInterface $loader, array $options): Environment
    {
        return new Environment($loader, $options);
    }

    /**
     * Creates the default loader
     *
     * @return LoaderInterface
     */
    protected function createDefaultLoader(): LoaderInterface
    {
        return new FilesystemLoader();
    }
    
    /**
     * Normalizes namespaced name in the format
     * "namespace::name" to "@namespace/name"
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeName(string $name) : string
    {
        $name = preg_replace('#^([^:]+)::(.*)$#', '@$1/$2', $name);

        if (! preg_match('#\.[a-z]+$#i', $name)) {
            return sprintf('%s.%s', $name, $this->ext);
        }

        return $name;
    }

    /**
     * Normalizes params into array
     *
     * @param  mixed $params
     * @return array
     */
    protected function normalizeParams($params): array
    {
        if (null === $params) {
            return [];
        }
    
        if (is_array($params)) {
            return $params;
        }
    
        if (is_object($params)) {
            return (array) $params;
        }
    
        if ($params instanceof Traversable) {
            return iterator_to_array($params);
        }
    
        throw new Exception(sprintf(
            'Template parameters must be array, object or instance of %s, %s given',
            Traversable::class,
            gettype($params)
        ));
    }
}
