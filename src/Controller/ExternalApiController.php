<?php

namespace App\Controller;

use App\Repository\CredentialRepository;
use App\Repository\PackRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ExternalApiController extends ApiController
{
    #[Route('/ext-api/login', name: 'external_api_login')]
    public function login(CredentialRepository $credentialRepository, JWTTokenManagerInterface $JWTManager, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $clientID = $data['clientID'] ?? null;
        $secret = $data['secret'] ?? null;

        if (null === $clientID && null === $secret) {
            $this->error('Client ID or secret was not provided', 403);
        }

        $cred = $credentialRepository->findOneBy(['client_id' => $clientID, 'secret' => $secret]);

        if (!$cred)
            $this->error('Invalid Client ID or secret', 403);


        return $this->json([
            'token' => $JWTManager->create($cred->getUser())
        ]);
    }
    #[Route('/ext-api/products', name: 'external_api_products_list', methods: 'GET')]
    public function listProducts(ProductRepository $productRepository): JsonResponse {
        return $this->responseOb($productRepository->findAllExceptDeleted());
    }    
    #[Route('/ext-api/product/{id}', name: 'external_api_product_get', methods: 'GET')]
    public function getProduct(ProductRepository $productRepository, int $id): JsonResponse {
        $product = $productRepository->findOneExceptDeleted($id);
        if (!$product) {
            return $this->error('The requested product was not found', 404);
        }
        return $this->json($product);
    }
    #[Route('/ext-api/packs', name: 'external_api_packs_list', methods: 'GET')]
    public function listPacks(PackRepository $packRepository): JsonResponse {
        return $this->responseOb($packRepository->findAllExceptDeleted());
    }    
    #[Route('/ext-api/pack/{id}', name: 'external_api_pack_get', methods: 'GET')]
    public function getPack(PackRepository $packRepository, int $id): JsonResponse {
        $pack = $packRepository->findOneExceptDeleted($id);
        if (!$pack) {
            return $this->error('The requested pack was not found', 404);
        }
        return $this->json($pack);
    }

}
