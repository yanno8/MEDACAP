<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION["id"])) {
    header("Location: ../../");
    exit();
} else {
    require_once "../../vendor/autoload.php";
    // Inclure le fichier de traitement
    $recommendationData = include "processRecommendations.php";

    // Create connection
    $conn = new MongoDB\Client("mongodb://localhost:27017");

    // Connecting in database
    $academy = $conn->academy;

    // Connecting in collections
    $allocations = $academy->allocations;

    // Extraire les données pour l'affichage
    $technicians = $recommendationData['technicians'];
    $scores = $recommendationData['scores'];
    $trainings = $recommendationData['trainings'];
    $missingGroups = $recommendationData['missingGroups'];
    $debug = $recommendationData['debug'];
    $levelOrder = [
        'Junior' => 1,
        'Senior' => 2,
        'Expert' => 3
    ];
    // Récupérer les valeurs des filtres depuis les paramètres GET
    $selectedCountry = $_GET['country'] ?? 'all';
    $selectedAgency = $_GET['agency'] ?? 'all';
    $selectedLevel = $_GET['level'] ?? 'all';
    $selectedManagerId = $_GET['manager'] ?? 'all';

    // Récupérer les techniciens
    $profileSession = $_SESSION['profile'];

    if (isset($_POST['submit'])) {
        $technicianID = $_POST['technicianID'];
        $trainingID = $_POST['trainingID'];

        $allocations->updateOne(
            [
                "user" => new MongoDB\BSON\ObjectId($technicianID),
                "training" => new MongoDB\BSON\ObjectId($trainingID)
            ],
            [
                '$set' => ['active' => true]
            ]
        );
        
    }

    // Votre code pour les pays et agences
    $country = isset($_SESSION["country"]) ? $_SESSION["country"] : 'all'; // 'all' par défaut

    // Fonction utilitaire pour obtenir l'ordre des niveaux
    function getLevelOrder($level) {
        $levelOrder = [
            'Junior' => 1,
            'Senior' => 2,
            'Expert' => 3
        ];
        return isset($levelOrder[$level]) ? $levelOrder[$level] : 1;
    }

    function renderTrainingCard($userId, $training, $allocations) {
        
        $trainingAllocation = $allocations->findOne([
            '$and' => [
                [
                    'user' => new MongoDB\BSON\ObjectId($userId),
                    'training' => new MongoDB\BSON\ObjectId($training['_id'])
                ]
            ],
        ]);

        if ($trainingAllocation['active'] == true ) {
            return '
                <span class="badgee badge-training" style="background-color: #50cd89;" data-bs-toggle="modal" data-bs-target="#kt_modal_' . $training['_id'] . '_' . $userId . '"
                title="' . htmlspecialchars($training['name']) . '">
                    ' . htmlspecialchars($training['code']) . '
                </span>
            ';
        } else {
            return '
                <span class="badgee badge-training" style="background-color: #6c757d;" data-bs-toggle="modal" data-bs-target="#kt_modal_' . $training['_id'] . '_' . $userId . '"
                title="' . htmlspecialchars($training['name']) . '">
                    ' . htmlspecialchars($training['code']) . '
                </span>
            ';
        }
    }
        
    function renderModal($userId, $allocations, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar) {
        $profile = $_SESSION["profile"];
        
        $trainingAllocation = $allocations->findOne([
            '$and' => [
                [
                    'user' => new MongoDB\BSON\ObjectId($userId),
                    'training' => new MongoDB\BSON\ObjectId($training['_id'])
                ]
            ],
        ]);
        
        $details = [
            $training_code => $training['code'],
            $label_training => $training['name'],
            $Type => $training['type'],
            $Brand => $training['brand'],
            $Level => $training['level']
        ];

        $specialities = [];
        foreach ($training['specialities'] as $speciality) {
            $specialities[] = $speciality;
        }
        $specialities = implode(', ', $specialities);
        
        if ($training['type'] == 'Distancielle' || $training['type'] == 'E-learning') {
            $details[$training_link] = $training['link'];
        }

        $detailsHtml = '';
        foreach ($details as $label => $value) {
            $detailsHtml .= '
                <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <label class="fs-4 fw-bolder mb-2">' . htmlspecialchars($label) . ' :</label>
                        <div class="ms-5 fs-5 fw-bold text-gray-900 mb-2">' . htmlspecialchars($value) . '</div>
                    </div>
                </div>
            ';
        }
                
        $applyHtml = '';
        if ($profile == 'Admin' || $profile == 'Directeur Filiale') {
            if ($trainingAllocation['active'] == false ) {
                $applyHtml .= '
                    <a href="./registerTechnician?user=' . $userId . '&training=' . $training['_id'] . '" type="button"
                        class="btn btn-primary text-white fw-bolder">
                            Inscrire le technicien à cette formation
                    </a>
                ';
            }
        }


        return '
            <div class="modal fade" id="kt_modal_' . $training['_id'] . '_' . $userId . '" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog mw-650px">
                    <div class="modal-content">
                        <div class="modal-header pb-0 border-0 justify-content-end">
                            <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"></i>
                            </div>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                data-kt-users-modal-action="close" data-bs-dismiss="modal"
                                data-kt-menu-dismiss="true">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16"
                                            height="2" rx="1"
                                            transform="rotate(-45 6 17.3137)"
                                            fill="black" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                            transform="rotate(45 7.41422 6)" fill="black" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                            <div class="text-center mb-13">
                                <h1 class="mb-3">' . htmlspecialchars($data) . '</h1>
                            </div>
                            <div class="mb-10 ">
                                <div class="mh-300px scroll-y me-n7 pe-7">
                                    ' . $detailsHtml . '
                                    <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                        <div class="d-flex align-items-center">
                                            <label class="fs-4 fw-bolder mb-2">' . $specialities_studies . ' :</label>
                                            <div class="ms-5 fs-5 fw-bold text-gray-900 mb-2">' . htmlspecialchars($specialities) . '</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                        <div class="d-flex align-items-center">
                                            <label class="fs-4 fw-bolder mb-2">' . $trainingDate . ' :</label>
                                            <a href="./planning"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir le calendrier des formations">
                                                ' . htmlspecialchars($voir_calendar) . '
                                            </a>
                                        </div>
                                    </div>
                                </div><br>
                                '.$applyHtml.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        
        return '
            <div class="modal fade" id="applyModal' . $training['_id'] . '" tabindex="-2" aria-hidden="true">
                <div class="modal-dialog mw-650px">
                    <div class="modal-content">
                        <div class="modal-header pb-0 border-0 justify-content-end">
                            <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"></i>
                            </div>
                        </div>
                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                            <div class="text-center mb-13">
                                <h1 class="mb-3">' . htmlspecialchars($data) . '</h1>
                            </div>
                            <div class="mb-10 ">
                                <div class="mh-300px scroll-y me-n7 pe-7">
                                    ' . $detailsHtml . '
                                    <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                        <div class="d-flex align-items-center">
                                            <label class="fs-4 fw-bolder mb-2">' . $specialities_studies . ' :</label>
                                            <div class="ms-5 fs-5 fw-bold text-gray-900 mb-2">' . htmlspecialchars($specialities) . '</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                        <div class="d-flex align-items-center">
                                            <label class="fs-4 fw-bolder mb-2">' . $trainingDate . ' :</label>
                                            <a href="./planning"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir le calendrier des formations">
                                                ' . htmlspecialchars($voir_calendar) . '
                                            </a>
                                        </div>
                                    </div>
                                </div><br>
                                '.$applyHtml.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
    ?>
    <?php include_once "partials/header.php"; ?>
    <script>
        var countries = <?php echo json_encode($countries); ?>;
        var agencies = <?php echo json_encode($agencies); ?>;
    </script>
    <!--begin::Title-->
    <?php if ($_SESSION['profile'] == 'Manager') { ?>
        <title><?php echo $train_collab ?> | CFAO Mobility Academy</title>
    <?php } else { ?>
        <title><?php echo $train_tech ?> | CFAO Mobility Academy</title>
    <?php } ?> 


    <!--end::Title-->
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Highlight every other row */
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Hover effect */
        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Center text */
        .text-center {
            text-align: center;
        }

        /* Badge colors for levels */
        .badgee {
            display: inline-block;
            padding: 0.3em 0.6em;
            font-size: 0.9em;
            font-weight: bold;
            color: #fff;
            /* background-color: #6c757d; */
            border-radius: 5px;
            margin: 3px 5px; /* Ajout de marges pour espacement */
            text-transform: uppercase;
            cursor: pointer; /* Apparence interactive */
        }

        .badgee:hover {
            background-color: #5a6268; /* Couleur plus foncée au survol */
        }

        .level-column {
            vertical-align: top; /* Alignement vertical des colonnes */
            text-align: center; /* Centrage des badges */
        }

        .empty-level {
            color: #888; /* Couleur pour "Aucune formation recommandée" */
            font-style: italic; /* Texte en italique */
        }

        .badge-junior {
            background-color: #007bff;
        }

        .badge-senior {
            background-color: #ffc107;
        }

        .badge-expert {
            background-color: #28a745;
        }

        /* Highlight missing information */
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .badge-training {
            position: relative;
            cursor: pointer;
        }

        .badge-training:hover::after {
            content: attr(title); /* Affiche le contenu de l'attribut title */
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 5px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 10;
            font-size: 12px;
        }
        .table-secondary {
            background-color: #f8f9fa;
            color: #6c757d; /* Optionnel: couleur du texte */
            pointer-events: none; /* Désactiver les interactions */
        }
        .popover {
            max-width: 300px; /* Limite la largeur du popover */
        }

        .popover-header {
            background-color: #ffc107; /* Couleur d'arrière-plan de l'en-tête */
            color: #fff; /* Couleur du texte de l'en-tête */
        }

        .popover-body {
            background-color: #fff; /* Couleur d'arrière-plan du corps */
            color: #333; /* Couleur du texte du corps */
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
                    <?php if ($_SESSION['profile'] == 'Manager') { ?>
                        <!--begin::Title-->
                        <h1 class="text-dark fw-bolder my-1 fs-1">
                            <?php echo $train_collab ?> 
                        </h1>
                        <!--end::Title-->
                    <?php } else { ?>
                        <!--begin::Title-->
                        <h1 class="text-dark fw-bolder my-1 fs-1">
                            <?php echo $train_tech ?> 
                        </h1>
                        <!--end::Title-->
                    <?php } ?> 
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-12"
                                placeholder="<?php echo $recherche?>">
                        </div>
                        <!--end::Search-->
                    </div>
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Post-->
        <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
            <!--begin::Container-->
            <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Filtres -->
                        <div class="container my-4">
                            <div class="row g-3 align-items-center">
                                <!-- Filtre Pays -->
                                <div class="col-md-4">
                                    <label for="country-filter" class="form-label d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill fs-2 me-2 text-primary"></i> Pays
                                    </label>
                                    <select id="country-filter" name="country" class="form-select" <?php if ($profileSession == 'Admin' || $profileSession == 'Resource Humaine' || $profileSession == 'Manager' || $profileSession == 'Directeur Pièce et Service' || $profileSession == 'Directeur des Opérations') echo 'disabled'; ?>>
                                        <option value="all" <?php if ($selectedCountry === 'all') echo 'selected'; ?>>Tous les pays</option>
                                        <?php foreach ($countries as $countryOption): ?>
                                        <option value="<?php echo htmlspecialchars($countryOption); ?>" 
                                                <?php if ($selectedCountry === $countryOption) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($countryOption); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Filtre Agences -->
                                <div class="col-md-4">
                                    <label for="agency-filter" class="form-label d-flex align-items-center">
                                        <i class="bi bi-building fs-2 me-2 text-warning"></i> Agence
                                    </label>
                                    <select id="agency-filter" name="agency" class="form-select" 
                                            <?php echo ($selectedCountry === 'all') ? 'disabled' : ''; ?>>
                                        <option value="all" <?php if ($selectedAgency === 'all') echo 'selected'; ?>>Toutes les agences</option>
                                        <?php if ($selectedCountry !== 'all' && isset($agencies[$selectedCountry])) {
                                            foreach ($agencies[$selectedCountry] as $agencyOption) {
                                                ?>
                                                <option value="<?php echo htmlspecialchars($agencyOption); ?>" 
                                                        <?php if ($selectedAgency === $agencyOption) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($agencyOption); ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Filtre Manager -->
                                <div class="col-md-4">
                                    <label for="manager-filter" class="form-label d-flex align-items-center">
                                        <i class="bi bi-person-fill fs-2 me-2 text-info"></i> Manager
                                    </label>
                                    <select id="manager-filter" name="manager" class="form-select" <?php if ($profileSession == 'Manager') echo 'disabled'; ?>>
                                        <option value="all" selected>Tous les managers</option>
                                        <!-- Options des managers seront insérées ici dynamiquement -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--end::Filtres -->
                        <!--begin::Table-->
                        <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table aria-describedby=""
                                    class="table align-middle table-bordered fs-6 gy-5 dataTable no-footer"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-black fw-bold fs-7 text-uppercase gs-0">

                                            <th class="min-w-150px sorting text-center " tabindex="0" aria-controls="kt_customers_table"
                                                rowspan="1" colspan="1"
                                                aria-label="Payment Method: activate to sort column ascending"
                                                style="width: 150px; text-align: center;"><?php echo $prenomsNomsTechs ?>
                                            </th>
                                            <?php foreach (['Junior', 'Senior', 'Expert'] as $level): ?>
                                                <th class="min-w-125px sorting text-center" tabindex="0" aria-controls="kt_customers_table"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Customer Name: activate to sort column ascending"
                                                    style="width: 125px; text-align: center;"><?php echo $training_done_by_tech.' '.$level ?>
                                                </th>
                                            <?php endforeach; ?>

                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600" id="table">
                                        <?php foreach ($filteredTechnicians as $technician): ?>
                                            <?php 
                                                $technicianId = (string)$technician['_id']; 
                                                $technicianLevel = $technician['level'];
                                                $technicianLevelOrder = $levelOrder[$technicianLevel] ?? 1; // Par défaut à 1 si niveau inconnu
                                                $technicianMissingGroups = $missingGroups[$technicianId] ?? [];
                                            ?>
                                            <tr>
                                                <!-- Nom et Prénom -->
                                                <td class="text-center">
                                                    <?php echo htmlspecialchars($technician['firstName'] . ' ' . $technician['lastName']); ?>    
                                                </td>
                                                <?php foreach (['Junior', 'Senior', 'Expert'] as $level): ?>
                                                    <?php   
                                                        $currentLevelOrder = getLevelOrder($level);
                                                        $isHigher = $currentLevelOrder > $technicianLevelOrder;
                                                    ?>
                                                    <!-- Cellule de Formation Recommandée -->
                                                    <td class="text-center <?php echo $isHigher ? 'table-secondary' : ''; ?>">
                                                    <?php if (!$isHigher): ?>
                                                        <?php if (!empty($trainings[$technicianId][$level])): ?>
                                                                <ul style="list-style: none; padding: 0; margin: 0;">
                                                                    <?php foreach ($trainings[$technicianId][$level] as $training): ?>
                                                                        <?php echo renderTrainingCard($technicianId, $training, $allocations); ?>
                                                                        <?php echo renderModal($technicianId, $allocations, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php else: ?>
                                                            <span class="empty-level"><?php echo $no_training_recommandation; ?></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <!-- Ajouter l'Icône du Popover si des groupes manquants existent pour ce niveau -->
                                                        <?php 
                                                            $missingGroupsForLevel = $missingGroups[$technicianId][$level] ?? [];
                                                            if (!empty($missingGroupsForLevel)):
                                                                // Construire le contenu du popover pour ce niveau
                                                                $popoverContent = '<strong>Besoins retenus dans les spécialités suivantes :</strong><br>';
                                                                foreach ($missingGroupsForLevel as $group) {
                                                                    $popoverContent .= '<strong>' . htmlspecialchars($group['groupName']) . ' :</strong><br> ' 
                                                                        . '<em>Type(s)   de formation :</em> ' . htmlspecialchars(implode(', ', $group['trainingTypes'])) . '<br>';
                                                                }
                                                                // Nettoyer le contenu en supprimant les <br> supplémentaires
                                                                $popoverContent = rtrim($popoverContent, '<br><br>');
                                                        ?>
                                                                <i 
                                                                    class="bi bi-info-circle-fill text-warning ms-2" 
                                                                    style="cursor: pointer;" 
                                                                    data-bs-toggle="popover" 
                                                                    data-bs-html="true" 
                                                                    data-bs-trigger="hover" 
                                                                    data-bs-content="<?php echo htmlspecialchars($popoverContent, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    title="Formations En Production">
                                                                </i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- Nouvelle colonne : nombre de formations -->
                                                    <!-- <td class="text-center <?php echo $isHigher ? 'table-secondary' : ''; ?>">
                                                    <?php if (!$isHigher): ?>
                                                        <?php echo !empty($trainings[$technicianId][$level]) 
                                                            ? count($trainings[$technicianId][$level]) 
                                                            : 0; ?>
                                                    <?php endif; ?>
                                                    </td> -->
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
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
                <div class="d-flex justify-content-end align-items-center" style="margin-top: 20px;">
                    <!--begin::Export-->
                    <button type="button" id="excel" class="btn btn-light-danger me-3" data-bs-toggle="modal"
                        data-bs-target="#kt_customers_export_modal">
                        <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                        <?php echo $excel ?>
                    </button>
                    <!--end::Export-->
                </div>
                <!--end::Export dropdown-->
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
    <script>
        
    $(document).ready(function() {
        $("#excel").on("click", function() {
            let table = document.getElementsByTagName("table");
            debugger;
            TableToExcel.convert(table[0], {
                name: `Mes Formations.xlsx`
            })
        });
    });

    </script>
    <script>
        const recommendationData = <?php echo json_encode($recommendationData); ?>;

        console.log("Technicians:", recommendationData.technicians);
        console.log("Scores:", recommendationData.scores);
        console.log("Trainings:", recommendationData.trainings);
        console.log("Debug Logs:", recommendationData.debug);
        console.log("Missing groups:", recommendationData.missingGroups);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    placement: 'right', 
                    trigger: 'hover'
                })
            })
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var profile = '<?php echo $profileSession ?>';
        document.addEventListener('DOMContentLoaded', function () {
            var countryFilter = document.getElementById('country-filter');
            var agencyFilter = document.getElementById('agency-filter');
            // var levelFilter = document.getElementById('level-filter');
            var managerFilter = document.getElementById('manager-filter');

            var agenciesByCountry = <?php echo json_encode($agencies); ?>;

            function getParameterByName(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name) || 'all';
            }


            function updateAgencyFilter() {
                var selectedCountry = countryFilter.value;
                if (selectedCountry === 'all') {
                    // Désactiver et vider le filtre Agences
                    agencyFilter.disabled = true;
                    agencyFilter.innerHTML = '<option value="all" selected>Toutes les agences</option>';
                } else if (profile === 'Manager') {
                    // Désactiver et vider le filtre Agences
                    agencyFilter.disabled = true;
                }  else {
                    // Activer le filtre Agences et charger les agences correspondantes
                    agencyFilter.disabled = false;
                    var agenciesForCountry = agenciesByCountry[selectedCountry] || [];
                    var options = '<option value="all"' + (agencyFilter.value === 'all' ? ' selected' : '') + '>Toutes les agences</option>';
                    agenciesForCountry.forEach(function (agency) {
                        var selected = agencyFilter.value === agency ? ' selected' : '';
                        options += '<option value="' + agency + '"' + selected + '>' + agency + '</option>';
                    });
                    agencyFilter.innerHTML = options;
                }
            }

            function updateManagerFilter() {
                var selectedCountry = countryFilter.value;
                var selectedAgency = agencyFilter.value;
                var selectedManagerId = getParameterByName('manager') || 'all';

                console.log("Dans updateManagerFilter");
                console.log("Pays sélectionné :", selectedCountry);
                console.log("Agence sélectionnée :", selectedAgency);

                var params = new URLSearchParams();
                params.append('country', selectedCountry);
                params.append('agency', selectedAgency);

                console.log('URL de la requête fetch :', 'getManagers.php?' + params.toString());

                // Requête AJAX pour récupérer les managers
                fetch('getManagers.php?' + params.toString())
                    .then(response => {
                        console.log('Réponse brute de fetch :', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues :', data);
                        // Vérifier si le manager sélectionné est dans la nouvelle liste
                        var managerExists = data.some(manager => manager.id === selectedManagerId);
                        if (!managerExists) {
                            selectedManagerId = 'all';
                        }
                        // Mettre à jour le sélecteur des managers
                        var options = '<option value="all"' + (selectedManagerId === 'all' ? ' selected' : '') + '>Tous les managers</option>';
                        data.forEach(function (manager) {
                            var selected = (manager.id === selectedManagerId) ? ' selected' : '';
                            options += '<option value="' + manager.id + '"' + selected + '>' + manager.name + '</option>';
                        });
                        managerFilter.innerHTML = options;
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des managers :', error);
                    });
            }

            // Initialiser les filtres au chargement
            var selectedCountry = getParameterByName('country') || 'all';
            var selectedLevel = getParameterByName('level') || 'all';

            countryFilter.value = selectedCountry;
            // levelFilter.value = selectedLevel;

            updateAgencyFilter();
            updateManagerFilter();


            // Écouter les changements sur le filtre Pays
            countryFilter.addEventListener('change', function () {
                // Mettre à jour le filtre Agences
                updateAgencyFilter();
                updateManagerFilter();
                // Appliquer les filtres
                applyFilters();

            });

            // Écouter les changements sur le filtre Agences
            agencyFilter.addEventListener('change', function () {
                updateManagerFilter();
                applyFilters();

            });

            // Appliquer les filtres lorsque le niveau ou le manager change
            // levelFilter.addEventListener('change', applyFilters);
            managerFilter.addEventListener('change', applyFilters);


            function applyFilters() {
                var country = countryFilter.value;
                var agency = agencyFilter.value;
                // var level = levelFilter.value;
                var manager = managerFilter.value;

                var params = new URLSearchParams();

                params.append('country', country);
                params.append('agency', agency);
                // params.append('level', level);
                params.append('manager', manager);

                // Recharger la page avec les nouveaux paramètres
                window.location.search = params.toString();
            }
        });


        // // Ajouter des écouteurs d'événements sur les filtres
        // document.getElementById('country-filter').addEventListener('change', applyFilters);
        // document.getElementById('agency-filter').addEventListener('change', applyFilters);
        // document.getElementById('level-filter').addEventListener('change', applyFilters);
        // document.getElementById('manager-filter').addEventListener('change', applyFilters);


    </script>
    <?php include_once "partials/footer.php"; ?>
<?php } ?>