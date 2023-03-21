<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'product_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $products = $em->getRepository(Product::class)->all();
        return $this->json($products);
    }

    #[Route('/api/product', name: 'product_create', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $number = $data["number"] ?? null;

        //Validation
        if (empty(trim($name))) {
            return $this->json(['code' => 400, 'message' => 'e_missingName'], 400);
        }
        //Prevent duplicate product numbers
        if ($number) {
            $old = $em->getRepository(Product::class)->findOneByNumber($number);
            if ($old) {
                return $this->json(['code' => 400, 'message' => 'e_duplicateNumber', 'data' => $old]);
            }
        }

        $product = new Product();
        $product->setName($name);
        $product->setNumber($number);

        $em->persist($product);
        $em->flush();

        return $this->json($product);
    } 
    #[Route('/api/product/{id}', name: 'product_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $number = $data["number"] ?? null;

        $product = $em->getRepository(Product::class)->get($id);
        if (!$product) {
            return $this->json(['code' => 404, 'message' => 'e_notFound']);
        }

        //Validation
        if (empty(trim($name))) {
            return $this->json(['code' => 400, 'message' => 'e_missingName'], 400);
        }
        //Prevent duplicate product numbers
        if ($number) {
            $old = $em->getRepository(Product::class)->findOneByNumber($number);
            if ($old->getId() != $id) {
                return $this->json(['code' => 400, 'message' => 'e_duplicateNumber', 'data' => $old]);
            }
        }

        $product->setName($name);
        $product->setNumber($number);
        $product->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($product);
        $em->flush();

        return $this->json($product);
    } 
    #[Route('/api/product/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $product = $em->getRepository(Product::class)->get($id);
        if (!$product) {
            return $this->json(['code' => 404, 'message' => 'e_notFound']);
        }

        $product->setDeleted(true);

        $em->persist($product);
        $em->flush();

        return $this->json(["message" => "deleted successfully"]);
    }

}
