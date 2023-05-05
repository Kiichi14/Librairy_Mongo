<?php

Class Books {

    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    // Méthodes Get books

    public function getAllBooks() {

        $collection = $this->conn->librairy->books;

        $result = $collection->find([]);

        echo json_encode(iterator_to_array($result));
    }

    public function getAllBookWithAvg() {

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                [ '$limit' => 50 ],
                ['$lookup' => [
                        'from' => 'comments', 'localField' => '_id', 'foreignField' => 'idBook', 'as' => 'avg_rate'
                    ]
                ],
                ['$project' => [
                    'book' => [
                        [
                            'title' => '$$ROOT.title',
                            'resume' => '$$ROOT.resume',
                            'author' => '$$ROOT.author',
                            'category' => '$$ROOT.category',
                            'editeur' => '$$ROOT.editeur',
                            'editions' => '$$ROOT.editions'
                        ]
                    ], 
                    'avg' => ['$round' => ['$avg' => '$avg_rate.rate']]]
                ]
            ]
        );

        echo json_encode(iterator_to_array($result));
    }

    public function getBookById($id) {

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                ['$match' => ['_id' => new MongoDB\BSON\ObjectID($id)]],
                ['$lookup' => [
                        'from' => 'comments', 'localField' => '_id', 'foreignField' => 'idBook', 'as' => 'avg_rate'
                    ]
                ],
                ['$project' => [
                    'book' => [
                        [
                            'title' => '$$ROOT.title',
                            'resume' => '$$ROOT.resume',
                            'author' => '$$ROOT.author',
                            'category' => '$$ROOT.category',
                            'editeur' => '$$ROOT.editeur',
                            'editions' => '$$ROOT.editions'
                        ]
                    ], 
                    'avg' => ['$round' => ['$avg' => '$avg_rate.rate']]]
                ]
            ]
        );

        echo json_encode(iterator_to_array(($result)));
    }

    // Méthodes Post Books

    public function insertBooks(){

        $collection = $this->conn->librairy->books;

        if(!empty($_POST['title']) && !empty($_POST['resume']) && !empty($_POST['author']) && !empty($_POST['category'])) {

            $result = $collection->insertOne([
                "title" => $_POST['title'],
                "resume" => $_POST['resume'],
                "author" => $_POST['author'],
                "category" => [$_POST['category']],
                "editeur" => $_POST['editeur'],
                "editions" => []
            ]);
    
            echo "Votre Livre a bien été ajouter";
        }
    }

    public function updateTagsBooks($id) {

        $collection = $this->conn->librairy->books;

        if(!empty($_POST['addtags']) && empty($_POST['tags']) && empty($_POST['replaceTags'])) {
            $result = $collection->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id")],
                ['$addToSet' => ['category' => $_POST['addtags']]]
            );
        }

        if(!empty($_POST['replaceTags']) && !empty($_POST['tags']) && empty($_POST['addTags'])) {
            $result = $collection->updateOne(
                ["_id" => new MongoDB\BSON\ObjectID("$id"), "category" => $_POST['tags']],
                ['$set' => ['category.$' => $_POST['replaceTags']]]
            );
        }

        echo "Votre livre a bien été mis a jour";
    }

    // Méthodes DELETE Books

    public function deleteBooks() {

        $collection = $this->conn->librairy->books;

        $book = $_POST['id'];

        $result = $collection->deleteOne(
            ["_id" => new MongoDB\BSON\ObjectID("$book")]
        );

        echo "Votre livre a bien été supprimer";
    }

    public function deleteTags($id) {
        
        $collection = $this->conn->librairy->books;

        $tags = $_POST['tags'];

        $result = $collection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectID("$id")],
            ['$pull' => ['category' => $tags]]
        );

        echo 'Le tags du livre a bien été retirer';

    }

}

?>