<?php

namespace App\Controller\Admin;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    // public function createEntity(string $entityFqcn)
    // {
    //     $product = new Product();
    //     $product->createdBy($this->getUser());

    //     return $product;
    // }

  
    
    public function configureFields(string $pageName): iterable
    {
       
        return [
            IdField::new('id'),
            TextField::new('Name'),
            TextField::new('Description'),
            TextField::new('Price'),
            ImageField::new('image')->setBasePath('/assets/uploads/')
                                    ->setUploadDir('public/assets/uploads/')
                                    ->setUploadedFileNamePattern('[randomhash].[extension]')
                                    ->setRequired(false),
        ];
     }
    
}
