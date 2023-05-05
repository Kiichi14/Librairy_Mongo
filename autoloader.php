<?php
session_start();

function custom_autoloader($className) {


    include 'src/'. $className . '.php';
}

spl_autoload_register('custom_autoloader');

?>