<?php

return (new Marek\CodingStandard\PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['vendor', 'deploy', 'deploy.php'])
            ->in(__DIR__)
    );
