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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $user = $this->getUser();

        $customer->setUser($user);

        $errors = $validator->validate($customer);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }

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
            throw new NotFoundHttpException("L utilisateur ne vous appartiene pas!");

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

    /**
     * @Route("/{id}", name="update_customers", methods={"PATCH"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param Customer $customer
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse|Response
     */
    public function update(Request $request, SerializerInterface $serializer, Customer $customer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $customerUpdate = $entityManager->getRepository(Customer::class)->findOneByUser($user, $customer);
        if (!$customerUpdate) {
            throw new NotFoundHttpException("L utilisateur ne vous appartiene pas!");

        }
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $customerUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($customerUpdate);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Le client a bien été mis à jour!'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", name="delete_customer", methods={"DELETE"})
     * @param Customer $customer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Customer $customer, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $customerToDelete = $entityManager->getRepository(Customer::class)->findOneByUser($user, $customer);
        if (!$customerToDelete) {
            throw new NotFoundHttpException("L'utilisateur ne vous appartiene pas!");
        }
        $entityManager->remove($customerToDelete);
        $entityManager->flush();
        return new Response(null, 204);
    }
}
