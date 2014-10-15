<?php
namespace App\Model;

use Nette;

class CategoryManager extends Nette\Object {

    /** @var  \DibiConnection */
    private $database;

    public function __construct(\DibiConnection $database){
        $this->database = $database;
    }

    public function getAllCategories(){
        return $this->database->query('SELECT * FROM category');
    }

    public function createCategory($data){
        $this->database->query('INSERT INTO category', $data);
    }

    public function deleteCategory($id){
        $this->database->query('DELETE FROM category WHERE id=%i', $id);
    }
} 