<?php 
    session_start();
    include_once "../language.php";

    if (!isset($_SESSION["profile"])) {
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
        $allocations = $academy->allocations;

        // Récupérer le filtre de niveau depuis GET ou défaut à 'Tous les Niveaux'
        
        $levelFilter = $_GET['level'] ?? null; 

        // Initialiser les variables pour chaque filiale
        $techniciansCa = [];
        $techniciansGa = [];
        $techniciansCo = [];
        $techniciansBe = [];
        $techniciansBu = [];
        $techniciansTo = [];
        $techniciansTc = [];
        $techniciansNigeria = [];
        $techniciansNiger = [];
        $techniciansMad = [];
        $techniciansMau = [];
        $techniciansRci = [];
        $techniciansGam = [];
        $techniciansBi = [];
        $techniciansEq = [];
        $techniciansGu = [];
        $techniciansMali = [];
        $techniciansRca = [];
        $techniciansRdc = [];
        $techniciansSen = [];
        $techniciansGh = [];
    
        // Définir la liste des filiales
        $subsidiaries = [
            "CAMEROON MOTORS INDUSTRIES" => &$techniciansCa,
            "CFAO MOTORS GABON" => &$techniciansGa,
            "CFAO MOTORS CONGO" => &$techniciansCo,
            "CFAO MOTORS BENIN" => &$techniciansBe,
            "CFAO MOTORS BURKINA" => &$techniciansBu,
            "CFAO MOTORS COTE D'IVOIRE" => &$techniciansRci,
            "CFAO (GAMBIA) LIMITED" => &$techniciansGa,
            "CFAO MOTORS GUINEE BISSAU" => &$techniciansBi,
            "CFAO MOTORS GUINEE" => &$techniciansGu,
            "CFAO MOTORS GUINEA EQUATORIAL" => &$techniciansEq,
            "CFAO MOTORS MALI" => &$techniciansMali,
            "CFAO MOTORS MADAGASCAR" => &$techniciansMad,
            "CFAO MOTORS NIGER" => &$techniciansNiger,
            "CFAO MOTORS CENTRAFRIQUE" => &$techniciansRca,
            "CFAO MOTORS RDC" => &$techniciansRdc,
            "CFAO MOTORS SENEGAL" => &$techniciansSen,
            "CFAO MOTORS TCHAD" => &$techniciansTc,
            "CFAO MOTORS TOGO" => &$techniciansTo,
            "CFAO MOTORS GHANA" => &$techniciansGh,
            "CFAO MOTORS NIGERIA" => &$techniciansNigeria,
            "COMPAGNIE MAURITANIENNE DE DISTRIBUTION AUTOMOBILE" => &$techniciansMau
        ];
    
        // Effectuer une seule requête pour récupérer tous les techniciens et managers actifs dans les filiales spécifiées
        $techniciansAndManagers = $users->find([
            '$and' => [
                ['subsidiary' => ['$in' => array_keys($subsidiaries)]],
                ['active' => true],
                ['$or' => [
                    ['profile' => 'Technicien'],
                    ['profile' => 'Manager', 'test' => true]
                ]]
            ]
        ])->toArray();
    
        // Parcourir les techniciens et managers récupérés et les affecter aux bonnes filiales
        foreach ($techniciansAndManagers as $technician) {
            $subsidiary = $technician['subsidiary'];
            if (isset($subsidiaries[$subsidiary])) {
                array_push($subsidiaries[$subsidiary], new MongoDB\BSON\ObjectId($technician['_id']));
            }
        }

        function getAllocation($allocations, $user, $level, $type, $activeManager = false) {
            $query = [
                'user' => new MongoDB\BSON\ObjectId($user),
                'level' => $level,
                'type' => $type,
                'active' => true
            ];
            if ($activeManager) {
                $query['activeManager'] = true;
            }
            return $allocations->findOne(['$and' => [$query]]);
        }

        function getResults($results, $user, $level, $typeR, $type) {
            return $results->findOne([
                '$and' => [
                    [
                        "user" => new MongoDB\BSON\ObjectId($user),
                        "level" => $level,
                        'typeR' => $typeR,
                        "type" => $type,
                    ],
                ],
            ]);
        }

        function calculateAverage($scores, $totals) {
            $scoreSum = array_sum($scores);
            $totalSum = array_sum($totals);
            return $totalSum === 0 ? ($scoreSum * 100 / 1) : ($scoreSum * 100 / $totalSum);
        }

        // Pour le graphe des totaux de chaque filiale
        function calculatePercentage ($factuel, $declaratif) {
            return ($factuel + $declaratif) / 2;
        }

        function calculateAveragePercentage($technicians, $allocations, $results, $level) {
            if (isset($level)) {
                $resultsFacScore = [];
                $resultsDeclaScore = [];
                $resultsFacTotal = [];
                $resultsDeclaTotal = [];

                foreach ($technicians as $tech) {
                    // Junior
                    $alloFac = getAllocation($allocations, $tech, $level, 'Factuel');
                    $alloDecla = getAllocation($allocations, $tech, $level, 'Declaratif', true);
                    
                    if ($alloFac && $alloDecla) {
                        $resultFac = getResults($results, $tech, $level, 'Technicien', "Factuel");
                        if ($resultFac) {
                            $resultsFacScore[] = $resultFac['score'];
                            $resultsFacTotal[] = $resultFac['total'];
                        }
                        
                        $resultDecla = getResults($results, $tech, $level, "Technicien - Manager", "Declaratif");
                        if ($resultDecla) {
                            $resultsDeclaScore[] = $resultDecla['score'];
                            $resultsDeclaTotal[] = $resultDecla['total'];
                        }
                    }
                }
                $percentageFacJu = calculateAverage($resultsFacScore, $resultsFacTotal);
                $percentageDeclaJu = calculateAverage($resultsDeclaScore, $resultsDeclaTotal);
                return calculatePercentage($percentageFacJu, $percentageDeclaJu);
            } else {
                $resultsFacScoreJu = [];
                $resultsDeclaScoreJu = [];
                $resultsFacTotalJu = [];
                $resultsDeclaTotalJu = [];
                $resultsFacScoreSe = [];
                $resultsDeclaScoreSe = [];
                $resultsFacTotalSe = [];
                $resultsDeclaTotalSe = [];
                $resultsFacScoreEx = [];
                $resultsDeclaScoreEx = [];
                $resultsFacTotalEx = [];
                $resultsDeclaTotalEx = [];
    
                foreach ($technicians as $tech) {
                    // Junior
                    $alloFacJu = getAllocation($allocations, $tech, 'Junior', 'Factuel');
                    $alloDeclaJu = getAllocation($allocations, $tech, 'Junior', 'Declaratif', true);
                    
                    if ($alloFacJu && $alloDeclaJu) {
                        $resultFacJu = getResults($results, $tech, "Junior", 'Technicien', "Factuel");
                        if ($resultFacJu) {
                            $resultsFacScoreJu[] = $resultFacJu['score'];
                            $resultsFacTotalJu[] = $resultFacJu['total'];
                        }
                        
                        $resultDeclaJu = getResults($results, $tech, "Junior", "Technicien - Manager", "Declaratif");
                        if ($resultDeclaJu) {
                            $resultsDeclaScoreJu[] = $resultDeclaJu['score'];
                            $resultsDeclaTotalJu[] = $resultDeclaJu['total'];
                        }
                    }
    
                    // Senior
                    $alloFacSe = getAllocation($allocations, $tech, 'Senior', 'Factuel');
                    $alloDeclaSe = getAllocation($allocations, $tech, 'Senior', 'Declaratif', true);
                    
                    if ($alloFacSe && $alloDeclaSe) {
                        $resultFacSe = getResults($results, $tech, "Senior", 'Technicien', "Factuel");
                        if ($resultFacSe) {
                            $resultsFacScoreSe[] = $resultFacSe['score'];
                            $resultsFacTotalSe[] = $resultFacSe['total'];
                        }
                        
                        $resultDeclaSe = getResults($results, $tech, "Senior", "Technicien - Manager", "Declaratif");
                        if ($resultDeclaSe) {
                            $resultsDeclaScoreSe[] = $resultDeclaSe['score'];
                            $resultsDeclaTotalSe[] = $resultDeclaSe['total'];
                        }
                    }
    
                    // Expert
                    $alloFacEx = getAllocation($allocations, $tech, 'Expert', 'Factuel');
                    $alloDeclaEx = getAllocation($allocations, $tech, 'Expert', 'Declaratif', true);
                    
                    if ($alloFacEx && $alloDeclaEx) {
                        $resultFacEx = getResults($results, $tech, "Expert", 'Technicien', "Factuel");
                        if ($resultFacEx) {
                            $resultsFacScoreEx[] = $resultFacEx['score'];
                            $resultsFacTotalEx[] = $resultFacEx['total'];
                        }
                        
                        $resultDeclaEx = getResults($results, $tech, "Expert", "Technicien - Manager", "Declaratif");
                        if ($resultDeclaEx) {
                            $resultsDeclaScoreEx[] = $resultDeclaEx['score'];
                            $resultsDeclaTotalEx[] = $resultDeclaEx['total'];
                        }
                    }
                }
    
                $percentageFacJu = calculateAverage($resultsFacScoreJu, $resultsFacTotalJu);
                $percentageDeclaJu = calculateAverage($resultsDeclaScoreJu, $resultsDeclaTotalJu);
                $percentageFacSe = calculateAverage($resultsFacScoreSe, $resultsFacTotalSe);
                $percentageDeclaSe = calculateAverage($resultsDeclaScoreSe, $resultsDeclaTotalSe);
                $percentageFacEx = calculateAverage($resultsFacScoreEx, $resultsFacTotalEx);
                $percentageDeclaEx = calculateAverage($resultsDeclaScoreEx, $resultsDeclaTotalEx);

                $datas = [
                    ['Completed' => calculatePercentage($percentageFacJu, $percentageDeclaJu)],
                    ['Completed' => calculatePercentage($percentageFacSe, $percentageDeclaSe)],
                    ['Completed' => calculatePercentage($percentageFacEx, $percentageDeclaEx)]
                ];

                // Filtrer les données pour ne garder que celles où 'completed' est supérieur à 0
                $validData = array_filter($datas, function($chart) {
                    return $chart['Completed'] > 0;
                });

                // Calculer la moyenne des 'completed'
                $averageCompleted = count($validData) > 0 ? round(array_sum(array_column($validData, 'Completed')) / count($validData)) : 0;
    
                return $averageCompleted;
            }
        }

        $resultBu = calculateAveragePercentage($techniciansBu, $allocations, $results, $levelFilter);
        $resultCa = calculateAveragePercentage($techniciansCa, $allocations, $results, $levelFilter);
        $resultRci = calculateAveragePercentage($techniciansRci, $allocations, $results, $levelFilter);
        $resultGa = calculateAveragePercentage($techniciansGa, $allocations, $results, $levelFilter);
        $resultMad = calculateAveragePercentage($techniciansMad, $allocations, $results, $levelFilter);
        $resultMali = calculateAveragePercentage($techniciansMali, $allocations, $results, $levelFilter);
        $resultRca = calculateAveragePercentage($techniciansRca, $allocations, $results, $levelFilter);
        $resultRdc = calculateAveragePercentage($techniciansRdc, $allocations, $results, $levelFilter);
        $resultSe = calculateAveragePercentage($techniciansSen, $allocations, $results, $levelFilter);
        
?>
    <?php include "./partials/header.php"; ?>
    <!--begin::Title-->
    <?php if (isset($levelFilter)) { ?>
        <title><?php echo 'Etat d\'Avancement des Résultats des Filiales Niveau ' . $levelFilter ?> | CFAO Mobility Academy</title>
    <?php } else { ?>
        <title><?php echo 'Etat d\'Avancement des Résultats des Filiales Tous Niveaux' ?> | CFAO Mobility Academy</title>
    <?php } ?>
    <!--end::Title-->

    <style>
        /* Chart container */
        .responsive-chart-container {
            width: 100%;
            position: relative;
            /* Make sure canvas is positioned correctly */
        }

        /* Canvas styling */
        .responsive-chart-container canvas {
            width: 100% !important;
            /* Make canvas responsive */
            height: auto !important;
            /* Maintain aspect ratio */
        }
    </style>

    <!--begin::Content-->
    <div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
        <?php if ( $_SESSION["profile"] == "Super Admin") { ?>
            <!--begin::Toolbar-->
            <div class="toolbar" id="kt_toolbar">
                <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
                    <!--begin::Info-->
                    <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                        <!--begin::Title-->
                        <h1 class="text-dark fw-bold my-1 fs-2">
                            <?php if (isset($levelFilter)) { ?>
                                <?php echo 'Etat d\'Avancement des Résultats des Filiales Niveau ' . $levelFilter ?>
                            <?php } else { ?>
                                <?php echo 'Etat d\'Avancement des Résultat sdes Filiales Tous Niveaux' ?>
                            <?php } ?>
                        </h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Info-->
                </div>
            </div>
            <!--end::Toolbar-->
            <!--begin::Filtres -->
            <div class="container my-4" style="margin-left: 30px;">
                <div class="row g-3 align-items-center">
                    <!-- Filtre Level -->
                    <div class="col-md-6">
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
                </div>
            </div>
            <!--end::Filtres -->
            <!--begin::Content-->
            <div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Post-->
                <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
                    <!--begin::Container-->
                    <div class=" container-xxl ">
                        <!--begin::Row-->
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!-- begin::Row -->
                            <div class="post fs-6 d-flex flex-column-fluid">
                                <!--begin::Container-->
                                <div class=" container-xxl ">
                                    <!--begin::Layout Builder Notice-->
                                    <div class="card mb-10">
                                        <div class="card-body d-flex align-items-center">
                                            <!--begin::Card body-->
                                            <div id="chart-container" class="responsive-chart-container">
                                                <canvas id="chart"></canvas>
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                    <!--end::Layout Builder Notice-->
                                </div>
                                <!--end::Container-->
                            </div>
                            <!-- end::Row -->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Post-->
            </div>
            <!--end::Content-->
        <?php } ?>
    </div>
    <!--end::Content-->
    <?php include "./partials/footer.php"; ?>
    <script>
        // Fonction pour appliquer les filtres et recharger la page avec de nouveaux paramètres
        function applyFilters() {
            const level = document.getElementById('level-filter').value;

            // Créer un objet URL à partir de l'URL actuelle
            const currentUrl = new URL(window.location.href);

            if (level === 'all') {
                // Supprimer le paramètre 'level' de l'URL
                currentUrl.searchParams.delete('level');
            } else {
                // Mettre à jour ou ajouter le paramètre 'level'
                currentUrl.searchParams.set('level', level);
            }

            // Rediriger vers la nouvelle URL
            window.location.href = currentUrl.toString();
        }

        // Define color ranges for percentage completion
        const getColorForCompletions = (percentage) => {
            if (percentage >= 80) return '#6CF95D'; // Green
            if (percentage >= 60) return '#FAF75A'; // Yellow
            return '#FB9258'; // Orange
        };
        
        const ctxC = document.getElementById('chart');
        const data = {
            labels: ['BURKINA FASO', 'CAMEROUN', "CÔTE D'IVOIRE", 'GABON', 'MADAGASCAR', 'MALI', 'RCA', 'RDC', 'SENEGAL'],
            datasets: [{
                type: 'bar',
                label: 'Résultat Général',
                data: [<?php echo ceil($resultBu) ?>, <?php echo ceil($resultCa) ?>, <?php echo ceil($resultRci) ?>,
                    <?php echo ceil($resultGa) ?>, <?php echo ceil($resultMad) ?>, <?php echo ceil($resultMali) ?>,
                    <?php echo ceil($resultRca) ?>, <?php echo ceil($resultRdc) ?>,
                    <?php echo ceil($resultSe) ?>
                ],
                borderColor: 'rgba(0, 0, 0, 0)', // Transparent border
                backgroundColor: [
                    getColorForCompletions(<?php echo ceil($resultBu) ?>), // Burkina
                    getColorForCompletions(<?php echo ceil($resultCa) ?>), // Cameroun
                    getColorForCompletions(<?php echo ceil($resultRci) ?>), // Côte d'Ivoire
                    getColorForCompletions(<?php echo ceil($resultGa) ?>), // Gabon
                    getColorForCompletions(<?php echo ceil($resultMad) ?>), // Madagascar
                    getColorForCompletions(<?php echo ceil($resultMali) ?>), // Mali
                    getColorForCompletions(<?php echo ceil($resultRca) ?>), // RCA
                    getColorForCompletions(<?php echo ceil($resultRdc) ?>), // RDC
                    getColorForCompletions(<?php echo ceil($resultSe) ?>) // Senegal
                ],
                datalabels: {
                    formatter: function(value, context) {
                        const label = context.chart.data.labels[context.dataIndex];
                        return 'Résultat ' + label;
                    },
                    color: '#000',
                    font: {
                        weight: 'bold'
                    },
                    anchor: 'end',
                    align: 'top',
                    offset: 4
                }
            }]
        };

        const chart = new Chart(ctxC, {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const labels = chart.data.labels;
                                const datasets = chart.data.datasets[0];
                                return labels.map((label, index) => ({
                                    text: `Résultat ${label}`,
                                    fillStyle: datasets.backgroundColor[index],
                                    hidden: !chart.isDatasetVisible(0),
                                    strokeStyle: 'transparent',
                                    lineWidth: 0,
                                    datasetIndex: 0,
                                    index: index
                                }));
                            },
                            font: {
                                size: 10
                            }
                        },
                        onClick: function(e, legendItem, legend) {
                            const index = legendItem.index;
                            const meta = legend.chart.getDatasetMeta(0);
                            const dataset = meta.data[index];

                            // Toggle visibility
                            dataset.hidden = !dataset.hidden;
                            legend.chart.update();
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#000',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        });        
    </script>
    <?php } ?>