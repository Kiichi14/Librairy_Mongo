<?php

Class Wishlist {
    
    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    public function addToWishlist(){

        $users = $this->conn->librairy->users;

        $users->updateOne(
            ["_id" => new MongoDB\BSON\ObjectID($_POST['idUser'])],
            ['$push' => ["wishlist" => $_POST['idEdition']]]
        );

        echo 'Le livre a bien été ajouté a votre Wishlist';
    }

    public function getWishlist($id) {

        $users = $this->conn->librairy->users;

        $result = $users->aggregate([
            [ '$limit' => 50 ],
            // Match sur l'id de l'utilsateur
            ['$match' => ["_id" => new MongoDB\BSON\ObjectID($id)]],
            // Décomposition du tableau librairie qui contient les id d'editions de livre
            ['$unwind' => '$wishlist'],
            [
                '$lookup' => [
                    'from' => 'books',
                    // Comme le id dans la librairie user sont sous forme de string on les convertit en objectId pour les comparer avec ceux de livres
                    'let' => ['wishlistId' => ['$toObjectId' => '$wishlist' ]],
                    'pipeline' => [
                        // pipeline de match sur les livres qui contienne les id d'éditions en décomposant le tableau éditions des livres
                        ['$match' => ['$expr' => ['$in' => ['$$wishlistId', '$editions.id']]]],
                        ['$unwind' => '$editions'],
                        ['$match' => ['$expr' => ['$eq' => ['$$wishlistId', '$editions.id']]]]
                    ],
                    'as' => 'bookInfo'
                ]
            ],
            // Décomposition de bookInfo afin d'en extraire les données
            ['$unwind' => '$bookInfo'],
            [
                '$lookup' => [
                    'from' => "users",
                    'localField' => "_id",
                    'foreignField' => "_id",
                    'as' => "userInfo"
                ]
            ],
            // Décomposition de userInfo afin d'en extraire les données
            ['$unwind' => '$userInfo'],
            [
                '$project' => [
                    'userName' => '$userInfo.name',
                    'format' => '$bookInfo.editions.format',
                    'nom' => '$bookInfo.editions.edition',
                    'title' => '$bookInfo.title',
                    'resume' => '$bookInfo.resume',
                    'author' => '$bookInfo.author',
                    'editeur' => '$bookInfo.editeur'
                ]
            ]
        ]);
        $json = $result->toArray();

        $obj = new stdClass();
        
        foreach($json as $row) {
            $obj->user = $row->userName;
            unset($row->userName, $row->_id);
        }
        $obj->wishlist = $json;
        echo json_encode($obj);
    }
}

?>