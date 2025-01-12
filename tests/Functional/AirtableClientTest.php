<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;

class AirtableClientTest extends KernelTestCase
{
    public function testIfKernelBootingAndAirtableClientIsRegistered(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has(AirtableClientInterface::class));
        $this->assertTrue($container->has("airtable_client"));
        $this->assertInstanceOf(AirtableClient::class, $container->get("airtable_client"));
    }
}
