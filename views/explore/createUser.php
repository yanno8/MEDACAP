<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["id"])) {
    header("Location: ../../");
    exit();
} else {
    require_once "../../vendor/autoload.php";
    require "../sendMail.php";

    // Create connection
    $conn = new MongoDB\Client("mongodb://localhost:27017");

    // Connecting in database
    $academy = $conn->academy;

    // Connecting in collections
    $users = $academy->users;
    $vehicles = $academy->vehicles;
    $tests = $academy->tests;
    $quizzes = $academy->quizzes;
    $allocations = $academy->allocations;
    $profiles = $academy->profiles;

    $profileOptions = $profiles->find(['active' => true])->toArray();

    if (isset($_POST["submit"])) {
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $emailAddress = $_POST["email"];
        $phone = $_POST["phone"] ?? 0;
        +$matriculation = $_POST["matricule"] ?? 99999999;
        $userName = $_POST["username"];
        $subsidiary = $_POST["subsidiary"];
        $departement = $_POST["department"];
        $fonction = $_POST["role"];
        $profile = $_POST["profile"];
        $agency = $_POST["agency"];
        $sex = $_POST["gender"];
        $pays = $_POST["country"] ?? "";
        $certificate = $_POST["certificate"];
        $birthDate = date("d-m-Y", strtotime($_POST["birthdate"]));
        $recrutementDate = date("d-m-Y", strtotime($_POST["recrutmentDate"]));
        $managerId = $_POST["manager"];
        if (isset($_POST["level"])) {
          $niv = $_POST["level"];
        }
        if (isset($_POST["specialitySenior"])) {
          $specialitySenior = $_POST["specialitySenior"];
        }
        if (isset($_POST["specialityExpert"])) {
          $specialityExpert = $_POST["specialityExpert"];
        }
        if (isset($_POST["brandJu"])) {
          $brandJunior = $_POST["brandJu"];
        }
        if (isset($_POST["brandSe"])) {
          $brandSenior = $_POST["brandSe"];
        }
        if (isset($_POST["brandEx"])) {
          $brandExpert = $_POST["brandEx"];
        }
        
        $fn = substr($firstName, -2);
        $ln = substr($lastName,0, 2);
        $ma = substr(+$matriculation, -3);

        $passWord = $ln.$ma.$fn;
        $techs = [];

        $password_hash = sha1($passWord);
        $member = $users->findOne([
            '$and' => [["username" => $userName], ["active" => true]],
        ]);
        if (
            empty($firstName) ||
            empty($lastName) ||
            empty($userName) ||
            empty($departement) ||
            empty($agency)
            // !filter_var($emailAddress, FILTER_VALIDATE_EMAIL) ||
            // preg_match('/^[\D]{15}$/', $phone) 
            // preg_match(
            //     '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{6,}$/',
            //     $passWord
            // )
        ) {
            $error = $champ_obligatoire;
            $email_error = $email_invalid;
            $phone_error = $phone_invalid;
            $password_error = $password_invalid;
        } elseif ($member) {
            $error_msg = $exist_user;
        } else {
            if ($profile == "Technicien") {
                if (!empty($managerId)) {
                    
                    $userManager = $users->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId($managerId),
                                "active" => true,
                            ],
                        ],
                    ]);

                    $personT = [
                        "users" => [],
                        "username" => $userName,
                        "matricule" => +$matriculation,
                        "firstName" => ucfirst($firstName),
                        "lastName" => ucfirst($lastName),
                        "email" => $emailAddress,
                        "phone" => $phone,
                        "gender" => $sex,
                        "level" => $niv,
                        "country" => $pays,
                        "profile" => $profile,
                        "birthdate" => $birthDate,
                        "recrutmentDate" => $recrutementDate,
                        "certificate" => ucfirst($certificate), 
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($departement),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($fonction),
                        "password" => $password_hash,
                        "visiblePassword" => $passWord,
                        "manager" => new MongoDB\BSON\ObjectId($managerId),
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personT);
                    $users->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($managerId)],
                        [
                            '$push' => [
                                "users" => new MongoDB\BSON\ObjectId(
                                    $user->getInsertedId()
                                ),
                            ],
                        ]
                    );

                    $technician = $users->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                
                    // Assurez-vous que les variables sont définies et valides
                    if (isset($userManager['lastName'], $technician['firstName'], $technician['lastName'])) {
                        // Échapper les données pour éviter les problèmes de sécurité
                        $managerLastName = htmlspecialchars($userManager['lastName']);
                        $technicianFirstName = htmlspecialchars($technician['firstName']);
                        $technicianLastName = htmlspecialchars($technician['lastName']);

                        // Créer le message
                        $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                    <p>Votre collaborateur <strong>' . $technicianFirstName . ' ' . $technicianLastName . '</strong>
                                    a été inscrit avec succès dans la plateforme de « <strong>Mesure des Compétences des Techniciens </strong> ». <br>
                                    Il vous est désormais possible de visualiser les informations enregistrées de vos collaborateurs (menu : <strong>Liste des Utilisateurs</strong>).</p>
                                    <p>Merci de valider la maîtrise de chacune des tâches professionnelles de votre collaborateur dans l\'espace de l\'application (menu : <strong>Liste des Collaborateurs à Evaluer</strong>).</p>
                                    <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                        // Sujet de l'e-mail
                        $subject = 'Confirmation d\'inscription  de ' . $technicianFirstName . ' ' . $technicianLastName.' sur la plateforme  « Mesure des Compétences des Techniciens »  Espace Technicien';

                        // Envoyer l'e-mail
                        $sendMail = sendMailRegisterUser($userManager['email'], $subject, $message);
                    }
                } else {
                    $personT = [
                        "users" => [],
                        "username" => $userName,
                        "matricule" => +$matriculation,
                        "firstName" => ucfirst($firstName),
                        "lastName" => ucfirst($lastName),
                        "email" => $emailAddress,
                        "phone" => $phone,
                        "gender" => $sex,
                        "level" => $niv,
                        "country" => $pays,
                        "profile" => $profile,
                        "birthdate" => $birthDate,
                        "recrutmentDate" => $recrutementDate,
                        "certificate" => ucfirst($certificate), 
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($departement),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($fonction),
                        "password" => $password_hash,
                        "visiblePassword" => $passWord,
                        "manager" => "",
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personT);
                }
                if (isset($_POST["brandEx"])) {
                    for ($i = 0; $i < count($brandExpert); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandSenior" => $brandExpert[$i]
                                ],
                            ]
                        );
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brandExpert[$i]
                                ],
                            ]
                        );
                    }
                }
                if (isset($_POST["brandSe"])) {
                    for ($i = 0; $i < count($brandSenior); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brandSenior[$i]
                                ],
                            ]
                        );
                    }
                }
                if ($niv == "Junior") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandJunior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testFac);

                    $testDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandJunior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => true,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => true,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $success_msg = $success_tech;
                } elseif ($niv == "Senior") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testJuFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testJuFac);

                    $testJuDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testJuDecla);

                    $testSeFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeFac = $tests->insertOne($testSeFac);

                    $testSeDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeDecla = $tests->insertOne($testSeDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialitySenior']); ++$n) {
                        $specialityFacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['brandSenior']); ++$n) {
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }

                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => true,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => true,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $saveTestSeFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                    ]);
                    $saveTestSeFac["total"] = count($saveTestSeFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeFac]
                    );

                    $allocateSeFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Senior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeFac);

                    $saveTestSeDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestSeDecla["total"] = count(
                        $saveTestSeDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeDecla]
                    );

                    $allocateSeDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeDecla);

                    $success_msg = $success_tech;
                } elseif ($niv == "Expert") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testJuFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testJuFac);

                    $testJuDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testJuDecla);

                    $testSeFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeFac = $tests->insertOne($testSeFac);

                    $testSeDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeDecla = $tests->insertOne($testSeDecla);

                    $testExFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $brandExpert,
                        "type" => "Factuel",
                        "level" => "Expert",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertExFac = $tests->insertOne($testExFac);

                    $testExDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $brandExpert,
                        "type" => "Declaratif",
                        "level" => "Expert",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertExDecla = $tests->insertOne($testExDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    for ($n = 0; $n < count($person['brandSenior']); ++$n) {
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                        $vehicleDeclacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialitySenior']); ++$n) {
                        $specialityFacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialityExpert']); ++$n) {
                        $specialityFacEx = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialityExpert'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacEx) {
                            for (
                                $a = 0;
                                $a < count($specialityFacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacEx = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialityExpert'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacEx) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($brandExpert); ++$n) {
                        $vehicleFacEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brandExpert[$n]],
                                ["type" => "Factuel"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacEx) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brandExpert[$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacEx) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }

                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => true,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => true,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $saveTestSeFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                    ]);
                    $saveTestSeFac["total"] = count($saveTestSeFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeFac]
                    );

                    $allocateSeFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Senior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeFac);

                    $saveTestSeDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestSeDecla["total"] = count(
                        $saveTestSeDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeDecla]
                    );

                    $allocateSeDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeDecla);

                    $saveTestExFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertExFac->getInsertedId()
                        ),
                    ]);
                    $saveTestExFac["total"] = count($saveTestExFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertExFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestExFac]
                    );

                    $allocateExFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertExFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Expert",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateExFac);

                    $saveTestExDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertExDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestExDecla["total"] = count(
                        $saveTestExDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertExDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestExDecla]
                    );

                    $allocateExDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertExDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Expert",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateExDecla);

                    $success_msg = $success_tech;
                }
            } elseif ($profile == "Manager - Technicien") {
                if (!empty($managerId)) {
                    
                    $userManager = $users->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId($managerId),
                                "active" => true,
                            ],
                        ],
                    ]);

                    $personM = [
                        "users" => [],
                        "username" => $userName,
                        "matricule" => +$matriculation,
                        "firstName" => ucfirst($firstName),
                        "lastName" => ucfirst($lastName),
                        "email" => $emailAddress,
                        "phone" => $phone,
                        "gender" => $sex,
                        "level" => $niv,
                        "country" => $pays,
                        "profile" => "Manager",
                        "birthdate" => $birthDate,
                        "recrutmentDate" => $recrutementDate,
                        "certificate" => ucfirst($certificate), 
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($departement),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($fonction),
                        "password" => $password_hash,
                        "visiblePassword" => $passWord,
                        "manager" => new MongoDB\BSON\ObjectId($managerId),
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personM);
                    $users->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($managerId)],
                        [
                            '$push' => [
                                "users" => new MongoDB\BSON\ObjectId(
                                    $user->getInsertedId()
                                ),
                            ],
                        ]
                    );

                    $technician = $users->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);

                
                    // Assurez-vous que les variables sont définies et valides
                    if (isset($userManager['lastName'], $technician['firstName'], $technician['lastName'])) {
                        // Échapper les données pour éviter les problèmes de sécurité
                        $managerLastName = htmlspecialchars($userManager['lastName']);
                        $technicianFirstName = htmlspecialchars($technician['firstName']);
                        $technicianLastName = htmlspecialchars($technician['lastName']);

                        // Créer le message
                        $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                    <p>Votre collaborateur <strong>' . $technicianFirstName . ' ' . $technicianLastName . '</strong>
                                    a été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                    il est désormais possible pour vous de visualiser si les informations enregistrées sont correctes dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                    ainsi que de compléter ses <strong>QCM Tâches Professionnelles Manager</strong>.</p>
                                    <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                        // Sujet de l'e-mail
                        $subject = 'Confirmation d\'inscription  de ' . $technicianFirstName . ' ' . $technicianLastName.' sur la plateforme  « Mesure des Compétences des Techniciens »  Espace Technicien';

                        // Envoyer l'e-mail
                        $sendMail = sendMailRegisterUser($userManager['email'], $subject, $message);
                    }
                } else {
                    $personM = [
                        "users" => [],
                        "username" => $userName,
                        "matricule" => +$matriculation,
                        "firstName" => ucfirst($firstName),
                        "lastName" => ucfirst($lastName),
                        "email" => $emailAddress,
                        "phone" => $phone,
                        "gender" => $sex,
                        "level" => $niv,
                        "country" => $pays,
                        "profile" => "Manager",
                        "birthdate" => $birthDate,
                        "recrutmentDate" => $recrutementDate,
                        "certificate" => ucfirst($certificate), 
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($departement),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($fonction),
                        "password" => $password_hash,
                        "visiblePassword" => $passWord,
                        "manager" => "",
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personM);
                }
                if (isset($_POST["brandEx"])) {
                    for ($i = 0; $i < count($brandExpert); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandSenior" => $brandExpert[$i]
                                ],
                            ]
                        );
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brandExpert[$i]
                                ],
                            ]
                        );
                    }
                }
                if (isset($_POST["brandSe"])) {
                    for ($i = 0; $i < count($brandSenior); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brandSenior[$i]
                                ],
                            ]
                        );
                    }
                }
                if ($niv == "Junior") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandJunior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testFac);

                    $testDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandJunior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $success_msg = $success_tech;
                } elseif ($niv == "Senior") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testJuFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testJuFac);

                    $testJuDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testJuDecla);

                    $testSeFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeFac = $tests->insertOne($testSeFac);

                    $testSeDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeDecla = $tests->insertOne($testSeDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialitySenior']); ++$n) {
                        $specialityFacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['brandSenior']); ++$n) {
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }

                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $saveTestSeFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                    ]);
                    $saveTestSeFac["total"] = count($saveTestSeFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeFac]
                    );

                    $allocateSeFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Senior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeFac);

                    $saveTestSeDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestSeDecla["total"] = count(
                        $saveTestSeDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeDecla]
                    );

                    $allocateSeDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeDecla);

                    $success_msg = $success_tech;
                } elseif ($niv == "Expert") {
                    $person = $users->findOne(
                        ["_id" => new MongoDB\BSON\ObjectId($user->getInsertedId())],
                    );
                    $testJuFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertFac = $tests->insertOne($testJuFac);

                    $testJuDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertDecla = $tests->insertOne($testJuDecla);

                    $testSeFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Factuel",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeFac = $tests->insertOne($testSeFac);

                    $testSeDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $person['brandSenior'],
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertSeDecla = $tests->insertOne($testSeDecla);

                    $testExFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $brandExpert,
                        "type" => "Factuel",
                        "level" => "Expert",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertExFac = $tests->insertOne($testExFac);

                    $testExDecla = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "brand" => $brandExpert,
                        "type" => "Declaratif",
                        "level" => "Expert",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $insertExDecla = $tests->insertOne($testExDecla);

                    for ($n = 0; $n < count($person['brandJunior']); ++$n) {
                        $vehicleFacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacJu = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandJunior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Junior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacJu) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacJu->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacJu->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    for ($n = 0; $n < count($person['brandSenior']); ++$n) {
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Factuel"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $person['brandSenior'][$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialitySenior']); ++$n) {
                        $specialityFacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityFacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacSe = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialitySenior'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Senior"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacSe) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacSe->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertSeDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacSe->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    
                    for ($n = 0; $n < count($person['specialityExpert']); ++$n) {
                        $specialityFacEx = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialityExpert'][$n]],
                                ["type" => "Factuelle"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($specialityFacEx) {
                            for (
                                $a = 0;
                                $a < count($specialityFacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityFacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $specialityDeclacEx = $academy->specialities->findOne([
                            '$and' => [
                                ["label" => $person['specialityExpert'][$n]],
                                ["type" => "Declarative"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);

                        if ($specialityDeclacEx) {
                            for (
                                $a = 0;
                                $a < count($specialityDeclacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $specialityDeclacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }
                    for ($n = 0; $n < count($brandExpert); ++$n) {
                        $vehicleFacEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brandExpert[$n]],
                                ["type" => "Factuel"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleFacEx) {
                            for (
                                $a = 0;
                                $a < count($vehicleFacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExFac->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleFacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }

                        $vehicleDeclacEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brandExpert[$n]],
                                ["type" => "Declaratif"],
                                ["level" => "Expert"],
                                ["active" => true],
                            ],
                        ]);
                        if ($vehicleDeclacEx) {
                            for (
                                $a = 0;
                                $a < count($vehicleDeclacEx->quizzes);
                                ++$a
                            ) {
                                $tests->updateOne(
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $insertExDecla->getInsertedId()
                                        ),
                                    ],
                                    [
                                        '$addToSet' => [
                                            "quizzes" =>
                                                $vehicleDeclacEx->quizzes[$a],
                                        ],
                                    ]
                                );
                            }
                        }
                    }

                    $saveTestFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                    ]);
                    $saveTestFac["total"] = count($saveTestFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestFac]
                    );

                    $allocateFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Junior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateFac);

                    $saveTestDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestDecla["total"] = count($saveTestDecla["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestDecla]
                    );

                    $allocateDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Junior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateDecla);

                    $saveTestSeFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                    ]);
                    $saveTestSeFac["total"] = count($saveTestSeFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeFac]
                    );

                    $allocateSeFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Senior",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeFac);

                    $saveTestSeDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestSeDecla["total"] = count(
                        $saveTestSeDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertSeDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestSeDecla]
                    );

                    $allocateSeDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertSeDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Senior",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateSeDecla);

                    $saveTestExFac = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertExFac->getInsertedId()
                        ),
                    ]);
                    $saveTestExFac["total"] = count($saveTestExFac["quizzes"]);
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertExFac->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestExFac]
                    );

                    $allocateExFac = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertExFac->getInsertedId()
                        ),
                        "type" => "Factuel",
                        "level" => "Expert",
                        "activeTest" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateExFac);

                    $saveTestExDecla = $tests->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $insertExDecla->getInsertedId()
                        ),
                    ]);
                    $saveTestExDecla["total"] = count(
                        $saveTestExDecla["quizzes"]
                    );
                    $tests->updateOne(
                        [
                            "_id" => new MongoDB\BSON\ObjectId(
                                $insertExDecla->getInsertedId()
                            ),
                        ],
                        ['$set' => $saveTestExDecla]
                    );

                    $allocateExDecla = [
                        "user" => new MongoDB\BSON\ObjectId(
                            $user->getInsertedId()
                        ),
                        "test" => new MongoDB\BSON\ObjectId(
                            $insertExDecla->getInsertedId()
                        ),
                        "type" => "Declaratif",
                        "level" => "Expert",
                        "activeTest" => false,
                        "activeManager" => false,
                        "active" => false,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $allocations->insertOne($allocateExDecla);

                    $success_msg = $success_manager;
                }
            } elseif ($profile == "Manager") {
                $personM = [
                    "users" => [],
                    "username" => $userName,
                    "matricule" => +$matriculation,
                    "firstName" => ucfirst($firstName),
                    "lastName" => ucfirst($lastName),
                    "email" => $emailAddress,
                    "phone" => $phone,
                    "gender" => $sex,
                    "level" => "",
                    "country" => $pays,
                    "profile" => "Manager",
                    "birthdate" => $birthDate,
                    "recrutmentDate" => $recrutementDate,
                    "certificate" => ucfirst($certificate), 
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($departement),
                    "role" => ucfirst($fonction),
                    "password" => $password_hash,
                    "visiblePassword" => $passWord,
                    "test" => false,
                    "active" => true,
                    "created" => date("d-m-Y H:i:s"),
                ];
                $user = $users->insertOne($personM);

                if ($pays == 'Cameroun') {
                    $link = 'http://10.68.0.7/medacap/';
                } else {
                    $link = 'http://129.0.64.34/medacap';
                }   
            
                // Assurez-vous que les variables sont définies et valides
                if (isset($lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées de vos collaborateurs sont correctes dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que de compléter leurs <strong>QCM Tâches Professionnelles Manager</strong>.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $userName . '<br>
                                    <strong>Mot de passe : </strong>' . $passWord . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($emailAddress, $subject, $message);
                }
                $success_msg = $success_manager;
            } elseif ($profile == "Admin") {
                $personA = [
                    "users" => [],
                    "username" => $userName,
                    "matricule" => +$matriculation,
                    "firstName" => ucfirst($firstName),
                    "lastName" => ucfirst($lastName),
                    "email" => $emailAddress,
                    "phone" => $phone,
                    "gender" => $sex,
                    "level" => "",
                    "country" => $pays,
                    "profile" => $profile,
                    "birthdate" => $birthDate,
                    "recrutmentDate" => $recrutementDate,
                    "certificate" => ucfirst($certificate), 
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($departement),
                    "role" => ucfirst($fonction),
                    "password" => $password_hash,
                    "visiblePassword" => $passWord,
                    "active" => true,
                    "created" => date("d-m-Y H:i:s"),
                ];
                $users->insertOne($personA);
                
                if ($pays == 'Cameroun') {
                    $link = 'http://10.68.0.7/medacap/';
                } else {
                    $link = 'http://129.0.64.34/medacap';
                }   
            
                // Assurez-vous que les variables sont définies et valides
                if (isset($lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $userName . '<br>
                                    <strong>Mot de passe : </strong>' . $passWord . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($emailAddress, $subject, $message);
                }
                $success_msg = $success_admin;
            } elseif ($profile == "Ressource Humaine") {
                $personRh = [
                    "users" => [],
                    "username" => $userName,
                    "matricule" => +$matriculation,
                    "firstName" => ucfirst($firstName),
                    "lastName" => ucfirst($lastName),
                    "email" => $emailAddress,
                    "phone" => $phone,
                    "gender" => $sex,
                    "level" => "",
                    "country" => $pays,
                    "profile" => $profile,
                    "birthdate" => $birthDate,
                    "recrutmentDate" => $recrutementDate,
                    "certificate" => ucfirst($certificate), 
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($departement),
                    "role" => ucfirst($fonction),
                    "password" => $password_hash,
                    "visiblePassword" => $passWord,
                    "active" => true,
                    "created" => date("d-m-Y H:i:s"),
                ];
                $users->insertOne($personRh);
                
                if ($pays == 'Cameroun') {
                    $link = 'http://10.68.0.7/medacap/';
                } else {
                    $link = 'http://129.0.64.34/medacap';
                }   
            
                // Assurez-vous que les variables sont définies et valides
                if (isset($lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $userName . '<br>
                                    <strong>Mot de passe : </strong>' . $passWord . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($emailAddress, $subject, $message);
                }
                $success_msg = $success_rh;
            } elseif ($profile == "Directeur Pièce et Service" || $profile == "Directeur des Opérations" || $profile == "Directeur Général" || $profile == "Directeur Groupe") {
              $personD = [
                  "users" => [],
                  "username" => $userName,
                  "matricule" => +$matriculation,
                  "firstName" => ucfirst($firstName),
                  "lastName" => ucfirst($lastName),
                  "email" => $emailAddress,
                  "phone" => $phone,
                  "gender" => $sex,
                  "level" => "",
                  "country" => $pays,
                  "profile" => $profile,
                  "birthdate" => $birthDate,
                  "recrutmentDate" => $recrutementDate,
                  "certificate" => ucfirst($certificate), 
                  "subsidiary" => ucfirst($subsidiary),
                  "agency" => ucfirst($agency),
                  "department" => ucfirst($departement),
                  "role" => ucfirst($fonction),
                  "password" => $password_hash,
                  "visiblePassword" => $passWord,
                  "active" => true,
                  "created" => date("d-m-Y H:i:s"),
              ];
              $users->insertOne($personD);
                
                if ($pays == 'Cameroun') {
                    $link = 'http://10.68.0.7/medacap/';
                } else {
                    $link = 'http://129.0.64.34/medacap';
                }   
            
                // Assurez-vous que les variables sont définies et valides
                if (isset($lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $userName . '<br>
                                    <strong>Mot de passe : </strong>' . $passWord . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($emailAddress, $subject, $message);
                }
              $success_msg = $success_directeur;
          }
        }
    }
    ?>

<?php include_once "partials/header.php"; ?>

<!--begin::Title-->
<title><?php echo $title_addUser ?> | CFAO Mobility Academy</title>
<!--end::Title-->

<style>
/* Ensure input fields have a white background */
input,
select {
    background-color: #fff !important;
    border: 1px solid #ced4da;
    /* Adjust border color as needed */
}

/* Style the dropdown arrow and other aspects */
select {
    color: #495057;
    /* Adjust text color as needed */
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
}

/* Ensure the options within the dropdown have a white background */
select option {
    background-color: #fff !important;
    color: #495057;
}

/* Style for the placeholder text */
input::placeholder {
    color: #6c757d;
    /* Adjust placeholder text color as needed */
}

/* Style for the select placeholder */
select option:empty {
    color: #6c757d;
    /* Adjust placeholder text color as needed */
}

/* General styling for select elements */
.form-select {
    background-color: #fff !important;
    /* White background */
    color: #495057;
    /* Dark text color for readability */
    border: 1px solid #ced4da;
    /* Light grey border */
    border-radius: 0.25rem;
    /* Rounded corners */
    padding: 0.375rem 0.75rem;
    /* Padding for better appearance */
    font-size: 1rem;
    /* Font size */
    line-height: 1.5;
    /* Line height */
}

/* Ensure options have a white background and consistent text color */
.form-select option {
    background-color: #fff !important;
    color: #495057;
}

/* Placeholder styling */
.form-select::placeholder {
    color: #6c757d;
    /* Grey color for placeholder text */
}
</style>



<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content"
    data-select2-id="select2-data-kt_content">
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
        <!--begin::Container-->
        <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
            <!--begin::Modal body-->
            <div class="container mt-5 w-50">
                <img src="../../public/images/logo.png" alt="10" height="170"
                    style="display: block; max-width: 75%; height: auto; margin-left: 25px;">
                <h1 class='my-3 text-center'><?php echo $title_addUser ?></h1>

                <?php if (isset($success_msg)) { ?>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <center><strong><?php echo $success_msg; ?></strong></center>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;
                        </span>
                    </button>
                </div>
                <?php } ?>
                <?php if (isset($error_msg)) { ?>
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <center><strong><?php echo $error_msg; ?></strong></center>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;
                        </span>
                    </button>
                </div>
                <?php } ?>

                <form method='POST'><br>
                    <!--begin::Input group-->
                    <div class='row fv-row mb-7'>
                        <!--begin::Input group-->
                        <div class='row g-9 mb-7'>
                            <!--begin::Col-->
                            <div class='col-md-6 fv-row'>
                                <!--begin::Label-->
                                <label
                                    class='required form-label fw-bolder text-dark fs-6'><?php echo $prenoms ?></label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class='form-control form-control-solid' name='firstName' />
                                <?php if (isset($error)) { ?>
                                <span class='text-danger'>
                                    <?php echo $error; ?>
                                </span>
                                <?php } ?>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class='col-md-6 fv-row'>
                                <!--begin::Label-->
                                <label class='required form-label fw-bolder text-dark fs-6'
                                    style="margin-left: 35px;"><?php echo $noms ?></label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class='form-control form-control-solid' name='lastName'
                                    style="margin-left: 35px;" />
                                <!--end::Input-->
                                <?php if (isset($error)) { ?>
                                <span class='text-danger'>
                                    <?php echo $error; ?>
                                </span>
                                <?php } ?>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <style>
                        /* Ensure that columns stack on top of each other on smaller screens */
                        @media (max-width: 767.98px) {
                            .col-12 {
                                margin-bottom: 1rem;
                                /* Add some space between stacked columns */
                            }
                        }

                        /* Ensure labels and inputs are aligned properly */
                        .form-label {
                            display: block;
                            /* Ensure labels are block-level elements */
                            margin-bottom: 0.5rem;
                            /* Add space below labels */
                        }

                        .form-control {
                            width: 100%;
                            /* Ensure input fields take full width of the column */
                        }
                        </style>


                        <!--begin::Input group-->
                        <div class='fv-row mb-7'>
                            <!--begin::Label-->
                            <label class='required form-label fw-bolder text-dark fs-6'><?php echo $username ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type='text' class='form-control form-control-solid' name='username' />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class='fv-row mb-7'>
                            <!--begin::Label-->
                            <label class='required form-label fw-bolder text-dark fs-6'><?php echo $matricule ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type='text' class='form-control form-control-solid' name='matricule' />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class='d-flex flex-column mb-7 fv-row'>
                            <!--begin::Label-->
                            <label class='form-label fw-bolder text-dark fs-6'>
                                <span class='required'><?php echo $gender ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name='gender' aria-label='Select a Country' data-control='select2'
                                data-placeholder='Sélectionnez votre sexe...'
                                class='form-select form-select-solid fw-bold'>
                                <option disabled selected>-- <?php echo $select_gender ?> --</option>
                                <option value='Feminin'>
                                    <?php echo $female ?>
                                </option>
                                <option value='Masculin'>
                                    <?php echo $male ?>
                                </option>
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <?php if (isset($error)) { ?>
                        <span class='text-danger'>
                            <?php echo $error; ?>
                        </span>
                        <?php } ?>
                        <!--begin::Input group-->
                        <div class='fv-row mb-7'>
                            <!--begin::Label-->
                            <label class='form-label fw-bolder text-dark fs-6'>
                                <span><?php echo $email ?></span>
                                <span class='ms-1' data-bs-toggle='tooltip'
                                    title='Votre adresse email doit être active'>
                                    <i class='ki-duotone ki-information fs-7'><span class='path1'></span><span
                                            class='path2'></span><span class='path3'></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type='email' class='form-control form-control-solid' name='email' />
                            <!--end::Input-->
                            <?php if (isset($email_error)) { ?>
                            <span class='text-danger'>
                                <?php echo $email_error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class='fv-row mb-7'>
                            <!--begin::Label-->
                            <label
                                class='required form-label fw-bolder text-dark fs-6'><?php echo $phoneNumber ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type='text' class='form-control form-control-solid' name='phone' />
                            <!--end::Input-->
                            <?php if (isset($phone_error)) { ?>
                            <span class='text-danger'>
                                <?php echo $phone_error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class='fv-row mb-15'>
                            <!--begin::Label-->
                            <label class='form-label fw-bolder text-dark fs-6'><?php echo $birthdate ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type='date' class='form-control form-control-solid' name='birthdate' />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class='d-flex flex-column mb-7 fv-row'>
                            <!--begin::Label-->
                            <label class='form-label fw-bolder text-dark fs-6'>
                                <span class='required'><?php echo "Pays"; ?></span> <span class='ms-1'
                                    data-bs-toggle='tooltip' title="Votre pays d'origine">
                                    <i class='ki-duotone ki-information fs-7'><span class='path1'></span><span
                                            class='path2'></span><span class='path3'></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <!--begin::Input group-->
                            <select name='country' aria-label='Sélectionner un pays' data-control='select2'
                                data-placeholder='<?php echo htmlspecialchars($select_country, ENT_QUOTES, 'UTF-8'); ?>'
                                class='form-select form-select-solid fw-bold'>
                                <option disabled selected>
                                    -- <?php echo htmlspecialchars($select_country, ENT_QUOTES, 'UTF-8'); ?> --</option>
                                <!-- Africa -->
                                <optgroup label="Afrique">
                                    <option value='Afrique du Sud'>Afrique du Sud</option>
                                    <option value='Algérie'>Algérie</option>
                                    <option value='Angola'>Angola</option>
                                    <option value='Benin'>Bénin</option>
                                    <option value='Botswana'>Botswana</option>
                                    <option value='Burkina Faso'>Burkina Faso</option>
                                    <option value='Burundi'>Burundi</option>
                                    <option value='Cabo Verde'>Cabo Verde</option>
                                    <option value='Cameroun'>Cameroun</option>
                                    <option value='RCA'>République Centrafricaine</option>
                                    <option value='Chad'>Tchad</option>
                                    <option value='Comores'>Comores</option>
                                    <option value='Congo'>Congo</option>
                                    <option value='RDC'>République Démocratique du Congo</option>
                                    <option value='Djibouti'>Djibouti</option>
                                    <option value='Égypte'>Égypte</option>
                                    <option value='Érythrée'>Érythrée</option>
                                    <option value='Eswatini'>Eswatini</option>
                                    <option value='Éthiopie'>Éthiopie</option>
                                    <option value='Gabon'>Gabon</option>
                                    <option value='Gambie'>Gambie</option>
                                    <option value='Ghana'>Ghana</option>
                                    <option value='Guinee'>Guinée</option>
                                    <option value='Guinee-Bissau'>Guinée-Bissau</option>
                                    <option value="Cote d'Ivoire">Côte d'Ivoire</option>
                                    <option value='Kenya'>Kenya</option>
                                    <option value='Lesotho'>Lesotho</option>
                                    <option value='Liberia'>Liberia</option>
                                    <option value='Libye'>Libye</option>
                                    <option value='Madagascar'>Madagascar</option>
                                    <option value='Malawi'>Malawi</option>
                                    <option value='Mali'>Mali</option>
                                    <option value='Maurice'>Île Maurice</option>
                                    <option value='Mauritanie'>Mauritanie</option>
                                    <option value='Maroc'>Maroc</option>
                                    <option value='Mozambique'>Mozambique</option>
                                    <option value='Namibie'>Namibie</option>
                                    <option value='Niger'>Niger</option>
                                    <option value='Nigeria'>Nigéria</option>
                                    <option value='Rwanda'>Rwanda</option>
                                    <option value='Sao Tomé-et-Principe'>Sao Tomé-et-Principe</option>
                                    <option value='Senegal'>Sénégal</option>
                                    <option value='Seychelles'>Seychelles</option>
                                    <option value='Sierra Leone'>Sierra Leone</option>
                                    <option value='Somalie'>Somalie</option>
                                    <option value='Soudan'>Soudan</option>
                                    <option value='Soudan du Sud'>Soudan du Sud</option>
                                    <option value='Tanzanie'>Tanzanie</option>
                                    <option value='Togo'>Togo</option>
                                    <option value='Tunisie'>Tunisie</option>
                                    <option value='Zambie'>Zambie</option>
                                    <option value='Zimbabwe'>Zimbabwe</option>
                                </optgroup>
                                <!-- America -->
                                <optgroup label="Amérique">
                                    <option value='Antigua-et-Barbuda'>Antigua-et-Barbuda</option>
                                    <option value='Argentine'>Argentine</option>
                                    <option value='Bahamas'>Bahamas</option>
                                    <option value='Barbade'>Barbade</option>
                                    <option value='Belize'>Belize</option>
                                    <option value='Bolivie'>Bolivie</option>
                                    <option value='Brésil'>Brésil</option>
                                    <option value='Canada'>Canada</option>
                                    <option value='Chili'>Chili</option>
                                    <option value='Colombie'>Colombie</option>
                                    <option value='Costa Rica'>Costa Rica</option>
                                    <option value='Cuba'>Cuba</option>
                                    <option value='Dominique'>Dominique</option>
                                    <option value='Équateur'>Équateur</option>
                                    <option value='El Salvador'>El Salvador</option>
                                    <option value='Grenade'>Grenade</option>
                                    <option value='Guatemala'>Guatemala</option>
                                    <option value='Guyana'>Guyana</option>
                                    <option value='Haïti'>Haïti</option>
                                    <option value='Honduras'>Honduras</option>
                                    <option value='Jamaïque'>Jamaïque</option>
                                    <option value='Mexique'>Mexique</option>
                                    <option value='Nicaragua'>Nicaragua</option>
                                    <option value='Panama'>Panama</option>
                                    <option value='Paraguay'>Paraguay</option>
                                    <option value='Pérou'>Pérou</option>
                                    <option value='République dominicaine'>République dominicaine</option>
                                    <option value='Saint-Christophe-et-Niévès'>Saint-Christophe-et-Niévès</option>
                                    <option value='Saint-Marin'>Saint-Marin</option>
                                    <option value='Saint-Vincent-et-les-Grenadines'>Saint-Vincent-et-les-Grenadines
                                    </option>
                                    <option value='Suriname'>Suriname</option>
                                    <option value='Trinité-et-Tobago'>Trinité-et-Tobago</option>
                                    <option value='Uruguay'>Uruguay</option>
                                    <option value='Venezuela'>Venezuela</option>
                                </optgroup>


                                <!-- Asia -->
                                <optgroup label="Asie">
                                    <option value='Afghanistan'>Afghanistan</option>
                                    <option value='Arabie Saoudite'>Arabie Saoudite</option>
                                    <option value='Arménie'>Arménie</option>
                                    <option value='Azerbaïdjan'>Azerbaïdjan</option>
                                    <option value='Bahreïn'>Bahreïn</option>
                                    <option value='Bangladesh'>Bangladesh</option>
                                    <option value='Bhoutan'>Bhoutan</option>
                                    <option value='Brunei'>Brunei</option>
                                    <option value='Cambodge'>Cambodge</option>
                                    <option value='Chine'>Chine</option>
                                    <option value='Chypre'>Chypre</option>
                                    <option value='Corée du Nord'>Corée du Nord</option>
                                    <option value='Corée du Sud'>Corée du Sud</option>
                                    <option value='Émirats Arabes Unis'>Émirats Arabes Unis</option>
                                    <option value='Georgie'>Géorgie</option>
                                    <option value='Inde'>Inde</option>
                                    <option value='Indonésie'>Indonésie</option>
                                    <option value='Irak'>Irak</option>
                                    <option value='Iran'>Iran</option>
                                    <option value='Israël'>Israël</option>
                                    <option value='Japon'>Japon</option>
                                    <option value='Jordanie'>Jordanie</option>
                                    <option value='Kazakhstan'>Kazakhstan</option>
                                    <option value='Kuwait'>Kuwait</option>
                                    <option value='Kyrgyzstan'>Kyrgyzstan</option>
                                    <option value='Laos'>Laos</option>
                                    <option value='Liban'>Liban</option>
                                    <option value='Malaisie'>Malaisie</option>
                                    <option value='Maldives'>Maldives</option>
                                    <option value='Mongolie'>Mongolie</option>
                                    <option value='Myanmar'>Myanmar</option>
                                    <option value='Népal'>Népal</option>
                                    <option value='Oman'>Oman</option>
                                    <option value='Pakistan'>Pakistan</option>
                                    <option value='Palestine'>Palestine</option>
                                    <option value='Qatar'>Qatar</option>
                                    <option value='Sri Lanka'>Sri Lanka</option>
                                    <option value='Syrie'>Syrie</option>
                                    <option value='Tadjikistan'>Tadjikistan</option>
                                    <option value='Thaïlande'>Thaïlande</option>
                                    <option value='Timor oriental'>Timor oriental</option>
                                    <option value='Turkménistan'>Turkménistan</option>
                                    <option value='Turquie'>Turquie</option>
                                    <option value='Yémen'>Yémen</option>
                                </optgroup>

                                <!-- Europe -->
                                <optgroup label="Europe">
                                    <option value='Albanie'>Albanie</option>
                                    <option value='Andorre'>Andorre</option>
                                    <option value='Autriche'>Autriche</option>
                                    <option value='Belgique'>Belgique</option>
                                    <option value='Bulgarie'>Bulgarie</option>
                                    <option value='Chypre'>Chypre</option>
                                    <option value='Croatie'>Croatie</option>
                                    <option value='Danemark'>Danemark</option>
                                    <option value='Espagne'>Espagne</option>
                                    <option value='Estonie'>Estonie</option>
                                    <option value='Finlande'>Finlande</option>
                                    <option value='France'>France</option>
                                    <option value='Grèce'>Grèce</option>
                                    <option value='Hongrie'>Hongrie</option>
                                    <option value='Irlande'>Irlande</option>
                                    <option value='Islande'>Islande</option>
                                    <option value='Italie'>Italie</option>
                                    <option value='Lettonie'>Lettonie</option>
                                    <option value='Lituanie'>Lituanie</option>
                                    <option value='Luxembourg'>Luxembourg</option>
                                    <option value='Malte'>Malte</option>
                                    <option value='Monaco'>Monaco</option>
                                    <option value='Norvège'>Norvège</option>
                                    <option value='Pays-Bas'>Pays-Bas</option>
                                    <option value='Pologne'>Pologne</option>
                                    <option value='Portugal'>Portugal</option>
                                    <option value='République tchèque'>République tchèque</option>
                                    <option value='Roumanie'>Roumanie</option>
                                    <option value='Royaume-Uni'>Royaume-Uni</option>
                                    <option value='Slovaquie'>Slovaquie</option>
                                    <option value='Slovénie'>Slovénie</option>
                                </optgroup>

                                <!-- Oceania -->
                                <optgroup label="Océanie">
                                    <option value='Australie'>Australie</option>
                                    <option value='Fidji'>Fidji</option>
                                    <option value='Kiribati'>Kiribati</option>
                                    <option value='Marshall'>Îles Marshall</option>
                                    <option value='Micronésie'>Micronésie</option>
                                    <option value='Nauru'>Nauru</option>
                                    <option value='Nouvelle-Zélande'>Nouvelle-Zélande</option>
                                    <option value='Palau'>Palau</option>
                                    <option value='Papouasie-Nouvelle-Guinée'>Papouasie-Nouvelle-Guinée</option>
                                    <option value='Samoa'>Samoa</option>
                                    <option value='Samoa américaines'>Samoa américaines</option>
                                    <option value=' Tonga'>Tonga</option>
                                    <option value='Tuvalu'>Tuvalu</option>
                                    <option value='Vanuatu'>Vanuatu</option>
                                </optgroup>
                            </select>
                            <!--end::Input group-->

                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $profil ?></span> <span class='ms-1'
                                    data-bs-toggle='tooltip' title="Choississez le profile de l' utilisateur">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="profile" aria-label="Select a Country"
                                data-placeholder="<?php echo $select_profil ?>"
                                class="form-select form-select-solid fw-bold">
                                <option disabled selected>-- <?php echo $select_profil ?> --</option>
                                <?php foreach ($profileOptions as $option) { 
                                    if ($option['name'] != 'Super Admin') { ?>
                                <option value="<?php echo $option['name'] ?>">
                                    <?php echo $option['name'] ?>
                                </option>
                                <?php } } ?>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $levelTech ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="form-check" style="margin-top: 10px">
                                <input class="form-check-input" onclick="checkedRa()" type="radio" name="level"
                                    value="Junior" id="junior">
                                <label class="form-check-label text-black">
                                    <?php echo $junior ?> (<?php echo $maintenance ?>)
                                </label>
                            </div>
                            <!--begin::Input group-->
                            <div class="form-check" style="margin-top: 10px">
                                <input class="form-check-input" onclick="checkedRa()" type="radio" name="level"
                                    value="Senior" id="senior">
                                <label class="form-check-label text-black">
                                    <?php echo $senior ?> (<?php echo $reparation ?>)
                                </label>
                            </div>
                            <!--begin::Input group-->
                            <div class="form-check" style="margin-top: 10px">
                                <input class="form-check-input" onclick="checkedRa()" type="radio" name="level"
                                    value="Expert" id="expert">
                                <label class="form-check-label text-black">
                                    <?php echo $expert ?> (<?php echo $diagnostic ?>)
                                </label>
                            </div>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $filiale ?></span> <span class="ms-1"
                                    data-bs-toggle="tooltip" title="<?php echo $select_subsidiary ?>">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="subsidiary" aria-label="Select a Country"
                                data-placeholder="<?php echo $select_subsidiary ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_subsidiary ?> --</option>
                                <option value="CAMEROON MOTORS INDUSTRIES">
                                    <?php echo $cami ?>
                                </option>
                                <option value="CFAO MOTORS BENIN">
                                    <?php echo $cfao_benin ?>
                                </option>
                                <option value="CFAO MOTORS BURKINA">
                                    <?php echo $cfao_burkina ?>
                                </option>
                                <option value="CFAO MOTORS CENTRAFRIQUE">
                                    <?php echo $cfao_centrafrique ?>
                                </option>
                                <option value="CFAO MOTORS CONGO">
                                    <?php echo $cfao_congo ?>
                                </option>
                                <option value="CFAO MOTORS COTE D'IVOIRE">
                                    <?php echo $cfao_cote_divoire ?>
                                </option>
                                <option value="CFAO MOTORS GABON">
                                    <?php echo $cfao_gabon ?>
                                </option>
                                <option value="CFAO (GAMBIA) LIMITED">
                                    <?php echo $cfao_gambia ?>
                                </option>
                                <option value="CFAO MOTORS GHANA">
                                    <?php echo $cfao_ghana ?>
                                </option>
                                <option value="CFAO MOTORS GUINEE">
                                    <?php echo $cfao_guinee ?>
                                </option>
                                <option value="CFAO MOTORS GUINEE BISSAU">
                                    <?php echo $cfao_guinee_bissau ?>
                                </option>
                                <option value="CFAO MOTORS GUINEA EQUATORIAL">
                                    <?php echo $cfao_guinee_equatorial ?>
                                </option>
                                <option value="CFAO MOTORS MADAGASCAR">
                                    <?php echo $cfao_madagascar ?>
                                </option>
                                <option value="CFAO MOTORS MALI">
                                    <?php echo $cfao_mali ?>
                                </option>
                                <option value="CFAO MOTORS NIGER">
                                    <?php echo $cfao_niger ?>
                                </option>
                                <option value="CFAO MOTORS NIGERIA">
                                    <?php echo $cfao_nigeria ?>
                                </option>
                                <option value="CFAO MOTORS RDC">
                                    <?php echo $cfao_rdc ?>
                                </option>
                                <option value="CFAO MOTORS SENEGAL">
                                    <?php echo $cfao_senegal ?>
                                </option>
                                <option value="CFAO MOTORS TCHAD">
                                    <?php echo $cfao_tchad ?>
                                </option>
                                <option value="CFAO MOTORS TOGO">
                                    <?php echo $cfao_togo ?>
                                </option>
                                <option value="COMPAGNIE MAURITANIENNE DE DISTRIBUTION AUTOMOBILE">
                                    <?php echo $cfao_cmda ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required form-label fw-bolder text-dark fs-6"><?php echo $agence ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" name="agency" />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $department ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <select onchange="enableBrand(this)" name="department" aria-label="Select a Country"
                                data-placeholder="<?php echo $select_department ?>"
                                class="form-select form-select-solid fw-bold">
                                <option disabled selected>-- <?php echo $select_department ?> --</option>
                                <option value="Equipment">
                                    Equipment
                                </option>
                                <option value="Motors">
                                    Motors
                                </option>
                                <option value="Equipment & Motors">
                                    Equipment & Motors
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px"
                            id="brandEquipmentJu">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $junior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandJu[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandMotorsJu">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $junior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandJu[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandEqMoJu">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $junior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandJu[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px"
                            id="brandEquipmentSe">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $senior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandSe[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandMotorsSe">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $senior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandSe[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandEqMoSe">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $senior ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandSe[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" id="metierSe">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $Speciality ?> <?php echo $senior ?></span> <span
                                    class="ms-1" data-bs-toggle="tooltip"
                                    title="Choississez la spécialité du collaborateur">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="specialitySenior[]" multiple aria-label="Select a Country"
                                data-control="select2" data-placeholder="<?php echo $select_speciality ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="Electricté">
                                    <?php echo $elec ?>
                                </option>
                                <option value="Hydraulique">
                                    <?php echo $hydraulique ?>
                                </option>
                                <option value="Moteur">
                                    <?php echo $moteur ?>
                                </option>
                                <option value="Transmission">
                                    <?php echo $transmission ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px"
                            id="brandEquipmentEx">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $expert ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandEx[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandMotorsEx">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $expert ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandEx[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" style="margin-top: 10px" id="brandEqMoEx">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $brand ?> <?php echo $expert ?></span>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="brandEx[]" multiple aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_brand ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="" disabled selected>-- <?php echo $select_brand ?> --</option>
                                <option value="FUSO">
                                    <?php echo $fuso ?>
                                </option>
                                <option value="HINO">
                                    <?php echo $hino ?>
                                </option>
                                <option value="JCB">
                                    <?php echo $jcb ?>
                                </option>
                                <option value="KING LONG">
                                    <?php echo $kingLong ?>
                                </option>
                                <option value="LOVOL">
                                    <?php echo $lovol ?>
                                </option>
                                <option value="MERCEDES TRUCK">
                                    <?php echo $mercedesTruck ?>
                                </option>
                                <option value="RENAULT TRUCK">
                                    <?php echo $renaultTruck ?>
                                </option>
                                <option value="SINOTRUCK">
                                    <?php echo $sinotruk ?>
                                </option>
                                <option value="TOYOTA BT">
                                    <?php echo $toyotaBt ?>
                                </option>
                                <option value="TOYOTA FORKLIFT">
                                    <?php echo $toyotaForklift ?>
                                </option>
                                <option value="BYD">
                                    <?php echo $byd ?>
                                </option>
                                <option value="CITROEN">
                                    <?php echo $citroen ?>
                                </option>
                                <option value="MERCEDES">
                                    <?php echo $mercedes ?>
                                </option>
                                <option value="MITSUBISHI">
                                    <?php echo $mitsubishi ?>
                                </option>
                                <option value="PEUGEOT">
                                    <?php echo $peugeot ?>
                                </option>
                                <option value="SUZUKI">
                                    <?php echo $suzuki ?>
                                </option>
                                <option value="TOYOTA">
                                    <?php echo $toyota ?>
                                </option>
                                <option value="YAMAHA BATEAU">
                                    <?php echo $yamahaBateau ?>
                                </option>
                                <option value="YAMAHA MOTO">
                                    <?php echo $yamahaMoto ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row d-none" id="metierEx">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class="required"><?php echo $Speciality ?> <?php echo $expert ?></span> <span
                                    class="ms-1" data-bs-toggle="tooltip"
                                    title="Choississez la spécialité du collaborateur">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                </span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="specialityExpert[]" multiple aria-label="Select a Country"
                                data-control="select2" data-placeholder="<?php echo $select_speciality ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value="Electricté et Electronique">
                                    <?php echo $elec ?>
                                </option>
                                <option value="Hydraulique">
                                    <?php echo $hydraulique ?>
                                </option>
                                <option value="Moteur">
                                    <?php echo $moteur ?>
                                </option>
                                <option value="Transmission">
                                    <?php echo $transmission ?>
                                </option>
                            </select>
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label
                                class="required form-label fw-bolder text-dark fs-6"><?php echo $certificat ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" name="certificate" />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required form-label fw-bolder text-dark fs-6"><?php echo $role ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid fw-bold" name="role" />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label
                                class="required form-label fw-bolder text-dark fs-6"><?php echo $recrutmentDate ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" class="form-control form-control-solid" name="recrutmentDate" />
                            <!--end::Input-->
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <!-- <div class="mb-10 fv-row" data-kt-password-meter="true"> -->
                        <!--begin::Wrapper-->
                        <!-- <div class="mb-1"> -->
                        <!--begin::Label-->
                        <!-- <label class="required form-label fw-bolder text-dark fs-6"><?php echo $password ?></label> -->
                        <!--end::Label-->
                        <!--begin::Input wrapper-->
                        <!-- <div class="position-relative mb-3">
                  <input class="form-control form-control-solid" type="password" name="password" autocomplete="off" />
                </div> -->
                        <!--end::Input wrapper-->
                        <!-- <?php if (isset($password_error)) { ?>
                <span class="text-danger">
                  <?php echo $password_error; ?>
                </span>
                <?php } ?>
                <?php if (isset($error)) { ?>
                <span class='text-danger'>
                  <?php echo $error; ?>
                </span>
                <?php } ?> -->
                        <!--begin::Meter-->
                        <!-- <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                  </div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                  </div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                  </div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px">
                  </div>
                </div> -->
                        <!--end::Meter-->
                        <!-- </div> -->
                        <!--end::Wrapper-->
                        <!--begin::Hint-->
                        <!-- <div class="text-muted"><?php echo $password_text ?></div> -->
                        <!--end::Input group-->
                        <!-- </div> -->
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="form-label fw-bolder text-dark fs-6">
                                <span class=""><?php echo $manager ?></span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Choississez le manager de cet technicien et uniquement quand le profil est technicien">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                </span>
                            </label>
                            <select name="manager" aria-label="Select a Country" data-control="select2"
                                data-placeholder="<?php echo $select_manager ?>"
                                class="form-select form-select-solid fw-bold">
                                <option value=""><?php echo $select_manager ?></option>
                                <?php
                  if ($_SESSION['profile'] == "Admin") {
                      $managers = $users->find([
                          '$and' => [
                              ["profile" => "Manager"],
                              ["subsidiary" => $_SESSION['subsidiary']],
                              ["active" => true],
                          ],
                      ]);
                  }
                  if ($_SESSION['profile'] == "Super Admin") {
                      $managers = $users->find([
                          '$and' => [
                              ["profile" => "Manager"],
                              ["active" => true],
                          ],
                      ]);
                  }
                  foreach ($managers as $manager) { ?>
                                <option value='<?php echo $manager->_id; ?>'>
                                    <?php echo $manager->firstName; ?> <?php echo $manager->lastName; ?>
                                </option>
                                <?php }
                  ?>
                            </select>
                            <?php if (isset($error)) { ?>
                            <span class='text-danger'>
                                <?php echo $error; ?>
                            </span>
                            <?php } ?>
                        </div>
                        <!--end::Input group-->
                        <!--end::Scroll-->
                        <!--end::Modal body-->
                        <!--begin::Modal footer-->
                        <div class=" modal-footer flex-center">
                            <!--begin::Button-->
                            <button type="submit" name="submit" class="btn btn-primary">
                                <span class="indicator-label">
                                    <?php echo $valider ?>
                                </span>
                                <span class="indicator-progress">
                                    Patientez... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Button-->
                        </div>
                        <!--end::Modal footer-->
                    </div>
                </form>
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Body-->
<?php include "./partials/footer.php"; ?>
<?php
}
?>

<script>
function enableBrand(answer) {
    checkedRa(answer.value);
}

function checkedRa(departValue) {
    var metierSe = document.querySelector('#metierSe');
    var metierEx = document.querySelector('#metierEx');
    var brandMotorsJu = document.querySelector('#brandMotorsJu');
    var brandEquipmentJu = document.querySelector('#brandEquipmentJu');
    var brandEqMoJu = document.querySelector('#brandEqMoJu');
    var brandMotorsSe = document.querySelector('#brandMotorsSe');
    var brandEquipmentSe = document.querySelector('#brandEquipmentSe');
    var brandEqMoSe = document.querySelector('#brandEqMoSe');
    var brandMotorsEx = document.querySelector('#brandMotorsEx');
    var brandEquipmentEx = document.querySelector('#brandEquipmentEx');
    var brandEqMoEx = document.querySelector('#brandEqMoEx');

    var junior = document.querySelector('#junior');
    var senior = document.querySelector('#senior');
    var expert = document.querySelector('#expert');
    if (junior.checked) {
        if (departValue == 'Motors') {
            brandMotorsJu.classList.remove('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.remove('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment & Motors') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.remove('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        }
    } else if (senior.checked) {
        if (departValue == 'Motors') {
            brandMotorsJu.classList.remove('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.remove('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.remove('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.remove('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment & Motors') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.remove('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.remove('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        }
    } else if (expert.checked) {
        if (departValue == 'Motors') {
            brandMotorsJu.classList.remove('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.remove('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.remove('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.remove('d-none');
            brandEqMoJu.classList.add('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.remove('d-none');
            brandEqMoSe.classList.add('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.remove('d-none');
            brandEqMoEx.classList.add('d-none');
        } else if (departValue == 'Equipment & Motors') {
            brandMotorsJu.classList.add('d-none');
            brandEquipmentJu.classList.add('d-none');
            brandEqMoJu.classList.remove('d-none');
            brandMotorsSe.classList.add('d-none');
            brandEquipmentSe.classList.add('d-none');
            brandEqMoSe.classList.remove('d-none');
            brandMotorsEx.classList.add('d-none');
            brandEquipmentEx.classList.add('d-none');
            brandEqMoEx.classList.remove('d-none');
        }
    }
    if (senior.checked) {
        metierSe.classList.remove('d-none');
        metierEx.classList.add('d-none');
    } else if (expert.checked) {
        metierSe.classList.remove('d-none');
        metierEx.classList.remove('d-none');
    } else {
        metierSe.classList.add('d-none');
        metierEx.classList.add('d-none');
    }
}

// Function to handle closing of the alert message
document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('.alert .close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            alert.remove();
        });
    });
});
</script>