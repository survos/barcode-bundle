<?php

use Castor\Attribute\AsTask;

use function Castor\{io, run, fs};


#[AsTask('setup', description: 'setup bundles and directories')]
function setup(): void
{
    run('composer req survos/barcode-bundle easycorp/easyadmin-bundle');
    fs()->mkdir(['src/Command','src/Entity', 'src/Repository']);
}
#[AsTask('database', description: 'Install bundles for the demo')]
function database(): void
{
    // use sqlite
    if (!fs()->exists('.env.local')) {
        fs()->appendToFile('.env.local', 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"' );
    }
    run('bin/console doctrine:schema:update --force');
}

#[AsTask('copy-files', description: 'Copy the demo-specific files from the bundle to the app')]
function copy_files(): void
{
    $base = 'vendor/survos/barcode-bundle/castor/skeleton';
    foreach ([
        'src/Entity/Product.php',
                 'src/Repository/ProductRepository.php',
                 'src/Command/ImportProductsCommand',
                 'templates/products.twig'] as $demoSource) {
        fs()->copy($base . $demoSource, $demoSource);
    }
}

#[AsTask('import', description: 'Import the data')]
function import(): void
{
    run("bin/console app:load-products");
}

#[AsTask('open', description: 'start the web server and open the page')]
function open(): void
{
    run("symfony server:start -d && symfony open:local --path=/");
}

#[AsTask('build', description: 'build the demo app')]
function build(): void
{
    setup();
    copy_files();
    database();
    open();
}



