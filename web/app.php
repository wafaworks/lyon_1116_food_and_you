<?php
umask(0002);
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

defined('ENV') or define('ENV', getenv('APP_ENV') === 'dev' ? 'dev' : 'prod');
defined('APPDEBUG') or define('APPDEBUG', ENV === 'dev');

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/

if (ENV == 'dev') {
    Debug::enable();
    $kernel = new AppKernel(ENV, APPDEBUG);
} else {
    include_once __DIR__ . '/../app/bootstrap.php.cache';
    $kernel = new AppKernel(ENV, APPDEBUG);
    $kernel->loadClassCache();
}

//require_once __DIR__.'/../app/AppCache.php';

//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
