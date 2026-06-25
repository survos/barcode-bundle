<?php

namespace Survos\BarcodeBundle;

use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Survos\BarcodeBundle\Service\BarcodeService;
use Survos\BarcodeBundle\Twig\BarcodeTwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SurvosBarcodeBundle extends AbstractBundle implements CompilerPassInterface
{
    const SERVICE_TAG = 'barcode.generator';
    protected string $extensionAlias = 'survos_barcode';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($this, PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }

    public function process(ContainerBuilder $container): void
    {

        $service = $container->getDefinition(BarcodeService::class);
        $generators = $container->findTaggedServiceIds(self::SERVICE_TAG);
        foreach ($generators as $id => $tags) {
            $class = $tags[0]['class'];
            $service->addMethodCall('addGenerator',
                [
                    $class,
                    $this->getImageFormat($class),
                ]);
        }
    }

    private function getImageFormat(string $generatorClass): ?string
    {
        return match ($generatorClass) {
            BarcodeGeneratorJPG::class => 'image/jpeg',
            BarcodeGeneratorPNG::class => 'image/png',
            default => null
        };
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $definition = $builder
            ->autowire('survos.barcode_twig', BarcodeTwigExtension::class)
            ->addTag('twig.extension');

        $definition->setArgument('$widthFactor', $config['widthFactor']);
        $definition->setArgument('$height', $config['height']);
        $definition->setArgument('$foregroundColor', $config['foregroundColor']);

        $builder->autowire(BarcodeService::class)
            ->setPublic(true);

        foreach ([
            BarcodeGeneratorSVG::class,
            BarcodeGeneratorHTML::class,
//            BarcodeGeneratorDynamicHTML::class,
            BarcodeGeneratorPNG::class,
            BarcodeGeneratorJPG::class
        ] as $generatorClass) {
            $builder->autowire($generatorClass)
                ->setPublic(true)
                ->setAutoconfigured(true)
                ->setAutowired(true)
                ->addTag(self::SERVICE_TAG, [
                    'class' => $generatorClass,
                    'image_format' => $this->getImageFormat($generatorClass)
                ]);
        }
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->scalarNode('widthFactor')->defaultValue(2)->end()
            ->scalarNode('height')->defaultValue(30)->end()
            ->scalarNode('foregroundColor')->defaultValue('green')->end()
            ->end();
        ;
    }
}
