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
    $results = $academy->results;
    $exams = $academy->exams;
    $tests = $academy->tests;
    $allocations = $academy->allocations;

    $tech = [];

    if ($_SESSION['profile'] == "Admin" || $_SESSION["profile"] == "Ressource Humaine" || $_SESSION['profile'] == "Directeur Pièce et Service" || $_SESSION['profile'] == "Directeur des Opérations") {
        $query = [
            "subsidiary" => $_SESSION['subsidiary'],
            "active" => true,
        ];
        
        if ($_SESSION["department"] != 'Equipment & Motors') {
            $query['department'] = $_SESSION["department"];
        }
        $technicians = $users->find($query)->toArray();
        foreach ($technicians as $techn) {
            if ($techn["profile"] == "Technicien") {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            } elseif ($techn["profile"] == "Manager" && $techn["test"] == true) {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            }
        }
    }
    if ($_SESSION['profile'] == "Super Admin" && isset($_GET['country'])) {
        $selectedCountry = $_GET['country'];

        $technicians = $users->find([
            '$and' => [
                [
                    "country" => $selectedCountry,
                    "active" => true,
                ],
            ],
        ])->toArray();
        foreach ($technicians as $techn) {
            if ($techn["profile"] == "Technicien") {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            } elseif ($techn["profile"] == "Manager" && $techn["test"] == true) {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            }
        }
    }
    if ($_SESSION['profile'] == "Super Admin" && !isset($_GET['country'])) {
        $technicians = $users->find([
            '$and' => [
                [
                    "active" => true,
                ],
            ],
        ])->toArray();
        foreach ($technicians as $techn) {
            if ($techn["profile"] == "Technicien") {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            } elseif ($techn["profile"] == "Manager" && $techn["test"] == true) {
                array_push($tech, new MongoDB\BSON\ObjectId($techn['_id']));
            }
        }
    }
    ?>
    <?php include_once "partials/header.php"; ?>
    <!--begin::Title-->
    <title><?php echo $list_result ?> | CFAO Mobility Academy</title>
    <!--end::Title-->

    <style>
        #kt_customers_table_wrapper td:nth-child(1) {
            position: sticky;
            left: 0;
        }
        #kt_customers_table_wrapper td:nth-child(1) {
            background: #edf2f7;
        }
        #kt_customers_table_wrapper th:nth-child(1) {
            z-index:2;
        }
    </style>

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
                        <?php echo $list_result ?> </h1>
                    <!--end::Title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-12"
                                placeholder="Recherche...">
                        </div>
                        <!--end::Search-->
                    </div>
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Toolbar-->
        <?php if ($_SESSION['profile'] == "Super Admin" || $_SESSION['profile'] == "Admin" || $_SESSION["profile"] == "Ressource Humaine" || $_SESSION['profile'] == "Directeur Pièce et Service" || $_SESSION['profile'] == "Directeur des Opérations") {?>
        <!--begin::Post-->
        <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
            <!--begin::Container-->
            <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <?php if ($_SESSION['profile'] === "Super Admin") { ?>
                            <!--begin::Filtres -->
                            <div class="container my-4">
                                <div class="row g-3 align-items-center">
                                    <!-- Filtre Pays -->
                                    <div class="col-md-6">
                                        <label for="country-select" class="form-label d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill fs-2 me-2 text-primary"></i> Pays
                                        </label>
                                        <select id="country-select" onchange="applyFilters()" name="country" class="form-select">
                                            <option value="all" <?php if (isset($selectedCountry) && $selectedCountry === 'all') echo 'selected'; ?>>Tous les pays</option>
                                            <?php if (isset($selectedCountry)) {
                                                foreach ($countries as $countryOption): ?>
                                                    <option value="<?php echo htmlspecialchars($countryOption); ?>" 
                                                            <?php if ($selectedCountry === $countryOption) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($countryOption); ?>
                                                    </option>
                                            <?php endforeach; } else { 
                                                foreach ($countries as $countryOption): ?>
                                                    <option value="<?php echo htmlspecialchars($countryOption); ?>">
                                                        <?php echo htmlspecialchars($countryOption); ?>
                                                    </option>
                                            <?php endforeach; } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--end::Filtres -->
                        <?php } ?>
                        <!--begin::Table-->
                        <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table aria-describedby=""
                                    class="table align-middle table-bordered fs-6 gy-5 dataTable no-footer"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-black fw-bold fs-7 text-uppercase gs-0">
                                        <?php if ($_SESSION['profile'] == "Super Admin" && !isset($_GET['country'])) {?>
                                            <th class="min-w-150px sorting text-center" tabindex="0"
                                                aria-controls="kt_customers_table" colspan="3"
                                                aria-label="Customer Name: activate to sort column ascending"
                                                style="width: 125px;"><?php echo $technicienss ?>
                                            </th>
                                        <?php } else { ?>
                                            <th class="min-w-150px sorting text-center" tabindex="0"
                                                aria-controls="kt_customers_table" colspan="2"
                                                aria-label="Customer Name: activate to sort column ascending"
                                                style="width: 125px;"><?php echo $technicienss ?>
                                            </th>
                                        <?php } ?>
                                            <th class="min-w-150px sorting text-center" tabindex="0"
                                                aria-controls="kt_customers_table" colspan="4"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $Level ?> <?php echo $junior ?></th>
                                            <th class="min-w-150px sorting text-center" tabindex="0"
                                                aria-controls="kt_customers_table" colspan="4"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $Level ?> <?php echo $senior ?></th>
                                            <th class="min-w-150px sorting text-center" tabindex="0"
                                                aria-controls="kt_customers_table" colspan="4"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $Level ?> <?php echo $expert ?></th>
                                            <tr></tr>
                                        <?php if ($_SESSION['profile'] == "Super Admin") {?>
                                            <th class="min-w-200px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $prenomsNoms ?></th>
                                            <th class="min-w-200px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $manager ?></th>
                                            <?php if ($_SESSION['profile'] == "Super Admin" && !isset($_GET['country'])) {?>
                                            <th class="min-w-150px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $pays ?></th>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <th class="min-w-200px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $prenomsNoms ?></th>
                                            <th class="min-w-200px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $agence ?></th>
                                        <?php } ?>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_connaissances_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_manager ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $note_test_junior ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_connaissances_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_manager ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $note_test_senior ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_connaissances_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_tech ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $qcm_tache_pro_manager ?></th>
                                            <th class="min-w-80px sorting text-center text-black fw-bold" tabindex="0"
                                                aria-controls="kt_customers_table"
                                                aria-label="Email: activate to sort column ascending"
                                                style="width: 155.266px;">
                                                <?php echo $note_test_expert ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600" id="table">
                                        <?php // if ($resultFacJu && $resultDeclaJu) {
                                            for (
                                                $i = 0;
                                                $i < count($tech);
                                                $i++
                                            ) {
                                                $user = $users->findOne([
                                                    '$and' => [
                                                        [
                                                            "_id" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                            "active" => true
                                                        ],
                                                    ],
                                                ]);
                                                if (!empty($user['manager'])) {
                                                    $manager = $users->findOne([
                                                        '$and' => [
                                                            ["_id" => new MongoDB\BSON\ObjectId($user['manager'])],
                                                            ["active" => true]
                                                        ],
                                                    ]);
                                                }
                                                
                                                $testFacJu = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Junior",
                                                                "type" => "Factuel",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $testDeclaJu = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Junior",
                                                                "type" => "Declaratif",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $testFacSe = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Senior",
                                                                "type" => "Factuel",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $testDeclaSe = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Senior",
                                                                "type" => "Declaratif",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $testFacEx = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Expert",
                                                                "type" => "Factuel",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $testDeclaEx = $tests
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "level" => "Expert",
                                                                "type" => "Declaratif",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $examFacJu = $exams
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "test" => new MongoDB\BSON\ObjectId($testFacJu['_id']),
                                                            ],
                                                        ],
                                                    ]);
                                                if (isset($testFacSe)) {
                                                    $examFacSe = $exams
                                                        ->findOne([
                                                            '$and' => [
                                                                [
                                                                    "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                    "test" => new MongoDB\BSON\ObjectId($testFacSe['_id']),
                                                                ],
                                                            ],
                                                        ]);
                                                }
                                                if (isset($testFacEx)) {
                                                    $examFacEx = $exams
                                                        ->findOne([
                                                            '$and' => [
                                                                [
                                                                    "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                    "test" => new MongoDB\BSON\ObjectId($testFacEx['_id']),
                                                                ],
                                                            ],
                                                        ]);
                                                }
                                                $examDeclaJu = $exams
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                "test" => new MongoDB\BSON\ObjectId($testDeclaJu['_id']),
                                                                "type" => "Technicien",
                                                            ],
                                                        ],
                                                    ]);
                                                if (isset($testDeclaSe)) {
                                                    $examDeclaSe = $exams
                                                        ->findOne([
                                                            '$and' => [
                                                                [
                                                                    "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                    "test" => new MongoDB\BSON\ObjectId($testDeclaSe['_id']),
                                                                    "type" => "Technicien",
                                                                ],
                                                            ],
                                                        ]);
                                                }
                                                if (isset($testDeclaEx)) {
                                                    $examDeclaEx = $exams
                                                        ->findOne([
                                                            '$and' => [
                                                                [
                                                                    "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                    "test" => new MongoDB\BSON\ObjectId($testDeclaEx['_id']),
                                                                    "type" => "Technicien",
                                                                ],
                                                            ],
                                                        ]);
                                                }
                                                if(!empty($user['manager'])) {
                                                    $examDeclaMaJu = $exams
                                                        ->findOne([
                                                            '$and' => [
                                                                [
                                                                    "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                    "manager" => new MongoDB\BSON\ObjectId($user['manager']),
                                                                    "test" => new MongoDB\BSON\ObjectId($testDeclaJu['_id']),
                                                                    "type" => "Manager",
                                                                ],
                                                            ],
                                                        ]);
                                                    if (isset($testDeclaSe)) {
                                                        $examDeclaMaSe = $exams
                                                            ->findOne([
                                                                '$and' => [
                                                                    [
                                                                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                        "manager" => new MongoDB\BSON\ObjectId($user['manager']),
                                                                        "test" => new MongoDB\BSON\ObjectId($testDeclaSe['_id']),
                                                                        "type" => "Manager",
                                                                    ],
                                                                ],
                                                            ]);
                                                    }
                                                    if (isset($testDeclaEx)) {
                                                        $examDeclaMaEx = $exams
                                                            ->findOne([
                                                                '$and' => [
                                                                    [
                                                                        "user" => new MongoDB\BSON\ObjectId($user['_id']),
                                                                        "manager" => new MongoDB\BSON\ObjectId($user['manager']),
                                                                        "test" => new MongoDB\BSON\ObjectId($testDeclaEx['_id']),
                                                                        "type" => "Manager",
                                                                    ],
                                                                ],
                                                            ]);
                                                    }
                                                }

                                                $allocateFacJu = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Factuel",
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $allocateFacSe = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Factuel",
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $allocateFacEx = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Factuel",
                                                            ],
                                                        ],
                                                    ]);
                                                    
                                                $allocateDeclaJu = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Declaratif",
                                                            ],
                                                        ],
                                                    ]);
                                                    
                                                $allocateDeclaSe = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Declaratif",
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $allocateDeclaEx = $allocations
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Declaratif",
                                                            ],
                                                        ],
                                                    ]);

                                                $resultFacJu = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Factuel",
                                                                "typeR" => "Technicien",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $resultFacSe = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Factuel",
                                                                "typeR" => "Technicien",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultFacEx = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Factuel",
                                                                "typeR" => "Technicien",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $resultDeclaJu = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Technicien - Manager",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaSe = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Technicien - Manager",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaEx = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Technicien - Manager",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $resultDeclaJuTech = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Techniciens",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaSeTech = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Techniciens",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaExTech = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Techniciens",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                            
                                                $resultDeclaJuMa = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Junior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Managers",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaSeMa = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Senior",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Managers",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                $resultDeclaExMa = $results
                                                    ->findOne([
                                                        '$and' => [
                                                            [
                                                                "user" => new MongoDB\BSON\ObjectId($tech[$i]),
                                                                "level" => "Expert",
                                                                "type" => "Declaratif",
                                                                "typeR" => "Managers",
                                                                "active" => true,
                                                            ],
                                                        ],
                                                    ]);
                                                if (isset($resultFacJu)) {
                                                    $percentageFacJu =
                                                        ceil(($resultFacJu["score"] *
                                                            100) /
                                                        $resultFacJu["total"]);
                                                }
                                                if (isset($resultDeclaJu)) {
                                                    $percentageDeclaJu =
                                                        ceil(($resultDeclaJu[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaJu["total"]);
                                                }
                                                if (isset($resultDeclaJuTech)) {
                                                    $percentageDeclaJuTech =
                                                        ceil(($resultDeclaJuTech[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaJuTech["total"]);
                                                }
                                                if (isset($resultDeclaJuMa)) {
                                                    $percentageDeclaJuMa =
                                                        ceil(($resultDeclaJuMa[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaJuMa["total"]);
                                                }
                                                if (isset($resultFacSe)) {
                                                    $percentageFacSe =
                                                        ceil(($resultFacSe["score"] *
                                                            100) /
                                                        $resultFacSe["total"]);
                                                }
                                                if (isset($resultDeclaSe)) {
                                                    $percentageDeclaSe =
                                                        ceil(($resultDeclaSe[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaSe["total"]);
                                                }
                                                if (isset($resultDeclaSeTech)) {
                                                    $percentageDeclaSeTech =
                                                        ceil(($resultDeclaSeTech[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaSeTech["total"]);
                                                }
                                                if (isset($resultDeclaSeMa)) {
                                                    $percentageDeclaSeMa =
                                                        ceil(($resultDeclaSeMa[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaSeMa["total"]);
                                                }
                                                if (isset($resultFacEx)) {
                                                    $percentageFacEx =
                                                        ceil(($resultFacEx["score"] *
                                                            100) /
                                                        $resultFacEx["total"]);
                                                }
                                                if (isset($resultDeclaEx)) {
                                                    $percentageDeclaEx =
                                                        ceil(($resultDeclaEx[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaEx["total"]);
                                                }
                                                if (isset($resultDeclaExTech)) {
                                                    $percentageDeclaExTech =
                                                        ceil(($resultDeclaExTech[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaExTech["total"]);
                                                }
                                                if (isset($resultDeclaExMa)) {
                                                    $percentageDeclaExMa =
                                                        ceil(($resultDeclaExMa[
                                                            "score"
                                                        ] *
                                                            100) /
                                                        $resultDeclaExMa["total"]);
                                                }
                                                if (isset($resultDeclaJu) && isset($resultFacJu)) {
                                                    $junior = ceil(($percentageFacJu + $percentageDeclaJu) / 2);
                                                }
                                                if (isset($resultDeclaSe) && isset($resultFacSe)) {
                                                    $senior = ceil(($percentageFacSe + $percentageDeclaSe) / 2);
                                                }
                                                if (isset($resultDeclaEx) && isset($resultFacEx)) {
                                                    $expert = ceil(($percentageFacEx + $percentageDeclaEx) / 2);
                                                }
                                                ?>
                                        <tr class="odd">
                                            <td class="text-center">
                                                <?php echo $user[
                                                    "firstName"
                                                ]; ?> <?php echo $user[
                                                    "lastName"
                                                ]; ?>
                                            </td>
                                            <?php if($_SESSION['profile'] == "Super Admin") { ?>
                                            <td class="text-center">
                                                <?php if (!empty($user['manager'])) { ?>
                                                <?php echo $manager[
                                                    "firstName"
                                                ]; ?> <?php echo $manager[
                                                    "lastName"
                                                ]; ?>
                                                <?php } ?>
                                            </td>
                                            <?php if($_SESSION['profile'] == "Super Admin" && !isset($_GET['country'])) { ?>
                                            <td class="text-center">
                                                <?php echo $user[
                                                    "country"
                                                ]; ?>
                                            </td>
                                            <?php } } ?>
                                            <?php if($_SESSION['profile'] == "Admin" || $_SESSION["profile"] == "Ressource Humaine" || $_SESSION['profile'] == "Directeur Pièce et Service" || $_SESSION['profile'] == "Directeur des Opérations") { ?>
                                            <td class="text-center">
                                                <?php echo $user[
                                                    "agency"
                                                ]; ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examFacJu) && $examFacJu['active'] == true ) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateFacJu['active'] == true ) { ?>
                                            <td class="text-center">
                                                <?php if($percentageFacJu < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageFacJu."%" ?>
                                                </span>
                                                <?php } else if($percentageFacJu < 80 ) { ?>
                                                    <?php if($percentageFacJu >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageFacJu."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageFacJu >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageFacJu."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateFacJu['active'] == false ) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaJu) && $examDeclaJu['active'] == true ) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaJu['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaJuTech < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaJuTech."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaJuTech < 80 ) { ?>
                                                    <?php if($percentageDeclaJuTech >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaJuTech."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaJuTech >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaJuTech."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaJu['active'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaMaJu) && $examDeclaMaJu['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaJu['activeManager'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaJuMa < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaJuMa."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaJuMa < 80 ) { ?>
                                                    <?php if($percentageDeclaJuMa >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaJuMa."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaJuMa >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaJuMa."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaJu['activeManager'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if($allocateDeclaJu['activeManager'] == true && $allocateDeclaJu['active'] == true && $allocateFacJu['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($junior < 60 ) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Junior&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-danger text-danger btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau junior"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $junior."%" ?>
                                                </a>
                                                <?php } else if($junior < 80 ) { ?>
                                                    <?php if($junior >= 60 ) { ?>
                                                    <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Junior&user=<?php echo $user->_id; ?>"
                                                        class="btn btn-light btn-active-light-warning text-warning btn-sm"
                                                        title="Cliquez ici pour voir le résultat du technicien pour le niveau junior"
                                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <?php echo $junior."%" ?>
                                                    </a>
                                                    <?php } ?>
                                                <?php } else if($junior >= 80) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Junior&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-success text-success btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau junior"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $junior."%" ?>
                                                </a>
                                                <?php } ?>
                                            </td>
                                            <?php } else { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if (isset($allocateFacSe) && isset($allocateDeclaSe)) { ?>
                                            <?php if(isset($examFacSe) && $examFacSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateFacSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageFacSe < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageFacSe."%" ?>
                                                </span>
                                                <?php } else if($percentageFacSe < 80 ) { ?>
                                                    <?php if($percentageFacSe >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageFacSe."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageFacSe >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageFacSe."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateFacSe['active'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaSe) && $examDeclaSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaSeTech < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaSeTech."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaSeTech < 80 ) { ?>
                                                    <?php if($percentageDeclaSeTech >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaSeTech."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaSeTech >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaSeTech."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaSe['active'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaMaSe) && $examDeclaMaSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaSe['activeManager'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaSeMa < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaSeMa."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaSeMa < 80 ) { ?>
                                                    <?php if($percentageDeclaSeMa >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaSeMa."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaSeMa >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaSeMa."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaSe['activeManager'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if($allocateDeclaSe['activeManager'] == true && $allocateDeclaSe['active'] == true && $allocateFacSe['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($senior < 60 ) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacSe["numberTest"] ?>&level=Senior&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-danger text-danger btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau senior"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $senior."%" ?>
                                                </a>
                                                <?php } else if($senior < 80 ) { ?>
                                                    <?php if($senior >= 60 ) { ?>
                                                    <a href="./result.php?numberTest=<?php echo $resultFacSe["numberTest"] ?>&level=Senior&user=<?php echo $user->_id; ?>"
                                                        class="btn btn-light btn-active-light-warning text-warning btn-sm"
                                                        title="Cliquez ici pour voir le résultat du technicien pour le niveau senior"
                                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <?php echo $senior."%" ?>
                                                    </a>
                                                    <?php } ?>
                                                <?php } else if($senior >= 80) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacSe["numberTest"] ?>&level=Senior&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-success text-success btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau senior"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $senior."%" ?>
                                                </a>
                                                <?php } ?>
                                            </td>
                                            <?php } else { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php } else { ?>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <?php } ?>
                                            <?php if (isset($allocateFacEx) && isset($allocateDeclaEx)) { ?>
                                            <?php if(isset($examFacEx) && $examFacEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateFacEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageFacEx < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageFacEx."%" ?>
                                                </span>
                                                <?php } else if($percentageFacEx < 80 ) { ?>
                                                    <?php if($percentageFacEx >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageFacEx."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageFacEx >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageFacEx."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateFacEx['active'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaEx) && $examDeclaEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaExTech < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaExTech."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaExTech < 80 ) { ?>
                                                    <?php if($percentageDeclaExTech >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaExTech."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaExTech >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaExTech."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaEx['active'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if(isset($examDeclaMaEx) && $examDeclaMaEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <span class="badge text-primary fs-7 m-1">
                                                    <?php echo $en_cours ?>
                                                </span>
                                            </td>
                                            <?php } elseif($allocateDeclaEx['activeManager'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($percentageDeclaExMa < 60 ) { ?>
                                                <span class="badge text-danger fs-7 m-1">
                                                    <?php echo $percentageDeclaExMa."%" ?>
                                                </span>
                                                <?php } else if($percentageDeclaExMa < 80 ) { ?>
                                                    <?php if($percentageDeclaExMa >= 60 ) { ?>
                                                    <span class="badge text-warning fs-7 m-1">
                                                        <?php echo $percentageDeclaExMa."%" ?>
                                                    </span>
                                                    <?php } ?>
                                                <?php } else if($percentageDeclaExMa >= 80) { ?>
                                                <span class="badge text-success fs-7 m-1">
                                                    <?php echo $percentageDeclaExMa."%" ?>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php } elseif($allocateDeclaEx['activeManager'] == false) { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php if($allocateDeclaEx['activeManager'] == true && $allocateDeclaEx['active'] == true && $allocateFacEx['active'] == true) { ?>
                                            <td class="text-center">
                                                <?php if($expert < 60 ) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Expert&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-danger text-danger btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau expert"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $expert."%" ?>
                                                </a>
                                                <?php } else if($expert < 80 ) { ?>
                                                    <?php if($expert >= 60 ) { ?>
                                                    <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Expert&user=<?php echo $user->_id; ?>"
                                                        class="btn btn-light btn-active-light-warning text-warning btn-sm"
                                                        title="Cliquez ici pour voir le résultat du technicien pour le niveau expert"
                                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <?php echo $expert."%" ?>
                                                    </a>
                                                    <?php } ?>
                                                <?php } else if($expert >= 80) { ?>
                                                <a href="./result.php?numberTest=<?php echo $resultFacJu["numberTest"] ?>&level=Expert&user=<?php echo $user->_id; ?>"
                                                    class="btn btn-light btn-active-light-success text-success btn-sm"
                                                    title="Cliquez ici pour voir le résultat du technicien pour le niveau expert"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <?php echo $expert."%" ?>
                                                </a>
                                                <?php } ?>
                                            </td>
                                            <?php } else { ?>
                                            <td class="text-center">
                                                <?php echo $completer ?>
                                            </td>
                                            <?php } ?>
                                            <?php } else { ?>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <td class="text-center" style="background-color: #f9f9f9;">
                                            </td>
                                            <?php } ?>
                                            <!--end::Menu-->
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div
                                    class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                    <div class="dataTables_length">
                                        <label><select id="kt_customers_table_length" name="kt_customers_table_length"
                                                class="form-select form-select-sm form-select-solid">
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                                <option value="300">300</option>
                                                <option value="500">500</option>
                                            </select></label>
                                    </div>
                                </div>
                                <div
                                    class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination" id="kt_customers_table_paginate">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
                <!--begin::Export dropdown-->
                <!-- <div class="d-flex justify-content-end align-items-center" style="margin-top: 20px;">
                    <button type="button" id="excel" title="Cliquez ici pour importer la table" class="btn btn-primary">
                        <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Excel
                    </button>
                </div> -->
                <!--end::Export dropdown-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
        <?php } ?>
    </div>
    <!--end::Body-->
    <script>
        function applyFilters() {
            var country = document.querySelector("#country-select").value;
            var urlParams = new URLSearchParams(window.location.search);

            // Mettre à jour ou ajouter le paramètre 'country' ou 'agency' dans l'URL en fonction du profil
            if (country && country !== "all") {
                urlParams.set('country', country);
            } else {
                urlParams.delete('country');
            }

            // Rediriger vers l'URL mise à jour
            window.location.search = urlParams.toString();
        }
    </script>
    <?php include_once "partials/footer.php"; ?>
<?php
} ?>
