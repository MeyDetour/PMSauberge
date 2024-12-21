<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class SettingsController extends AbstractController
{
    #[Route('/settings/get', name: 'get_settings', methods: 'GET')]
    public function index(SettingsRepository $repository): Response
    {
        return $this->json($repository->findAll()[0], 200, [], ['groups' => ['settings']]);
    }

    #[Route('/settings/edit', name: 'edit_settings', methods: 'PUT')]
    public function edit(SettingsRepository $repository, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {   if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
        return $this->json(["message" => "You are not allowed to do this"], 405);
    }
        $settingsEdited = $serializer->deserialize($request->getContent(), Settings::class, 'json');
        $settings = $repository->findAll()[0];

        if (!is_bool($settingsEdited->isWebsiteOpen())) {
            return $this->json(["message" => "Is the website Open ?"], 406, []);
        }

        if (!$settings) {
            $settings = new Settings();
            $settings->setWebsiteOpen(false);
            $settings->setOtherSharedRoom('Cinema');
        } else {
            $settings->setOtherSharedRoom($settingsEdited->getOtherSharedRoom());
            $settings->setBelongings($settingsEdited->getBelongings());
            $settings->setWebsiteOpen($settingsEdited->isWebsiteOpen());
        }
        $manager->persist($settings);
        $manager->flush();

        return $this->json(["message"=>"ok"], 200,);
    }
}
