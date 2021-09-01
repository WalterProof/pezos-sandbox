<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()->in(['src']);

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'               => true,
        'array_indentation'      => true,
        'binary_operator_spaces' => [
            'operators' => ['=>' => 'align', '=' => 'align'],
        ],
        'declare_strict_types'       => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'no_alias_functions'         => true,
        'no_extra_blank_lines'       => [
            'tokens' => [
                'break',
                'continue',
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_useless_else'                     => true,
        'no_useless_return'                   => true,
        'ordered_class_elements'              => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order'                        => true,
    ])
    ->setUsingCache(true)
    ->setFinder($finder);

return $config;
