<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CustomerController
 * @package App\Controller
 * @Route("/api/customers")
 */
class CustomerController extends AbstractController
{


    /**
     * @Route("/", name="add_customer", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(Request $request,UserRepository $userRepository,SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $user = $userRepository->find(26);

        $customer->setUser($user);

        $entityManager->persist($customer);

        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Le client a bien été ajouté'
        ];
        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/{id}", name="show_customer", methods={"GET"})
     * @param Customer $customer
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(Customer $customer, CustomerRepository $customerRepository,UserRepository $userRepository,SerializerInterface $serializer)
    {
        $user = $userRepository->find(26);

        $customerToFind = $customerRepository->findOneByUser($user, $customer->getId());
        if (!$customerToFind) {
            throw new NotFoundHttpException();
        }
        $data = $serializer->serialize($customerToFind, 'json', [
            'groups' => ['show']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }



    /**
     * @Route("/{page<\d+>?1}", name="list_customers", methods={"GET"})
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(Request $request, CustomerRepository $customerRepository, UserRepository $userRepository, SerializerInterface $serializer)
    {
        dd($this->getUser());
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }

        $user = $userRepository->find(26);

        $phones = $customerRepository->findAllCustomersByUser($user->getId(), $page, 10);
        $data = $serializer->serialize($phones, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
