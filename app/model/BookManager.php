<?php
/**
 * Created by PhpStorm.
 * User: vojtech
 * Date: 10/6/14
 * Time: 1:48 PM
 */

namespace App\Model;

use Nette,
    Nette\Caching\Storages\FileStorage,
    Nette\Caching\Cache;


class BookManager extends Nette\Object {

    /** @var  \DibiConnection */
    private $database;

    /** @var  Cache */
    private $cache;

    public function __construct(\DibiConnection $database, Cache $cache){
        $this->database = $database;
        $this->cache = $cache;
    }

    public function getAllBooks($offset, $limit){
        $booksCached = $this->cache->load('allBooks');
        if($booksCached === null){
            $query = "SELECT book.id, book.name, book.author, book.isdn, category.description FROM book
            INNER JOIN category ON book.category_id = category.id ORDER BY book.id LIMIT $limit OFFSET $offset";
            $books = $this->database->query($query)->fetchAll();
            $this->cache->save('allBooks', $books, array(
                Cache::TAGS => array("tg1"),
            ));
            $booksCached = $books;
        }
        return $booksCached;
    }

    public function getNumberOfBooks(){
        $result = $this->database->query('SELECT id FROM book');
        return count($result);
    }

    public function cleanCache(){
//        $this->cache->clean(array(
//            Cache::TAGS => array("tg1"),
//        ));
        $this->cache->remove('allBooks');
    }


    public function createBook($data){
        $this->database->query('INSERT INTO book', $data);
        $this->cleanCache();
    }

    public function updateBook($data, $id){
        $this->database->query('UPDATE book SET', $data, 'WHERE id=%i', $id);
        $this->cleanCache();
    }

    public function deleteBook($id){
        $this->database->query('DELETE FROM book WHERE id=%i', $id);
        $this->cleanCache();
    }

    public function getBookById($id){
        return $this->database->query('SELECT * FROM book WHERE id=%i', $id)->fetch();
    }

    public function deleteAllBooks(){
        $this->database->query('DELETE FROM book');
        $this->cleanCache();
    }

    public function findMatch($books){
        $matches = array();
        foreach($books as $book){
            preg_match_all("/Fifty/", $book->name, $matchesTmp);
            $matches = array_merge($matches, $matchesTmp);
        }

        return $matches;
    }
}