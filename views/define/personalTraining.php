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
        $trainings = $academy->trainings;
        $applications = $academy->applications;
        $allocations = $academy->allocations;

        $technicianId = $_SESSION["id"];
        
        $levels = [
            'Junior' => ['Junior'], 
            'Senior' => ['Junior', 'Senior'], 
            'Expert' => ['Junior', 'Senior', 'Expert']
        ];

        // Récupérer les valeurs des filtres depuis les paramètres GET
        $selectedBrand = $_GET['brand'] ?? 'all';
        $selectedLevel = $_GET['level'] ?? 'all';

        $userData = $users->findOne([
            '$and' => [
                [
                    "_id" => new mongodb\bson\objectid($technicianId),
                    "active" => true
                ]
            ],
        ]);
        
        $trainingTypes = ['Coaching', 'Présentielle', 'Distancielle', 'E-learning', 'Mentoring'];
        $trainingsData = [];
        
        $query = [
            '$and' => [
                ["users" => new mongodb\bson\objectid($technicianId)],
                ["active" => true]
            ]
        ];

        if ($selectedBrand != 'all') {
            $query['$and'][] = ["brand" => $selectedBrand];
        }

        if ($selectedLevel != 'all') {
            $query['$and'][] = ["level" => $selectedLevel];
        }

        $trainingDatas = $trainings->find($query)->toArray();
        
        // Initialiser le tableau $trainingsData avant la boucle
        $trainingsData = [
            'Coaching' => [],
            'Présentielle' => [],
            'Distancielle' => [],
            'E-learning' => [],
            'Mentoring' => []
        ];

        foreach ($trainingDatas as $trainingData) {
            $applicationData = $applications->findOne([
                '$and' => [
                    ["user" => new mongodb\bson\objectid($technicianId)],
                    ["training" => new mongodb\bson\objectid($trainingData['_id'])],
                    ["active" => true]
                ]
            ]);
            
            if (isset($applicationData)) {
                // Vérifier le type de formation et l'ajouter au tableau approprié
                foreach ($trainingTypes as $type) {
                    if ($trainingData['type'] == $type) {
                        $trainingsData[$type][] = $trainingData; // Ajoute le trainingData au type correspondant
                    }
                }
            }
        }  

        function getBootstrapClass($level) {
            return $level == 'Senior' ? 'danger' : ($level == 'Junior' ? 'warning' : 'success');
        }
    
        function getImageClass($type) {
            if ($type == 'Coaching' || $type == 'Mentoring') {
                return 'mentoring & coaching.png';
            } elseif ($type == 'Distancielle' || $type == 'E-learning') {
                return 'formation_distantielle.png';
            } elseif ($type == 'Présentielle') {
                return 'formation_presentiel.webp';
            }
        }
    
        function renderTrainingNotNecessaryCard() {
            return '
                <div class="card-soon">
                    <div class="card-content">
                        <h2 class="card-title">Pas de formations disponibles.</h2>
                    </div>
                </div>
            ';
        }
    
        function renderTrainingCard($technicianId, $training) {
            $levelClass = getBootstrapClass($training['level']);
            $imageClass = getImageClass($training['type']);
            return '
                <div class="card" data-bs-toggle="modal" data-bs-target="#kt_modal_' . $training['_id'] . '_' . $technicianId . '">
                    <img src="../../public/images/' . $imageClass . '" alt="Image" class="card-image">
                    <div class="card-content">
                        <h2 class="card-title">' . htmlspecialchars($training['label']) . '</h2>
                        <h2 class="text-uppercase text-dark fw-bold fs-5">' . htmlspecialchars($training['brand']) . '</h2>
                        <p class="card-description limited-lines fs-5">' . htmlspecialchars($training['type']) . '</p>
                        <div class="badge badge-light-' . $levelClass . ' fs-5">' . htmlspecialchars($training['level']) . '</div>
                    </div>
                </div>
            ';
        }
    
        function renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar) {
            $trainingApply = $applications->findOne([
                '$and' => [
                    [
                        'user' => new MongoDB\BSON\ObjectId($technicianId),
                        'training' => new MongoDB\BSON\ObjectId($training['_id']),
                        'active' => true
                    ]
                ],
            ]);

            $details = [
                $training_code => $training['code'],
                $label_training => $training['label'],
                $Type => $training['type'],
                $Brand => $training['brand'],
                $Level => $training['level'],
                $trainingDate => $trainingApply['period'],
                $training_location => $trainingApply['place'],
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

            return '
                <div class="modal fade" id="kt_modal_' . $training['_id'] . '_' . $technicianId . '" tabindex="-1" aria-hidden="true">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }
?>
<?php include "partials/header.php"; ?>
<script>
    const trainingsData = <?php echo json_encode($trainingsData); ?>;

    console.log("trainingsData:", trainingsData);
</script>
<style>
    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        margin: 10px;
        width: 250px;
        overflow: hidden;
        transition: transform 0.2s;
        cursor: pointer;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-soon {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        margin: 10px;
        width: 350px;
        height: 200px;
        display: flex;
        justify-content: center; /* Centre horizontalement */
        align-items: center; /* Centre verticalement */
        text-align: center; /* Centre le texte */
        overflow: hidden;
        transition: transform 0.2s;
    }

    .card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-content {
        padding: 15px;
    }

    .card-title {
        font-size: 1.5em;
        margin: 0 0 10px;
    }

    .card-description {
        font-size: 1em;
        margin: 0 0 15px;
    }
    
    /* Hide dropdown content by default */
    .dropdown-content {
        display: none;
        margin-top: 25px;
        /* Adjust as needed */
        transition: opacity 0.3s ease, max-height 0.3s ease;
        /* Smooth transition for dropdown visibility */
    }
    
    /* Style the toggle button */
    .dropdown-toggle {
        background-color: #fff;
        color: white;
        border: none;
        padding: 10px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease, color 0.3s ease;
        /* Smooth transition for background and text color */
    
    }
    
    .dropdown-toggle i {
        margin-left: 5px;
        font-size: 14px;
        /* Set a proper size for the icon */
        transition: transform 0.3s ease;
        /* Smooth rotation transition */
    }
    
    
    /* Ensure no extra content or pseudo-elements */
    .dropdown-toggle::before,
    .dropdown-toggle::after {
        content: none;
        /* Ensure no extra content or pseudo-elements */
    }
    
    .dropdown-toggle.open i {
        transform: rotate(180deg);
    }
    
    /* Optional: Style for better visibility */
    .title-and-cards-container {
        margin-bottom: 25px;
        /* Adjust as needed */
    }
    
    .limited-lines {
        display: -webkit-box; /* Pour les navigateurs basés sur WebKit */
        -webkit-box-orient: vertical; /* Orientation verticale */
        -webkit-line-clamp: 2; /* Limiter à 2 lignes */
        overflow: hidden; /* Masquer le débordement */
        text-overflow: ellipsis; /* Ajouter des points de suspension (...) */
    }
</style>
<!--begin::Title-->
<title><?php echo $list_formation_recommandee ?> | CFAO Mobility Academy</title>
<!--end::Title-->
<!--begin::Content-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bolder my-1 fs-1">
                    <?php echo $list_formation_recommandee ?>
                </h1>
                <!--end::Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Filtres -->
    <div class="container my-4">
        <div class="row g-3 align-items-center">
            <!-- Filtre Level -->
            <div class="col-md-5" style="margin-left: 40px;">
                <label for="level-filter" class="form-label d-flex align-items-center">
                    <i class="bi bi-bar-chart-fill fs-2 me-2 text-warning"></i> Niveau
                </label>
                <select id="level-filter" name="level" class="form-select">
                    <option value="all" <?php if ($selectedLevel === 'all') echo 'selected'; ?>>Tous les niveaux</option>
                    <?php foreach ($levels[$userData['level']] as $levelOption): ?>
                    <option value="<?php echo htmlspecialchars($levelOption); ?>" 
                            <?php if ($selectedLevel === $levelOption) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($levelOption); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Filtre Brand -->
            <div class="col-md-5" style="margin-left: 40px;">
                <label for="brand-filter" class="form-label d-flex align-items-center">
                    <i class="bi bi-car-front-fill fs-2 me-2 text-danger"></i> Marques
                </label>
                <select id="brand-filter" name="brand" class="form-select" <?php if ($selectedLevel === 'all') echo 'disabled'; ?>>
                    <option value="all" <?php if ($selectedBrand === 'all') echo 'selected'; ?>>Tous les marques</option>
                    <?php 
                    $userBrands = [];
                    foreach ($userData['brand'.$selectedLevel] as $brand) {
                        array_push($userBrands, $brand);
                    }
                    // Filtrer le tableau pour éliminer les espaces vides
                    $filteredBrands = array_filter($userBrands, function($brandUser) {
                        return trim($brandUser) !== ""; // Garder uniquement les valeurs non vides
                    });
                    foreach ($filteredBrands as $brandOption): ?>
                    <option value="<?php echo htmlspecialchars($brandOption); ?>" 
                            <?php if ($selectedBrand === $brandOption) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($brandOption); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <!--end::Filtres -->
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" style="margin-left: 30px;">
        <!--begin::Container-->
        <div class="container-xxl ">
            <!--begin::Title-->
            <div style="margin-top: 20px; margin-bottom : 20px">
                <h6 class="text-dark fw-bold my-1 fs-2">
                    <?php if (count($trainingsData['Présentielle']) > 9) { ?>
                        <?php echo count($trainingsData['Présentielle']).' '.$formation_presentielles ?>
                    <?php } else { ?>
                        <?php echo '0'.count($trainingsData['Présentielle']).' '.$formation_presentielles ?>
                    <?php } ?>
                </h6>
            </div>
            <!--end::Title-->
            <?php if (count($trainingsData['Présentielle']) == 0) { ?>
                <?php echo renderTrainingNotNecessaryCard(); ?>
            <?php } else { ?>
                <!--begin::Layout Builder Notice-->
                <div class="row" style="margin-top: 10px;">
                    <?php  
                        $limit = 4; // Limite d'affichage
                        $count = 0; // Compteur d'éléments affichés
                        foreach ($trainingsData['Présentielle'] as $training) { 
                            if ($count >= $limit) {
                                break; // Sortir de la boucle si la limite est atteinte
                            }
                    ?>
                        <?php echo renderTrainingCard($technicianId, $training); ?>
                        <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                    <?php 
                        $count++; // Incrémenter le compteur
                    } 
                    ?>
                </div>
                <!--end::Layout Builder Notice-->
                <?php if (count($trainingsData['Présentielle']) > 4) { ?>
                    <!-- Dropdown Container -->
                    <div class="dropdown-container">
                        <button class="dropdown-toggle " style="color: black;">Plus de Formations Présentielles
                            <i class="fas fa-chevron-down"></i></button>
                        <!-- Hidden Content -->
                        <div class="dropdown-content">
                            <!-- Begin::Row -->
                            <div class="row" style="margin-top: 10px;">
                                <?php  
                                    $startIndex = 4; // Index à partir duquel commencer l'affichage
                                    foreach ($trainingsData['Présentielle'] as $index => $training) { 
                                        if ($index < $startIndex) {
                                            continue; // Passer les 4 premiers éléments
                                        }
                                    ?>
                                    <?php echo renderTrainingCard($technicianId, $training); ?>
                                    <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                                <?php } ?>
                            </div>
                            <!-- End::Row -->
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <!--begin::Title-->
            <div style="margin-top: 20px; margin-bottom : 20px">
                <h6 class="text-dark fw-bold my-1 fs-2">
                    <?php if (count($trainingsData['E-learning']) > 9) { ?>
                        <?php echo count($trainingsData['E-learning']).' '.$formation_onlines ?>
                    <?php } else { ?>
                        <?php echo '0'.count($trainingsData['E-learning']).' '.$formation_onlines ?>
                    <?php } ?>
                </h6>
            </div>
            <!--end::Title-->
            <?php if (count($trainingsData['E-learning']) == 0) { ?>
                <?php echo renderTrainingNotNecessaryCard(); ?>
            <?php } else { ?>
                <!--begin::Layout Builder Notice-->
                <div class="row" style="margin-top: 10px;">
                    <?php 
                        $limit = 4; // Limite d'affichage
                        $count = 0; // Compteur d'éléments affichés
                        foreach ($trainingsData['E-learning'] as $training) { 
                            if ($count >= $limit) {
                                break; // Sortir de la boucle si la limite est atteinte
                            }
                    ?>
                        <?php echo renderTrainingCard($technicianId, $training); ?>
                        <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                    <?php 
                        $count++; // Incrémenter le compteur
                    } 
                    ?>
                </div>
                <!--end::Layout Builder Notice-->
                <?php if (count($trainingsData['E-learning']) > 4) { ?>
                    <!-- Dropdown Container -->
                    <div class="dropdown-container">
                        <button class="dropdown-toggle" style="color: black;">Plus de Formations E-learning
                            <i class="fas fa-chevron-down"></i></button>
                        <!-- Hidden Content -->
                        <div class="dropdown-content">
                            <!-- Begin::Row -->
                            <div class="row" style="margin-top: 10px;">
                                <?php  
                                    $startIndex = 4; // Index à partir duquel commencer l'affichage
                                    foreach ($trainingsData['E-learning'] as $index => $training) { 
                                        if ($index < $startIndex) {
                                            continue; // Passer les 4 premiers éléments
                                        }
                                    ?>
                                    <?php echo renderTrainingCard($technicianId, $training); ?>
                                    <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                                <?php } ?>
                            </div>
                            <!-- End::Row -->
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <!--begin::Title-->
            <div style="margin-top: 20px; margin-bottom : 20px">
                <h6 class="text-dark fw-bold my-1 fs-2">
                    <?php if (count($trainingsData['Distancielle']) > 9) { ?>
                        <?php echo count($trainingsData['Distancielle']).' '.$formation_distancielles ?>
                    <?php } else { ?>
                        <?php echo '0'.count($trainingsData['Distancielle']).' '.$formation_distancielles ?>
                    <?php } ?>
                </h6>
            </div>
            <!--end::Title-->
            <?php if (count($trainingsData['Distancielle']) == 0) { ?>
                <?php echo renderTrainingNotNecessaryCard(); ?>
            <?php } else { ?>
                <!--begin::Layout Builder Notice-->
                <div class="row" style="margin-top: 10px;">
                    <?php
                        $limit = 4; // Limite d'affichage
                        $count = 0; // Compteur d'éléments affichés
                        foreach ($trainingsData['Distancielle'] as $training) { 
                            if ($count >= $limit) {
                                break; // Sortir de la boucle si la limite est atteinte
                            }
                    ?>
                        <?php echo renderTrainingCard($technicianId, $training); ?>
                        <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                    <?php 
                        $count++; // Incrémenter le compteur
                    } 
                    ?>
                </div>
                <!--end::Layout Builder Notice-->
                <?php if (count($trainingsData['Distancielle']) > 4) { ?>
                    <!-- Dropdown Container -->
                    <div class="dropdown-container">
                        <button class="dropdown-toggle" style="color: black;">Plus de Formations Distancielles
                            <i class="fas fa-chevron-down"></i></button>
                        <!-- Hidden Content -->
                        <div class="dropdown-content">
                            <!-- Begin::Row -->
                            <div class="row" style="margin-top: 10px;">
                                <?php  
                                    $startIndex = 4; // Index à partir duquel commencer l'affichage
                                    foreach ($trainingsData['Distancielle'] as $index => $training) { 
                                        if ($index < $startIndex) {
                                            continue; // Passer les 4 premiers éléments
                                        }
                                    ?>
                                    <?php echo renderTrainingCard($technicianId, $training); ?>
                                    <?php echo renderModal($technicianId, $applications, $training, $data, $training_code, $label_training, $Brand, $Type, $Level, $specialities_studies, $training_location, $training_link, $trainingDate, $voir_calendar); ?>
                                <?php } ?>
                            </div>
                            <!-- End::Row -->
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Content-->
<script>
    const trainingsData = <?php echo json_encode($trainingsData); ?>;

    console.log("trainingsData:", trainingsData);
</script>
<script>
    $(document).ready(function() {
        $('.dropdown-toggle').click(function() {
            // Cible le contenu du dropdown associé au bouton cliqué
            var $dropdownContent = $(this).next('.dropdown-content');
            var isVisible = $dropdownContent.is(':visible');

            // Basculer l'affichage du contenu du dropdown
            $dropdownContent.slideToggle();
            
            // Ajouter ou retirer la classe 'open' en fonction de l'état
            $(this).toggleClass('open', !isVisible);
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        var levelFilter = document.getElementById('level-filter');
        var brandFilter = document.getElementById('brand-filter');

        function getParameterByName(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name) || 'all';
        }

        // Initialiser les filtres au chargement
        var selectedBrand= getParameterByName('brand') || 'all';
        var selectedLevel = getParameterByName('level') || 'all';

        brandFilter.value = selectedBrand;
        levelFilter.value = selectedLevel;

        // Appliquer les filtres lorsque le niveau ou le manager change
        brandFilter.addEventListener('change', applyFilters);
        levelFilter.addEventListener('change', applyFilters);


        function applyFilters() {
            var brand = brandFilter.value;
            var level = levelFilter.value;

            var params = new URLSearchParams();

            params.append('brand', brand);
            params.append('level', level);

            // Recharger la page avec les nouveaux paramètres
            window.location.search = params.toString();
        }
    });
</script>
<?php include "partials/footer.php"; ?>
<?php } ?>