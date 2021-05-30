<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;

/**
 * @internal
 */
class ServiceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @test
     */
    public function theServiceExists(): void
    {
        static::assertTrue(self::$container->has('fqcnClient'));
        $client = self::$container->get('fqcnClient');

        static::assertInstanceOf(AirtableClient::class, $client);
    }

    /**
     * @test
     */
    public function theServiceAliasExists(): void
    {
        static::assertTrue(self::$container->has('aliasClient'));
        $client = self::$container->get('aliasClient');

        static::assertInstanceOf(AirtableClient::class, $client);
    }
}
