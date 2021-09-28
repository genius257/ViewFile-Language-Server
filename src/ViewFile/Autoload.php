<?php

namespace Genius257\ViewFileLanguageServer\ViewFile;

class Autoload {
    public static function findAutoloaders(string $path) : array {
        //TODO: not implemented
        return [];
    }

    public static function findComposerJsons(string $path) : array {
        $path = \is_dir($path) ? $path : \dirname($path);
        if (!\is_dir($path)) {
            throw new \Exception("the string provided is not a valid path");
        }
        $result = [];
        do {
            if (file_exists($path.'/composer.json')) {
                $result[] = $path.'/composer.json';
            }
        } while ($path !== \dirname($path) && $path = \dirname($path));

        return $result;
    }

    //public static function 

    /**
    * Resolve Fqsen to file path
    */
    public static function resolveFqsen(string $fqsen, string $path) : string {
        $composerJsons = static::findComposerJsons($path);

        $loader = new \Composer\Autoload\ClassLoader(\dirname(\dirname(\dirname(__FILE__))));

        foreach($composerJsons as $composerJson) {
            //echo \dirname($composerJson).PHP_EOL;
            $composerPath = dirname($composerJson).'/vendor/composer';
            //is_dir

            if (file_exists($composerPath.'/autoload_namespaces.php')) {
                $map = require $composerPath . '/autoload_namespaces.php';
                foreach ($map as $namespace => $path) {
                    $loader->set($namespace, $path);
                }
            }

            if (file_exists($composerPath.'/autoload_psr4.php')) {
                $map = require $composerPath . '/autoload_psr4.php';
                foreach ($map as $namespace => $path) {
                    $loader->setPsr4($namespace, $path);
                }
            }

            if (file_exists($composerPath.'/autoload_classmap.php')) {
                $classMap = require $composerPath . '/autoload_classmap.php';
                if ($classMap) {
                    $loader->addClassMap($classMap);
                }
            }
        }

        $file = $loader->findFile($fqsen);
        
        return $file;
    }

    public function __construct() {
        //
    }
}