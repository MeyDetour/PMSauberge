<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $afaire = [
            "Send back data for get rooms"
        ];
        $routes = [
            'Room' => [
                [
                    'name' => 'Get Rooms',
                    'route' => '/api/rooms',
                    'methode' => 'GET',
                    'body' => [
                        "id" => 36,
                        "name" => "Room 0",
                        "hasPrivateShowerroom" => false,
                        "hasLocker" => false,
                        "isPrivate" => false,
                        "beds" => [
                            [
                                "id" => 217,
                                "isDunkBed" => false,
                                "isSittingApart" => false,
                                "state" => "inspected",
                                "number" => 0,
                                "cleanedBy" => null,
                                "inspectedBy" => null
                            ],
                            [
                                "id" => 218,
                                "isDunkBed" => false,
                                "isSittingApart" => false,
                                "state" => "inspected",
                                "number" => 1,
                                "cleanedBy" => null,
                                "inspectedBy" => null
                            ],
                            [
                                "id" => 219,
                                "isDunkBed" => false,
                                "isSittingApart" => false,
                                "state" => "inspected",
                                "number" => 2,
                                "cleanedBy" => null,
                                "inspectedBy" => null
                            ]
                        ]
                    ],
                    'sendBack' => "",
                    'token' => true
                ],
                [
                    'name' => 'New Room',
                    'route' => '/api/room/new',
                    'methode' => 'POST',
                    'body' => [
                        "name" => "Name of room",
                        "hasLocker" => false,
                        "private" => false,
                        "hasPrivateShowerroom" => false
                    ],
                    'sendBack' => "The room created",
                    'token' => true
                ], [
                    'name' => 'Edit Room',
                    'route' => '/api/room/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "name" => "Changed name",
                        "hasLocker" => false,
                        "private" => false,
                        "hasPrivateShowerroom" => false
                    ],
                    'sendBack' => "The room modified",
                    'token' => true
                ], [
                    'name' => 'Remove room and its beds',
                    'route' => '/api/room/remove/{id}',
                    'methode' => 'DELETE',
                    'body' => null,
                    'sendBack' => "ok if it's done",
                    'token' => true
                ]
            ],
            "Bed" => [
                [
                    'name' => 'Get one bed',
                    'route' => '/api/bed/get/{id}',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => ["id" => 13,
                        "isDunkBed" => false,
                        "isSittingApart" => true,
                        "state" => "cleaned",
                        "room" => [
                            "id" => 6,
                            "name" => "room1",
                            "hasPrivateShowerroom" => false,
                            "hasLocker" => false,
                            "isPrivate" => false
                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'New bed',
                    'route' => '/api/bed/new',
                    'methode' => 'POST',
                    'body' => [
                        "dunkBed" => false,
                        "sittingApart" => false,
                        "state" => "blocked, cleaned, inspected, notcleaned ",
                        "room" => "the ID of room (int)"
                    ],
                    'sendBack' => "The bed created",
                    'token' => true
                ], [
                    'name' => 'Edit bed',
                    'route' => '/api/bed/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "dunkBed" => false,
                        "sittingApart" => false,
                        "state" => "  blocked, cleaned, inspected, notcleaned ",
                        "room" => "the ID of room (int)"
                    ],
                    'sendBack' => ["id" => 13,
                        "isDunkBed" => false,
                        "isSittingApart" => true,
                        "state" => "cleaned",
                        "room" => [
                            "id" => 6,
                            "name" => "the name of room",
                            "hasPrivateShowerroom" => false,
                            "hasLocker" => false,
                            "isPrivate" => false
                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'Remove bed',
                    'route' => '/api/remove/{id}',
                    'methode' => 'DELETE',
                    'body' => null,
                    'sendBack' => "ok if it's deleted",
                    'token' => true
                ], [
                    'name' => 'Turn bed status on inspected',
                    'route' => '/api/bed/inspect/{id}',
                    'methode' => 'PATCH',
                    'body' => null,
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Turn bed status on cleaned',
                    'route' => '/api/bed/clean/{id}',
                    'methode' => 'PATCH',
                    'body' => null,
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Edit status of bed',
                    'route' => '/api/edit/status/{id}',
                    'methode' => 'PATCH',
                    'body' => [
                        "status" => "  blocked, cleaned, inspected, notcleaned "
                    ],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
            ]
        ];
        return $this->render("home/index.html.twig", [
            'routes' => $routes
        ]);

    }
}
