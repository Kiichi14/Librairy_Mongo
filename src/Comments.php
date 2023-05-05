<?php

Class Comments {

    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    // Méthodes INSERT commentaire

    public function addComments($idBook, $idUser) {

        $comments = $this->conn->librairy->comments;

        $result = $comments->insertOne([
            'idBook' => new MongoDB\BSON\ObjectID($idBook),
            'idUser' => new MongoDB\BSON\ObjectID($idUser),
            'comment' => $_POST['comment'],
            'rate' => (int)$_POST['rate']
        ]);

        echo 'Votre commentaire a bien été publié';

    }

    // Méthodes GET commentaire

    public function getBookComments($id) {

        $comments = $this->conn->librairy->comments;

        $result = $comments->aggregate([
            ['$limit' => 50],
            ['$match' => 
                [
                'idBook' => new MongoDB\BSON\ObjectID($id)
                ]
            ],
            ['$lookup' =>
                [
                    'from' => 'books',
                    'localField' => 'idBook',
                    'foreignField' => '_id', 
                    'as' => 'book'
                ]
            ],
            ['$lookup' => 
                [
                    'from' => 'users',
                    'localField' => 'idUser',
                    'foreignField' => '_id',
                    'as' => 'user'
                ]
            ],
            ['$project' => 
                [
                    'book._id' => 0,
                    'book.editions' => 0,
                    'user._id' => 0,
                    'user.password' => 0,
                    'user.librairy' => 0, 
                    'user.wishlist' => 0,
                    '_id' => 0,
                    'idBook' => 0,
                    'idUser' => 0,
                ]
            ]
        ]);

        $json = $result->toArray();

        $obj = new stdClass();

        foreach($json as $row) {
            $obj->book = $row->book;
            unset($row->book);
        }

        $obj->comment = $json;

        echo json_encode($obj);
    }

    public function getAvgRate($id) {

        $comments = $this->conn->librairy->comments;

        $result = $comments->aggregate(
            [['$group' => [
                '_id' => new MongoDB\BSON\ObjectID($id),
                'avg_rate' => [
                    '$avg' => '$rate'
                ]
            ]]]
        );

        echo json_encode(iterator_to_array($result));
    }

    public function updateRate($idBook, $idUser){

        $comments = $this->conn->librairy->comments;

        $result = $comments->updateOne(
             ['idBook' => new MongoDB\BSON\ObjectID($idBook), 'idUser' => new MongoDB\BSON\ObjectID($idUser)],
             ['$set' => ['rate' => (int)$_POST['rate']]]
        );

        echo 'Votre note a bien été mis a jour';
    }

}

?>