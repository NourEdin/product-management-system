<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Entity\Product;
use App\Repository\PackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PackController extends ApiController
{
    #[Route('/api/packs', name: 'pack_list', methods: ['GET'])]
    public function list(PackRepository $packRepository, Request $request): JsonResponse
    {
        $options = [
            "offset" => $request->query->get('offset'),
            "max" => $request->query->get('max'),
            "term" => $request->query->get('term'),
            "sort" => $request->query->get('sort'),
            "order" => $request->query->get('order'),
        ];

        $total = $packRepository->getTotalExceptDeleted($options);
        $packs = $packRepository->findAllExceptDeleted($options);
        return $this->responseOb(["packs" => $packs, "total" => $total]);
    }

    #[Route('/api/pack', name: 'pack_create', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $productIds = $data["productIds"] ?? [];

        //Validation
        if (empty(trim($name))) {
            return $this->error('e_missingName');
        }
        if (count($productIds) < 2) {
            return $this->error('e_addMoreProducts');
        }
        
        $pack = new Pack();

        //Validate product id's existence, and if they do, add them to the pack
        $missingProducts = $this->validatePackProducts($em, $pack, $productIds);

        if (count($missingProducts)) {
            return $this->error('e_productsNotFound', 400, ['products' => $missingProducts]);
        }
        
        $pack->setName($name);

        $em->persist($pack);
        $em->flush();

        return $this->responseOb($pack);
    } 
    #[Route('/api/pack/{id}', name: 'pack_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $data = $request->toArray();
        $name = $data["name"] ?? null;
        $productIds = $data["productIds"] ?? [];

        $pack = $em->getRepository(Pack::class)->findOneExceptDeleted($id);
        if (!$pack) {
            return $this->error('e_notFound', 404);
        }

        //Validation
        if (empty(trim($name))) {
            return $this->error('e_missingName');
        }
        if (count($productIds) < 2) {
            return $this->error('e_addMoreProducts');
        }

        //Validate product id's existence, and if they do, add them to the pack
        $missingProducts = $this->validatePackProducts($em, $pack, $productIds);

        if (count($missingProducts)) {
            return $this->error('e_productsNotFound', 400, ['products' => $missingProducts]);
        }

        //Remove extra products from the pack
        foreach ($pack->getProducts() as $product) {
            if (!in_array($product->getId(), $productIds))
                $pack->removeProduct($product);
        }
        
        $pack->setName($name);
        $pack->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($pack);
        $em->flush();

        return $this->responseOb($pack);
    } 
    /**
     * Loops over the provide product ids and adds them to the pack if they exist
     * @return array missing ids
     */
    private function validatePackProducts($em, $pack, $productIds) {
        $missingProducts = [];

        foreach ($productIds as $productID) {
            $product = $em->getRepository(Product::class)->findOneExceptDeleted($productID);
            if (!$product) {
                $missingProducts[] = $productID;
            } else {
                $pack->addProduct($product);
            }
        }
        return $missingProducts;
    }
    #[Route('/api/pack/{id}', name: 'pack_delete', methods: ['DELETE'])]
    public function delete(PackRepository $packRepository, int $id): JsonResponse
    {
        $pack = $packRepository->findOneExceptDeleted($id);
        if (!$pack) {
            return $this->error('e_notFound', 404);
        }

        $packRepository->softDelete($pack, true);

        return $this->responseOb([]);
    }
    #[Route('/api/pack/{id}', name: 'pack_get', methods: ['GET'])]
    public function show(PackRepository $packRepository, int $id): JsonResponse
    {
        $pack = $packRepository->findOneExceptDeleted($id);
        if (!$pack) {
            return $this->error('e_notFound', 404);
        }

        return $this->responseOb($pack);
    }

}
