<?php
/**
 * Created by PhpStorm.
 * User: vojtech
 * Date: 10/1/14
 * Time: 10:59 AM
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Model\BookManager,
    App\Model\CategoryManager;

class BookPresenter extends BasePresenter {

    /** @var BookManager */
    private $bookManager;

    /** @var  CategoryManager */
    private $categoryManager;

    private $previousBookPage = 1;

    public function __construct(BookManager $bookManager, CategoryManager $categoryManager){
        $this->bookManager = $bookManager;
        $this->categoryManager = $categoryManager;
    }

    public function renderShow($page = 1){
        if($page != $this->previousBookPage){
            $this->bookManager->cleanCache();
        }
        $this->previousBookPage = $page;
        $pageLimit = 20;
        $numberOfBooks = $this->bookManager->getNumberOfBooks();
        $paginator = new Nette\Utils\Paginator();

        $paginator->setItemCount($numberOfBooks);
        $paginator->setItemsPerPage($pageLimit);
        $paginator->setPage($page);
        if ($page < 1 || $page > $paginator->getLastPage()) {
            $page = 1;
        }
        $this->template->paginator = $paginator;
        $books = $this->bookManager->getAllBooks($paginator->getOffset(), $pageLimit);
        $this->template->books = $books;
        $this->template->numberOfBooks = $numberOfBooks;
    }

    public function renderCreate(){

    }

    public function actionGenerate(){
        $categoryId = null;
        foreach ($this->categoryManager->getAllCategories() as $category){
            $categoryId = $category->id;
            break;
        }
        $data = array(
            'name' => 'generatedName',
            'author' => 'generatedAuthor',
            'isdn' => 'generatedISDN',
            'category_id' => $categoryId
        );
        for($ii = 0; $ii < 1000; $ii++){
            $this->bookManager->createBook($data);
        }
        $this->redirect('Book:show');
    }

    public function actionDeleteAll(){
        $this->bookManager->deleteAllBooks();
        $this->bookManager->cleanCache();
        $this->redirect('Book:show');
    }

    public function actionCleanCache(){
        $this->bookManager->cleanCache();
        $this->redirect('Book:show');
    }

    public function renderUpdate($id){
        $book = $this->bookManager->getBookById($id)->toArray();
        $this['createBookForm']->setDefaults($book);
        $createButton = $this['createBookForm']->getComponent('createBook');
        $this['createBookForm']->removeComponent($createButton);
        $this['createBookForm']->addSubmit('createBook', 'Update Book');
    }

    public function createComponentCreateBookForm(){
        $categories = $this->categoryManager->getAllCategories()->fetchPairs('id', 'description');
        $form = new Form();
        $form->addText('name', 'Book name')->setRequired();
        $form->addText('author', 'Book author');
        $form->addText('isdn', 'ISDN');
        $form->addSelect('category_id', 'Category: ', $categories);
        $form->addSubmit('createBook', 'Create Book');
        $form->onSuccess[] = $this->myCreateBookFormSubmitted;
        return $form;
    }

    public function myCreateBookFormSubmitted($form){
        if($this->getAction() == "create"){
            $this->bookManager->createBook($form->getValues());
            $this->presenter->flashMessage('Book created successfully');
        }
        else{
            $this->bookManager->updateBook($form->getValues(), $this->getParameter('id'));
            $this->presenter->flashMessage('Book updated successfully');
        }
        $this->redirect('Book:show');
    }

    public function handleDelete($id){
        $this->bookManager->deleteBook($id);
        $this->flashMessage("Book successfully deleted");
        $this->redirect('Book:show');
    }
} 