<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

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
                    'sendBack' => [
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
                        ], ["..."]]
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
                    'description' => 'Returns a list of rooms, Also provides key metrics like the total number of beds in each room.',
                    'route' => '/api/rooms',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        [
                            "id" => "int (AI) (NOT NULL)",
                            "name" => "string",
                            "hasPrivateShowerroom" => "boolean",
                            "hasLocker" => "boolean",
                            "private" => "boolean",
                            "bedsNumber" => 'int',
                            "hasTable" => "boolean",
                            "hasBalcony" => "boolean",
                            "hasWashtub" => "boolean",
                            "hasBin" => "boolean",
                            "hasWardrobe" => "boolean",
                            "beds" => [
                                [
                                    "id" => "int (AI) (NOT NULL)",
                                    "occupied" => "boolean",
                                    "number" => "string"
                                ], ["..."]

                            ]
                        ], ['...']
                    ],
                    'token' => true
                ], [
                    'name' => 'Get Rooms Names',
                    'description' => 'Returns a list of rooms with their names without details',
                    'route' => '/api/rooms/names',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        [
                            "id" => "int (AI) (NOT NULL)",
                            "name" => "string",
                        ], ['...']
                    ],
                    'token' => true
                ], [
                    'name' => 'Get Room',
                    'description' => 'Returns a room, their associated beds, and any ongoing reservations linked to those beds. Also provides key metrics like the total number of beds in each room.',
                    'route' => '/api/room/{id}',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        [
                            "id" => "int (AI) (NOT NULL)",
                            "name" => "string",
                            "hasPrivateShowerroom" => "boolean",
                            "hasLocker" => "boolean",
                            "private" => "boolean",
                            "bedsNumber" => 'int',
                            "beds" => [
                                [
                                    "id" => "int (AI) (NOT NULL)",
                                    "sittingApart" => "boolean",
                                    "state" => "blocked/cleaned/inspected/notcleaned",
                                    "number" => 0,
                                    "doubleBed" => "boolean",
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
                    'name' => 'Remove room and its beds', 'description' => 'Remove room: Deletes a room if it has no associated beds.',
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
                    'description' => 'Fetches details of a specific bed and all associated reservations, only if the bed is not marked as modified.',
                    'route' => '/api/bed/get/{id}',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        "bed" => ["id" => 13,
                            "sittingApart" => "boolean",
                            "state" => "string",
                            "number" => "int",
                            "doubleBed" => "int",
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
                            "roomId" => "int",
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

                        "roomId" => "int",
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

                        "roomId" => "int",
                    ],
                    'sendBack' => [
                        "id" => 13,
                        "sittingApart" => "boolean",
                        "state" => "string",
                        "number" => "int",
                        "doubleBed" => "int",
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
                            "private" => "boolean",

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
                        "sittingApart" => "boolean",
                        "state" => "string",
                        "number" => "int",
                        "doubleBed" => "int",
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
                            "private" => "boolean",

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
                    'route' => '/api/bed/{id}/change/occupation',
                    'description' => 'Marks a bed as "unoccupied" or "occupied".',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
            ],
            "Client" => [
                [
                    'name' => 'Get all clients',
                    'route' => '/api/clients',
                    'description' => 'Get all clients: Returns a list of all clients with only their first name, last name, and date of birth.',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "firstName" => "string",
                        "lastName" => "string",
                        "birthDate" => "d.m.Y H:i",

                    ],
                    'token' => true
                ],
                [
                    'name' => 'Remove Client',
                    'route' => '/api/remove/client/{client id}/from/booking/{booking id}',
                    'description' => 'Remove client: Removes the client from a reservation if the reservation has not passed. If the client has no other reservations, they are deleted; otherwise, they are only removed from the specific reservation.', 'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Edit Client',
                    'route' => '/api/bed/{id}/change/occupation',
                    'description' => 'Edit client: Allows modifying only the first name and last name. The date of birth is non-editable if the client has reservations under their name.',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ], [
                    'name' => 'Get Client',
                    'route' => '/api/client/{id}',
                    'description' => 'Retrieve a client: Displays all details of the client, including reservations they made and reservations in which they are listed.',
                    'methode' => 'get',
                    'body' => [
                        "id" => 'int (AI) ',
                        "firstName" => "string",
                        "lastName" => "string",
                        "birthDate" => "d.m.Y H:i",
                        "bookings" => [
                            [
                                "id" => 'int (AI) ',
                                "startDate" => "d.m.Y H:i",
                                "endDate" => "d.m.Y H:i",
                                "createdAt" => "d.m.Y H:i",
                                "phoneNumber" => "string"
                            ],

                        ],
                        "bookingsAuthor" => []
                    ],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
            ],


            "Booking" => [
                [
                    'name' => 'Filling of beds based on booking',

                    'description' => 'Get all filling percentage of bed and number of client wich come',
                    'route' => '/api/bookings/state',
                    'methode' => 'GET',
                    'body' => null,
                    'sendBack' => [
                        "clientsToCome" => "int",
                        "clientsDeparture" => "int",
                        "globalFillingPercentage" => "int",
                        "privateRoomFillingPercentage" => "int",
                        "morningFillingPercentage" => "int",
                        "nightFillingPercentage" => "int",
                    ],
                    'token' => true
                ],
                [
                    'name' => 'Create booking',
                    'route' => '/api/bookinging/new',
                    'description' => 'Create reservation: Ensures all required fields are provided and valid, and checks if an adult is included in the group. If no adult is found, an error is returned. Beds are assigned based on the number of people. If the hotel lacks enough available beds, an error is returned. Group size must perfectly match the available capacity in a shared room but has no restriction for private rooms. For immediate reservations, the beds must be available and cleaned.',

                    'methode' => 'POST',
                    'body' => [

                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "clients" => [
                            ["firstName" => "string", "lastName" => "string", "birthDate" => "d.m.Y 00:00"],
                            ["firstName" => "string", "lastName" => "string", "birthDate" => "d.m.Y 00:00"]
                        ],
                        "wantPrivateRoom" => "boolean"
                    ],
                    'sendBack' => "booking",
                    'token' => true
                ], [
                    'name' => 'edit booking',
                    'description' => 'Modify reservation: Allows changing the email, dates (which also updates the assigned beds), phone number, and the option to switch to a private room. However, you cannot modify clients here. Use the "edit client of booking" or "remove client of booking" functions instead.',
                    'route' => '/api/booking/edit/{id}',
                    'methode' => 'PUT',
                    'body' => [
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "wantPrivateRoom" => "boolean"
                    ],
                    'sendBack' => "booking",
                    'token' => true
                ], [
                    'name' => 'finish booking ',
                    'description' => 'Mark booking as finished',

                    'route' => '/api/booking/finish/{id}',
                    'methode' => 'PATCH',
                    'body' => [],
                    'sendBack' => "ok if it's done",
                    'token' => true
                ],
                [
                    'name' => 'gat all bookings ',
                    'description' => 'Retrieve all reservations: Returns a list of all reservations without detailed information.',
                    'route' => '/api/bookings',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clientsNumber" => "int"
                    ],
                    'token' => true
                ], [
                    'name' => 'gat one booking ',
                    'description' => 'Retrieve a single reservation: Provides detailed information about a specific reservation, including the assigned beds and associated clients.',
                    'route' => '/api/booking/{id}',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "beds" => [
                            ["id" => 'int (AI) ',
                                "state" => "inspected/cleaned/blocked/notcleaned/deleted",
                                "room" => [
                                    "id" => 'int (AI) ',
                                    "name" => "string"
                                ],
                                "number" => "int"
                            ], ["..."]
                        ],
                        "advencement" => "string",
                        "clients" => [
                            [
                                "id" => 'int (AI) ',
                                "firstName" => "string",
                                "lastName" => "string",
                                "birthDate" => "d.m.Y H:i"
                            ],

                        ],
                        "mainClient" => [
                            "id" => 'int (AI) ',
                            "firstName" => "string",
                            "lastName" => "string",
                            "birthDate" => "d.m.Y H:i"
                        ],
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all passed bookings ',
                    'route' => '/api/bookings/passed',
                    'description' => 'Fetches all past reservations.',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clientsNumber" => "int"
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all waiting ',
                    'route' => '/api/bookings/get/waiting',
                    'description' => 'Get all reservations in a waiting state: Retrieves reservations that are currently marked as "waiting."',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clientsNumber" => "int"
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all in progress ',
                    'route' => '/api/bookings/get/progress', 'description' => 'Retrieve pending reservations: Fetch reservations that are pending, along with past, refunded, completed, ongoing, or upcoming ones.',

                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clientsNumber" => "int"
                    ],
                    'token' => true
                ], [
                    'name' => 'gat all done ',
                    'route' => '/api/bookings/get/done', 'description' => 'Retrieve completed reservations: Fetch reservations marked as completed, along with those that are pending, refunded, ongoing, upcoming, and past.',
                    'methode' => 'GET',
                    'body' => [],
                    'sendBack' => [
                        "id" => 'int (AI) ',
                        "startDate" => "d.m.Y H:i",
                        "endDate" => "d.m.Y H:i",
                        "createdAt" => "d.m.Y H:i",
                        "phoneNumber" => "string",
                        "mail" => "string",
                        "price" => "int",
                        "finished" => "boolean",
                        "paid" => "boolean",
                        "advencement" => "string",
                        "clientsNumber" => "int"
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

    #[Route('/send-mail', name: 'app_test')]
    public function test(MailerInterface $mailer, LoggerInterface $logger, Environment $twig): Response
    {
//        php bin/console messenger:consume async -vv

        try {
            $htmlContent = $twig->render('emails/registration_email.html.twig', [
                'title' => 'Bienvenue sur notre site!',
                'user_name' => 'Jean Dupont'
            ]);

            $email = (new Email())
                ->from(new Address('meydetour-contact@zohomail.eu'))
                ->to(new Address('meynever@gmail.com'))
                ->subject('Bienvenue sur le site de Gaëlle Ghizoli !')
                ->text('Sending emails is fun again!')
                ->html($htmlContent);

            $mailer->send($email);
            return $this->render("home/test.html.twig", [

            ]);

        } catch (TransportExceptionInterface $e) {
            // Log l'erreur pour diagnostic
            $errorMessage = $e->getMessage();
            dd($errorMessage);
            return new Response('Erreur lors de l\'envoi : ' . $errorMessage, 500);
        }
    }

}
