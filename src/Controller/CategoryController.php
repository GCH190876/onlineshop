<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Form\CategoryType;
use Exception;

use function PHPUnit\Framework\throwException;

class CategoryController extends AbstractController
{
    /**
     * @Route ("/category", name="category_index")
     */
    public function indexCategory() {
        $categories = $this->getDoctrine()
                       ->getRepository(Category::class)
                       ->findAll();
        return $this->render(
            "category/index.html.twig",
            [
               "categories" => $categories
            ]
        );
    }

    /**
     * @Route ("/category/create", name="category_create")
    */
    public function createnewCategory(Request $request) {
        $category = new Category();        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info","Add successfully !");
            return $this->redirectToRoute("category_index");
        } 

        return $this->render(
            "category/create.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }

     /**
     * @Route ("/category/delete/{id}", name="category_delete")
     */
    public function deleteCategory($id) {
        try{
            $category = $this->getDoctrine()
                      ->getRepository(Category::class)
                      ->find($id);
        if ($category == null) {
            $this->addFlash("Error","Delete failed !");
            return $this->redirectToRoute("category_index");
        }
        $manager = $this->getDoctrine()
                        ->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash("Info","Delete succeed !");
        return $this->redirectToRoute("category_index");
        }catch(Exception $e){
            throwException($e);
            $this->addFlash("Error","Delete failed !");
        }return $this->redirectToRoute("category_index");
    }

     /**
     * @Route ("/category/{id}", name="category_detail")
     */
    public function viewdetailCategory($id) {
            $category = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->find($id);
            return $this->render(
                "category/detail.html.twig",
                [
                   "category" => $category
                ]
            );
    }

    /**
    * @Route ("/category/update/{id}", name="category_update")
    */
    public function updateCategory(Request $request, $id) {
        $category = $this->getDoctrine()
                      ->getRepository(Category::class)
                      ->find($id);      
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info","Update successfully !");
            return $this->redirectToRoute("category_index");
        }  

        return $this->render(
            "category/update.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }
}
