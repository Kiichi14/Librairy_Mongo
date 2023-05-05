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
            ['$match' => ["_id" => new MongoDB\BSON\ObjectID($id)]],
            ['$unwind' => '$wishlist'],
            [
                '$lookup' => [
                    'from' => 'books',
                    'let' => ['wishlistId' => ['$toObjectId' => '$wishlist' ]],
                    'pipeline' => [
                        ['$match' => ['$expr' => ['$in' => ['$$wishlistId', '$editions.id']]]],
                        ['$unwind' => '$editions'],
                        ['$match' => ['$expr' => ['$eq' => ['$$wishlistId', '$editions.id']]]]
                    ],
                    'as' => 'bookInfo'
                ]
            ],
            ['$unwind' => '$bookInfo'],
            [
                '$lookup' => [
                    'from' => "users",
                    'localField' => "_id",
                    'foreignField' => "_id",
                    'as' => "userInfo"
                ]
            ],
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