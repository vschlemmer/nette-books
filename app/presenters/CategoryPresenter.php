<?php
/**
 * Created by PhpStorm.
 * User: vojtech
 * Date: 10/5/14
 * Time: 3:07 PM
 */

namespace App\Presenters;

use App\Model\CategoryManager,
    Nette\Application\UI\Form;

class CategoryPresenter extends BasePresenter {

    /** @var  CategoryManager */
    private $categoryManager;

    public function __construct(CategoryManager $categoryManager){
        $this->categoryManager = $categoryManager;
    }

    public function renderShow(){
        $this->template->categories = $this->categoryManager->getAllCategories();
    }

    public function renderCreate(){

    }

    public function createComponentCreateCategoryForm(){
        $form = new Form();
        $form->addText('description', 'Description')->setRequired();
        $form->addSubmit('createCategory', 'CreateCategory');
        $form->onSuccess[] = $this->createCategoryFormSubmitted;
        return $form;
    }

    public function createCategoryFormSubmitted(Form $form){
        $data = $form->getValues();
        $this->categoryManager->createCategory($data);
        $this->flashMessage('Category successfully created');
        $this->redirect('Category:show');
    }

    public function handleDelete($id){
        $this->categoryManager->deleteCategory($id);
        $this->flashMessage('Category successfully deleted');
        $this->redirect('Category:show');
    }
} 