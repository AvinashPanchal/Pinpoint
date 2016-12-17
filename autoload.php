<?php
namespace ThreeDS;
spl_autoload_register(function($class) {
    if (stripos($class, __NAMESPACE__) === 0)
    {
        @include(__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $class),'\\') . '.php');
    }
}
);