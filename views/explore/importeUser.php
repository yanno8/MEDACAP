<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["id"])) {
    header("Location: ../../");
    exit();
} else {
    require_once "../../vendor/autoload.php";
    include_once "../sendMail.php";

    if (isset($_POST["submit"])) {
        // Create connection
        $conn = new MongoDB\Client("mongodb://localhost:27017");

        // Connecting in database
        $academy = $conn->academy;

        // Connecting in collections
        $users = $academy->users;
        $vehicles = $academy->vehicles;
        $tests = $academy->tests;
        $allocations = $academy->allocations;

        $filePath = $_FILES["excel"]["tmp_name"];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $data = $spreadsheet->getActiveSheet()->toArray();

        // remove header
        unset($data[0]);

        foreach ($data as $row) {
            $brandJunior = [];
            $brandSenior = [];
            $brandExpert = [];
            
            $specialitySenior = [];
            $specialityExpert = [];

            $username = $row["0"];
            $matricule = $row["1"];
            $firstName = $row["2"];
            $lastName = $row["3"];
            $email = $row["4"];
            $phone = $row["5"];
            $gender = $row["6"];
            $birthdate = date($row["7"]);
            $level = $row["8"];
            $country = $row["9"];
            $profile = $row["10"];
            $certificate = $row["11"];
            $subsidiary = $row["12"];
            $agency = $row["13"];
            $department = $row["14"];
            $role = $row["15"];
            $recrutmentDate = date($row["16"]);
            $usernameManager = $row["17"];
            $password = sha1($row["18"]);
            $brandJunior = array_map('strtoupper', explode(",", $row["19"] ?? ''));
            $brandSenior = array_map('strtoupper', explode(",", $row["20"] ?? ''));
            $brandExpert = array_map('strtoupper', explode(",", $row["21"] ?? ''));
            $specialitySenior = array_map('trim', explode(",", $row["23"] ?? ''));
            $specialityExpert = array_map('trim', explode(",", $row["24"] ?? ''));

            $member = $users->findOne([
                '$and' => [["username" => $username], ["active" => true]],
            ]);
            if (isset($usernameManager)) {
                $manager = $users->findOne([
                    '$and' => [
                        [
                            "username" => $usernameManager,
                            "active" => true,
                        ],
                    ],
                ]);
            }
            if ($member) {
                $person = [
                    "matricule" => $matricule,
                    "firstName" => ucfirst($firstName),
                    "lastName" => strtoupper($lastName),
                    "email" => $email,
                    "phone" => $phone,
                    "gender" => $gender,
                    "level" => $level,
                    "country" => $country,
                    "profile" => $profile,
                    "birthdate" => $birthdate,
                    "recrutmentDate" => $recrutmentDate,
                    "certificate" => ucfirst($certificate),
                    "subsidiary" => strtoupper($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($department),
                    "role" => ucfirst($role),
                    "password" => $password,
                    "visiblePassword" => $row["18"],
                    "updated" => date("d-m-Y H:i:s"),
                ];
                $users->updateOne(
                    ["_id" => new MongoDB\BSON\ObjectId($member->_id)],
                    [
                        '$set' => $person
                    ]
                );
                if ($profile == "Technicien") {
                    $success_msg = $success_tech;
                }
                if ($profile == "Manager") {
                    $success_msg = $success_manager;
                }
                if ($profile == "Admin") {
                    $success_msg = $success_admin;
                }
            } elseif ($profile == "Technicien") {
                if (!empty($usernameManager)) {
                    
                    $userManager = $users->findone([
                        '$and' => [
                            [
                                "username" => $usernameManager,
                                "active" => true,
                            ],
                        ],
                    ]);

                    $person = [
                        "users" => [],
                        "username" => $username,
                        "matricule" => $matricule,
                        "firstName" => ucfirst($firstName),
                        "lastName" => strtoupper($lastName),
                        "email" => $email,
                        "phone" => $phone,
                        "gender" => $gender,
                        "level" => $level,
                        "country" => $country,
                        "profile" => $profile,
                        "birthdate" => $birthdate,
                        "recrutmentDate" => $recrutmentDate,
                        "certificate" => ucfirst($certificate),
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($department),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($role),
                        "password" => $password,
                        "visiblePassword" => $row["18"],
                        "manager" => new MongoDB\BSON\ObjectId($manager->_id),
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($person);

                    $users->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($manager->_id)],
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
                    $person = [
                        "users" => [],
                        "username" => $username,
                        "matricule" => $matricule,
                        "firstName" => ucfirst($firstName),
                        "lastName" => strtoupper($lastName),
                        "email" => $email,
                        "phone" => $phone,
                        "gender" => $gender,
                        "level" => $level,
                        "country" => $country,
                        "profile" => $profile,
                        "birthdate" => $birthdate,
                        "recrutmentDate" => $recrutmentDate,
                        "certificate" => ucfirst($certificate),
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($department),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($role),
                        "password" => $password,
                        "visiblePassword" => $_row["18"],
                        "manager" => "",
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($person);
                }
                if (isset($brandExpert)) {
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
                if (isset($brandSenior)) {
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
                if ($level == "Junior") {
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
                } elseif ($level == "Senior") {
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
                } elseif ($level == "Expert") {
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
                if (!empty($usernameManager)) {
                    
                    $userManager = $users->findone([
                        '$and' => [
                            [
                                "username" => $usernameManager,
                                "active" => true,
                            ],
                        ],
                    ]);
                    $personM = [
                        "users" => [],
                        "username" => $username,
                        "matricule" => $matricule,
                        "firstName" => ucfirst($firstName),
                        "lastName" => strtoupper($lastName),
                        "email" => $email,
                        "phone" => $phone,
                        "gender" => $gender,
                        "level" => $level,
                        "country" => $country,
                        "profile" => "Manager",
                        "birthdate" => $birthdate,
                        "recrutmentDate" => $recrutmentDate,
                        "certificate" => ucfirst($certificate),
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($department),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($role),
                        "password" => $password,
                        "visiblePassword" => $row["18"],
                        "manager" => new MongoDB\BSON\ObjectId($manager->_id),
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personM);
                    $users->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($manager->_id)],
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
                        "username" => $username,
                        "matricule" => $matricule,
                        "firstName" => ucfirst($firstName),
                        "lastName" => strtoupper($lastName),
                        "email" => $email,
                        "phone" => $phone,
                        "gender" => $gender,
                        "level" => $level,
                        "country" => $country,
                        "profile" => "Manager",
                        "birthdate" => $birthdate,
                        "recrutmentDate" => $recrutmentDate,
                        "certificate" => ucfirst($certificate),
                        "subsidiary" => ucfirst($subsidiary),
                        "agency" => ucfirst($agency),
                        "department" => ucfirst($department),
                        "brandJunior" => $brandJunior ?? [],
                        "brandSenior" => $brandSenior ?? [],
                        "brandExpert" => $brandExpert ?? [],
                        "specialitySenior" => $specialitySenior ?? [],
                        "specialityExpert" => $specialityExpert ?? [],
                        "role" => ucfirst($role),
                        "password" => $password,
                        "visiblePassword" => $row["18"],
                        "manager" => "",
                        "test" => true,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $user = $users->insertOne($personM);
                }
                if (isset($brandExpert)) {
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
                if (isset($brandSenior)) {
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
                if ($level == "Junior") {
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
                } elseif ($level == "Senior") {
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
                } elseif ($level == "Expert") {
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

                    $success_msg = $success_manager;
                }
            } elseif ($profile == "Manager") {
                $personM = [
                    "users" => [],
                    "username" => $username,
                    "matricule" => $matricule,
                    "firstName" => ucfirst($firstName),
                    "lastName" => strtoupper($lastName),
                    "email" => $email,
                    "phone" => $phone,
                    "gender" => $gender,
                    "level" => "",
                    "country" => $country,
                    "profile" => "Manager",
                    "birthdate" => $birthdate,
                    "recrutmentDate" => $recrutmentDate,
                    "certificate" => ucfirst($certificate),
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($department),
                    "role" => ucfirst($role),
                    "password" => $password,
                    "visiblePassword" => $row["18"],
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
                if (isset($email, $lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées de vos collaborateurs sont correctes dans l\'espace <strong>Liste des Utilisateurs</strong>, 
                                ainsi que de compléter leurs <strong>QCM Tâches Professionnelles Manager</strong>.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $username . '<br>
                                    <strong>Mot de passe : </strong>' . $row["18"] . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 300px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($email, $subject, $message);
                }
                $success_msg = $success_manager;
            } elseif ($profile == "Ressource Humaine") {
                $personRh = [
                    "users" => [],
                    "username" => $userName,
                    "matricule" => $matriculation,
                    "firstName" => ucfirst($firstName),
                    "lastName" => ucfirst($lastName),
                    "email" => $email,
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
                    "password" => $password,
                    "visiblePassword" => $row["18"],
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
                if (isset($email, $lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $username . '<br>
                                    <strong>Mot de passe : </strong>' . $row["18"] . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($email, $subject, $message);
                }
                $success_msg = $success_rh;
            } elseif ($profile == "Admin") {
                $personA = [
                    "users" => [],
                    "username" => $username,
                    "matricule" => $matricule,
                    "firstName" => ucfirst($firstName),
                    "lastName" => strtoupper($lastName),
                    "email" => $email,
                    "phone" => $phone,
                    "gender" => $gender,
                    "level" => $level,
                    "country" => $country,
                    "profile" => $profile,
                    "birthdate" => $birthdate,
                    "recrutmentDate" => $recrutmentDate,
                    "certificate" => ucfirst($certificate),
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($department),
                    "role" => ucfirst($role),
                    "password" => $password,
                    "visiblePassword" => $row["18"],
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
                if (isset($email, $lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $username . '<br>
                                    <strong>Mot de passe : </strong>' . $row["18"] . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($email, $subject, $message);
                }
                $success_msg = $success_admin;
            } elseif ($profile == "Directeur Pièce et Service" || $profile == "Directeur des Opérations" || $profile == "Directeur Général" || $profile == "Directeur Groupe") {
                $personD = [
                    "users" => [],
                    "username" => $username,
                    "matricule" => $matricule,
                    "firstName" => ucfirst($firstName),
                    "lastName" => strtoupper($lastName),
                    "email" => $email,
                    "phone" => $phone,
                    "gender" => $gender,
                    "level" => $level,
                    "country" => $country,
                    "profile" => $profile,
                    "birthdate" => $birthdate,
                    "recrutmentDate" => $recrutmentDate,
                    "certificate" => ucfirst($certificate),
                    "subsidiary" => ucfirst($subsidiary),
                    "agency" => ucfirst($agency),
                    "department" => ucfirst($department),
                    "role" => ucfirst($role),
                    "password" => $password,
                    "visiblePassword" => $row["18"],
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
                if (isset($email, $lastName)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($lastName);

                    // Créer le message
                    $message = '<p>Bonjour M.' . $managerLastName . ',</p>
                                <p>Vous avez été inscrit avec succès dans la plateforme de <strong>Mesure des Compétences de CFAO Mobility Academy</strong>, 
                                il est désormais possible pour vous de visualiser si les informations enregistrées des techniciens dans l\'espace <strong>Liste des Utilisateur</strong>, 
                                ainsi que leurs résultats dans l\'espace <strong>Résultats des Techniciens par niveau</strong> tout en faisant le suivi de l\'évolution des tests et QCM.</p>
                                <p>Vos accès : <br>
                                    <strong>Identifiant : </strong>' . $username . '<br>
                                    <strong>Mot de passe : </strong>' . $row["18"] . '<br>
                                    <strong>Lien de plateforme : </strong>' . $link . '<br>
                                </p>
                                <p style="font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation d\'inscription  de ' . $firstName . ' ' . $lastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailRegisterUser($email, $subject, $message);
                }
                $success_msg = $success_directeur;
            }
        }
    }
    ?>
<?php include_once "partials/header.php"; ?>
<style>
input {
    background-color: #fff ! important;
    border-style: solid;
}
</style>
<!--begin::Title-->
<title><?php echo $import_user ?> | CFAO Mobility Academy</title>
<!--end::Title-->


<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container-xxl">
            <!--begin::Modal body-->
            <div class="container mt-5 w-50 text-center">
                <img src="../../public/images/logo.png" alt="10" height="170" style="display: block; max-width: 75%; height: auto; margin-left: 25px;">
                <h1 class="my-3 text-center"><?php echo $import_user ?></h1>

                <?php if (isset($success_msg)) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <center><strong><?php echo $success_msg; ?></strong></center>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                <?php if (isset($error_msg)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <center><strong><?php echo $error_msg; ?></strong></center>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>

                <form enctype='multipart/form-data' method='POST'><br>
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <!--begin::Label-->
                        <label class="required form-label fw-bolder text-dark fs-6">Importer des utilisateurs via <?php echo $excel ?></label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div class="input-group">
                            <input type="file" class="form-control form-control-solid" placeholder="" name="excel" style="text-align: center;" />
                            <div class="input-group-append">
                                <span class="input-group-text" style = "height: 10px !important; padding: 15px;">.xlsx</span>
                            </div>
                        </div>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="submit" name="submit" class="btn btn-primary">
                            <span class="indicator-label">
                                <?php echo $valider ?>
                            </span>
                            <span class="indicator-progress">
                                Patientez... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Body-->

<script>
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
<?php include_once "partials/footer.php"; ?>

<?php
} ?>
