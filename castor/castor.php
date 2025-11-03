<?php

use Castor\Attribute\AsTask;

use function Castor\{io, run, fs, variable};

// Configuration - can be overridden via castor.yaml or environment
function getBundleName(): string
{
    return variable('bundle_name', 'survos/barcode-bundle');
}

function getSkeletonPath(): string
{
    $bundleName = getBundleName();
    return sprintf('vendor/%s/castor/skeleton', $bundleName);
}

#[AsTask('setup', description: 'Setup bundles and directories')]
function setup(): void
{
    io()->title('Installing required bundles');
    run('composer req survos/barcode-bundle easycorp/easyadmin-bundle');

    io()->title('Creating directories');
    $dirs = ['src/Command', 'src/Entity', 'src/Repository', 'templates'];
    foreach ($dirs as $dir) {
        if (!fs()->exists($dir)) {
            fs()->mkdir($dir);
            io()->success("Created {$dir}");
        } else {
            io()->note("{$dir} already exists");
        }
    }
}

#[AsTask('database', description: 'Configure and initialize database')]
function database(): void
{
    io()->title('Configuring database');

    if (!fs()->exists('.env.local')) {
        $dbUrl = 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"';
        fs()->appendToFile('.env.local', $dbUrl . PHP_EOL);
        io()->success('Created .env.local with SQLite configuration');
    } else {
        io()->note('.env.local already exists');
    }

    io()->title('Creating database schema');
    run('bin/console doctrine:schema:update --force --dump-sql');
}

#[AsTask('copy-files', description: 'Copy demo files from bundle to app')]
function copy_files(): void
{
    $base = getSkeletonPath();

    if (!fs()->exists($base)) {
        io()->error("Skeleton path not found: {$base}");
        io()->note('Make sure the bundle is installed via composer');
        return;
    }

    $files = [
        'src/Entity/Product.php',
        'src/Repository/ProductRepository.php',
        'src/Command/ImportProductsCommand.php', // Fixed typo
        'templates/products.html.twig', // More specific extension
    ];

    io()->title('Copying skeleton files');

    foreach ($files as $file) {
        $source = $base . '/' . $file;
        $target = $file;

        if (!fs()->exists($source)) {
            io()->warning("Source file not found: {$source}");
            continue;
        }

        // Create parent directory if needed
        $targetDir = dirname($target);
        if (!fs()->exists($targetDir)) {
            fs()->mkdir($targetDir);
        }

        fs()->copy($source, $target);
        io()->success("Copied {$file}");
    }
}

#[AsTask('import', description: 'Import demo data')]
function import(): void
{
    io()->title('Importing product data');
    run('bin/console app:import-products'); // Match your actual command name
}

#[AsTask('open', description: 'Start web server and open in browser')]
function open(): void
{
    io()->title('Starting Symfony server');
    run('symfony server:start -d');
    run('symfony open:local --path=/product'); // Adjust path as needed
}

#[AsTask('build', description: 'Complete demo setup (all steps)')]
function build(): void
{
    io()->section('Building complete demo application');

    setup();
    copy_files();
    database();
    import();
    open();

    io()->success('Demo application built successfully!');
    io()->note('Visit the opened browser to see the demo');
}

#[AsTask('clean', description: 'Remove generated files and reset')]
function clean(): void
{
    if (!io()->confirm('This will remove generated files. Continue?', false)) {
        return;
    }

    io()->title('Cleaning up demo files');

    $filesToRemove = [
        'src/Entity/Product.php',
        'src/Repository/ProductRepository.php',
        'src/Command/ImportProductsCommand.php',
        'templates/products.html.twig',
        'var/data.db',
    ];

    foreach ($filesToRemove as $file) {
        if (fs()->exists($file)) {
            fs()->remove($file);
            io()->success("Removed {$file}");
        }
    }
}
