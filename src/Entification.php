<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\EntificationInterface;
use Core\Entify\Interfaces\RulesLoaderInterface;
use Core\Entify\DataProvider\Interfaces\DataProviderPaginatedInterface;
use Core\Entify\DataProvider\Interfaces\DataProviderFormInterface;
use Core\Entify\DataProvider\ArrayDataProvider;
use Core\Entify\DataProvider\FormDataProvider;
use Core\Entify\HandlerFactory;
use Psr\Http\Message\ServerRequestInterface;
use function is_string;

/**
 * This is factory for giving us prepared data providers.
 * This is start point to use Entify.
 */
class Entification implements EntificationInterface
{

    /**
     * Rules loader interface
     * @var RulesLoaderInterface
     */
    protected RulesLoaderInterface $loader;

    public function __construct(RulesLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @inheritdoc
     */
    public function getArrayProvider(
            array $data,
            string|array $rules
    ): DataProviderPaginatedInterface
    {
        $r = $this->resolveRules($rules);
        $factory = new HandlerFactory($this->loader->getRules($r['name'], $r['rules']));
        $handlers = $factory->getHandlers();
        return new ArrayDataProvider(
                $handlers,
                $data
        );
    }

    /**
     * @inheritdoc
     */
    public function getFormProvider(
            ServerRequestInterface $request,
            string|array $rules,
            ?string $uploadDir = null,
            array $allowed = ['image/jpeg', 'image/png'],
            string $maxSize = '2M'
    ): DataProviderFormInterface
    {
        $r = $this->resolveRules($rules);
        $factory = new HandlerFactory($this->loader->getRules($r['name'], $r['rules']));
        $handlers = $factory->getHandlers();
        return new FormDataProvider($handlers, $request, $uploadDir, $allowed, $maxSize);
    }

    protected function resolveRules(array|string $rules): array
    {
        if (is_string($rules)) {
            $name = $rules;
            $rulesArray = null;
        } else {
            $name = 'default';
            $rulesArray = $rules;
        }
        return [
            'name' => $name,
            'rules' => $rulesArray
        ];
    }

}
