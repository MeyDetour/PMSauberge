<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/doc', name: 'app_doc')]
    public function index(): Response
    {

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
                        "firstName" => "string",
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
                        "username" => "string",
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
                    'body' => null,
                    'sendBack' => [
                        [
                            "id" => 1,
                            "name" => "Room 0",
                            "hasPrivateShowerroom" => false,
                            "hasLocker" => false,
                            "isPrivate" => false,
                            "bedsNumber" => 'int',
                            "beds" => [
                                [
                                    "id" => 1,
                                    "isSittingApart" => false,
                                    "state" => "inspected",
                                    "number" => 0,
                                    "isDoubleBed" => false,
                                    "bedShape" => "singleBed",
                                    "hasLamp" => false,
                                    "hasLittleStorage" => false,
                                    "hasShelf" => false,
                                    "bookings" => [
                                        [
                                            "id" => 1,
                                            "startDate" => "30.10.2024 10=>00",
                                            "endDate" => "02.11.2024 12=>00",
                                            "phoneNumber" => "07 82 40 507770",
                                            "mail" => "07 82 40 80 49"
                                        ]
                                    ],
                                    "currentBooking" => null
                                ],
                                ["..."]
                            ],
                            "hasTable" => false,
                            "hasBalcony" => false,
                            "hasWashtub" => false,
                            "hasBin" => false,
                            "hasWardrobe" => false
                        ],['...']
                    ],
                    'token' => true
                ],
                [
                    'name' => 'New Room',
                    'route' => '/api/room/new',
                    'methode' => 'POST',
                    'body' => [
                        "name" => "string",
                        "hasLocker" => "boolean",
                        "hasTable" => "boolean",
                        "hasBalcony" => "boolean",
                        "hasWashtub" => "boolean",
                        "hasBin" => "boolean",
                        "hasWardrobe" => "boolean",
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
                        "hasTable" => "boolean",
                        "hasBalcony" => "boolean",
                        "hasWashtub" => "boolean",
                        "hasBin" => "boolean",
                        "hasWardrobe" => "boolean",
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
                        "occupied" => "boolean",
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
                            "hasTable" => "boolean",
                            "hasBalcony" => "boolean",
                            "hasWashtub" => "boolean",
                            "hasBin" => "boolean",
                            "hasWardrobe" => "boolean",
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
                    'sendBack' => ['message' => "ok"],
                    'token' => true
                ], [
                    'name' => 'Edit bed',
                    'route' => '/api/bed/edit/{id}',
                    'methode' => 'PUT',
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
                    'sendBack' => ["id" => 13,
                        "isSittingApart" => "boolean",
                        "state" => "inspected",
                        "number" => "int",
                        "isDoubleBed" => "int",
                        "isOccupied" => "boolean",
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
                            "hasTable" => "boolean",
                            "hasBalcony" => "boolean",
                            "hasWashtub" => "boolean",
                            "hasBin" => "boolean",
                            "hasWardrobe" => "boolean",
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
                ], [
                    'name' => 'Toggle occupied state of bed ',
                    'route' => '/bed/{id}/change/occupation',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
            ],

            "Booking" => [
                [
                    'name' => 'to book',
                    'route' => '/booking/new',
                    'methode' => 'POST',
                    'body' => [

                        "startDate" => "2022-12-03 12:00",
                        "endDate" => "2023-12-05 12:00",
                        "phoneNumber" => "07 82 40 50 80",
                        "mail" => "07 82 40 80 49",
                        "clients" => [
                            ["firstName" => "Mey", "lastName" => "DETOUR", "birthDate" => "2015-12-03 00:00"],
                            ["firstName" => "Maxence", "lastName" => "Abrile", "birthDate" => "2002-12-03 00:00"]
                        ],
                    ],
                    'sendBack' => "booking",
                    'token' => true
                ], [
                    'name' => 'edit book',
                    'route' => '/booking/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "startDate" => "2022-12-03 12:00",
                        "endDate" => "2023-12-05 12:00",
                        "phoneNumber" => "07 82 40 50 80",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clients" => [
                            ["firstName" => "Mey", "lastName" => "DETOUR", "birthDate" => "2015-12-03 00:00"],
                            ["firstName" => "Maxence", "lastName" => "Abrile", "birthDate" => "2002-12-03 00:00"]
                        ],
                    ],
                    'sendBack' => "booking",
                    'token' => true
                ], [
                    'name' => 'finish booking ',
                    'route' => '/booking/finish/{id}',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
                [
                    'name' => 'gat all bookings ',
                    'route' => '/bookings/get/all',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => "int",
                        "startDate" => "datetime",
                        "endDate" => "datetime",
                        "createdAt" => "datetime",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "isFinished" => "boolean",
                        "isPaid" => "boolean",
                        "advencement" => "string",
                        "clients" => [
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ],
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ]
                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all waiting ',
                    'route' => '/bookings/get/waiting',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => "int",
                        "startDate" => "datetime",
                        "endDate" => "datetime",
                        "createdAt" => "datetime",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "isFinished" => "boolean",
                        "isPaid" => "boolean",
                        "advencement" => "string",
                        "clients" => [
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ],
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ]
                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all in progress ',
                    'route' => '/bookings/get/progress',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => "int",
                        "startDate" => "datetime",
                        "endDate" => "datetime",
                        "createdAt" => "datetime",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "isFinished" => "boolean",
                        "isPaid" => "boolean",
                        "advencement" => "string",
                        "clients" => [
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ],
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ]
                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all done ',
                    'route' => '/bookings/get/done',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => "int",
                        "startDate" => "datetime",
                        "endDate" => "datetime",
                        "createdAt" => "datetime",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "isFinished" => "boolean",
                        "isPaid" => "boolean",
                        "advencement" => "string",
                        "clients" => [
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ],
                            [
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "datetime"
                            ]
                        ]
                    ],
                    'token' => true
                ],
            ]
        ];
        return $this->render("home/index.html.twig", [
            'routes' => $routes
        ]);

    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render("home/home.html.twig", [

        ]);
    }

}
