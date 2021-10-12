# tolkam/template

Templating component with multiple renderers (engines) support.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Tolkam\Template\Renderer\Twig\TwigRenderer;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

$twigEnvironment = new Environment(new ArrayLoader([
    '@namespace/template.myExtension' => 'Hello {{ name }}!',
]));
$renderer = new TwigRenderer($twigEnvironment, 'myExtension');

// using unified way of rendering templates
// (without knowledge of namespaces resolution and file extensions)
echo $renderer->render('@namespace/template', ['name' => 'John']);
````

## License

Proprietary / Unlicensed 🤷
