<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["profile"])) {
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
    $trainings = $academy->trainings;
    $allocations = $academy->allocations;
    $applications = $academy->applications;
    $validations = $academy->validations;

    $levelFilter = $_GET["level"];
    $techFilter = $_GET['user'];
    $brandFilter = $_GET['brand'];
    $trainingFilter = $_GET['training'];

    if ($_SESSION['profile'] == 'Manager') {
        $filter = [
            'manager' => new MongoDB\BSON\ObjectId($_SESSION["id"]),
            'profile' => 'Technicien',
            'active' => true
        ];
    } else {
        $filter = [
            'subsidiary' => $_SESSION['subsidiary'],
            'profile' => 'Technicien',
            'active' => true
        ];
    }

    if($brandFilter != 'all') {
        $filter['brandJunior'] = $brandFilter;
    }
    
    $technicians = $users->find($filter)->toArray();

    $levelTechs = [];
    $technicianIds = [];
    foreach ($technicians as $technician) {
        $levelTechs[] = $technician['level'];
        $technicianIds[] = (string)$technician['_id'];
    }
    
    // Supprimer les doublons
    $levelTechs = array_unique($levelTechs);
    
    // ----------------------------------------------------------
    // 5) Extraire la liste des marques (teamBrands)
    // ----------------------------------------------------------
   
    $teamBrands = [];
    
    // Initialiser le tableau de filtres
    $filter = ['active' => true];

    foreach ($technicians as $t) {
        if ($levelFilter === 'all') {
            $levelsToConsider = ['Junior', 'Senior', 'Expert'];
        } else {
            $levelsToConsider = [$levelFilter];
        }
        $brandFields = [];
        foreach ($levelsToConsider as $level) {
            $brandField = 'brand'. ucfirst(strtolower($level));
            foreach ($t[$brandField] as $brand) {
                $brandFields[] = $brand;
            }
            if (!empty($brandFields) && is_array($brandFields)) {
                foreach ($brandFields as $b) {
                    $bTrim = trim($b);
                    if ($bTrim !== '' && !in_array($bTrim, $teamBrands)) {
                        $teamBrands[] = $bTrim;
                    }
                }
            }
        }
    }
    sort($teamBrands);

    $dataTrainings = [];
    $trainingSelected = [];
    $trainingTechSelected = [];
    if ($techFilter == 'all' && $levelFilter == 'all' && $brandFilter == 'all' && $trainingFilter == 'all') {
        foreach ($technicians as $technician) {
            $trainingData = $trainings->find([
                'users' => new MongoDB\BSON\ObjectId($technician['_id']),
                'active' => true
            ])->toArray();
            foreach ($trainingData as $train) {
                $dataTrainings[] = $train['_id'];
            }
            
            $techTrainingDatas = $validations->find([
                'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                'status' => 'Validé',
                'active' => true
            ])->toArray();
            foreach ($techTrainingDatas as $techTrainingData) {
                if (isset($techTrainingData)) {
                    $trainingSelected[] = $techTrainingData['training'];
                    $trainingTechSelected[] = $techTrainingData['user'];
                }
            }
        }
    } else if ($techFilter == 'all') {
        // Ajouter des filtres en fonction des valeurs
        if ($levelFilter != 'all') {
            $filter['level'] = $levelFilter; // Ajouter le filtre de niveau
        }

        if ($brandFilter != 'all') {
            $filter['brand'] = $brandFilter; // Ajouter le filtre de marque
        }

        if ($trainingFilter != 'all') {
            $filter['_id'] = new MongoDB\BSON\ObjectId($trainingFilter); // Ajouter le filtre de foramtion
        }
        
        foreach ($technicians as $technician) {
            $filter['users'] = new MongoDB\BSON\ObjectId($technician['_id']); // Ajouter le filtre du technicien
            $trainingData = $trainings->find($filter)->toArray();
            
            foreach ($trainingData as $train) {
                $dataTrainings[] = $train['_id'];
            }
        
            $techTrainingDatas = $validations->find([
                'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                'status' => 'Validé',
                'active' => true
            ])->toArray();
            foreach ($techTrainingDatas as $techTrainingData) {
                if (isset($techTrainingData)) {
                    $trainingSelected[] = $techTrainingData['training'];
                    $trainingTechSelected[] = $techTrainingData['user'];
                }
            }
        }
    } else if ($techFilter != 'all') {
        // Ajouter des filtres en fonction des valeurs
        $filter['users'] = new MongoDB\BSON\ObjectId($techFilter); // Ajouter le filtre du technicien

        if ($levelFilter != 'all') {
            $filter['level'] = $levelFilter; // Ajouter le filtre de niveau
        }

        if ($brandFilter != 'all') {
            $filter['brand'] = $brandFilter; // Ajouter le filtre de marque
        }

        if ($trainingFilter != 'all') {
            $filter['_id'] = new MongoDB\BSON\ObjectId($trainingFilter); // Ajouter le filtre de foramtion
        }

        // Si un filtre technique est spécifié, récupérer les formations pour ce technicien uniquement
        $trainingData = $trainings->find($filter)->toArray();
        foreach ($trainingData as $train) {
            $dataTrainings[] = $train['_id'];
        }
        
        $techTrainingDatas = $validations->find([
            'user' => new MongoDB\BSON\ObjectId($techFilter),
            'status' => 'Validé',
            'active' => true
        ])->toArray();
        foreach ($techTrainingDatas as $techTrainingData) {
            if (isset($techTrainingData)) {
                $trainingSelected[] = $techTrainingData['training'];
                $trainingTechSelected[] = $techTrainingData['user'];
            }
        }
    }

    // Convertir le tableau associatif en tableau indexé
    $dataTrainings = array_unique($dataTrainings);
    $trainingSelected = array_unique($trainingSelected);
    
    // Réindexer le tableau pour que les clés soient consécutives
    $trainingSelected = array_values($trainingSelected);

    $trainingDtas = count($dataTrainings);

    $trainingDatas = [];

    foreach ($dataTrainings as $index => $trainingData) {
        $training = $trainings->findOne([
            '_id' => new MongoDB\BSON\ObjectId($trainingData),
            'active' => true
        ]);

        $trainingDatas[] = $training;
    }
    
    // Construire le pipeline conditionnellement
    $pipeline2 = [];

    $pipeline2[] = [
        '$match' => [
            'profile'    => 'Manager',
            'subsidiary' => $_SESSION['subsidiary']
        ]
    ];
    // Ajouter le lookup sur les subordinates
    $pipeline2[] = [
        '$lookup' => [
            'from' => 'users',
            'localField' => 'users',
            'foreignField' => '_id',
            'as' => 'subordinates'
        ]
    ];

    // Unwind les subordinates
    $pipeline2[] = [
        '$unwind' => '$subordinates'
    ];

    // Match seulement les techniciens
    $pipeline2[] = [
        '$match' => [
            'subordinates.profile' => 'Technicien'
        ]
    ];

    // Ajouter un match sur technicianId si nécessaire
    if ($techFilter !== 'all') {
        try {
            $filterTechnicianId = new MongoDB\BSON\ObjectId($techFilter);
            $pipeline2[] = [
                '$match' => [
                    'subordinates._id' => $filterTechnicianId
                ]
            ];
        } catch (MongoDB\Driver\Exception\InvalidArgumentException $e) {
            echo "technicianId invalide: " . htmlspecialchars($e->getMessage());
            exit();
        }
    }

    // Lookup sur les trainings
    $pipeline2[] = [
        '$lookup' => [
            'from' => 'trainings',
            'localField' => 'subordinates._id',
            'foreignField' => 'users',
            'as' => 'trainings'
        ]
    ];

    // Unwind les trainings
    $pipeline2[] = [
        '$unwind' => '$trainings'
    ];

    if ($brandFilter !== 'all') {
        $pipeline2[] = [
            '$match' => [
                'trainings.brand' => $brandFilter
            ]
        ];
    }
    // Filtrer par level si nécessaire
    if ($levelFilter !== 'all') {
        $pipeline2[] = [
            '$match' => [
                'trainings.level' => $levelFilter
            ]
        ];
    }

    // Juste avant le $facet :
    $brandAndLevelPipeline = [];

    if ($techFilter === 'all') {
        // CAS DISTINCT : group par trainingId
        $brandAndLevelPipeline = [
            // 1er group => group par brand/level/trainingId
            [
                '$group' => [
                    '_id' => [
                        'brand'      => '$trainings.brand',
                        'level'      => '$trainings.level',
                        'trainingId' => '$trainings._id'
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            // 2e group => brand + level => on additionne
            [
                '$group' => [
                    '_id' => [
                        'brand' => '$_id.brand',
                        'level' => '$_id.level'
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            // 3e group final
            [
                '$group' => [
                    '_id' => '$_id.brand',
                    'totalByBrand' => ['$sum' => '$count'],
                    'totalByLevel' => [
                        '$push' => [
                            'k' => '$_id.level',
                            'v' => '$count'
                        ]
                    ]
                ]
            ],
            [
                '$addFields' => [
                    'totalByLevel' => ['$arrayToObject' => '$totalByLevel']
                ]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'totalTrainingsByBrandAndLevel' => [
                        '$push' => [
                            'k' => '$_id',
                            'v' => [
                                'totalByBrand' => '$totalByBrand',
                                'totalByLevel' => '$totalByLevel'
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$addFields' => [
                    'totalTrainingsByBrandAndLevel' => [
                        '$arrayToObject' => '$totalTrainingsByBrandAndLevel'
                    ]
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'totalTrainingsByBrandAndLevel' => 1
                ]
            ]
        ];
    } else {
        // CAS NON-DISTINCT : group par brand/level, on compte TOUT
        $brandAndLevelPipeline = [
            // group par brand + level SANS trainingId
            [
                '$group' => [
                    '_id' => [
                        'brand'      => '$trainings.brand',
                        'level'      => '$trainings.level',
                        'trainingId' => '$trainings._id'
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            // on n'a plus besoin du 1er regroupement distinct
            [
                '$group' => [
                    '_id' => '$_id.brand',
                    'totalByBrand' => ['$sum' => '$count'],
                    'totalByLevel' => [
                        '$push' => [
                            'k' => '$_id.level',
                            'v' => '$count'
                        ]
                    ]
                ]
            ],
            [
                '$addFields' => [
                    'totalByLevel' => ['$arrayToObject' => '$totalByLevel']
                ]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'totalTrainingsByBrandAndLevel' => [
                        '$push' => [
                            'k' => '$_id',
                            'v' => [
                                'totalByBrand' => '$totalByBrand',
                                'totalByLevel' => '$totalByLevel'
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$addFields' => [
                    'totalTrainingsByBrandAndLevel' => [
                        '$arrayToObject' => '$totalTrainingsByBrandAndLevel'
                    ]
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'totalTrainingsByBrandAndLevel' => 1
                ]
            ]
        ];
    }

    $pipeline2[] = [
        '$facet' => [
            'brandAndLevel' => $brandAndLevelPipeline,
            'totalOccurrences' => [
                [
                    '$group' => [
                        '_id' => null,
                        'totalOccurrences' => ['$sum' => 1]
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'totalOccurrences' => 1
                    ]
                ]
            ]
        ]
    ];

    // Exécuter le pipeline
    try {
        $result2 = $users->aggregate($pipeline2)->toArray();
        $document2 = $result2[0] ?? null;

        if ($document2) {
            // Aucune donnée trouvée
            $brandAndLevelArr = $document2['brandAndLevel'][0] ?? null;
            if ($brandAndLevelArr) {
                $trainingsByBrandAndLevel = $brandAndLevelArr['totalTrainingsByBrandAndLevel'] ?? [];
            } else {
                $trainingsByBrandAndLevel = [];
            }
            // Récupération de la partie totalOccurrences
            $occurrencesArr = $document2['totalOccurrences'][0] ?? [];
            $totalOccurrences = $occurrencesArr['totalOccurrences'] ?? 0;
        } else {
            // Pas de résultat => vides
            $trainingsByBrandAndLevel = [];
            $totalOccurrences = 0;
        }
        //     $trainingsByBrandAndLevel = [];
        // } else {
        //     $trainingsByBrandAndLevel = $document2['totalTrainingsByBrandAndLevel'] ?? [];
        // }
    } catch (MongoDB\Exception\Exception $e) {
        echo "Erreur lors du pipeline2 : " . htmlspecialchars($e->getMessage());
        exit();
    }
    
    $numDays = $totalOccurrences * 5;
    
    // Sauvegarde des formations avec un status et une date de validation
    if (isset($_POST['selectDates'])) {
        $userIds = $_POST["userIds"] ?? [];
        $trainingId = $_POST["trainingId"] ?? '';
        $selectedDate = $_POST["date"] ?? '';
        $currentYear = $_POST["currentYear"] ?? '';
        $place = $_POST["place"] ?? '';
        $currentUser = $_SESSION['id'];
        
        function sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingTechSelected, $trainingName, $status) {
            // Assurez-vous que les variables sont définies et valides
            if (isset($manager, $training) && count($technicians) != 0) {
                // Échapper les données pour éviter les problèmes de sécurité
                $managerLastName = htmlspecialchars($manager['lastName']);
                $managerFirstName = htmlspecialchars($manager['firstName']);
                // Utilisation de implode pour joindre les éléments avec un tiret et un retour à la ligne
                $technicianName = implode('<br>- ', $technicians);
                $technicianName = '- ' . $technicianName; // Ajoute un tiret au début
                $trainingLabel = htmlspecialchars($training['label']);
                $trainingLevel = htmlspecialchars($training['level']);
                $trainingPlace = htmlspecialchars($place);

                // Créer le message
                $message = '<p>Bonjour,</p><p>Nous avez reçu une confirmation de pré-inscription de <strong>' . $managerFirstName . ' ' . $managerLastName . '</strong> 
                    pour le(s) collaborateur(s): <br><strong>' . $technicianName . '</strong></p>
                    <p>A la formation <strong>' . $trainingLabel . '</strong>,
                        niveau <strong>' . $trainingLevel . '</strong> pour la période du 
                    <strong>' . $selectedDate . '</strong> à <strong>' . $trainingPlace . '</strong>.</p>
                    <p style="margin-top: 50px; font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                // Sujet de l'e-mail
                $subject = 'Confirmation de la pré-inscription de ' . $managerFirstName . ' ' . $managerLastName;
                // Envoyer l'e-mail
                sendMailSelectDone($subject, $message);
                if ($status == 'Validé') {
                    $response = [
                        'success' => true,
                        'training' => 0,
                        'status' => 'Validé',
                        'technicians' => 0,
                        'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                    ];
                    echo json_encode($response);
                    exit(); // Terminer le script après avoir envoyé la réponse
                } else {
                    $response = [
                        'success' => true,
                        'training' => 1,
                        'status' => 'Validé',
                        'technicians' => count($trainingTechSelected),
                        'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                    ];
                    echo json_encode($response);
                    exit(); // Terminer le script après avoir envoyé la réponse
                }
            } else {
                if ($status == 'Validé') {
                    $response = [
                        'success' => true,
                        'training' => 0,
                        'status' => 'Validé',
                        'technicians' => 0,
                        'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                    ];
                    echo json_encode($response);
                    exit(); // Terminer le script après avoir envoyé la réponse
                } else {
                    $response = [
                        'success' => true,
                        'training' => 1,
                        'status' => 'Validé',
                        'technicians' => count($trainingTechSelected),
                        'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                    ];
                    echo json_encode($response);
                    exit(); // Terminer le script après avoir envoyé la réponse
                }
            }
        }
        
        $training = $trainings->findOne(['_id' => new MongoDB\BSON\ObjectId($trainingId)]);
        $trainingName = $training['label'];
        $trainingTechSelected = [];
        $trainingSelected = [];
        $technicians = [];
        $status = [];
        foreach ($userIds as $userId) {
            $exist = $applications->findOne([
                'user' => new MongoDB\BSON\ObjectId($currentUser),
                'period' => $selectedDate // Nouvelle valeur pour le champ 'period'
            ]);

            $applicationTechs = $allocations->findOne([
                '$and' => [
                    [
                        "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                        "training" => new MongoDB\BSON\ObjectId($trainingId), // ID de la formation
                        "type" => "Training"
                    ]
                ],
            ]);

            if (isset($exist)) {
                $response = [
                    'success' => false,
                    'message' => 'Vous avez déjà sélectionnée cette période pour une autre formation. Veuillez sélectionner une autre période.'
                ];
                echo json_encode($response);
                exit(); // Terminer le script après avoir envoyé la réponse
            } else if (isset($applicationTechs)) {
                $app = $applications->findOne([
                    "user" => new MongoDB\BSON\ObjectId($applicationTechs['user']), // ID de l'utilisateur
                    "training" => new MongoDB\BSON\ObjectId($trainingId), // ID de la formation
                    "active" => false
                ]); 

                $validated = $validations->findOne([
                    "user" => new MongoDB\BSON\ObjectId($applicationTechs['user']), // ID de l'utilisateur
                    "training" => new MongoDB\BSON\ObjectId($trainingId), // ID de la formation
                    "active" => false
                ]);
                
                if (isset($validated)) {
                    $status[] = $validated['status'];
                    $validations->updateOne(
                        [
                            "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                            "training" => new MongoDB\BSON\ObjectId($trainingId) // ID de la formation
                        ],[
                            '$set' => [
                                'status' => 'Validé', // Nouvelle valeur pour le champ 'year'
                                'updated' => date("d-m-Y H:i:s") // Exemple d'ajout d'un champ de date
                            ]
                        ]                        
                    );
                } else {
                    $validates = [
                        'user' => new MongoDB\BSON\ObjectId($applicationTechs['user']),
                        'training' => new MongoDB\BSON\ObjectId($trainingId),
                        'manager' => new MongoDB\BSON\ObjectId($currentUser),
                        'status' => 'Validé',
                        'active' => true,
                        'created' => date("d-m-Y H:i:s")
                    ];
                    $validations->insertOne($validates);
                }                

                if ($app) {
                    $applications->updateOne(
                        [
                            "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                            "training" => new MongoDB\BSON\ObjectId($trainingId) // ID de la formation
                        ],[
                            '$set' => [
                                'period' => $selectedDate, // Nouvelle valeur pour le champ 'period'
                                'year' => $currentYear, // Nouvelle valeur pour le champ 'year'
                                'status' => 'En attente', // Nouvelle valeur pour le champ 'year'
                                'active' => true, // Nouvelle valeur pour le champ 'active'
                                'updated' => date("d-m-Y H:i:s") // Exemple d'ajout d'un champ de date
                            ]
                        ]                        
                    );
                } else {
                    $applicates = [
                        'user' => new MongoDB\BSON\ObjectId($applicationTechs['user']),
                        'training' => new MongoDB\BSON\ObjectId($trainingId),
                        'manager' => new MongoDB\BSON\ObjectId($currentUser),
                        'place' => $place,
                        'period' => $selectedDate,
                        'year' => $currentYear,
                        'status' => 'En attente',
                        'active' => true,
                        'created' => date("d-m-Y H:i:s")
                    ];
                    $applications->insertOne($applicates);
                }
                
                $technician = $users->findOne([
                    '$and' => [
                        [
                            "_id" => new MongoDB\BSON\ObjectId($applicationTechs['user']), // ID de l'utilisateur
                            "active" => true
                        ]
                    ],
                ]);

                $technicians[] = $technician['firstName'].' '.$technician['lastName'];

                $technicians = array_unique($technicians);

                $techTrainingDatas = $applications->find([
                    "user" => new MongoDB\BSON\ObjectId($applicationTechs['user']), // ID de l'utilisateur
                    "training" => new MongoDB\BSON\ObjectId($trainingId), // ID de la formation
                    'active' => true
                ])->toArray();

                $trainingTechSelected[] = $applicationTechs['user'];
            }
        }
    
        $manager = $users->findOne([
            '$and' => [
                [
                    "_id" => new MongoDB\BSON\ObjectId($currentUser), // ID du manager
                    "active" => true
                ]
            ],
        ]);
    
        $trainingTechSelected = array_unique($trainingTechSelected);
    
        sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingTechSelected, $trainingName, $status[0] = 'En attente');
    }

    // Sauvegarde des formations avec un status de validation
    if (isset($_POST['selectValidation'])) {
        $userIds = $_POST["userIds"] ?? [];
        $trainingId = $_POST["trainingId"] ?? '';
        $status = $_POST["selectedValue"] ?? '';
        $currentUser = $_SESSION['id'];

        $trainingTechSelected = [];
        $trainingSelected = [];

        $training = $trainings->findOne(['_id' => new MongoDB\BSON\ObjectId($trainingId)]);
        $trainingName = $training['label'];

        foreach ($userIds as $userId) {
            $applicationTechs = $allocations->findOne([
                "user" => new MongoDB\BSON\ObjectId($userId),
                "training" => new MongoDB\BSON\ObjectId($trainingId),
                "type" => "Training"
            ]);

            if ($applicationTechs) {
                $app = $validations->findOne([
                    "user" => new MongoDB\BSON\ObjectId($applicationTechs['user']),
                    "training" => new MongoDB\BSON\ObjectId($trainingId),
                    "active" => true
                ]);

                if ($app) {
                    // Mise à jour de l'enregistrement existant
                    $validations->updateOne(
                        [
                            "user" => new MongoDB\BSON\ObjectId($userId),
                            "training" => new MongoDB\BSON\ObjectId($trainingId)
                        ],
                        [
                            '$set' => [
                                'status' => $status,
                                'updated' => date("d-m-Y H:i:s")
                            ]
                        ]
                    );
                } else {
                    // Insertion d'un nouvel enregistrement
                    $applicates = [
                        'user' => new MongoDB\BSON\ObjectId($applicationTechs['user']),
                        'training' => new MongoDB\BSON\ObjectId($trainingId),
                        'manager' => new MongoDB\BSON\ObjectId($currentUser ),
                        'status' => $status,
                        'active' => true,
                        'created' => date("d-m-Y H:i:s")
                    ];
                    $validations->insertOne($applicates);
                }

                $trainingTechSelected[] = $applicationTechs['user'];
            }
        }
        if ($status == 'Validé') {
            // Élimination des doublons
            $trainingTechSelected = array_unique($trainingTechSelected);
    
            // Préparation de la réponse
            $response = [
                'success' => true,
                'training' => 1,
                'technicians' => count($trainingTechSelected),
                'message' => 'Formation « ' . htmlspecialchars($trainingName) . ' » ' . htmlspecialchars($status) . '.'
            ];
            echo json_encode($response);
            exit(); // Terminer le script après avoir envoyé la réponse
        } else {
            // Préparation de la réponse
            $response = [
                'success' => true,
                'training' => 0,
                'technicians' => 0,
                'message' => 'Formation « ' . htmlspecialchars($trainingName) . ' » ' . htmlspecialchars($status) . '.'
            ];
            echo json_encode($response);
            exit(); // Terminer le script après avoir envoyé la réponse
        }
    }

    ?>

<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo 'Validation des Plans de Formations'  ?> | CFAO Mobility Academy</title>
<!--end::Title-->

<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content"
    data-select2-id="select2-data-kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bolder my-1 fs-1">
                    <?php echo 'Validation des Plans de Formations'  ?> </h1>
                <!--end::Title-->
                <div class="card-title">
                </div>
            </div>
            <!--end::Info-->
        </div>
        <!--begin::Filtres -->
        <div class="container my-4">
            <div class="row g-3 align-items-center">
                <!-- Filtre Level -->
                <div class="col-md-3">
                    <label for="level-filter" class="form-label d-flex align-items-center">
                        <i class="bi bi-bar-chart-fill fs-2 me-2 text-warning"></i> Niveaux
                    </label>
                    <select id="level-filter" name="level" class="form-select" onchange="applyFilters()">
                        <option value="all" <?php if ($levelFilter === 'all') echo 'selected'; ?>>Tous les niveaux</option>
                        <?php foreach (['Junior', 'Senior', 'Expert'] as $levelOption): ?>
                        <option value="<?php echo htmlspecialchars($levelOption); ?>"
                                <?php if ($levelFilter === $levelOption) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($levelOption); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Formations -->
                <div class="col-md-3">
                    <label for="training-filter" class="form-label d-flex align-items-center">
                        <i class="fas fa-book-open fs-2 text-success me-2"></i> Formations
                    </label>
                    <select id="training-filter" class="form-select" onchange="applyFilters()">
                        <option value="all" <?php if ($trainingFilter === 'all') echo 'selected'; ?>>Toutes les formations
                        </option>
                        <?php foreach ($trainingDatas as $td): ?>
                        <option value="<?php echo htmlspecialchars($td['_id']); ?>"
                            <?php if ($trainingFilter === htmlspecialchars($td['_id'])) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($td['label']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Marques -->
                <div class="col-md-3">
                    <label for="brand-filter" class="form-label d-flex align-items-center">
                        <i class="bi bi-car-front-fill fs-2 me-2 text-danger"></i> Marques
                    </label>
                    <select id="brand-filter" class="form-select" onchange="applyFilters()">
                        <option value="all" <?php if ($brandFilter === 'all') echo 'selected'; ?>>Toutes les marques
                        </option>
                        <?php foreach ($teamBrands as $b): ?>
                        <option value="<?php echo htmlspecialchars($b); ?>"
                            <?php if ($brandFilter === $b) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($b); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Manager -->
                <div class="col-md-3">
                    <label for="tech-filter" class="form-label d-flex align-items-center">
                        <i class="bi bi-person-fill fs-2 me-2 text-info"></i> Techniciens
                    </label>
                    <select id="tech-filter" class="form-select" onchange="applyFilters()">
                        <option value="all" <?php if ($techFilter === 'all') echo 'selected'; ?>>
                            Tous les techniciens
                        </option>
                        <?php foreach ($technicians as $t): ?>
                        <option value="<?php echo htmlspecialchars($t['_id']); ?>"
                            <?php if ($techFilter === htmlspecialchars($t['_id'])) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($t['firstName'] .' '. $t['lastName']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <!--end::Filtres -->
    </div>
    <!--end::Toolbar-->
    
    <!-- begin:: Marques du Technicien -->
    <div class="text-center mb-6">
        <div class="row justify-content-center">
            <div class='col-6 col-sm-4 col-md-5'>
                <div class='card custom-card h-80'>
                    <div class='card-body d-flex flex-column justify-content-center align-items-center'>
                        <!--begin::Name-->
                        <!--begin::Animation-->
                        <i class="fas fa-book fs-2 text-danger mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div class="min-w-80px" data-kt-countup="true" data-kt-countup-value="<?php echo count($trainingDatas) ?>"></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo $recommaded_training ?>
                        </div>
                        <!--end::Title-->
                        <!--end::Name-->
                    </div>
                </div>
            </div>
            <div class='col-6 col-sm-4 col-md-5'>
                <div class='card custom-card h-80'>
                    <div class='card-body d-flex flex-column justify-content-center align-items-center'>
                        <!--begin::Name-->
                        <!--begin::Animation-->
                        <i class="fas fa-calendar-alt fs-2 text-warning mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div class="min-w-80px" data-kt-countup="true" 
                            data-kt-countup-value="<?php echo (int)$numDays; ?>" data-kt-countup-separator=" "></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo $training_duration ?> 
                        </div>
                        <!--end::Title-->
                        <!--end::Name-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mb-6">
        <div class="row justify-content-center">
            <div class='col-6 col-sm-4 col-md-5'>
                <div class='card custom-card h-80'>
                    <div class='card-body d-flex flex-column justify-content-center align-items-center'>
                        <!--begin::Name-->
                        <!--begin::Animation-->
                        <i class="fas fa-book fs-2 text-info mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div id="selectTraining" class="min-w-80px" data-kt-countup="true"></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo 'Formations Selectionnées' ?> 
                        </div>
                        <!--end::Title-->
                        <!--end::Name-->
                    </div>
                </div>
            </div>
            <div class='col-6 col-sm-4 col-md-5'>
                <div class='card custom-card h-80'>
                    <div class='card-body d-flex flex-column justify-content-center align-items-center'>
                        <!--begin::Name-->
                        <!--begin::Animation-->
                        <i class="fas fa-calendar-alt fs-2 text-warning mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div id="durationTraining" class="min-w-80px" data-kt-countup="true" data-kt-countup-separator=" "></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo $training_duration ?> 
                        </div>
                        <!--end::Title-->
                        <!--end::Name-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end::Marques du Technicien -->

    
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
        <!--begin::Container-->
        <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
            <!--begin::Title-->
            <div style="margin-top: 25px; margin-bottom : 25px">
                <div>
                    <h6 class="form-label text-dark my-1 fs-5">
                        <?php echo 'Liste des formations préconisées' ?>
                    </h6>
                </div>
            </div>
            <!--end::Title-->
            
            <div id="message"></div>

            <!--begin::Form-->
            <form name="form" method="POST">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table aria-describedby=""
                                    class="table align-middle table-bordered fs-6 gy-5 dataTable no-footer"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-black fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-300px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Customer Name: activate to sort column ascending"
                                                style="width: 300px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo $label_training ?>
                                            </th>
                                            <th class="min-w-130px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Created Date: activate to sort column ascending"
                                                style="width: 130px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo $Brand ?>
                                            </th>
                                            <th class="min-w-100px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Payment Method: activate to sort column ascending"
                                                style="width: 100px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo $Level ?>
                                            </th>
                                            <th class="min-w-150px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Company: activate to sort column ascending"
                                                style="width: 150px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo 'Validation de la formation' ?>
                                            </th>
                                            <th class="min-w-150px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1" id="year"
                                                aria-label="Created Date: activate to sort column ascending"
                                                style="width: 150px; text-align: center; vertical-align: middle; height: 20px;">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600" id="table" style='text-align: center; vertical-align: middle; height: 50px;'>
                                        <?php foreach ($trainingDatas as $index => $training) {
                                            $periods = [];
                                            $trainingValidations = [];
                                            $currentYear = date('Y'); // Get the current year

                                            if ($techFilter == 'all') {
                                                // Récupérer tous les techniciens associés à cette formation
                                                foreach ($technicians as $technician) {
                                                    $trainingAllocates = $applications->find([
                                                        'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                                                        'training' => new MongoDB\BSON\ObjectId($training['_id']),
                                                        'active' => true
                                                    ])->toArray();

                                                    // Vérifier si tous les techniciens ont l'année courante dans leurs années d'allocation
                                                    foreach ($trainingAllocates as $trainingAllocate) {
                                                        $periods[] = $trainingAllocate['period'];
                                                    }

                                                    $trainingValidates = $validations->find([
                                                        'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                                                        'training' => new MongoDB\BSON\ObjectId($training['_id']),
                                                        'active' => true
                                                    ])->toArray();

                                                    foreach ($trainingValidates as $trainingValidate) {
                                                        $trainingValidations[] = $trainingValidate['status'];
                                                    }
                                                }
                                            } else {
                                                // Initialiser le tableau de filtres
                                                $filter = [
                                                    'user' => new MongoDB\BSON\ObjectId($techFilter),
                                                    'training' => new MongoDB\BSON\ObjectId($training['_id']),
                                                    'active' => true
                                                ];

                                                $trainingAllocates = $applications->find($filter)->toArray();
                                                foreach ($trainingAllocates as $trainingAllocate) {
                                                    $periods[] = $trainingAllocate['period'];
                                                }
                                                
                                                $trainingValidates = $validations->find([
                                                    'user' => new MongoDB\BSON\ObjectId($techFilter),
                                                    'training' => new MongoDB\BSON\ObjectId($training['_id']),
                                                    'active' => true
                                                ])->toArray();
                                                
                                                foreach ($trainingValidates as $trainingValidate) {
                                                    $trainingValidations[] = $trainingValidate['status'];
                                                }
                                            }

                                            $periods = array_unique($periods);
                                            $trainingValidations = array_unique($trainingValidations);
                                        ?>
                                        <tr class="odd" etat="<?php echo htmlspecialchars($training->active); ?>">
                                            <td><?php echo htmlspecialchars($training->label); ?></td>
                                            <input type="text" id="training-<?php echo $index; ?>" 
                                            value="<?php echo htmlspecialchars($training['_id']); ?>" hidden>
                                            <td><?php echo htmlspecialchars($training->brand); ?></td>
                                            <td><?php echo htmlspecialchars($training->level); ?></td>
                                            <td>
                                                <select id="select-<?php echo $index; ?>" name="validation" class="form-select">
                                                    <?php foreach (['En attente', 'Validé', 'Non validé'] as $option): ?>
                                                        <option value="<?php echo $option ?>"
                                                            <?php if (in_array($option, $trainingValidations)) echo 'selected'; ?>>
                                                            <?php echo $option ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="date-<?php echo $index; ?>" name="date" class="form-select">
                                                    <option value="" disabled selected>-- Période --</option>
                                                    <?php foreach ($training['startDates'] as $i => $dateOption): 
                                                        if ($dateOption != '') {
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($dateOption . ' au ' . $training['endDates'][$i] . ' - Lieu : ' . $training['places'][$i]); ?>"
                                                            <?php if (in_array($dateOption . ' au ' . $training['endDates'][$i], $periods)) echo 'selected'; ?>>
                                                            <?php echo htmlspecialchars($dateOption . ' au ' . $training['endDates'][$i] . ' - Lieu : ' . $training['places'][$i]); ?>
                                                        </option>
                                                    <?php } endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Body-->
<script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js">
</script>
<script src="../../public/js/main.js"></script>
<?php include_once "partials/footer.php"; ?>
<script>
    // Afficher l'année dans l'élément avec l'ID 'currentYear'
    document.getElementById('year').textContent = 'Période de Formation de ' + currentYear;
    
    const technicians = <?php echo json_encode($technicianIds); ?>;
    const trainingSelect = <?php echo json_encode($trainingSelected); ?>;
    const trainingTechSelected = <?php echo json_encode($trainingTechSelected); ?>;
    const trainingDatas = <?php echo json_encode($trainingDatas); ?>;
    const result2 = <?php echo json_encode($result2); ?>;

    console.log("Technicians:", technicians);
    console.log("trainingSelect:", trainingSelect);
    console.log("trainingTechSelected:", trainingTechSelected);
    console.log("trainingDatas:", trainingDatas);
    console.log("result2:", result2);

    // Sélectionner toutes les selects dans le tableau
    const selectValidations = document.querySelectorAll('select[name="validation"]');
    const selectDates = document.querySelectorAll('select[name="date"]');
    const trainingSelected = document.querySelector('#selectTraining');
    const trainingDuration = document.querySelector('#durationTraining');

    var totalTechs  = trainingTechSelected.length;
    
    trainingSelected.setAttribute('data-kt-countup-value', trainingSelect.length);
    trainingDuration.setAttribute('data-kt-countup-value', (totalTechs * 5));
    
    // Initialisation des variables
    var total = trainingSelected.getAttribute("data-kt-countup-value");

    // Parcourir chaque case de validation
    selectValidations.forEach(select => {
        // Écouter les changements de la case de validation
        select.addEventListener('change', function() {
            const rowIndex = this.id.split('-')[1]; // Récupérer l'index de la ligne
            const selectedValue = this.value; // Récupérer la valeur du select
            const userId = document.querySelector('#tech-filter').value; // Récupérer la valeur des techniciens
            const trainingId = document.querySelector('#training-' + rowIndex).value; // Récupérer la'id de la formation'

            console.log('rowIndex', rowIndex);
            console.log('selectedValue', selectedValue);
            console.log('trainingId', trainingId);
            
            // Récupérer les IDs des techniciens
            let techniciansIds = userId === 'all' ? technicians : [userId];

            console.log('techniciansIds', techniciansIds);

            // Envoyer les données via AJAX
            const datas = {
                userIds: techniciansIds,
                trainingId: trainingId,
                selectedValue: selectedValue,
                currentYear: currentYear
            };
            
            // Envoyer une requête AJAX pour activer
            $.ajax({
                type: 'POST',
                data: { ...datas, selectValidation: 1 },
                dataType: 'json',
                success: function(response) {
                    // Si la réponse est déjà un objet JSON, pas besoin de la parser
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    console.log('response', response);
                    if (response && response.success) {
                        total = parseInt(total) + response.training;
                        totalTechs = parseInt(totalTechs) + response.technicians;
                        updateUI();
                        showMessage('success', response.message);
                    } else {
                        showMessage('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('error', error);
                    showMessage('error', 'Une erreur est survenue. Veuillez réessayer.');
                }
            });
        });
    });

    // Parcourir chaque case à cocher
    selectDates.forEach(select => {
        // Écouter les changements de la case à cocher
        select.addEventListener('change', function() {
            const rowIndex = this.id.split('-')[1]; // Récupérer l'index de la ligne
            const selectDate = this.value; // Récupérer la valeur du select
            const userId = document.querySelector('#tech-filter').value; // Récupérer la valeur des techniciens
            const trainingId = document.querySelector('#training-' + rowIndex).value; // Récupérer la'id de la formation'
            // Sélectionnez le <select> par son ID
            const selectElement = document.getElementById(`select-${rowIndex}`);

            // Trouver l'index de "Lieu"
            var indexDate = selectDate.indexOf(" - Lieu");
            // Vérifier si "Lieu" est présent dans la chaîne
            if (indexDate !== -1) {
                // Extraire le texte avant "Lieu"
                var selectedDate = selectDate.substring(0, indexDate).trim();
            }
            // Trouver l'index de "Lieu :"
            var indexPlace = selectDate.indexOf("Lieu :");
            // Vérifier si "Lieu :" est présent dans la chaîne
            if (indexPlace !== -1) {
                // Extraire le texte avant "Lieu :"
                var selectedPlace = selectDate.substring(indexPlace + 6).trim();
            }
            
            console.log('selectedDate', selectedDate);
            console.log('selectedPlace', selectedPlace);
            console.log('rowIndex', rowIndex);
            console.log('trainingId', trainingId);
            
            // Récupérer les IDs des techniciens
            let techniciansIds = userId === 'all' ? technicians : [userId];

            console.log('techniciansIds', techniciansIds);

            // Envoyer les données via AJAX
            const datas = {
                userIds: techniciansIds,
                trainingId: trainingId,
                date: selectedDate,
                place: selectedPlace,
                currentYear: currentYear
            };
            
            // Envoyer une requête AJAX pour activer
            $.ajax({
                type: 'POST',
                data: { ...datas, selectDates: 1 },
                dataType: 'json',
                success: function(response) {
                    // Si la réponse est déjà un objet JSON, pas besoin de la parser
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    console.log('response', response);
                    if (response && response.success) {
                        total = parseInt(total) + response.training;
                        totalTechs = parseInt(totalTechs) + response.technicians;
                        // Récupérez le status de la réponse
                        const status = response.status;                 
                        updateUI();
                        showMessage('success', response.message);
                        // Sélectionnez l'option correspondante
                        const optionToSelect = selectElement.querySelector(`option[value="${status}"]`);
                        if (optionToSelect) {
                            optionToSelect.selected = true;
                        }                        
                    } else {
                        showMessage('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('error', error);
                    showMessage('error', 'Une erreur est survenue. Veuillez réessayer.');
                }
            });
        });
    });

    // Fonction pour mettre à jour l'interface utilisateur
    function updateUI() {
        trainingSelected .setAttribute('data-kt-countup-value', total);
        trainingDuration.setAttribute('data-kt-countup-value', (totalTechs * 5));
        trainingSelected.innerHTML = total;
        trainingDuration.innerHTML = (totalTechs * 5);
    }

    // Fonction pour afficher les messages
    function showMessage(type, message) {
        const alertType = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#message').html(`
            <div class="alert ${alertType} alert-dismissible fade show" role="alert">
                <center><strong>${message}</strong></center>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
    }

    // Fonction pour appliquer les filtres et recharger la page avec de nouveaux paramètres
    function applyFilters() {
        const level = document.getElementById('level-filter').value;
        const brand = document.getElementById('brand-filter').value;
        const technician = document.getElementById('tech-filter').value;
        const training = document.getElementById('training-filter').value;

        let query = `?user=${encodeURIComponent(technician)}&training=${encodeURIComponent(training)}&brand=${encodeURIComponent(brand)}&level=${encodeURIComponent(level)}`;
        window.location.href = query;
    }

    $(document).ready(function() {
        $("#excel").on("click", function() {
            let table = document.getElementsByTagName("table");
            debugger;
            TableToExcel.convert(table[0], {
                name: `Users.xlsx`
            })
        });
    });
</script>
<?php } ?>