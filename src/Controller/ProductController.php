<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ProductController
 * @Route("/api/phones")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_phone", methods={"GET"})
     * @param Product $phone
     * @param ProductRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(Product $phone, ProductRepository $phoneRepository, SerializerInterface $serializer)
    {
        $phone = $phoneRepository->find($phone->getId());
        $data = $serializer->serialize($phone, 'json', [
            'groups' => ['show']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/{page<\d+>?1}", name="list_phone", methods={"GET"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(Request $request,ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');
        if(is_null($page) || $page < 1) {
            $page = 1;
        }
        $phones = $productRepository->findAllPhones($page, 10);
        $data = $serializer->serialize($phones, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }


}
