<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// https://www.lws.fr/hebergement-cpanel.php
#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'api_product')]
    public function index(ProductRepository $product): JsonResponse
    {
        $all = $product->findAll();
        $count = count($all);
        return $this->json( [
            'product counted'=>$count,
            'Product list' => $all,
            
        ]);
    }
    #[Route('/product/new', name: 'api_product_new')]
    public function new(ManagerRegistry $doctrine,Request $request,UserRepository $prov): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $idAuth=$request->request->get('id');
        $user = $prov->find($idAuth);
        $product = new Product();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setPrice($request->request->get('price'));
        $product->setQuantity($request->request->get('quantity'));
        $product->setProvider($user);
   
        $entityManager->persist($product);
        $entityManager->flush();
   
        return $this->json('Created new product successfully with id ' . $product->getId());
    }
    #[Route('/product/show/{id}', name: 'api_product_show')]
    public function show(ProductRepository $product ,int $id): JsonResponse
    {
        $element = $product->find($id);
        
        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id " . $id, 404);
        }
         
   
        return $this->json($element);
    }
    #[Route('/product/edit/{id}', name: 'api_product_edit')]
    public function edit(ProductRepository $product,UserRepository $prov ,int $id,Request $req,ManagerRegistry $doctrine): JsonResponse
    {
        $idAuth=$req->request->get('id');
        $user = $prov->find($idAuth);
        $em = $doctrine->getManager();
        $element = $product->find($id);

        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id" . $id, 404);
        }
     
        $element->setName($req->request->get('name'));
        $element->setDescription($req->request->get('description'));
        $element->setPrice($req->request->get('price'));
        $element->setQuantity($req->request->get('quantity'));
        $element->setProvider($user);
        
        $em->flush();
   
        $data =  [
            'id' => $element->getId(),
            'name' => $element->getName(),
            'description' => $element->getDescription(),
        ];
           
        return $this->json($data);        
    }
    #[Route('/product/delete/{id}', name: 'api_product_delete')]
    public function delete(ProductRepository $product ,int $id,ManagerRegistry $doctrine): JsonResponse
    {
        $element = $product->find($id);
        $em = $doctrine->getManager();

        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id " . $id, 404);
        }
        $em->remove($element);
        $em->flush();
   
        return $this->json("Element supprimé avec succès");
    }
}
