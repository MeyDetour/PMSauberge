<?php

namespace App\Controller;

use App\Entity\Bed;
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

    #[Route('/user/edit/{id}', name: 'edit_user', methods: 'put')]
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

    #[Route('/user/remove/{id}', name: 'remove_user', methods: 'DELETE')]
    public function remove(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->json(["message" => "You are not allowed to do this"], 405, [], ['groups' => 'rooms']);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user, 200, [], ['groups' => ['user']]);
    }

    #[Route('/user/new', name: 'create_user', methods: 'post')]
    public function create(Request $request, SerializerInterface $serializer, UserRepository $userRepository ,EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->json(["message" => "You are not allowed to do this"], 405);
        }
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if (!is_string($user->getEmail())) {
            return $this->json(["message" => "Please enter Email"], 406);
        }
        if ($userRepository->findOneBy(['email'=>$user->getEmail()])){
            return $this->json(["message" => "Email already taken"], 404);

        }

        if (!is_string($user->getLastName())) {
            return $this->json(["message" => "Please enter Last Name"], 406);
        }
        if (!is_string($user->getFirstName())) {
            return $this->json(["message" => "Please enter First Name"], 406);
        }
        if (!is_string($user->getProfession())) {
            return $this->json(["message" => "Please enter Profession"], 406);
        }
        if (!is_string($user->getPhoneNumber())) {
            return $this->json(["message" => "Please enter Phone Number"], 406);
        }
        $user->setCreatedAt(new \DateTimeImmutable());
        //default password = auberjeune
        $user->setPassword('$2y$13$S1.n46J.zCZdGHylsAnnju9QTif0zN7sheYwQ9Q.z533PNaDABnAy');
        $user->setRoles(["ROLE_USER"]);
        $user->setActive(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user, 200, [], ['groups' => ['user']]);
    }
}
