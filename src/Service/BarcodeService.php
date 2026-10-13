<?php

declare(strict_types=1);

namespace Survos\BarcodeBundle\Service;

class BarcodeService
{
    public function __construct(private array $generators = [])
    {
    }

    public function addGenerator(string $class, ?string $imageFormat = null)
    {
        $this->generators[(new \ReflectionClass($class))->getShortName()] =
            ['class' => $class,
                'image_format' => $imageFormat,
            ];
    }

    public function getGenerators(): array
    {
        return $this->generators;
    }

    public function getGenerator(string $shortName): array
    {
        return $this->generators[$shortName];
    }
}
