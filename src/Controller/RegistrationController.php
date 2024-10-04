<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register',methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserRepository $userRepository): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $userExisted = $userRepository->findOneBy(['email' => $user->getEmail()]);

        if ($userExisted) {
            return $this->json(["message" => "Duplicated emal"], 409);
        }
        $plainPassword = $user->getPassword();
        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

        $user->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([$user], 409, [], ['groups' => ['user']]);

    }
}
