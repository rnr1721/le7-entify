<?php

declare(strict_types=1);

namespace Entities;

use Core\Entify\Interfaces\RulesModelInterface;

class Contacts implements RulesModelInterface
{

    public function getRules(): array
    {
        return [
            'name' => [
                'label' => _('User name'),
                'validate' => 'minlength:1|maxlength:20',
            ],
            'lastname' => [
                'label' => _('User lastname'),
                'validate' => ''
            ],
            'age' => [
                'label' => _('User Age'),
                'validate' => 'min:18|max:90',
                'convert' => 'int'
            ],
            'description' => [
                'label' => _('Description'),
                'validate' => 'required',
                'default' => '<script>alert()</script>Lorem ipsum dolor sit amet',
                'escape' => true
            ],
            'email' => [
                'label' => _('User email'),
                'validate' => 'email|required'
            ],
            'password' => [
                'label' => _('User password'),
                'validate' => '',
                'hide' => true,
            ],
            'skills' => [
                'label' => _('Skills level'),
                'validate' => '',
                // At first, convert to int
                'convert' => 'int',
                // Check something
                'check' => function (int $value) {
                    if ($value > 5) {
                        return true;
                    }
                    return 'Fuck! Your knowledge not enough!';
                },
                // Filter something
                'filter' => function(int $value) {
                    return $value * 0.5;
                }
            ]
        ];
    }

}
