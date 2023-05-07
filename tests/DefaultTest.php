<?php

declare(strict_types=1);

use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\Interfaces\HandlerFactoryInterface;
use Core\Entify\EntityOptions;
use Core\Entify\HandlerFactory;
use Core\Entify\DefaultFilterLibrary;
use Core\Entify\EntityHandlerDefault;
use Core\Entify\Rules;
use Core\Entify\RulesLoaderClass;
use Core\Entify\Entification;
use Core\Entify\EntityHandlers;
use Core\Utils\ValidatorFactory;
use Core\Entify\Interfaces\EntityHandlerDefaultInterface;
use Core\Entify\Interfaces\EntityInterface;

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

class DefaultTest extends PHPUnit\Framework\TestCase
{

    protected function setUp(): void
    {
        
    }

    public function testRules()
    {
        $rules = new Rules('contacts', []);
        $this->assertEquals('contacts', $rules->getName());
        $this->assertIsArray($rules->getRules());
    }

    public function testRulesLoaderClass()
    {
        $loader = new RulesLoaderClass('\\Entities\\');
        $rulesArray = [
            'name' => [
                'label' => 'Name',
                'validate' => ''
            ],
            'email' => [
                'label' => 'Email',
                'validate' => ''
            ],
            'message' => [
                'label' => 'Message',
                'validate' => ''
            ]
        ];
        $rulesFromArray = $loader->getRules('messages', $rulesArray);
        $this->assertTrue($rulesFromArray instanceof Rules);
        $this->assertEquals('messages', $rulesFromArray->getName());

        $rulesFromClass = $loader->getRules('contacts');
        $this->assertTrue($rulesFromClass instanceof Rules);
        $this->assertEquals('contacts', $rulesFromClass->getName());
        $this->assertIsArray($rulesFromClass->getRules());
    }

    public function testFilters()
    {
        $filters = new DefaultFilterLibrary('contacts');
        $allow = $filters->filter_allowed('field1', '<script>alert();</script>one', null);
        $this->assertEquals('alert();one', $allow);
        $array = $filters->filter_array('field1', ['one'], true);
        $this->assertEquals(['one'], $array);
        $escape = $filters->filter_escape('field1', '<script>alert();</script>', true);
        $this->assertEquals('&lt;script&gt;alert();&lt;/script&gt;', $escape);
        $this->assertNull($filters->getErrors());
        $filters->filter_check('field1', 55, function (int $number) {
            if ($number === 56) {
                return true;
            } else {
                return 'not correct';
            }
        });
        $this->assertEquals(1, count($filters->getErrors()));
        $filter = $filters->filter_filter('field1', 55, function (int $number) {
            return $number + 1;
        });
        $this->assertEquals(56, $filter);
    }

    public function testHandlerFactory()
    {
        $rules = $this->getRules();
        $handlerFactory = new HandlerFactory($rules);
        $this->assertTrue($handlerFactory instanceof HandlerFactoryInterface);
        $handlers = $handlerFactory->getHandlers();
        $this->assertTrue($handlers instanceof EntityHandlersInterface);
    }

    public function testDefaultHandler()
    {
        $rulesName = 'contacts';
        $handler = new EntityHandlerDefault(
                $this->getValidator(),
                $this->getRules(),
                $this->getFilters($rulesName),
                $this->getOptions()
        );

        $result = $handler->handle([
            'name' => 'Joe',
            'lastname' => 'Doe',
            'age' => '30',
            'email' => 'admin@example',
            'password' => '',
            'skills' => 6
        ]);

        $this->assertIsInt($result[0]['age']);
        $wait = '&lt;script&gt;alert()&lt;/script&gt;Lorem ipsum dolor sit amet';
        $this->assertEquals($wait, $result[0]['description']);
        $this->assertEquals(1, count($handler->getErrors()));
    }

    public function testEntityHandlers()
    {
        $handler = new EntityHandlerDefault(
                $this->getValidator(),
                $this->getRules(),
                $this->getFilters('contacts'),
                $this->getOptions()
        );
        $handlersObject = new EntityHandlers($handler);

        $handlers = $handlersObject->getHandlers();

        $this->assertEquals(1, count($handlers));

        $this->assertTrue($handlers[EntityHandlerDefault::class] instanceof EntityHandlerDefaultInterface);
    }

    public function testEntification()
    {

        $loader = new RulesLoaderClass('\\Entities\\');
        $entification = new Entification($loader);

        $data = [
            [
                'name' => 'Joe',
                'lastname' => 'Doe',
                'age' => '30',
                'email' => 'admin@example',
                'password' => '',
                'skills' => 6
            ]
        ];

        $provider = $entification->getArrayProvider($data, 'contacts');
        $entity = $provider->getEntity();
        $this->assertTrue($entity instanceof EntityInterface);
    }

    public function getRules(): Rules
    {
        $rulesLoader = new RulesLoaderClass('\\Entities\\');
        return $rulesLoader->getRules('contacts');
    }

    public function getValidator()
    {
        $factory = new ValidatorFactory();
        return $factory->getValidator();
    }

    public function getFilters(string $rulesName)
    {
        return new DefaultFilterLibrary($rulesName);
    }

    public function getOptions()
    {
        return new EntityOptions();
    }

}
