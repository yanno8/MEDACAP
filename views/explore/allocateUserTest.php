<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["id"])) {
    header("Location: ../../");
    exit();
} else {

    require_once "../../vendor/autoload.php";

    // Create connection
    $conn = new MongoDB\Client("mongodb://localhost:27017");

    // Connecting in database
    $academy = $conn->academy;

    // Connecting in collections
    $users = $academy->users;
    $vehicles = $academy->vehicles;
    $allocations = $academy->allocations;
    $tests = $academy->tests;
    $results = $academy->results;

    if (isset($_POST["submit"])) {
        if (isset($_POST["brand"])) {
            $brand = $_POST["brand"];
        }
        if (isset($_POST["level"])) {
            $level = $_POST["level"];
        }
        $technician = $_POST["technician"];

        if (!$brand || !$technician || !$level) {
            $error = $champ_obligatoire;
        } else {
            $user = $users->findOne([
                '$and' => [
                    ["_id" => new MongoDB\BSON\ObjectId($technician)],
                    ["active" => true],
                ],
            ]);
            $testExistFacJu = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Factuel'],
                    ["level" => 'Junior'],
                ],
            ]);
            $testExistFDeclaJu = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Declaratif'],
                    ["level" => 'Junior'],
                ],
            ]);
            $testExistFacSe = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Factuel'],
                    ["level" => 'Senior'],
                ],
            ]);
            $testExistFDeclaSe = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Declaratif'],
                    ["level" => 'Senior'],
                ],
            ]);
            $testExistFacEx = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Factuel'],
                    ["level" => 'Expert'],
                ],
            ]);
            $testExistFDeclaEx = $tests->findOne([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["type" => 'Declaratif'],
                    ["level" => 'Expert'],
                ],
            ]);
            $resultJu = $results->find([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["level" => "Junior"],
                    ["active" => true],
                ],
            ]);
            $resultSe = $results->find([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["level" => "Senior"],
                    ["active" => true],
                ],
            ]);
            $resultEx = $results->find([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                    ["level" => "Expert"],
                    ["active" => true],
                ],
            ]);
            if ($level == 'Junior') {
                foreach ($resultJu as $result) {
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
                        [
                            '$set' => [
                                "active" => false
                            ],
                        ]
                    );
                }
                if ($testExistFacJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFac = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFac['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFac['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => true
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacJu) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        "level" =>"Junior",
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFac['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => "Junior",
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brandJunior" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDecla = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDecla['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDecla['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                                ["type" => 'Declaratif'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDeclaJu) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        'level' => 'Junior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDecla['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Declaratif',
                        'level' => 'Junior',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                }
                $result = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Junior'],
                        ["active" => true],
                    ],
                ]);
                foreach ($result as $result) {
                    $result['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($result->_id)],
                        [
                            '$set' => $result
                        ]
                    );
                }
            } elseif ($level == 'Senior') {
                if ($testExistFacJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFac = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFac['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFac['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacJu) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        "level" => $vehicleFac->level,
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFac['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => $vehicleFac->level,
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDecla = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDecla['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDecla['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDeclaJu) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        'level' => 'Junior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDecla['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Declaratif',
                        'level' => 'Junior',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistFacSe) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Senior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFacSe['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFacSe['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacSe['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Senior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacSe) {
                    $testFacSe = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        'level' => 'Senior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertSe = $tests->insertOne($testFacSe);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFacSe['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateSe = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertSe->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => 'Senior',
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateSe);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaSe) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDeclaSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Senior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDeclaSe['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDeclaSe['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaSe['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Senior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDeclaSe) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        "level" => 'Senior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertSe = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDeclaSe['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateSe = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertSe->getInsertedId()),
                        'type' => 'Declaratif',
                        "level" => 'Senior',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateSe);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                }
                $result = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Junior'],
                        ["active" => true],
                    ],
                ]);
                foreach ($result as $result) {
                    $result['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($result->_id)],
                        [
                            '$set' => $result
                        ]
                    );
                }
                $resultSe = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Senior'],
                        ["active" => true],
                    ],
                ]);
                foreach ($resultSe as $resultSe) {
                    $resultSe['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($resultSe->_id)],
                        [
                            '$set' => $resultSe
                        ]
                    );
                }
            } else {
                if ($testExistFacJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFac = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFac['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFac['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacJu['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacJu) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        "level" => $vehicleFac->level,
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFac['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => $vehicleFac->level,
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaJu) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDecla = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Junior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDecla['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDecla['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaJu['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistDeclaJu['_id'])],
                                ["type" => 'Declaratif'],
                                ["level" => 'Junior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDecla) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        'level' => 'Junior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insert = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDecla['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocate = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
                        'type' => 'Declaratif',
                        'level' => 'Junior',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocate);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistFacSe) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFacSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Senior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFacSe['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFacSe['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacSe['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacSe['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Senior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacSe) {
                    $testFacSe = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        'level' => 'Senior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertSe = $tests->insertOne($testFacSe);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFacSe['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateSe = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertSe->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => 'Senior',
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateSe);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaSe) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDeclaSe = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Senior'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDeclaSe['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDeclaSe['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaSe['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistDeclaSe['_id'])],
                                ["type" => 'Declaratif'],
                                ["level" => 'Senior'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDeclaSe) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        "level" => 'Senior',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertSe = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDeclaSe['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertSe->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateSe = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertSe->getInsertedId()),
                        'type' => 'Declaratif',
                        "level" => 'Senior',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateSe);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistFacEx) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacEx['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleFacEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Factuel'],
                                ["level" => 'Expert'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleFacEx['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistFacEx['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleFacEx['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistFacEx['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistFacEx['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistFacEx['_id'])],
                                ["type" => 'Factuel'],
                                ["level" => 'Expert'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistFacEx) {
                    $testFacEx = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Factuel",
                        'level' => 'Expert',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertEx = $tests->insertOne($testFacEx);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleFacSe['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateEx = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertEx->getInsertedId()),
                        'type' => 'Factuel',
                        'level' => 'Expert',
                        'activeTest' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateEx);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                } elseif ($testExistDeclaEx) {
                    for ($i = 0; $i < count($brand); $i++) {
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaEx['_id'])],
                            [
                                '$push' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        $vehicleDeclaEx = $vehicles->findOne([
                            '$and' => [
                                ["brand" => $brand[$i]],
                                ["type" => 'Declaratif'],
                                ["level" => 'Expert'],
                                ["active" => true],
                            ],
                        ]);
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                        for ($i = 0; $i < count($vehicleDeclaEx['quizzes']); $i++) {
                            $tests->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaEx['_id'])],
                                [
                                    '$addToSet' => [
                                        "quizzes" => $vehicleDeclaEx['quizzes'][$i]
                                    ],
                                ]
                            );
                        }
                        $tests->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($testExistDeclaEx['_id'])],
                            [
                                '$set' => [
                                    "total" => count($testExistDeclaEx['quizzes'])
                                ],
                            ]
                        );
                        $allocate = $allocations->findOne([
                            '$and' => [
                                ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                                ["test" => new MongoDB\BSON\ObjectId($testExistDeclaEx['_id'])],
                                ["type" => 'Declaratif'],
                                ["level" => 'Expert'],
                            ],
                        ]);
                        if ($allocate['active'] == true) {
                            $allocations->updateOne(
                                ["_id" => new MongoDB\BSON\ObjectId($allocate['_id'])],
                                [
                                    '$set' => [
                                        "active" => false
                                    ],
                                ]
                            );
                        }
                    }
                } elseif (!$testExistDeclaEx) {
                    $testFac = [
                        "quizzes" => [],
                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                        "brand" => $brand,
                        "type" => "Declaratif",
                        "level" => 'Expert',
                        "total" => 0,
                        "active" => true,
                        "created" => date("d-m-y h:i:s"),
                    ];
                    $insertEx = $tests->insertOne($testFac);

                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                        [
                            '$addToSet' => [
                                "quizzes" => $vehicleDeclaEx['quizzes'][$i]
                            ],
                        ]
                    );
                    $test = $tests->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                            ["active" => true],
                        ],
                    ]);
                    $test['total'] = count($test['quizzes']);
                    $tests->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($insertEx->getInsertedId())],
                        [
                            '$set' => $test
                        ]
                    );
                    $allocateEx = [
                        'user' => new MongoDB\BSON\ObjectId($user['_id']),
                        'test' => new MongoDB\BSON\ObjectId($insertEx->getInsertedId()),
                        'type' => 'Declaratif',
                        "level" => 'Expert',
                        'activeTest' => false,
                        'activeManager' => false,
                        'active' => false,
                        'created' => date("d-m-y h:i:s"),
                    ];
                    $allocations->insertOne($allocateEx);
                    for ($i = 0; $i < count($brand); $i++) {
                        $users->updateOne(
                            ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
                            [
                                '$addToSet' => [
                                    "brand" => $brand[$i]
                                ],
                            ]
                        );
                    }
                }
                $result = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Junior'],
                        ["active" => true],
                    ],
                ]);
                foreach ($result as $result) {
                    $result['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($result->_id)],
                        [
                            '$set' => $result
                        ]
                    );
                }
                $resultSe = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Senior'],
                        ["active" => true],
                    ],
                ]);
                foreach ($resultSe as $resultSe) {
                    $resultSe['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($resultSe->_id)],
                        [
                            '$set' => $resultSe
                        ]
                    );
                }
                $resultEx = $results->find([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
                        ["level" => 'Expert'],
                        ["active" => true],
                    ],
                ]);
                foreach ($resultEx as $resultEx) {
                    $resultEx['active'] = false;
                    $results->updateOne(
                        ["_id" => new MongoDB\BSON\ObjectId($resultEx->_id)],
                        [
                            '$set' => $resultEx
                        ]
                    );
                }
            }
            $success_msg = $success_allocation;
        }
    }
    ?>

<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $title_allocateUserTest ?> | CFAO Mobility Academy</title>
<!--end::Title-->


<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content"
    data-select2-id="select2-data-kt_content">
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
        <!--begin::Container-->
        <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
            <!--begin::Modal body-->
            <div class="container mt-5 w-50">
                <img src="../../public/images/logo.png" alt="10" height="170" style="display: block; max-width: 75%; height: auto; margin-left: 25px;">
                <h1 class="my-3 text-center"><?php echo $allocate_test?></h1>

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

                <form method="POST"><br>
                <!--begin::Input group-->
                <div class='fv-row mb-7'>
                    <div class="d-flex flex-column mb-7 fv-row">
                        <!--begin::Label-->
                        <label class="form-label fw-bolder text-dark fs-6">
                            <span class="required"><?php echo $technicien ?></span>

                            <span class="ms-1" data-bs-toggle="tooltip" title="Veuillez choisir le technicien">
                                <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                        class="path2"></span><span class="path3"></span></i> </span>
                        </label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="technician" aria-label="Select a Country"
                            data-control="select2" data-placeholder=<?php echo $select_technicien ?>
                            class="form-select form-select-solid fw-bold">
                            <option value=""><?php echo $select_technicien ?></option>
                            <?php
                            $user = $users->find([
                                '$and' => [
                                    ["profile" => "Technicien"],
                                    ["active" => true],
                                ],
                            ]);
                            foreach ($user as $user) { ?>
                            <option value='<?php echo $user->_id; ?>'>
                                <?php echo $user->firstName; ?> <?php echo $user->lastName; ?>
                            </option>
                            <?php }
                            ?>
                        </select>
                        <?php if (isset($error)) { ?>
                        <span class='text-danger'>
                            <?php echo $error; ?>
                        </span>
                        <?php } ?>
                        <!--end::Input-->
                    </div>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                    <div class="row fv-row mb-7">
                        <!--begin::Col-->
                        <div class="col-xl-6">
                            <div class="d-flex flex-column mb-7 fv-row">
                                <!--begin::Label-->
                                <label class="form-label fw-bolder text-dark fs-6">
                                    <span class="required"><?php echo $brand ?></span>

                                    <span class="ms-1" data-bs-toggle="tooltip" title="Veuillez choisir le véhicule">
                                        <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span></i> </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select name="brand[]" multiple aria-label="Select a Country" data-control="select2"
                                    data-placeholder="<?php echo $select_brand ?>"
                                    class="form-select form-select-solid fw-bold">
                                    <option value=""><?php echo $select_brand ?></option>
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
                                    <option value="MUTSUBISHI">
                                      <?php echo $mutsubishi ?>
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
                                <?php if (isset($error)) { ?>
                                <span class='text-danger'>
                                    <?php echo $error; ?>
                                </span>
                                <?php } ?>
                                <!--end::Input-->
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-xl-6">
                            <div class="d-flex flex-column mb-7 fv-row">
                                <!--begin::Label-->
                                <label class="form-label fw-bolder text-dark fs-6">
                                    <span class="required"><?php echo $Level ?></span>

                                    <span class="ms-1" data-bs-toggle="tooltip" title="Veuillez choisir le technicien">
                                        <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span></i> </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select name="technician" aria-label="Select a Country"
                                    data-control="select2" data-placeholder=<?php echo $select_level ?>
                                    class="form-select form-select-solid fw-bold">
                                    <option value=""><?php echo $select_level ?></option>
                                    <option value='Junior'>
                                        <?php echo $junior ?> (<?php echo $maintenance ?>)
                                    </option>
                                    <option value='Senior'>
                                        <?php echo $senior ?> (<?php echo $reparation ?>)
                                    </option>
                                    <option value='Expert'>
                                        <?php echo $expert ?> (<?php echo $diagnostic ?>)
                                    </option>
                                </select>
                                <?php if (isset($error)) { ?>
                                <span class='text-danger'>
                                    <?php echo $error; ?>
                                </span>
                                <?php } ?>
                                <!--end::Input-->
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="text-center" style="margin-bottom: 50px;">
                        <button type="submit" name="submit" class="btn btn-lg btn-primary">
                            <?php echo $valider; ?>
                        </button>
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
