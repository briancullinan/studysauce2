<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

if ( isset($_SERVER) &&
    is_array($_SERVER) &&
    isset($_SERVER['HTTP_HOST']) &&
    preg_match('/test\.studysauce\.com/', $_SERVER['HTTP_HOST']) &&
    in_array(@$_SERVER['REMOTE_ADDR'], ['68.230.47.111', '71.36.230.3', '127.0.0.1', 'fe80::1', '::1'])) {
    Symfony\Component\Debug\Debug::enable();
    $kernel = new AppKernel('test', true);
}
else {
    $kernel = new AppKernel('prod', false);
}
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
