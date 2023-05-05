<?php

Class User {

    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    public function insertUser() {
        $users = $this->conn->librairy->users;
        
        if(!empty($_POST['name']) && !empty($_POST['password'])) {
            $result = $users->insertOne([
                "name" => $_POST['name'],
                "password" => $_POST['password'],
                "librairy" => [],
                "wishlist" => []
            ]);
        }

        echo "l'utilisateur a bien été crée";
    }

    public function getAllUsers(){

        $users = $this->conn->librairy->users;

        $result = $users->find([], ['limit' => 50]);

        echo json_encode(iterator_to_array($result));
    }

    public function getUserById($id){
        
        $users = $this->conn->librairy->users;

        $result = $users->findOne([
            "_id" => new MongoDB\BSON\ObjectID("$id")
        ]);

        echo json_encode($result);

    }

    public function updateUser($id) {

        $users = $this->conn->librairy->users;

        if(!empty($_POST['name']) && empty($_POST['password'])) {
            $result = $users->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id")],
                ['$set' => ['name' => $_POST['name']]]
            );
        }

        if(empty($_POST['name']) && !empty($_POST['password'])) {
            $result = $users->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id")],
                ['$set' => ['password' => $_POST['password']]]
            );
        }

        if(!empty($_POST['name']) && !empty($_POST['password'])) {
            $result = $users->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id")],
                ['$set' => ['name' => $_POST['name'] ,'password' => $_POST['password']]]
            );
        }

        echo 'Vos informations ont bien été mis a jour';
    }
}

?>