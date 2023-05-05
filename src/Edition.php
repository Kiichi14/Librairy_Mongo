<?php

Class Edition {
    
    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    // Méthode INSERT éditions

    public function addEdition($id) {

        $collection = $this->conn->librairy->books;

        if(!empty($_POST['format']) && !empty($_POST['edition'])) {
            $result = $collection->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id")],
                ['$push' => ["editions" => ["id" => new MongoDB\BSON\ObjectID(),"format" => $_POST['format'], "edition" => $_POST['edition']]]]
            );
        }

        echo 'Une edition a bien été ajouté a votre livre';

    }

}

?>