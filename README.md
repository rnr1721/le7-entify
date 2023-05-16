# le7-entify
Entify is entity framework for le7 PHP MVC framework or any PHP project

This project is a universal validator/converter. With it, it is possible to
validate/normalize arrays. For example, you can use this project using the
rules:

- Validate/render forms (and uploaded files)
- Validate/render arrays
- Validate/render data from the database

## Requirements

- PHP 8.1
- Composer

## Installation

```shell
composer require rnr1721/le7-entify
```

## Testing

```shell
composer test
```

## Usage

1. Create rules in a class or array.
2. Create a DataProvider with Data
3. Get the entity
4. Get the verified entity

```php
use Core\Entify\RulesLoaderClass;
use Core\Entify\Entification;

// Loader for rules
$loader = new RulesLoaderClass();

// Get Entify factory. We can create $loader and $entifications in container
$entification = new Entification($loader);

// Make our rules. In this example is array,
// But more comfortable use classes
$rulesArray = [
		'name' => [
			'label' => 'User login',
			'validate' => 'required|minlength:3|maxlength:20'
	],
		'email' => [
			'label' => 'Email',
			'validate' => 'required|email'
	],
		'message' => [
			'label' => 'Message',
			'validate' => 'minlength:30|maxlength:200'
			'escape' => true
	],
		'age' => [
			'label' => 'Message',
			'convert' => 'int',
			'validate' => 'min:18|max:90'
	],
];

// Now get our data
$data = [
	'name' => 'John',
	'email' => 'johndoe@example.com',
	'message' => 'my message text',
	'age' => '33'
];

$provider = $entification->getArrayProvider($data, $rulesArray);

// Get entity
$entity = $provider->getEntity();

// our validated and normalized array
print_r($entity->export())

// Get validation errors if present
$entity->getErrors();

```
But it is basic usage. You can make more great things with Entify.

## Rules in classes

For some reasons you may need to store rules in class, not in arrays.
It great for storing rules for many-time usage from different places of
your code. For example, we create file in namespaces Entities:

```php
<?php

declare(strict_types=1);

namespace Entities;

use Core\Entify\Interfaces\ModelInterface;

class Contactform implements ModelInterface
{

    public function getRules(): array
    {
        return [
                        'name' => [
                                'label' => 'User login',
                                'validate' => 'required|minlength:3|maxlength:20'
                ],
                        'email' => [
                                'label' => 'Email',
                                'validate' => 'required|email'
                ],
                        'message' => [
                                'label' => 'Message',
                                'validate' => 'minlength:30|maxlength:200'
                                'escape' => true
                ],
                        'age' => [
                                'label' => 'Message',
                                'convert' => 'int',
                                'validate' => 'min:18|max:90'
                ],
        ];
    }

}
```

Thats all! Now we can use our rules. This example similar with previous example,
but in this case we use rules model, not array:

```php
use Core\Entify\RulesLoaderClass;
use Core\Entify\Entification;

// Loader for rules. Warning! Now we set namespace with entities rules!
$loader = new RulesLoaderClass('\\Entities\\');

// Get Entify factory
$entification = new Entification($loader);

// Now get our data
$data = [
	'name' => 'John',
	'email' => 'johndoe@example.com',
	'message' => 'my message text',
	'age' => '33'
];

// Contatform - is class with rules. Now we can set it here
// Starts with lowercase
$provider = $entification->getArrayProvider($data, 'contactform');

// Get entity
$entity = $provider->getEntity();

// our validated and normalized array
print_r($entity->export())

// Get validation errors if present
$entity->getErrors();

```

## DataProviders

By default you can use Array and Form providers in Entification class,
but you can write own DataProviders (DataProviderInterface) or
Renderers (EntityRendererInterface).

## Rules

You can use many rules for validation and normalisation entities.
Rules runs one-by-one, list of rules for each field is queue. For example,
in this case filter 'filter' will run after 'convert'

```php
$rules = [
    'age' => [
        'label' => 'User age',
        'convert' => int,
        'filter' => function(mixed $data){
                        return $data + 1;
                    }
    ]
];
```

## Available filters

### validate

This is validation filter. It uses le7-validator for validate, and you can
read more about validation rules here: https://github.com/rnr1721/le7-validator

Required field.
Can be: string

- **required**: The field must be filled
- **min:{n}**: Field value must be at least {n}
- **max:{n}**: Field value must be no more than {n}
- **minlength:{n}**: The length of the string field value must be at least {n}
- **maxlength:{n}**: The length of the string field value must be no more than {n}
- **email**: The field value must be a valid email address
- **notempty**: The field value must not be empty or contain only spaces
- **numeric**: Validates that only numeric data
- **email_dns**:  validates the format of an email address and checks if the domain part of the email address has a valid DNS record
- **url**: Validate URL
- **url_active**: If URL address is valid and exists
- **date**: Validates that value is date
- **date_format:{n}**: Validates date format: Example: date_format:Y-m-d
- **date_before:{n}**: Validate date before some date. Example: date_before:2022-05-15
- **date_after:{n}**: Validate date after some date. Example: date_after:2022-05-15
- **boolean**: Validate boolean

for example:
```php
$rules = [
    'username' => [
        'label' => 'Username',
        // Required field, minimum length is 3, maximum 20 symbols
        'validate' => 'required|minlength:3|maxlength:20'
    ]
];
```

### label

This is label (human-readable name) of field. You can use gettext functions
such as _('My great label') for internationalisation. It used by validator,
when it form errors messages, or you can use it when you render entity in
future.

Required field.
Can be: string

for example:
```php
$rules = [
    'email' => [
        'label' => 'User Email',
        'validate' => 'required|email'
    ]
];
```

### check

This filter used for own callable for check field.
Callable can return true if succes, or string as error.

Not-Required field.
Can be: false or callable

```php
$rules = [
    'username' => [
        'label' => 'User login',
        'validate' => '',
        'check' => function(mixed $data){
                        if ($data === 'john') {
                            return true;
                        }
                        return 'Incorrect name';
                    }
    ]
];
```

### default

If field not exists in entity, it will be added and when you will get entity,
field will be present with this default value

Not-Required field
Can be: mixed

### convert

Convert value to some data type

Not-Required field
Can be: string (values: 'int', 'string', 'bool', 'float', 'double', 'bool')

for example:

```php
$rules = [
    'age' => [
        'label' => 'User age',
        'validate' => 'min:12|max:99',
        'convert' => 'int'
    ]
];
```

### hide

Hide value in entity. You will get entity without value

Not-Required field
Can be: true or false

for example:

```php
$rules = [
    'password' => [
        'label' => 'User password',
        'validate' => '',
        'hide' => true
    ]
];
```

### escape

Value will be escaped with htmlspecialchars() function

Not-Required field
Can be: true or false

for example:

```php
$rules = [
    'description' => [
        'label' => 'Description',
        'validate' => 'min:30|max:500',
        'escape' => true
    ]
];
```

### allowed

This filter will process value with strip_tags PHP function

Not-Required field
Can be: null or string - second argument for strip_tags function

for example:

```php
$rules = [
    'description' => [
        'label' => 'Description',
        'validate' => 'min:30|max:500',
        'allowed' => '<p><a><br>'
    ]
];
```

### filter

This filter allow to process value with your own callable.
Callable need return mixed value

Not-Required field
Can be: callable

for example:

```php
$rules = [
    'age' => [
        'label' => 'User age',
        'convert' => int,
        'filter' => function(mixed $data){
                        return $data + 1;
                    }
    ]
];
```

### string, int, float, null, array, object, resource, callable

This filter will throw exception if value will not be some type

ot-Required field
Can be: null or true

### meta

Any meta information for your purposes

Not-Required field
Can be: mixed

## Entities

If you make entity from array, or form or write your own DataProvider,
you need to know that DefaultHandler need these types of arrays:

```php
$data = [
    'login' => 'myuser',
    'password' => '7777777'
];
```

```php
$data = [
    [
        'login' => 'myuser',
        'password' => '7777777'
    ]
];
```
If you use this type of array:

```php
$data = [
    'myuser',
    '7777777'
];
```
You will got the error.

## Use options while got entity

Steps in Default Handler while array of data come from DataProvider to Entity:
(this processes run in EntityHandlerDefault, method handle()):

1. Check array format
2. Standartization (Check if in entity present all fields in rules or for redundant fields)
3. Validation (run validator for all entities)
4. Filters (run filters)
5. Remove hide ('hide') fields

In these example you can read how use some options:

```php
use Core\Entify\RulesLoaderClass;
use Core\Entify\Entification;

// Loader for rules. Warning! Now we set namespace with entities rules!
$loader = new RulesLoaderClass('\\Entities\\');

// Get Entify factory
$entification = new Entification($loader);

// Now get our data
$data = [
	'name' => 'John',
	'email' => 'johndoe@example.com',
	'message' => 'my message text',
	'age' => '33'
];

// Contatform - is class with rules. Now we can set it here
// Starts with lowercase
$provider = $entification->getArrayProvider($data, 'contactform');

// Get entity
$entity = $provider->getEntity();

// If this, the fields that not present in rules, will be deleted
// Default is true;
$entity->getOptions()->setDeleteRedundant(true);

// Skip validation (only skip validator)
// Default is false
$entity->getOptions()->setSkipValidation(true);

// If validator got errors, any filters will not be applied
// Default false
$entity->getOptions()->setReturnIfValidationErrors(true);

// If some field of array not present in rules, return before filters and validation
// Default false
$entity->getOptions()->setReturnIfNotExistsErrors(true);

// our validated and normalized array
print_r($entity->export())

// Get validation errors if present
$entity->getErrors();
```
