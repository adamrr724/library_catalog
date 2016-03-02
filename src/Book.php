<?php
	class Book
		{
		private $title;
		private $id;

		function __construct($title, $id = null)
		{
			$this->title = $title;
			$this->id = $id;
		}

		function getTitle()
		{
			return $this->title;
		}

		function setTitle($new_title)
		{
			$this->title = $new_title;
		}

		function getId()
		{
			return $this->id;
		}

		function save()
		{
			$GLOBALS['DB']->exec("INSERT INTO books (title) VALUES ('{$this->getTitle()}');");
			$this->id = $GLOBALS['DB']->lastInsertId();
		}

		static function getAll()
		{
			$returned_books = $GLOBALS['DB']->query("SELECT * FROM books");
			$books = array();
			foreach($returned_books as $book){
				 $title = $book['title'];
				 $id = $book['id'];
				 $new_book = new Book($title, $id);
				 array_push($books, $new_book);
			}
			return $books;
		}

		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM books");
		}

		// FIND A SPECIFIC BOOK BY ID
		static function find($id)
		{
			$all_books = Book::getAll();
			$found_book = null;
			foreach($all_books as $book) {
				$book_id = $book->getId();
				if ($book_id == $id) {
					$found_book = $book;
				}
			}
			return $found_book;
		}

		function update($new_title)
		{
		   $GLOBALS['DB']->exec("UPDATE books SET title = '{$new_title}' WHERE id={$this->getId()};");
		   $this->setTitle($new_title);
		}

		function delete()
		{
		   $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
		   $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE book_id = {$this->getId()};");

		}

		function addAuthor($author)
		{
			$GLOBALS['DB']->exec("INSERT INTO books_authors (book_id, author_id) VALUES ({$this->getId()}, {$author->getId()}) ;");
		}

		function getAuthors()
		{
			$query = $GLOBALS['DB']->query("SELECT authors.* FROM books JOIN books_authors ON (books.id = books_authors.book_id) JOIN authors ON (books_authors.author_id = authors.id) WHERE books.id = {$this->getId()}; ");
			$returned_authors = $query->fetchAll(PDO::FETCH_ASSOC);
			$authors = array();
			foreach($returned_authors as $author){
				$first_name = $author['first_name'];
				$last_name = $author['last_name'];
				$id = $author['id'];
				$new_author = new Author($first_name, $last_name, $id);
				array_push( $authors, $new_author);
			}
			return $authors;
		}

		static function search($search_term)
		{
			$query = $GLOBALS['DB']->query("SELECT * FROM books WHERE title LIKE '%{$search_term}%'");
			$all_books = $query->fetchAll(PDO::FETCH_ASSOC);

			$found_books = array();
			foreach ($all_books as $book) {
				$title = $book['title'];
				$id = $book['id'];
				$new_book = new Book($title, $id);
				array_push($found_books, $new_book);
			}
			return $found_books;
		}
	}
 ?>
