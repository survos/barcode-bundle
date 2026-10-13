<?php

declare(strict_types=1);

namespace Survos\BarcodeBundle\Tests\Functional;

use Survos\BarcodeBundle\Service\BarcodeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class FunctionalTest extends KernelTestCase
{
    private Container $container;

    public function testSomething(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $this->container = static::getContainer();

        /** @var BarcodeService $barcodeService */
        $barcodeService = $this->container->get(BarcodeService::class);

        //        $config = $barcodeService->getConfig();
        // check that the config comes from being loaded in config.yml, not the defaults
        $this->assertGreaterThan(1, count($barcodeService->getGenerators()));
    }

    // to get rid of "Test code or tested code did not remove its own exception handlers"
    // https://marceichenseher.de/de/hintergrund/php-symfony-und-phpunit-test-code-or-tested-code-did-not-remove-its-own-exception-handlers-aufloesen/
    protected static function ensureKernelShutdown(): void
    {
        $wasBooted = static::$booted;
        parent::ensureKernelShutdown();

        if ($wasBooted) {
            restore_exception_handler();
        }
    }
}
