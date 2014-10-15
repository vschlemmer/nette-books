<?php
/**
 * Created by PhpStorm.
 * User: vojtech
 * Date: 10/1/14
 * Time: 10:57 AM
 */

namespace App\Model;

use Nette;


class BookManagerNette extends Nette\Object {

    /** @var  @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database){
        $this->database = $database;
    }

    public function getAllBooks(){
        return $this->database->table('book');
    }

    public function createBook($data){
        if($data){
            $this->database->table('book')->insert($data);
        }
    }

    public function deleteBook($id){
        $this->database->table('book')->where(array(
            'id' => $id
        ))->delete();
    }


} 