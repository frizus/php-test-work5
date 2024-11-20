<?php
$finder = (new PhpCsFixer\Finder())
    ->exclude(['.git', '.docker', 'vendor', 'database/migrations'])
    ->ignoreUnreadableDirs()
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setFinder($finder);
