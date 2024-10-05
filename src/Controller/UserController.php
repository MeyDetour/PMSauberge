<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'users', methods: 'get')]
    public function get(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['id' => 'DESC']);
        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                unset($users[$i]);
                array_unshift($users, $user);
            }
        }
        return $this->json($users, 200, [], ['groups' => ['user']]);
    }

    #[Route('/user/edit/{id}', name: 'app_user', methods: 'put')]
    public function edit(Request $request, SerializerInterface $serializer, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->json(["message" => "You are not allowed to do this"], 405, [], ['groups' => 'rooms']);
        }

        $userEdited = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPhoneNumber($userEdited->getPhoneNumber());
        $user->setProfession($userEdited->getProfession());
        $user->setWebsite($userEdited->getWebsite());
        $user->setLastName($userEdited->getLastName());
        $user->setFirstName($userEdited->getFirstName());
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user, 200, [], ['groups' => ['user']]);
    }
}
