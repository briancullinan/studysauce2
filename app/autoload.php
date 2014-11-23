<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->addPsr4('WhiteOctober\\SwiftMailerDBBundle\\',realpath(__DIR__.'/../vendor/whiteoctober/swiftmailerdbbundle/'));
$loader->addPsr4('HWI\\Bundle\\OAuthBundle\\',realpath(__DIR__.'/../vendor/hwi/OAuthBundle/'));
$loader->addPsr4('Buzz\\',realpath(__DIR__.'/../vendor/kriswallsmith/Buzz/lib/Buzz/'));
$loader->addPsr4('Guzzle\\',realpath(__DIR__.'/../vendor/guzzle/Guzzle/'));
$loader->addPsr4('Aws\\',realpath(__DIR__.'/../vendor/amazon/Aws/'));
$loader->addClassMap(require __DIR__.'/../vendor/authorizenet/sdk-php/classmap.php');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
