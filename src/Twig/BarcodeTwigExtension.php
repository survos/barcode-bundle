<?php

declare(strict_types=1);

namespace Survos\BarcodeBundle\Twig;

use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Survos\BarcodeBundle\Service\BarcodeService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BarcodeTwigExtension extends AbstractExtension
{
    public function __construct(
        private BarcodeService $barcodeService,
        private int $widthFactor,
        private int $height,
        private string $foregroundColor,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('barcode', [$this, 'barcode'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('barcode', [$this, 'barcode'], ['is_safe' => ['html'],
            ]),
        ];
    }

    public function barcode(
        string $value,
        float|int|string|null $widthFactor = null,
        ?int $height = null,
        ?string $foregroundColor = null,
        string $type = BarcodeGenerator::TYPE_CODE_128,
        string $generatorClass = 'BarcodeGeneratorSVG',
    ): string {
        $generatorData = $this->barcodeService->getGenerator($generatorClass);
        /** @var BarcodeGeneratorSVG|BarcodeGeneratorPNG $generatorInstance */
        $generatorInstance = new $generatorData['class']();
        $imageFormat = $generatorData['image_format'] ?? null;
        $widthFactor = match ($generatorClass) {
            BarcodeGeneratorSVG::class => (float) $this->widthFactor,
            default => $widthFactor ?? $this->widthFactor,
        };
        //        dd($generatorClass, $widthFactor);
        $barcodeData = $generatorInstance->getBarcode(
            barcode: $value,
            type: $type,
            widthFactor: $widthFactor,
            height: $height ?? $this->height,
            // hack for array / string issue https://github.com/picqer/php-barcode-generator/issues/172
            foregroundColor: $imageFormat
                ? [0, 0, 0] :
                $foregroundColor ?? $this->foregroundColor
        );
        if ($imageFormat) {
            $barcodeData = base64_encode($barcodeData);
        }

        return $barcodeData;
    }
}
