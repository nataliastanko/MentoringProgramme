<?php
/**
 * Customize the Bootstrap Process before Running Tests
 * https://symfony.com/doc/current/testing/bootstrap.html
 */

// clear cache before running
if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'php "%s/console" cache:clear --env=%s --no-warmup',
        __DIR__.'/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}

require __DIR__.'/autoload.php';
