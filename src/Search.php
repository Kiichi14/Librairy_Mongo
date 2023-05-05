<?php

Class Search {

    private $conn;

    public function __construct(){
        $this->conn = (new Connection)->openConnection();
    }

    public function searchByAuthor($author) {

        $new = str_replace('%20', ' ', $author);

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                ['$match' => ['author' => $new]],
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

    public function searchByTags($category){

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                ['$match' => ['category' => $category]],
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

    public function searchByTitle($title) {

        $new = str_replace('%20', ' ', $title);

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                ['$match' => ['title' => $new]],
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

    public function searchByEditor($editor) {

        $new = str_replace('%20', ' ', $editor);

        $collection = $this->conn->librairy->books;

        $result = $collection->aggregate(
            [
                ['$match' => ['editeur' => $new]],
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
}

?>