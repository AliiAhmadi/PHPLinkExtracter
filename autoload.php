<?php

function loader(string $class)
{
    $fullPath = "App/" . $class;

    if (file_exists($fullPath)) {
        require_once "$fullPath";
    } else {
        die("could not find $class in $fullPath");
    }
}


spl_autoload_register("loader");
