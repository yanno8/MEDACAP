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
        
        $users = $academy->users;
        $allocations = $academy->allocations;

        $objId = new MongoDB\BSON\ObjectId($_SESSION["id"]);

        if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == false) {
            $manager = $users->findOne([
                "_id" => $objId,
                "active" => true
            ]);

            $technicians = $manager['users'];
            
            // Fonction pour recupérer les affectations terminés
            function getAllocation($allocations, $user, $level, $type) {
                $query = [
                    'user' => $user,
                    'type' => $type,
                    'level' => $level
                ];
                return $allocations->find(['$and' => [$query]]);
            }

            function getAllocationByUser ($allocations, $users, $level) {
                $response = [];
                $declaratifDone = [];
                $declaratifPending = [];
                $declaratifTotal = [];

                foreach ($users as $user) {

                    $declaratifs = getAllocation($allocations, new MongoDB\BSON\ObjectId($user), $level, 'Declaratif');
        
                    foreach($declaratifs as $declaratif) {
                        $declaratifTotal[] = $declaratif;
                        if ($declaratif['activeManager'] == true) {
                            $declaratifDone[] = $declaratif;
                        } else {
                            $declaratifPending[] = $declaratif;
                        }
                    }
                }

                $response = [
                    "declaratifDone" => count($declaratifDone),
                    "declaratifPending" => count($declaratifPending),
                    "declaratifTotal" => count($declaratifTotal),
                ];

                return $response;
            }

            $resultJu = getAllocationByUser ($allocations, $technicians, 'Junior');
            $resultSe = getAllocationByUser ($allocations, $technicians, 'Senior');
            $resultEx = getAllocationByUser ($allocations, $technicians, 'Expert');

        } else if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == true){
            $manager = $users->findOne([
                "_id" => $objId,
                "active" => true
            ]);

            $technicians = $manager['users'];
            
            // Fonction pour recupérer les affectations terminés
            function getAllocationManager($allocations, $user, $level, $type) {
                $query = [
                    'user' => $user,
                    'type' => $type,
                    'level' => $level
                ];
                return $allocations->find(['$and' => [$query]]);
            }

            function getAllocationByUsers ($allocations, $users, $level) {
                $response = [];
                $declaratifDone = [];
                $declaratifPending = [];
                $declaratifTotal = [];

                foreach ($users as $user) {

                    $declaratifs = getAllocationManager($allocations, new MongoDB\BSON\ObjectId($user), $level, 'Declaratif');
        
                    foreach($declaratifs as $declaratif) {
                        $declaratifTotal[] = $declaratif;
                        if ($declaratif['activeManager'] == true) {
                            $declaratifDone[] = $declaratif;
                        } else {
                            $declaratifPending[] = $declaratif;
                        }
                    }
                }

                $response = [
                    "declaratifDone" => count($declaratifDone),
                    "declaratifPending" => count($declaratifPending),
                    "declaratifTotal" => count($declaratifTotal),
                ];

                return $response;
            }

            $resultJu = getAllocationByUsers ($allocations, $technicians, 'Junior');
            $resultSe = getAllocationByUsers ($allocations, $technicians, 'Senior');
            $resultEx = getAllocationByUsers ($allocations, $technicians, 'Expert');
            
            // Fonction pour recupérer les affectations terminés
            function getAllocationTech($allocations, $user, $type) {
                $query = [
                    'user' => $user,
                    'type' => $type,
                ];
                return $allocations->find(['$and' => [$query]]);
            }

            function getAllocationByUser ($allocations, $user) {
                $response = [];
                $factuelDone = [];
                $factuelPending = [];
                $factuelTotal = [];
                $declaratifDone = [];
                $declaratifPending = [];
                $declaratifTotal = [];

                $factuels = getAllocationTech($allocations, $user, 'Factuel');
                $declaratifs = getAllocationTech($allocations, $user, 'Declaratif');
    
                foreach($factuels as $factuel) {
                    $factuelTotal[] = $factuel;
                    if ($factuel['active'] == true) {
                        $factuelDone[] = $factuel;
                    } else {
                        $factuelPending[] = $factuel;
                    }
                }
                foreach($declaratifs as $declaratif) {
                    $declaratifTotal[] = $declaratif;
                    if ($declaratif['active'] == true) {
                        $declaratifDone[] = $declaratif;
                    } else {
                        $declaratifPending[] = $declaratif;
                    }
                }

                $response = [
                    "factuelDone" => count($factuelDone),
                    "factuelPending" => count($factuelPending),
                    "factuelTotal" => count($factuelTotal),
                    "declaratifDone" => count($declaratifDone),
                    "declaratifPending" => count($declaratifPending),
                    "declaratifTotal" => count($declaratifTotal),
                ];

                return $response;
            }

            $results = getAllocationByUser ($allocations, $objId);
            
        } else {
            // Fonction pour recupérer les affectations terminés
            function getAllocation($allocations, $user, $type) {
                $query = [
                    'user' => $user,
                    'type' => $type,
                ];
                return $allocations->find(['$and' => [$query]]);
            }

            function getAllocationByUser ($allocations, $user) {
                $response = [];
                $factuelDone = [];
                $factuelPending = [];
                $factuelTotal = [];
                $declaratifDone = [];
                $declaratifPending = [];
                $declaratifTotal = [];

                $factuels = getAllocation($allocations, $user, 'Factuel');
                $declaratifs = getAllocation($allocations, $user, 'Declaratif');
    
                foreach($factuels as $factuel) {
                    $factuelTotal[] = $factuel;
                    if ($factuel['active'] == true) {
                        $factuelDone[] = $factuel;
                    } else {
                        $factuelPending[] = $factuel;
                    }
                }
                foreach($declaratifs as $declaratif) {
                    $declaratifTotal[] = $declaratif;
                    if ($declaratif['active'] == true) {
                        $declaratifDone[] = $declaratif;
                    } else {
                        $declaratifPending[] = $declaratif;
                    }
                }

                $response = [
                    "factuelDone" => count($factuelDone),
                    "factuelPending" => count($factuelPending),
                    "factuelTotal" => count($factuelTotal),
                    "declaratifDone" => count($declaratifDone),
                    "declaratifPending" => count($declaratifPending),
                    "declaratifTotal" => count($declaratifTotal),
                ];

                return $response;
            }

            $results = getAllocationByUser ($allocations, $objId);
        }
?>
<?php include "./partials/header.php"; ?>

<!--begin::Title-->
<title><?php echo $tableau ?> | CFAO Mobility Academy</title>
<!--end::Title-->
<!--begin::Content-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bold my-1 fs-1">
                    <?php echo $tableau ?>
                </h1>
                <!--end::Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->
    <?php if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == false) { ?>
        <!--begin::Content-->
        <div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content" style="margin-top: -10px">
            <!--begin::Post-->
            <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
                <!--begin::Container-->
                <div class=" container-xxl ">
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo count($technicians) ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo $technicienss ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $resultJu['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles Niveau Junior' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $resultSe['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles Niveau Senior' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $resultEx['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles Niveau Expert' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Toolbar-->
                        <div class="toolbar" id="kt_toolbar" style="margin-top: 50px; margin-bottom: -20px">
                            <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
                                <!--begin::Info-->
                                <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bold my-1 fs-2">
                                        <?php echo 'Etat d\'Avancement des QCM tâches professionnelles manager réalisés' ?>
                                    </h1>
                                    <!--end::Title-->
                                </div>
                                <!--end::Info-->
                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!-- begin::Row -->
                        <div>
                            <div id="chartQCM" class="row">
                                <!-- Dynamic cards will be appended here -->
                            </div>
                        </div>
                        <!-- endr::Row -->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Post-->
        </div>
        <!--end::Content-->
    <?php } else if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == true) { ?>
        <!--begin::Content-->
        <div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content" style="margin-top: -10px">
            <!--begin::Post-->
            <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
                <!--begin::Container-->
                <div class=" container-xxl ">
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $results['factuelTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Connaissances' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $results['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles Techniciens' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $resultJu['declaratifTotal'] + $resultSe['declaratifTotal'] + $resultEx['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles Managers' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo count($technicians) ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo $technicienss ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Toolbar-->
                        <div class="toolbar" id="kt_toolbar" style="margin-top: 50px; margin-bottom: -20px">
                            <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
                                <!--begin::Info-->
                                <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bold my-1 fs-2">
                                        <?php echo 'Etat d\'Avancement des QCM tâches professionnelles manager réalisés' ?>
                                    </h1>
                                    <!--end::Title-->
                                </div>
                                <!--end::Info-->
                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!-- begin::Row -->
                        <div>
                            <div id="chartQCM" class="row">
                                <!-- Dynamic cards will be appended here -->
                            </div>
                        </div>
                        <!-- endr::Row -->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Post-->
        </div>
        <!--end::Content-->
    <?php } else{ ?>
        <!--begin::Content-->
        <div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content" style="margin-top: -10px">
            <!--begin::Post-->
            <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
                <!--begin::Container-->
                <div class=" container-xxl ">
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                        <!--begin::Toolbar-->
                        <!-- <div class="toolbar" id="kt_toolbar" style="margin-top: 20px; margin-bottom: -30px"> -->
                            <!-- <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap"> -->
                                <!--begin::Info-->
                                <!-- <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2"> -->
                                    <!--begin::Title-->
                                    <!-- <h1 class="text-dark fw-bold my-1 fs-2">
                                        <?php echo 'Nombre de QCM' ?>
                                    </h1> -->
                                    <!--end::Title-->
                                <!-- </div> -->
                                <!--end::Info-->
                            <!-- </div> -->
                        <!-- </div> -->
                        <!--end::Toolbar-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $results['factuelTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Connaissances' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo $results['declaratifTotal'] ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de QCM Tâches Professionnelles' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <!--begin::Card-->
                            <div class="card h-100 ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <!--begin::Animation-->
                                    <div
                                        class="fs-lg-1hx fs-2x fw-bold text-gray-800 d-flex justify-content-center text-center">
                                        <div class="min-w-70px" data-kt-countup="true"
                                            data-kt-countup-value="<?php echo (($results['factuelTotal'] + $results['declaratifTotal']) / 2) ?>">
                                        </div>
                                    </div>
                                    <!--end::Animation-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">
                                        <?php echo 'Nombre de Tests' ?> </div>
                                    <!--end::Title-->
                                    <!--end::Name-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Toolbar-->
                        <div class="toolbar" id="kt_toolbar" style="margin-top: 50px; margin-bottom: -20px">
                            <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
                                <!--begin::Info-->
                                <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bold my-1 fs-2">
                                        <?php echo 'Etat d\'Avancement des QCM réalisés' ?>
                                    </h1>
                                    <!--end::Title-->
                                </div>
                                <!--end::Info-->
                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!-- begin::Row -->
                        <div>
                            <div id="chartQCM" class="row">
                                <!-- Dynamic cards will be appended here -->
                            </div>
                        </div>
                        <!-- endr::Row -->
                    </div>
                    <!--end::Row-->
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

<!-- <script>
    const results = <?php echo json_encode($results, JSON_HEX_APOS | JSON_HEX_QUOT); ?>

    console.log('results', results);
</script> -->

<?php if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == false) { ?>
    <script>
        // Graphiques pour les resultats du groupe
        document.addEventListener('DOMContentLoaded', function() {
            function addIfZero(done, total) {
                let percentage;
                if (total == 0) {
                    percentage = (done * 100) / 1;
                } else {
                    percentage = (done * 100) / total;
                }
                return percentage;
            }
            
            // Calculate percentages
            let percentageJu = addIfZero(<?php echo $resultJu['declaratifDone'] ?>, <?php echo $resultJu['declaratifTotal'] ?>);
            let percentageSe = addIfZero(<?php echo $resultSe['declaratifDone'] ?>, <?php echo $resultSe['declaratifTotal'] ?>);
            let percentageEx = addIfZero(<?php echo $resultEx['declaratifDone'] ?>, <?php echo $resultEx['declaratifTotal'] ?>);

            // Data for each chart
            const chartData = [
                {
                    title: 'Nombre de QCM tâches professionnelles niveau junior réalisées <?php echo $resultJu['declaratifDone'] ?> / <?php echo $resultJu['declaratifTotal'] ?> QCM',
                    total: 100,
                    completed: percentageJu, // Moyenne des compétences acquises
                    data: [percentageJu, 100 - percentageJu], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [percentageJu + '% de Tâches professionnelles niveau junior réalisées',
                        100 - percentageJu + '% de QCM Tâches professionnelles niveau junior à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
                {
                    title: 'Nombre de QCM tâches professionnelles niveau senior réalisées <?php echo $resultSe['declaratifDone'] ?> / <?php echo $resultSe['declaratifTotal'] ?> QCM',
                    total: 100,
                    completed: percentageSe, // Moyenne des compétences acquises
                    data: [percentageSe, 100 - percentageSe], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        percentageSe + '% de Tâches professionnelles niveau senior réalisées',
                        100 - percentageSe + '% de QCM Tâches professionnelles niveau senior à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
                {
                    title: 'Nombre de QCM tâches professionnelles niveau expert réalisées <?php echo $resultEx['declaratifDone'] ?> / <?php echo $resultEx['declaratifTotal'] ?> QCM',
                    total: 100,
                    completed: percentageEx, // Moyenne des compétences acquises
                    data: [percentageEx, 100 - percentageEx], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        percentageEx + '% de Tâches professionnelles niveau expert réalisées',
                        100 - percentageEx + '% de QCM Tâches professionnelles niveau expert à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
            ];
            
            // Calculate the average for "Total : 03 Niveaux" based on non-zero values
            const validData = chartData.filter(chart => chart.completed > 0);
            const averageCompleted = validData.length > 0 ?
                Math.round(validData.reduce((acc, chart) => acc + chart.completed, 0) / validData.length) :
                0;
            const averageData = [averageCompleted, 100 - averageCompleted];
            
            chartData.push({
                title: 'Nombre de QCM tâches professionnelles tous niveaux réalisées  <?php echo $resultJu['declaratifDone'] + $resultSe['declaratifDone'] + $resultEx['declaratifDone'] ?> / <?php echo $resultJu['declaratifTotal'] + $resultSe['declaratifTotal'] + $resultEx['declaratifTotal'] ?> QCM',
                total: 100,
                completed: averageCompleted,
                data: averageData,
                labels: [
                    `${averageCompleted}% de QCM Tâches professionnelles tous niveaux réalisées`,
                    `${100 - averageCompleted}% de QCM Tâches professionnelles tous niveaux à réaliser`
                ],
                backgroundColor: ['#4303ec', '#D3D3D3']
            });
        
            const container = document.getElementById('chartQCM');
        
            // Loop through the data to create and append cards
            chartData.forEach((data, index) => {
                // Calculate the completed percentage
                const completedPercentage = Math.round((data.completed / data.total) * 100);
        
                // Create the card element
                const cardHtml = `
                    <div class="col-md-6 col-lg-3 col-xl-2.5 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex justify-content-center text-center flex-column p-4">
                                <h5>Pourcentage QCM réalisés: ${completedPercentage}%</h5>
                                <canvas id="doughnutChart${index}" width="200" height="200"></canvas>
                                <h5 class="mt-2">${data.title}</h5>
                            </div>
                        </div>
                    </div>
                `;
        
                // Append the card to the container
                container.insertAdjacentHTML('beforeend', cardHtml);
                // Initialize the Chart.js doughnut chart
                new Chart(document.getElementById(`doughnutChart${index}`).getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Data',
                            data: data.data,
                            backgroundColor: data.backgroundColor,
                            borderColor: data.backgroundColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a +
                                        b, 0);
                                    let percentage = Math.round((value / sum) * 100);
                                    // Round up to the nearest whole number
                                    return percentage + '%';
                                },
                                color: '#fff',
                                display: true,
                                anchor: 'center',
                                align: 'center',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw || 0;
                                        const dataset = tooltipItem.dataset.data;
                                        let sum = dataset.reduce((a, b) => a + b, 0);
                                        let percentage = Math.round((value / sum) * 100);
                                        // Round up to the nearest whole number
                                        return `Pourcentage: ${percentage}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php } else if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == true) { ?>
    <script>
        // Graphiques pour les resultats du groupe
        document.addEventListener('DOMContentLoaded', function() {
            function addIfZero(done, total) {
                let percentage;
                if (total == 0) {
                    percentage = (done * 100) / 1;
                } else {
                    percentage = (done * 100) / total;
                }
                return percentage;
            }
            
            // Calculate percentages
            let percentageJu = addIfZero(<?php echo $resultJu['declaratifDone'] ?>, <?php echo $resultJu['declaratifTotal'] ?>);
            let percentageSe = addIfZero(<?php echo $resultSe['declaratifDone'] ?>, <?php echo $resultSe['declaratifTotal'] ?>);
            let percentageEx = addIfZero(<?php echo $resultEx['declaratifDone'] ?>, <?php echo $resultEx['declaratifTotal'] ?>);

            const datas = [
                {
                    completed: percentageJu
                }, {
                    completed: percentageSe
                }, {
                    completed: percentageEx
                }
            ];

            // Data for each chart
            const chartData = [
                {
                    title: 'Nombre de QCM connaissances réalisées <?php echo $results['factuelDone'] ?> / <?php echo $results['factuelTotal'] ?> QCM',
                    total: 100,
                    completed: <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>, // Moyenne des compétences acquises
                    data: [
                        <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>,
                        100 - <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>
                    ], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        '<?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>% de QCM Connaissances réalisées',
                        '<?php echo 100 - round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>% de QCM Connaissances à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
                {
                    title: 'Nombre de QCM tâches professionnelles techniciens réalisées <?php echo $results['declaratifDone'] ?> / <?php echo $results['declaratifTotal'] ?> QCM',
                    total: 100,
                    completed: <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>, // Moyenne des compétences acquises
                    data: [
                        <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>,
                        100 - <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>
                    ], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        '<?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>% de Tâches professionnelles réalisées',
                        '<?php echo 100 - round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>% de QCM Tâches professionnelles à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
            ];
            
            // Calculate the average for "Total : 03 Niveaux" based on non-zero values
            const validData = datas.filter(chart => chart.completed > 0);
            const averageCompleted = validData.length > 0 ?
                Math.round(validData.reduce((acc, chart) => acc + chart.completed, 0) / validData.length) :
                0;
            const averageData = [averageCompleted, 100 - averageCompleted];
            
            chartData.push({
                title: 'Nombre de QCM tâches professionnelles managers réalisées  <?php echo $resultJu['declaratifDone'] + $resultSe['declaratifDone'] + $resultEx['declaratifDone'] ?> / <?php echo $resultJu['declaratifTotal'] + $resultSe['declaratifTotal'] + $resultEx['declaratifTotal'] ?> QCM',
                total: 100,
                completed: averageCompleted,
                data: averageData,
                labels: [
                    `${averageCompleted}% de QCM Tâches professionnelles managers réalisées`,
                    `${100 - averageCompleted}% de QCM Tâches professionnelles managers à réaliser`
                ],
                backgroundColor: ['#4303ec', '#D3D3D3']
            });
        
            const container = document.getElementById('chartQCM');
        
            // Loop through the data to create and append cards
            chartData.forEach((data, index) => {
                // Calculate the completed percentage
                const completedPercentage = Math.round((data.completed / data.total) * 100);
        
                // Create the card element
                const cardHtml = `
                    <div class="col-md-6 col-lg-4 col-xl-2.5 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex justify-content-center text-center flex-column p-4">
                                <h5>Pourcentage QCM réalisés: ${completedPercentage}%</h5>
                                <canvas id="doughnutChart${index}" width="200" height="200"></canvas>
                                <h5 class="mt-2">${data.title}</h5>
                            </div>
                        </div>
                    </div>
                `;
        
                // Append the card to the container
                container.insertAdjacentHTML('beforeend', cardHtml);
                // Initialize the Chart.js doughnut chart
                new Chart(document.getElementById(`doughnutChart${index}`).getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Data',
                            data: data.data,
                            backgroundColor: data.backgroundColor,
                            borderColor: data.backgroundColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a +
                                        b, 0);
                                    let percentage = Math.round((value / sum) * 100);
                                    // Round up to the nearest whole number
                                    return percentage + '%';
                                },
                                color: '#fff',
                                display: true,
                                anchor: 'center',
                                align: 'center',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw || 0;
                                        const dataset = tooltipItem.dataset.data;
                                        let sum = dataset.reduce((a, b) => a + b, 0);
                                        let percentage = Math.round((value / sum) * 100);
                                        // Round up to the nearest whole number
                                        return `Pourcentage: ${percentage}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php } else { ?>
    <script>
         // Graphiques pour les resultats du groupe
        document.addEventListener('DOMContentLoaded', function() {
            function addIfZero(done, total) {
                let percentage;
                if (total == 0) {
                    percentage = (done * 100) / 1;
                } else {
                    percentage = (done * 100) / total;
                }
                return percentage;
            }
            
            // Calculate percentages
            let factuel = addIfZero(<?php echo $results['factuelDone'] ?>, <?php echo $results['factuelTotal'] ?>);
            let declaratif = addIfZero(<?php echo $results['declaratifDone'] ?>, <?php echo $results['declaratifTotal'] ?>);

            // Data for each chart
            const chartData = [
                {
                    title: 'Nombre de QCM connaissances réalisées <?php echo $results['factuelDone'] ?> / <?php echo $results['factuelTotal'] ?> QCM',
                    total: 100,
                    completed: <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>, // Moyenne des compétences acquises
                    data: [
                        <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>,
                        100 - <?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>
                    ], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        '<?php echo round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>% de QCM Connaissances réalisées',
                        '<?php echo 100 - round(($results['factuelDone'] * 100) / $results['factuelTotal']) ?>% de QCM Connaissances à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
                {
                    title: 'Nombre de QCM tâches professionnelles réalisées <?php echo $results['declaratifDone'] ?> / <?php echo $results['declaratifTotal'] ?> QCM',
                    total: 100,
                    completed: <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>, // Moyenne des compétences acquises
                    data: [
                        <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>,
                        100 - <?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>
                    ], // Moyenne des compétences acquises vs. Moyenne des compétences à acquérir
                    labels: [
                        '<?php echo round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>% de Tâches professionnelles réalisées',
                        '<?php echo 100 - round(($results['declaratifDone'] * 100) / $results['declaratifTotal']) ?>% de QCM Tâches professionnelles à réaliser'
                    ],
                    backgroundColor: ['#4303ec', '#D3D3D3']
                },
            ];
        
            const container = document.getElementById('chartQCM');
        
            // Loop through the data to create and append cards
            chartData.forEach((data, index) => {
                // Calculate the completed percentage
                const completedPercentage = Math.round((data.completed / data.total) * 100);
        
                // Create the card element
                const cardHtml = `
                    <div class="col-md-6 col-lg-6 col-xl-2.5 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex justify-content-center text-center flex-column p-4">
                                <h5>Pourcentage QCM réalisés: ${completedPercentage}%</h5>
                                <canvas id="doughnutChart${index}" width="200" height="200"></canvas>
                                <h5 class="mt-2">${data.title}</h5>
                            </div>
                        </div>
                    </div>
                `;
        
                // Append the card to the container
                container.insertAdjacentHTML('beforeend', cardHtml);
                // Initialize the Chart.js doughnut chart
                new Chart(document.getElementById(`doughnutChart${index}`).getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Data',
                            data: data.data,
                            backgroundColor: data.backgroundColor,
                            borderColor: data.backgroundColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a +
                                        b, 0);
                                    let percentage = Math.round((value / sum) * 100);
                                    // Round up to the nearest whole number
                                    return percentage + '%';
                                },
                                color: '#fff',
                                display: true,
                                anchor: 'center',
                                align: 'center',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw || 0;
                                        const dataset = tooltipItem.dataset.data;
                                        let sum = dataset.reduce((a, b) => a + b, 0);
                                        let percentage = Math.round((value / sum) * 100);
                                        // Round up to the nearest whole number
                                        return `Pourcentage: ${percentage}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php } ?>
<?php
}
?>