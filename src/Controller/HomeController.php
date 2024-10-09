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
            'User' => [
                [
                    'name' => 'Sign in',
                    'route' => '/api/login_check',
                    'methode' => 'GET',
                    'body' => [
                        "username" => "string",
                        "password" => "string"
                    ],
                    'sendBack' => ["token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjgwNzAzMTEsImV4cCI6MTcyODQzMDMxMSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6Im1leSJ9.DqW5kXcBBIdovh5Jv7pz7p9R61ydZtW5V-puOYFpc0Idcmd_cgBQh8Hwh8MDOHvH4avrwV2_QmHRrYYpmvWmRhL99_1ukcU4ltuI_h8yUtc-tQ-dd-vf94CsreOtJ_FIs27CnHODx20JzQkKG9XNmjW8GhV02aIjFjXVm_3HlgoSoY_mjxs3FUHd1_K0-73dbXnKQc44onNATm-BeC12oWCuUm8UPNXoL71LXw6CD91duK6eSs53E165jHXFJTLHW3lS5UPKksPSHHQJq_YEBXusnA1LEP-L3NydRLiWTegjPpd7Fh8DPODNXlOTpQ7PSb-YAHJbpU6rCxqgFw-A4g"],
                    'token' => false
                ], [
                    'name' => 'Sign up',
                    'route' => '/register',
                    'methode' => 'POST',
                    'body' => [
                        "email" => "string",
                        "password" => "string",
                        "firstName" => "string",
                        "lastName" => "string",
                        "profession" => "string",
                        "phoneNumber" => "string"
                    ],
                    'sendBack' => [
                        "id" => "int",
                        "email" => "string",
                        "roles" => [
                            "ROLE_USER"
                        ],
                        "firstName" =>"string",
                        "lastName" => "string",
                        "website" => "string",
                        "profession" => "string",
                        "phoneNumber" => "string"
                    ],
                    'token' => false
                ], [
                    'name' => 'Get employees',
                    'route' => '/api/users',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' =>
                        [
                            "id" => "int",
                            "email" => "string",
                            "roles" => [
                                "ROLE_USER"
                            ],
                            "firstName" => "string",
                            "lastName" => "string",
                            "website" => "string",
                            "profession" => "string",
                            "phoneNumber" => "string"
                            ,
                        ], ["..."]
                    , 'token' => true
                ], [
                    'name' => 'edit one user if you are admin',
                    'route' => '/api/user/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "username" =>"string",
                        "firstName" => "string",
                        "lastName" => "string",
                        "website" => "string",
                        "profession" => "string",
                        "phoneNumber" => "string"
                    ],
                    'sendBack' =>
                        [
                            "id" => "int",
                            "email" => "string",
                            "roles" => [
                                "ROLE_USER"
                            ],
                            "firstName" => "string",
                            "lastName" => "string",
                            "website" => "string",
                            "profession" => "string",
                            "phoneNumber" => "string"
                        ]
                    , 'token' => true
                ],
            ],
            'settings' => [
                [
                    'name' => 'Get settings',
                    'route' => '/api/settings/get',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        "id" => 1,
                        "isTheWebsiteOpen" => "boolean",
                        "belongings" => "string contatened with ',' ",
                        "otherSharedRoom" => "string contatened with ','"
                    ],
                    'token' => false
                ], [
                    'name' => 'Edit settings',
                    'route' => '/api/settings/edit',
                    'methode' => 'PUT',
                    'body' => [
                        "isTheWebsiteOpen" => "boolean",
                        "belongings" => "string contatened with ','",
                        "otherSharedRoom" => "string contatened with ','"
                    ],
                    'sendBack' => [
                        "message" => "ok"],
                    'token' => false
                ]
            ],
            'Room' => [
                [
                    'name' => 'Get Rooms',
                    'route' => '/api/rooms',
                    'methode' => 'GET',
                    'body' => [
                        "id" => 36,
                        "name" => "Room 0",
                        "hasPrivateShowerroom" => "boolean",
                        "hasLocker" => "boolean",
                        "isPrivate" => "boolean",
                        "beds" => [
                            [
                                "id" => "int",
                                "isSittingApart" => "boolean",
                                "state" => "inspected,notCleaned,cleaned,blocked",
                                "number" => "int",
                                "isDoubleBed" => "int",
                                "isOccupied"=>"boolean",
                                "bedShape" => "string",
                                "hasLamp" => "boolean",
                                "hasLittleStorage" => "boolean",
                                "hasShelf" => "boolean",
                            ],
                            "..."

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
                        "name" => "string",
                        "hasLocker" => "boolean",
                        "private" => "boolean",
                        "hasPrivateShowerroom" => "boolean"
                    ],
                    'sendBack' => "The room created",
                    'token' => true
                ], [
                    'name' => 'Edit Room',
                    'route' => '/api/room/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "name" => "string",
                        "hasLocker" => "boolean",
                        "private" => "boolean",
                        "hasPrivateShowerroom" => "boolean"
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
                        "isSittingApart" => "boolean",
                        "state" => "string",
                        "number" => "int",
                        "isDoubleBed" => "int",
                        "isOccupied"=>"boolean",
                        "bedShape" => "string",
                        "hasLamp" => "boolean",
                        "hasLittleStorage" => "boolean",

                        "hasShelf" => "boolean",
                        "cleanedBy" => [
                            "id" => "int",
                            "email" => "string",
                        ], "inspectedBy" => [
                            "id" => "int",
                            "email" => "string",
                        ],
                        "room" => [
                            "id" => 6,
                            "name" => "room1",
                            "hasPrivateShowerroom" => "boolean",
                            "hasLocker" => "boolean",
                            "isPrivate" => "boolean",

                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'New bed',
                    'route' => '/api/bed/new',
                    'methode' => 'POST',
                    'body' => [
                        "number" => "int",
                        "doubleBed" => "boolean",
                        "dunkBed" => "boolean",
                        "hasLamp" => "boolean",
                        "hasLittleStorage" => "boolean",
                        "hasShelf" => "boolean",
                        "bedShape" => "topBed,bottomBed,singleBed",
                        "sittingApart" => "boolean",
                        "state" => "cleaned,inspected,notCleaned,blocked",
                        "room" => "int"
                    ],
                    'sendBack' => ['message'=>"ok"],
                    'token' => true
                ], [
                    'name' => 'Edit bed',
                    'route' => '/api/bed/edit/{id}',
                    'methode' => 'PUT',
                    'body' =>  [
                        "number" => "int",
                        "doubleBed" => "boolean",
                        "dunkBed" => "boolean",
                        "hasLamp" => "boolean",
                        "hasLittleStorage" => "boolean",
                        "hasShelf" => "boolean",
                        "bedShape" => "topBed,bottomBed,singleBed",
                        "sittingApart" => "boolean",
                        "state" => "cleaned,inspected,notCleaned,blocked",
                        "room" => "int"
                    ],
                    'sendBack' => ["id" => 13,
                        "isSittingApart" => "boolean",
                        "state" => "inspected",
                        "number" => "int",
                        "isDoubleBed" => "int",
                        "isOccupied"=>"boolean",
                        "bedShape" => "string",
                        "hasLamp" => "boolean",
                        "hasLittleStorage" => "boolean",

                        "hasShelf" => "boolean",
                        "cleanedBy" => [
                            "id" => "int",
                            "email" => "string",
                        ], "inspectedBy" => [
                            "id" => "int",
                            "email" => "string",
                        ],
                        "room" => [
                            "id" => 6,
                            "name" => "room1",
                            "hasPrivateShowerroom" => "boolean",
                            "hasLocker" => "boolean",
                            "isPrivate" => "boolean",

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
                    'name' => 'Edit housekeeping status of bed',
                    'route' => '/api/edit/status/{id}',
                    'methode' => 'PATCH',
                    'body' => [
                        "status" => "  blocked, cleaned, inspected, notcleaned "
                    ],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],[
                    'name' => 'Toggle occupied state of bed ',
                    'route' => '/bed/{id}/change/occupation',
                    'methode' => 'PATCH',
                    'body' => [ ],
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
