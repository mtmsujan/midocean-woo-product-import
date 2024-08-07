<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbb76f5fef2dad70b723fc0b439daec70
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbb76f5fef2dad70b723fc0b439daec70::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbb76f5fef2dad70b723fc0b439daec70::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbb76f5fef2dad70b723fc0b439daec70::$classMap;

        }, null, ClassLoader::class);
    }
}
