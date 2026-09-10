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
            
            $techTrainingDatas = $applications->find([
                'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                'active' => true
            ])->toArray();
            foreach ($techTrainingDatas as $techTrainingData) {
                if (isset($techTrainingData['period']) && isset($techTrainingData['year'])) {
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
        
            $techTrainingDatas = $applications->find([
                'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                'active' => true
            ])->toArray();
            foreach ($techTrainingDatas as $techTrainingData) {
                if (isset($techTrainingData['period']) && isset($techTrainingData['year'])) {
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
        
        $techTrainingDatas = $applications->find([
            'user' => new MongoDB\BSON\ObjectId($techFilter),
            'active' => true
        ])->toArray();
        foreach ($techTrainingDatas as $techTrainingData) {
            if (isset($techTrainingData['period']) && isset($techTrainingData['year'])) {
                $trainingSelected[] = $techTrainingData['training'];
                $trainingTechSelected[] = $techTrainingData['user'];
            }
        }
    }

    // Convertir le tableau associatif en tableau indexé
    $dataTrainings = array_unique($dataTrainings);
    $trainingSelected = array_unique($trainingSelected);

    $trainingDtas = count($dataTrainings);

    $trainingDatas = [];

    foreach ($dataTrainings as $index => $trainingData) {
        $training = $trainings->findOne([
            '_id' => new MongoDB\BSON\ObjectId($trainingData),
            'active' => true
        ]);

        $trainingDatas[] = $training;
    }
    
    if (isset($_POST['active'])) {
        $userIds = $_POST["userIds"] ?? [];
        $trainingId = $_POST["trainingId"] ?? '';
        $selectedDate = $_POST["date"] ?? '';
        $currentYear = $_POST["currentYear"] ?? '';
        $place = $_POST["place"] ?? '';
        $currentUser = $_SESSION['id'];
        
        function sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingSelected, $trainingTechSelected, $trainingName) {
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
                $response = [
                    'success' => true,
                    'training' => count($trainingSelected),
                    'technicians' => count($trainingTechSelected),
                    'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                ];
                echo json_encode($response);
                exit(); // Terminer le script après avoir envoyé la réponse
            } else {
                $response = [
                    'success' => true,
                    'training' => count($trainingSelected),
                    'technicians' => count($trainingTechSelected),
                    'message' => 'Formation  « ' . $trainingName . ' » pré-inscrite avec succès.'
                ];
                echo json_encode($response);
                exit(); // Terminer le script après avoir envoyé la réponse
            }
        }
        if(empty($selectedDate)) {
            $response = [
                'success' => false,
                'message' => 'Aucune période sélectionnée. Veuillez sélectionner au moins une période.'
            ];
            echo json_encode($response);
            exit(); // Terminer le script après avoir envoyé la réponse
        } else {
            $training = $trainings->findOne(['_id' => new MongoDB\BSON\ObjectId($trainingId)]);
            $trainingName = $training['label'];
            $trainingTechSelected = [];
            $trainingSelected = [];
            $technicians = [];
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

                    if ($app) {
                        $applications->updateOne(
                            [
                                "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                                "training" => new MongoDB\BSON\ObjectId($trainingId) // ID de la formation
                            ],[
                                '$set' => [
                                    'period' => $selectedDate, // Nouvelle valeur pour le champ 'period'
                                    'year' => $currentYear, // Nouvelle valeur pour le champ 'year'
                                    'status' => 'Pending', // Nouvelle valeur pour le champ 'year'
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
                            'status' => 'Pending',
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

                    foreach ($techTrainingDatas as $techTrainingData) {
                        if (isset($techTrainingData['period']) && isset($techTrainingData['year'])) {
                            $trainingSelected[] = $techTrainingData['training'];
                            $trainingTechSelected[] = $techTrainingData['user'];
                        }
                    }
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

            $trainingSelected = array_unique($trainingSelected);

            $trainingTechSelected = array_unique($trainingTechSelected);

            sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingSelected, $trainingTechSelected, $trainingName);
        }
    }
    
    if (isset($_POST['disabled'])) {
        $userIds = $_POST["userIds"] ?? [];
        $trainingId = $_POST["trainingId"] ?? '';
        $selectedDate = $_POST["date"] ?? '';
        $place = $_POST["place"] ?? '';
        $currentUser = $_SESSION['id'];
        
        function sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingSelected, $trainingTechSelected, $trainingName) {
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

                // Créer le message'
                $message = '<p>Bonjour,</p><p>Vous avez reçu une annulation de pré-inscription de <strong>' . $managerFirstName . ' ' . $managerLastName . '</strong> 
                    pour le(s) collaborateur(s): <br><strong>' . $technicianName . '</strong></p>
                    <p>A la formation <strong>' . $trainingLabel . '</strong>,
                        niveau <strong>' . $trainingLevel . '</strong> pour la période du 
                    <strong>' . $selectedDate . '</strong> à <strong>' . $trainingPlace . '</strong>.</p>
                    <p style="margin-top: 50px; font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                // Sujet de l'e-mail
                $subject = 'Annulation de la pré-inscription de ' . $managerFirstName . ' ' . $managerLastName;

                // Envoyer l'e-mail
                sendMailSelectDone($subject, $message);
                $response = [
                    'success' => true,
                    'training' => count($trainingSelected),
                    'technicians' => count($trainingTechSelected),
                    'message' => 'Formation  « ' . $trainingName . ' » rétiréé avec succès.'
                ];
                echo json_encode($response);
                exit(); // Terminer le script après avoir envoyé la réponse
            } else {
                $response = [
                    'success' => true,
                    'training' => count($trainingSelected),
                    'technicians' => count($trainingTechSelected),
                    'message' => 'Formation  « ' . $trainingName . ' » rétiréé avec succès.'
                ];
                echo json_encode($response);
                exit(); // Terminer le script après avoir envoyé la réponse
            }
        }
        if(empty($selectedDate)) {
            $response = [
                'success' => false,
                'message' => 'Aucune période rétiréé. Veuillez sélectionner au moins une période à retirer.'
            ];
            echo json_encode($response);
            exit(); // Terminer le script après avoir envoyé la réponse
        } else {
            $training = $trainings->findOne(['_id' => new MongoDB\BSON\ObjectId($trainingId)]);
            $trainingName = $training['label'];
            $trainingTechSelected = [];
            $trainingSelected = [];
            $technicians = [];
            foreach ($userIds as $userId) {
                $techTrainingDatas = $applications->find([
                    "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                    "training" => new MongoDB\BSON\ObjectId($trainingId), // ID de la formation
                    'active' => true
                ])->toArray();
                foreach ($techTrainingDatas as $techTrainingData) {
                    if (isset($techTrainingData['period']) && isset($techTrainingData['year'])) {
                        $trainingSelected[] = $techTrainingData['training'];
                        $trainingTechSelected[] = $techTrainingData['user'];
                    }
                }

                $applications->updateOne(
                    [
                        "user" => new MongoDB\BSON\ObjectId($userId), // ID de l'utilisateur
                        "training" => new MongoDB\BSON\ObjectId($trainingId) // ID de la formation
                    ],[
                        '$set' => [
                            'active' => false, // Nouvelle valeur pour le champ 'active'
                            'updated' => date("d-m-Y H:i:s") // Exemple d'ajout d'un champ de date
                        ]
                    ]                        
                );

                $technician = $users->findOne([
                    '$and' => [
                        [
                            "_id" => new MongoDB\BSON\ObjectId($techTrainingData['user']), // ID de l'utilisateur
                            "active" => true
                        ]
                    ],
                ]);

                $technicians[] = $technician['firstName'].' '.$technician['lastName'];
            }
            $technicians = array_unique($technicians);
            $manager = $users->findOne([
                '$and' => [
                    [
                        "_id" => new MongoDB\BSON\ObjectId($currentUser), // ID du manager
                        "active" => true
                    ]
                ],
            ]);
            $trainingSelected = array_unique($trainingSelected);
            $trainingTechSelected = array_unique($trainingTechSelected);
            sendTrainingMail($training, $selectedDate, $place, $technicians, $manager, $trainingSelected, $trainingTechSelected, $trainingName);
        }
    }

    ?>

<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo 'Elaboration des Plans de Formations'  ?> | CFAO Mobility Academy</title>
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
                    <?php echo 'Elaboration des Plans de Formations'  ?> </h1>
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
                        <i class="fas fa-users fs-2 text-info mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div id="trainingPlace" class="min-w-80px" data-kt-countup="true"></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo 'Place(s) demandée(s) pour' ?> 
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
                        <i class="fas fa-book-open fs-2 text-primary mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div id="selectTraining" class="min-w-80px" data-kt-countup="true"></div>
                        </div>
                        <!--end::Animation-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">
                            <?php echo 'Session(s) de formation générant' ?> 
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
                        <i class="fas fa-calendar-alt fs-2 text-warning mb-2"></i>
                        <div class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                            <div id="durationTraining" class="min-w-80px" data-kt-countup="true"></div>
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
                                            <th class="min-w-150px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Company: activate to sort column ascending"
                                                style="width: 150px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo 'Nombre de techniciens' ?>
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
                                                aria-label="Payment Method: activate to sort column ascending"
                                                style="width: 150px; text-align: center; vertical-align: middle; height: 20px;">
                                                <?php echo 'Période de Formation' ?>
                                            </th>
                                            <th class="min-w-10px sorting" tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1" id="year"
                                                aria-label="Created Date: activate to sort column ascending"
                                                style="width: 10px; text-align: center; vertical-align: middle; height: 20px;">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600" id="table" style='text-align: center; vertical-align: middle; height: 50px;'>
                                        <?php foreach ($trainingDatas as $index => $training) {
                                            $periods = [];
                                            $isChecked = false; // Initialize a variable to track if the checkbox should be checked
                                            $currentYear = date('Y'); // Get the current year
                                            $trainingAllocations = [];

                                            if ($techFilter == 'all') {
                                                // Récupérer tous les techniciens associés à cette formation
                                                foreach ($technicians as $technician) {
                                                    $trainingAllocates = $applications->find([
                                                        'user' => new MongoDB\BSON\ObjectId($technician['_id']),
                                                        'training' => new MongoDB\BSON\ObjectId($training['_id']),
                                                        'active' => true
                                                    ])->toArray();

                                                    foreach ($trainingAllocates as $trainingAllocate) {
                                                        $trainingAllocations[] = $trainingAllocate;
                                                    }
                                                }

                                                // Vérifier si tous les techniciens ont l'année courante dans leurs années d'allocation
                                                foreach ($trainingAllocations as $trainingAllocation) {
                                                    $periods[] = $trainingAllocation['period'];
                                                    if ($trainingAllocation['year'] == $currentYear) {
                                                        $isChecked = true; // Si un technicien a l'année courante, mettre à jour
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
                                                    $trainingAllocations[] = $trainingAllocate;
                                                    $periods[] = $trainingAllocate['period'];
                                                    if ($trainingAllocate['year'] == $currentYear) {
                                                        $isChecked = true; // Si un technicien a l'année courante, mettre à jour
                                                    }
                                                }
                                            }

                                            $periods = array_unique($periods);
                                        ?>
                                        <tr class="odd" etat="<?php echo htmlspecialchars($training->active); ?>">
                                            <td><?php echo htmlspecialchars($training->label); ?></td>
                                            <td>
                                                <?php
                                                    $datas = [];
                                                    foreach ($technicians as $user) {
                                                        $trainingDt = $trainings->find([
                                                            '_id' => new MongoDB\BSON\ObjectId($training['_id']),
                                                            'users' => new MongoDB\BSON\ObjectId($user['_id']),
                                                            'active' => true
                                                        ])->toArray();
                                                        foreach ($trainingDt as $tr) {
                                                            $datas[] = $tr;
                                                        }
                                                    }
                                                    echo count($datas); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($training->brand); ?></td>
                                            <td><?php echo htmlspecialchars($training->level); ?></td>
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
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input id="checkbox-<?php echo $index; ?>" class="form-check-input" value="<?php echo htmlspecialchars($training['_id']); ?>"
                                                    <?php 
                                                        if ($isChecked) echo 'checked'; // Add the checked attribute if $isChecked is true
                                                        // Check if any dateOption is empty to disable the checkbox
                                                        foreach ($training['startDates'] as $dateOption): 
                                                            if ($dateOption == "") echo 'disabled'; 
                                                        endforeach; 
                                                    ?>
                                                    style='height: 25px; width: 25px; margin-top: 20px; margin-left: 5px; cursor: pointer' type="checkbox">
                                                    <label for="checkbox-<?php echo $index; ?>" class="custom-checkbox" style='height: 25px; width: 25px; margin-top: 26px; margin-left: 5px;'></label>
                                                </div>
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
    document.getElementById('year').textContent = 'Année ' + currentYear;
    
    const technicians = <?php echo json_encode($technicianIds); ?>;
    const trainingSelect = <?php echo json_encode($trainingSelected); ?>;
    const trainingTechSelected = <?php echo json_encode($trainingTechSelected); ?>;
    const trainingDatas = <?php echo json_encode($trainingDatas); ?>;

    console.log("Technicians:", technicians);
    console.log("trainingSelect:", trainingSelect);
    console.log("trainingTechSelected:", trainingTechSelected);
    console.log("trainingDatas:", trainingDatas);

    // Sélectionner toutes les checkboxes dans le tableau
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const trainingSelected = document.querySelector('#selectTraining');
    const trainingPlace = document.querySelector('#trainingPlace');
    const trainingDuration = document.querySelector('#durationTraining');
    
    const selectedTraining = [];
    
    checkboxes.forEach(checkbox => {
        // Appliquer les styles en fonction de l'état de la case à cocher
        if (checkbox.checked) {
            selectedTraining.push(checkbox);
        }
    });
    trainingSelected.setAttribute('data-kt-countup-value', trainingSelect.length);
    trainingPlace.setAttribute('data-kt-countup-value', trainingTechSelected.length);
    trainingDuration.setAttribute('data-kt-countup-value', (trainingTechSelected.length * 5));

    console.log('trainingSelectedValue', trainingPlace.getAttribute("data-kt-countup-value"));
    
    // Initialisation des variables
    var total = trainingSelected.getAttribute("data-kt-countup-value");
    var totalTechs = trainingPlace.getAttribute("data-kt-countup-value");

    trainingSelected.innerHTML = `${total} / ${trainingDatas.length}`;

    // Parcourir chaque case à cocher
    checkboxes.forEach(checkbox => {
        const label = checkbox.nextElementSibling; 

        // Appliquer des styles si la case est désactivée
        if (checkbox.disabled) {
            label.style.backgroundColor = 'red'; // Couleur de fond rouge
            label.style.border = '2px solid red'; // Bordure rouge
            label.style.color = 'white'; // Couleur de la croix
            label.style.display = 'flex'; // S'assurer que le label est affiché
            label.style.alignItems = 'center'; // Centrer verticalement
            label.style.justifyContent = 'center'; // Centrer horizontalement
            label.style.width = '25px'; // Largeur du label
            label.style.height = '25px'; // Hauteur du label
            label.style.borderRadius = '5px'; // Arrondir les coins
            label.textContent = '✖'; // Afficher la croix
        }

        // Appliquer les styles en fonction de l'état de la case à cocher
        if (checkbox.checked) {
            selectedTraining.push(checkbox);
            checkbox.classList.add('bg-success'); // Couleur de fond verte
        } else {
            checkbox.classList.remove('bg-success'); // Retirer la couleur de fond
        }

        // Écouter les changements de la case à cocher
        checkbox.addEventListener('change', function() {
            const rowIndex = this.id.split('-')[1]; // Récupérer l'index de la ligne
            const selectDate = document.querySelector(`#date-${rowIndex}`).value; // Récupérer la valeur de la date
            const userId = document.querySelector('#tech-filter').value; // Récupérer la valeur des techniciens
            const trainingId = this.value; // Récupérer la valeur de la case à cocher

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

            if (this.checked == true) {
                // Si la case est cochée
                checkbox.classList.add('bg-success'); // ajouter la classe 'bg-success'

                // Envoyer une requête AJAX pour activer
                $.ajax({
                    type: 'POST',
                    data: { ...datas, active: 1 },
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
            } else {
                // Si la case est décochée
                checkbox.classList.remove('bg-success'); // retirer la classe 'bg-success'

                // Envoyer une requête AJAX pour désactiver
                $.ajax({
                    type: 'POST',
                    data: { ...datas, disabled: 1 },
                    dataType: 'json',
                    success: function(response) {
                        // Si la réponse est déjà un objet JSON, pas besoin de la parser
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        console.log('response', response);
                        if (response && response.success) {
                            total = parseInt(total) - response.training;
                            totalTechs = parseInt(totalTechs) - response.technicians;
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
            }
        });
    });

    // Fonction pour mettre à jour l'interface utilisateur
    function updateUI() {
        trainingSelected .setAttribute('data-kt-countup-value', total);
        trainingPlace.setAttribute('data-kt-countup-value', totalTechs);
        trainingDuration.setAttribute('data-kt-countup-value', (totalTechs * 5));
        trainingSelected.innerHTML = total;
        trainingPlace.innerHTML = totalTechs;
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