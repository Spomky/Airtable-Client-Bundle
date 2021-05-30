<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Yoanbernabeu\AirtableClientBundle\AirtableClientBundle;

class TestKernel extends Kernel
{
    public function __construct(string $environment)
    {
        parent::__construct($environment, false);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new AirtableClientBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_test.yaml');
        $loader->load(__DIR__.'/config/services_test.yaml');
    }
}
