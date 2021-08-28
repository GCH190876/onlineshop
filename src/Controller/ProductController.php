<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use function PHPUnit\Framework\throwException;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index")
     */
    public function indexProduct() 
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            'product/index.html.twig',
            [
                'products' => $products
            ]
        );
    }

    /**
     * @Route("/product/detail/{id}", name="product_detail")
     */
    public function detailProduct($id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render(
            'product/detail.html.twig',
            [
                'product' => $product
            ]
        );
    }

    /**
     * @Route("product/create", name="product_create")
     */
    public function createProduct(Request $request) {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get Image from uploaded file
            $image = $product->getImage();

            //create an unique image name
            $fileName = md5(uniqid());
            //get image extension
            $fileExtension = $image->guessExtension();
            //merge image name & image extension => get a complete image name
            $imageName = $fileName . '.' . $fileExtension;

            //move upload file to a predefined location
            try {
                $image->move(
                    $this->getParameter('product_image'), $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }

            //set imageName to database
            $product->setImage($imageName);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash("Info", "Create product succeed !");
            return $this->redirectToRoute("product_index"); 
        }

        return $this->render(
            'product/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("product/update/{id}", name="product_update")
     */
    public function updateProduct(Request $request, $id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form['image']->getData();
            if ($uploadFile != null) {
                //get Image from uploaded file
                $image = $product->getImage();

                //create an unique image name
                $fileName = md5(uniqid());
                //get image extension
                $fileExtension = $image->guessExtension();
                //merge image name & image extension => get a complete image name
                $imageName = $fileName . '.' . $fileExtension;

                //move upload file to a predefined location
                try {
                    $image->move(
                        $this->getParameter('product_image'), $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }

                //set imageName to database
                $product->setImage($imageName);
            } 

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash("Info", "Update product succeed !");
            return $this->redirectToRoute("product_index"); 
        }

        return $this->render(
            'product/update.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function deleteProduct($id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product == null) {
            $this->addFlash("Error", "Delete product failed !");
            return $this->redirectToRoute("product_index");
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();

        $this->addFlash("Info","Delete product succeed !");
        return $this->redirectToRoute("product_index");
    }


}
