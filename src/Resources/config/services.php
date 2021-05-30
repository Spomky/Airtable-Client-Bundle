<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(AirtableClient::class)
        ->args([
            '%yoanbernabeu_airtable_client.airtable_client.key%',
            '%yoanbernabeu_airtable_client.airtable_client.id%',
            service(HttpClientInterface::class),
            service(ObjectNormalizer::class),
        ])
        ->alias('yoanbernabeu_airtable_client.airtable_client')
    ;
};