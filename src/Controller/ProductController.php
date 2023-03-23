<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{
    #[Route('/api/products', name: 'product_list', methods: ['GET'])]
    public function list(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $options = [
            "offset" => $request->query->get('offset'),
            "max" => $request->query->get('max'),
            "term" => $request->query->get('term'),
            "sort" => $request->query->get('sort'),
            "order" => $request->query->get('order'),
        ];

        $total = $productRepository->getTotalExceptDeleted($options);
        $products = $productRepository->findAllExceptDeleted($options);
        return $this->responseOb(["products" => $products, "total" => $total]);
    }

    #[Route('/api/product', name: 'product_create', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $number = $data["number"] ?? null;

        //Validation
        if (empty(trim($name))) {
            return $this->error('e_missingName');
        }
        //Prevent duplicate product numbers
        if ($number) {
            $old = $em->getRepository(Product::class)->findOneByNumber($number);
            if ($old) {
                return $this->error('e_duplicateNumber', 400, $old);
            }
        }

        $product = new Product();
        $product->setName($name);
        $product->setNumber($number);

        $em->persist($product);
        $em->flush();

        return $this->responseOb($product);
    } 
    #[Route('/api/product/{id}', name: 'product_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $number = $data["number"] ?? null;

        $product = $em->getRepository(Product::class)->findOneExceptDeleted($id);
        if (!$product) {
            return $this->error('e_notFound', 404);
        }

        //Validation
        if (empty(trim($name))) {
            return $this->error('e_missingName');
        }
        //Prevent duplicate product numbers
        if ($number) {
            $old = $em->getRepository(Product::class)->findOneByNumber($number);
            if ($old && $old->getId() != $id) {
                return $this->error('e_duplicateNumber');
            }
        }

        $product->setName($name);
        $product->setNumber($number);
        $product->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($product);
        $em->flush();

        return $this->responseOb($product);
    } 
    #[Route('/api/product/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->findOneExceptDeleted($id);
        if (!$product) {
            return $this->error('e_notFound', 404);
        }

        $productRepository->softDelete($product, true);

        return $this->responseOb([]);
    }
    #[Route('/api/product/{id}', name: 'product_get', methods: ['GET'])]
    public function show(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->findOneExceptDeleted($id);
        if (!$product) {
            return $this->error('e_notFound', 404);
        }

        return $this->responseOb($product);
    }

}
