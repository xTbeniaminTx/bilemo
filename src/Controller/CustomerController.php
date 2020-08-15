<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $user = $this->getUser();

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
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(Customer $customer, CustomerRepository $customerRepository, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $customerToFind = $customerRepository->findOneByUser($user, $customer->getId());
        if (!$customerToFind) {
            throw new NotFoundHttpException("L utilisateur ne vous appartien pas!");
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
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }

        $user = $this->getUser();

        $phones = $customerRepository->findAllCustomersByUser($user, $page, 10);
        $data = $serializer->serialize($phones, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
