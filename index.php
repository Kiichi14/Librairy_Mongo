<?php

include 'autoloader.php';
require 'vendor/autoload.php';

// Déclaration des instances de class disponible

$router = new AltoRouter();
$books = new Books();
$user = new User();
$edition = new Edition();
$search = new Search();
$librairy = new Librairy();
$wishlist = new Wishlist();
$comments = new Comments();

//------------------------------------Route GET------------------------------

if($_SERVER['REQUEST_METHOD'] === 'GET') {
/*
	Books
*/
	// Route Get de tous les livres
	$router->map( 'GET', '/index', function() use($books) {
		$books->getAllBooks();
	});

	// Route GET de un livre par son id
	$router->map( 'GET', '/book/[a:id]', function($id) use($books) {
		$books->getBookById($id);
	});
	// Route GET des livres avec leur moyenne
	$router->map('GET', '/index/rate', function() use($books) {
		$books->getAllBookWithAvg();
	});
/*
	Users
*/
	// Route GET de tous les utilisateurs
	$router->map( 'GET', '/user', function() use($user) {
		$user->getAllUsers();
	});

	// Route GET user par son id
	$router->map( 'GET', '/user/[a:id]', function($id) use($user) {
		$user->getUserById($id);
	});
}
/*
	Search
*/
	// Route GET books par l'auteur
	$router->map('GET', '/book/author/[*:author]', function($author) use($search) {
		$search->searchByAuthor($author);
	});

	// Route GET des livres par catégories
	$router->map('GET', '/book/category/[*:category]', function($category) use($search) {
		$search->searchByTags($category);
	});

	// Route GET des livres par titre
	$router->map('GET', '/book/title/[*:title]', function($title) use($search) {
		$search->searchByTitle($title);
	});

	// Route GET des livres par editeur
	$router->map('GET', '/book/editor/[*:editor]', function($editor) use($search) {
		$search->searchByEditor($editor);
	});
/*
	Librairie
*/
	// Route GET de tous les livres présent dans la librairie d'un utilisateur par l'édition
	$router->map('GET', '/librairy/user/[a:id]', function($id) use($librairy) {
		$librairy->getBooksLibrairy($id);
	});

	// Route GET du nombre de livre dans la bibliothéque d'un utilisateur
	$router->map('GET', '/librairy/count/[a:id]', function($id) use($librairy) {
		$librairy->countBooks($id);
	});
/*
	Wishlist
*/
	// Route GET de tous les livres présent dans la wishlist d'un utilisateur par l'édition
	$router->map('GET', '/wishlist/user/[a:id]', function($id) use($wishlist) {
		$wishlist->getWishlist($id);
	});
/*
	Comments
*/
	// Route GET des comentaire d'un seul livre
	$router->map('GET', '/comments/book/[a:id]', function($id) use($comments) {
		$comments->getBookComments($id);
	});

	// Route GET de la moyenne des notes pour un seul livre
	$router->map('GET', '/comments/average/[a:id]', function($id) use($comments) {
		$comments->getAvgRate($id);
	});
//------------------------------------Route POST------------------------------

if($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Route insertion de livre dans la base
	$router->map( 'POST', '/insert', function() use($books) {
		$books->insertBooks();
	});
/*
	User
*/
	$router->map( 'POST', '/user/add', function() use($user) {
		$user->insertUser();
	});
/*
	Edition
*/
	$router->map( 'POST', '/add/edition/[a:id]', function($id) use($edition) {
		$edition->addEdition($id);
	});
/*
	Comments
*/	
	$router->map('POST', '/comment/[a:book]/[a:user]', function($book, $user) use($comments) {
		$comments->addComments($book, $user);
	});
//------------------------------------Route UPDATE------------------------------

/*
	Books
*/
	// Route Update de livre
	$router->map( 'POST', '/update/[a:id]', function($id) use($books){
		$books->updateTagsBooks($id);
	});

/*
	User
*/
	// Route UPDATE d'un utilisateur
	$router->map('POST', '/update/user/[a:id]', function($id) use($user) {
		$user->updateUser($id);
	});
/*
	Librairie
*/
	// Route UPDATE de la librairie d'un utilisateur (ajout de livre)
	$router->map('POST', '/add/librairy', function() use($librairy) {
		$librairy->addBookLibrairy();
	});

	// Route Update statut du livre vers reading
	$router->map('POST', '/update/reading/status/reading', function() use($librairy) {
		$librairy->bookToReading();
	});

	// Route UPDATE statut du livre vers finish
	$router->map('POST', '/update/reading/status/finish', function() use($librairy) {
		$librairy->bookToFinish();
	});
/*
	Wishlist
*/
	// Route UPDATE de la wishlist d'un utilisateur (ajout de livre)
	$router->map('POST', '/add/wishlist', function() use($wishlist) {
		$wishlist->addToWishlist();
	});
/*
	Comment
*/
	// Route UPDATE de la note d'un livre par un utilisateur
	$router->map('POST', '/update/rate/[a:idBook]/[a:idUser]', function($idBook, $idUser) use($comments) {
		$comments->updateRate($idBook, $idUser);
	});
//------------------------------------Route DELETE------------------------------
/*
	Books
*/
	// Route DELETE d'un livre en général
	$router->map('POST', '/delete/book', function() use($books){
		$books->deleteBooks();
	});

	// Route DELETE de tags d'un livre
	$router->map( 'POST', '/delete/tags/[a:id]', function($id) use($books) {
		$books->deleteTags($id);
	});
}
/*
	Librairie
*/
	// Route DELETE de livre dans la librairie de l'utilisateur
	$router->map('POST', '/delete/librairy/[a:id]', function($id) use($librairy) {
		$librairy->deleteBooksFromLibrairy($id);
	});
//------------------------------------Match Router------------------------------------

$match = $router->match();

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

?>