<?php

Class Librairy {

    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    public function getBooksLibrairy($id) {

        $users = $this->conn->librairy->users;

        $result = $users->aggregate([
            ['$match' => ["_id" => new MongoDB\BSON\ObjectID($id)]],
            ['$unwind' => '$librairy'],
            [
                '$lookup' => [
                    'from' => 'books',
                    'let' => ['librairyId' => ['$toObjectId' => '$librairy.id' ]],
                    'pipeline' => [
                        ['$match' => ['$expr' => ['$in' => ['$$librairyId', '$editions.id']]]],
                        ['$unwind' => '$editions'],
                        ['$match' => ['$expr' => ['$eq' => ['$$librairyId', '$editions.id']]]]
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
        $obj->librairy = $json;
        echo json_encode($obj);
    }

    public function addBookLibrairy() {

        $users = $this->conn->librairy->users;

        $users->updateOne(
            ["_id" => new MongoDB\BSON\ObjectID($_POST['idUser'])],
            ['$push' => ["librairy" => ["id" => $_POST['idEdition'], "status" => "not read", "finish_time" => NULL]]]
        );

        echo 'Le livre a bien été ajouté a votre librairie';
    }

    public function bookToReading(){

        $users = $this->conn->librairy->users;

        $users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($_POST['idUser']), 'librairy.id' => $_POST['idEdition']],
            ['$set' => ['librairy.$.status' => 'reading']]
        );

        echo 'Bonne Lecture !!!';
    }

    public function bookToFinish() {

        $users = $this->conn->librairy->users;

        $users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($_POST['idUser']), 'librairy.id' => $_POST['idEdition']],
            ['$set' => ['librairy.$.status' => 'finish', 'librairy.$.finish_time' => new \MongoDB\BSON\UTCDateTime()]]
        );

        echo "Sa vous a plus n'hesiter pas a laisser un commentaire sur ce live ;)";
    }

    public function deleteBooksFromLibrairy($id) {

        $users = $this->conn->librairy->users;

        $users->updateOne(
            ["_id" => new MongoDB\BSON\ObjectID($id)],
            ['$pull' => ["librairy" => ["id" => $_POST['id']]]]
        );

        echo 'le livre a bien été supprimer de votre librairie';
    }

    public function countBooks($id) {

        $users = $this->conn->librairy->users;

        $result = $users->aggregate([
            ['$match' => ["_id" => new MongoDB\BSON\ObjectID($id)]],
            ['$project' => [
                "numberOfBooks" => ['$size' => '$librairy'],
                "booksReading" => ['$size' => [
                    '$filter' => [
                        'input' => '$librairy',
                        'cond' => [
                            '$eq' => ['$$this.status', "reading"]
                        ]
                    ]    
                ]],
                "bookFinishMonth" => ['$size' => [
                    '$filter' => [
                        'input' => '$librairy',
                        'cond' => [
                            //'$eq' => ['$$this.status', "finish"]
                            '$and' => [
                                [
                                    '$eq' => ['$$this.status', "reading"]
                                ],
                                [
                                    '$eq' => ['$month' => '$$this.finish_time', '$month' => new \MongoDB\BSON\UTCDateTime()]
                                ]
                            ]
                        ]
                    ]
                ]]
            ]]
        ]);

        echo json_encode(iterator_to_array($result));
    }
}

?>