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
                    'description' => 'This function allows you to log in when you already have an account. You will receive a token to use in all requests requiring it, placed in the header under "Authorization" with "Bearer".',
                    'methode' => 'GET',
                    'body' => [
                        "username" => "string (NOT NULL)",
                        "password" => "string (NOT NULL)"
                    ],
                    'sendBack' => ["token" => "string"],
                    'token' => false
                ], [
                    'name' => 'Sign up',
                    'route' => '/register',
                    'methode' => 'POST',
                    'description' => 'Allows account creation when one does not already exist. The provided email must be unique. If an account with the email exists, an error will appear. This option is mainly used in debug mode and is commented out in production. Use "create user" to add users.',

                    'body' => [
                        "email" => "string (NOT NULL)",
                        "password" => "string (NOT NULL)",
                        "firstName" => "string (NOT NULL)",
                        "lastName" => "string (NOT NULL)",
                        "profession" => "string (NULL)",
                        "phoneNumber" => "string (NULL)"
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
                    'name' => 'Create user',
                    'route' => '/api/user/new',
                    'description' => 'This function creates a user and is accessible only to administrators. It allows adding users by providing all required information. The default password will be "auberjeune".',

                    'methode' => 'POST',
                    'body' => [
                        "email" => "string (NOT NULL)",
                        "password" => "string (NOT NULL)",
                        "firstName" => "string (NOT NULL)",
                        "lastName" => "string (NOT NULL)",
                        "profession" => "string (NOT NULL)",
                        "phoneNumber" => "string (NOT NULL)"
                    ],
                    'sendBack' => [
                        "id" => "int (AI) (NOT NULL)",
                        "email" => "string",
                        "roles" => [
                            "string"
                        ],
                        "firstName" => "string",
                        "lastName" => "string",
                        "website" => "string",
                        "profession" => "string",
                        "phoneNumber" => "string",
                        "isActive" => "boolean"
                    ],
                    'token' => true
                ], [
                    'name' => 'Get employees',
                    'description' => 'This function returns a list of users/employees with their roles and professions.',

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
                    'description' => 'This function allows modifying a user. Administrator privileges are required.',

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
                    'description' => 'Simply retrieves settings for display.',
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
                    'description' => 'Allows modifying settings if you are an admin. You must provide all listed options. These details inform clients about provided amenities and other parameters.',
                    'route' => '/api/settings/edit',
                    'methode' => 'PUT',
                    'body' => [
                        "isTheWebsiteOpen" => "boolean (NOT NUL)",
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
                    'description' => 'Returns a list of rooms, their associated beds, and any ongoing reservations linked to those beds. Also provides key metrics like the total number of beds in each room.',
                    'route' => '/api/rooms',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        [
                            "id" => "int (AI) (NOT NULL)",
                            "name" => "string",
                            "hasPrivateShowerroom" => "boolean",
                            "hasLocker" => "boolean",
                            "isPrivate" => "boolean",
                            "bedsNumber" => 'int',
                            "beds" => [
                                [
                                    "id" => "int (AI) (NOT NULL)",
                                    "isSittingApart" => "boolean",
                                    "state" => "blocked/cleaned/inspected/notcleaned",
                                    "number" => 0,
                                    "isDoubleBed" => "boolean",
                                    "bedShape" => "singleBed",
                                    "hasLamp" => "boolean",
                                    "hasLittleStorage" => "boolean",
                                    "hasShelf" => "boolean",
                                    "currentBooking" => "Booking Object"
                                ],
                                ["..."]
                            ],
                            "hasTable" => "boolean",
                            "hasBalcony" => "boolean",
                            "hasWashtub" => "boolean",
                            "hasBin" => "boolean",
                            "hasWardrobe" => "boolean"
                        ], ['...']
                    ],
                    'token' => true
                ],
                [
                    'name' => 'New Room',
                    'description' => 'Creates a room with all specified options.',
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
                    'description' => 'To modify a room, all of its options must be provided.',
                    'methode' => 'PUT',
                    'body' => [
                        "name" => "string (NOT NULL)",
                        "hasLocker" => "boolean (NOT NULL)",
                        "hasTable" => "boolean (NOT NULL)",
                        "hasBalcony" => "boolean (NOT NULL)",
                        "hasWashtub" => "boolean (NOT NULL)",
                        "hasBin" => "boolean (NOT NULL)",
                        "hasWardrobe" => "boolean (NOT NULL)",
                        "private" => "boolean (NOT NULL)",
                        "hasPrivateShowerroom" => "boolean (NOT NULL)"
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

                'name' => 'Get one bed',
                'description' => 'Fetches details of a specific bed and all associated reservations, only if the bed is not marked as modified.',
                'route' => '/api/bed/get/{id}',
                'methode' => 'GET',
                'body' => null,
                'sendBack' => [
                    "bed" => ["id" => 13,
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

                        ],
                        "bookings" => [

                            "id" => "int (AI) (NOT NULL)",
                            "startDate" => "d.m.Y H:i",
                            "endDate" => "d.m.Y H:i",
                            "phoneNumber" => "string",
                            "mail" => "string"

                        ]
                    ],
                    'token' => true
                ], [
                    'name' => 'New bed',
                    'description' => 'Create bed: Creates a bed with default "false" occupancy and a status automatically set to "cleaned". The number must be unique, and the room ID must be valid and correspond to an existing room.',
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
                        "room" => "int",
                        "bookings" => []
                    ],
                    'sendBack' => ['message' => "ok"],
                    'token' => true
                ], [
                    'name' => 'Edit bed',
                    'route' => '/api/bed/edit/{id}',
                    'description' => 'Edit bed: Allows modifying a bed, but you cannot change its status here. Use the designated functions for status updates.',

                    'methode' => 'PUT',
                    'body' => [
                        "number" => "int (NOT NULL)",
                        "doubleBed" => "boolean (NOT NULL)",
                        "dunkBed" => "boolean (NOT NULL)",
                        "hasLamp" => "boolean",
                        "hasLittleStorage" => "boolean",
                        "hasShelf" => "boolean",
                        "bedShape" => "topBed,bottomBed,singleBed",
                        "sittingApart" => "boolean",
                        "room" => "int"
                    ],
                    'sendBack' => [
                        "id" => 13,
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
                    'name' => 'Remove bed',
                    'description' => 'Deletes a bed by changing its state to "deleted". The bed is no longer visible or reservable but can be restored with a specific request. You can also view all deleted beds.',
                    'route' => '/api/remove/{id}',
                    'methode' => 'DELETE',
                    'body' => null,
                    'sendBack' => "ok if it's deleted",
                    'token' => true
                ], [
                    'name' => 'UnRemove bed',

                    'description' => 'Allows restoring a deleted bed.',
                    'route' => '/api/unremove/{id}',
                    'methode' => 'PATCH',
                    'body' => null,
                    'sendBack' => "ok",
                    'token' => true
                ], [
                    'description' => 'Fetches all beds marked as deleted and restores them with a specific request.',
                    'name' => 'Get all deleted beds',
                    'route' => '/api/beds/deleted',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        "id" => 13,
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
                    'name' => 'Turn bed status on inspected',
                    'route' => '/api/bed/inspect/{id}',
                    'methode' => 'PATCH',
                    'description' => 'Changes a bed\'s status to "inspected" only if it is currently "cleaned" and not deleted. The inspector is linked to the user making the request.',
                    'body' => null,
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Turn bed status on cleaned',
                    'route' => '/api/bed/clean/{id}',
                    'methode' => 'PATCH',
                    'description' => 'This function changes the status of a bed to "cleaned", unless the bed is deleted.',
                    'body' => null,
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Edit housekeeping status of bed',

                    'description' => 'Modifies the status of a bed.',
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
                    'description' => 'Marks a bed as "unoccupied" or "occupied".',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],],


            "Booking" => [
                [
                    'name' => 'Create booking',
                    'route' => '/api/bookinging/new',

                    'description' => 'Creates a reservation.',    'methode' => 'POST',
                    'body' => [

                        "startDate" => "2022-12-03 12:00",
                        "endDate" => "2023-12-05 12:00",
                        "phoneNumber" => "07 82 40 50 80",
                        "mail" => "07 82 40 80 49",
                        "clients" => [
                            ["firstName" => "Mey", "lastName" => "DETOUR", "birthDate" => "2015-12-03 00:00"],
                            ["firstName" => "Maxence", "lastName" => "Abrile", "birthDate" => "2002-12-03 00:00"]
                        ],
                        "wantPrivateRoom" => "boolean"
                    ],
                    'sendBack' => "booking",
                    'token' => true
                ], [
                    'name' => 'edit booking',

                    'description' => 'Modifies a bed by updating all attributes.',   'route' => '/api/booking/edit/{id}',
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
                    'route' => '/api/booking/finish/{id}',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
                [
                    'name' => 'gat all bookings ',
                    'description' => 'Get all bookings: Automatically marks expired reservations as "done".',
                    'route' => '/api/bookings',
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
                ],[
                    'name' => 'gat all passed bookings ',
                    'route' => '/api/bookings/passed',
                    'description' => 'Fetches all past reservations.',
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
                     'route' => '/api/bookings/get/waiting',
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
                    'route' => '/api/bookings/get/progress',
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
                    'route' => '/api/bookings/get/done',
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
