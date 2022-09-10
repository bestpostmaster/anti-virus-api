<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/bin',
        __DIR__.'/tests'
    ]);

$config = new PhpCsFixer\Config();
return $config->setRules([

    '@Symfony' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'braces' => [
        'allow_single_line_closure' => true,
    ],
    'global_namespace_import' => [
        'import_classes' => false,
        'import_constants' => false,
        'import_functions' => false,
    ],
    'heredoc_to_nowdoc' => false,
    'increment_style' => ['style' => 'post'],
    'no_unreachable_default_argument_value' => false,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'phpdoc_line_span' => [
        'property' => 'single',
        'const' => 'single',
    ],
    'phpdoc_summary' => false,
    'single_line_throw' => false,
    'yoda_style' => false,

    'declare_strict_types' => true
])
    ->setFinder($finder)
    ;
