<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->addPsr4('FOS\\UserBundle\\',realpath(__DIR__.'/../vendor/friendsofsymfony/user-bundle/'));
$loader->addPsr4('WhiteOctober\\SwiftMailerDBBundle\\',realpath(__DIR__.'/../vendor/whiteoctober/swiftmailerdbbundle/'));

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
