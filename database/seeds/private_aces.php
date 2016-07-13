<?php

use Microffice\User;
use Microffice\AccessControl\Acl;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

$aces = [
    'Dworkin' => [
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('view')
        ],
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('create')
        ],
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('edit')
        ],
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('delete')
        ],
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('undelete')
        ],
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('master')
        ],
        [
            'object' => 'Microffice\User',
            'field' => 'trashed',
            'mask' => app(MaskBuilderContract::class)
                        ->add('owner')
        ],
        [
            'object' => 'Microffice\AccessControl\Acl',
            'mask' => app(MaskBuilderContract::class)
                        ->add('view')
        ],
        [
            'object' => 'Microffice\AccessControl\Acl',
            'mask' => app(MaskBuilderContract::class)
                        ->add('edit')
        ],
        [
            'object' => 'Microffice\AccessControl\Acl',
            'mask' => app(MaskBuilderContract::class)
                        ->add('master')
        ],
        [
            'object' => 'Microffice\AccessControl\Acl',
            'mask' => app(MaskBuilderContract::class)
                        ->add('owner')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('view')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('create')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('edit')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('delete')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('undelete')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('master')
        ],
        [
            'object' => 'Microffice\AccessControl\Ace',
            'mask' => app(MaskBuilderContract::class)
                        ->add('owner')
        ],/**/
    ],
    'Martin' => [
        [
            'object' => 'Microffice\User',
            'mask' => app(MaskBuilderContract::class)
                        ->add('view')
        ],/**/
    ],
];