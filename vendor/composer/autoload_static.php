<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb074cb5b8fb49f71f6c47d2975a201ee
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'JFB\\Converter\\Compatibility\\' => 28,
            'JFB\\Converter\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'JFB\\Converter\\Compatibility\\' => 
        array (
            0 => __DIR__ . '/../..' . '/compatibility',
        ),
        'JFB\\Converter\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb074cb5b8fb49f71f6c47d2975a201ee::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb074cb5b8fb49f71f6c47d2975a201ee::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb074cb5b8fb49f71f6c47d2975a201ee::$classMap;

        }, null, ClassLoader::class);
    }
}