<?php

Class Connection {

    public function openConnection()
    {
        require 'vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        return $client;
    }
}

?>