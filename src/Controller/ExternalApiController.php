<?php

namespace App\Controller;

use App\Repository\CredentialRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ExternalApiController extends AbstractController
{
    #[Route('/ext-api/login', name: 'external_api_login')]
    public function login(CredentialRepository $credentialRepository, JWTTokenManagerInterface $JWTManager, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $clientID = $data['clientID'] ?? null;
        $secret = $data['secret'] ?? null;

        if (null === $clientID && null === $secret) {
            throw new CustomUserMessageAuthenticationException('Client ID or secret was not provided');

        }

        $cred = $credentialRepository->findOneBy(['client_id' => $clientID, 'secret' => $secret]);

        if (!$cred)
            throw new CustomUserMessageAuthenticationException('Invalid Client ID or secret');


        return $this->json([
            'token' => $JWTManager->create($cred->getUser())
        ]);
    }
    #[Route('/ext-api/products', name: 'external_api_products_list', methods: 'GET')]
    public function listProducts(ProductRepository $productRepository): JsonResponse {
        return $this->json($productRepository->all());
    }    
    #[Route('/ext-api/product/{id}', name: 'external_api_product_get', methods: 'GET')]
    public function getProduct(ProductRepository $productRepository, int $id): JsonResponse {
        $product = $productRepository->get($id);
        if (!$product) {
            return $this->json(['code' => 404, 'message' => 'The requested product was not found']);
        }
        return $this->json($product);
    }

}
