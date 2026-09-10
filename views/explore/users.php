<?php
session_start();
include_once "../language.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION["profile"])) {
    header("Location: ../../");
    exit();
} else {
     ?>
<?php
require_once "../../vendor/autoload.php";
// Create connection
$conn = new MongoDB\Client("mongodb://localhost:27017");
// Connecting in database
$academy = $conn->academy; 
// Connecting in collections
$users = $academy->users;
$allocations = $academy->allocations;
$connections = $academy->connections;

if (isset($_POST["excel"])) {
    $spreadsheet = new Spreadsheet();
    $excel_writer = new Xlsx($spreadsheet);
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->setCellValue("A1", "Nom d'utilisateur");
    $activeSheet->setCellValue("B1", "Matricule");
    $activeSheet->setCellValue("C1", "Prénoms");
    $activeSheet->setCellValue("D1", "Noms");
    $activeSheet->setCellValue("E1", "Email");
    $activeSheet->setCellValue("F1", "Numéro de téléphone");
    $activeSheet->setCellValue("G1", "Sexe");
    $activeSheet->setCellValue("H1", "Date de naissance");
    $activeSheet->setCellValue("I1", "Niveau technique");
    $activeSheet->setCellValue("J1", "Pays");
    $activeSheet->setCellValue("K1", "Profil");
    $activeSheet->setCellValue("L1", "Diplôme");
    $activeSheet->setCellValue("M1", "Filiale");
    $activeSheet->setCellValue("N1", "Département");
    $activeSheet->setCellValue("O1", "Fonction");
    $activeSheet->setCellValue("P1", "Date de recrutement");
    $activeSheet->setCellValue("Q1", "Mot de Passe");
    $myObj = $users->find();
    $i = 2;
    foreach ($myObj as $row) {
        $activeSheet->setCellValue("A" . $i, $row->username);
        $activeSheet->setCellValue("B" . $i, $row->matricule);
        $activeSheet->setCellValue("C" . $i, $row->firstName);
        $activeSheet->setCellValue("D" . $i, $row->lastName);
        $activeSheet->setCellValue("E" . $i, $row->email);
        $activeSheet->setCellValue("F" . $i, $row->phone);
        $activeSheet->setCellValue("G" . $i, $row->gender);
        $activeSheet->setCellValue("H" . $i, $row->birthdate);
        $activeSheet->setCellValue("I" . $i, $row->level);
        $activeSheet->setCellValue("J" . $i, $row->country);
        $activeSheet->setCellValue("K" . $i, $row->profile);
        $activeSheet->setCellValue("L" . $i, $row->certificate);
        $activeSheet->setCellValue("M" . $i, $row->subsidiary);
        $activeSheet->setCellValue("N" . $i, $row->department);
        $activeSheet->setCellValue("O" . $i, $row->role);
        $activeSheet->setCellValue("P" . $i, $row->recrutmentDate);
        $activeSheet->setCellValue("Q" . $i, $row->visiblePassword);
        $i++;
    }
    $filename = "Utilisateurs.xlsx";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment;filename=" . $filename);
    header("cache-Control: max-age=0");
    $excel_writer->save("php://output");
}
$time = time();
?>

<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $list_user ?> | CFAO Mobility Academy</title>
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
                    <?php echo $list_user ?> </h1>
                <!--end::Title-->
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
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
        <!--begin::Container-->
        <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table aria-describedby=""
                                class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer"
                                id="kt_customers_table">
                                <?php
                                // Start output buffering
                                ob_start();

                                // Array to hold column data
                                $columns = [
                                    'prenomsNoms' => ['label' => $prenomsNoms, 'profiles' => ['Admin', 'Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']],
                                    'email' => ['label' => $email, 'profiles' => ['Admin']],
                                    'pays' => ['label' => $pays, 'profiles' => ['Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']],
                                    'profil' => ['label' => $profil, 'profiles' => ['Admin', 'Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']],
                                    'department' => ['label' => $department, 'profiles' => ['Admin', 'Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']],
                                    'levelTech' => ['label' => $levelTech, 'profiles' => ['Admin', 'Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']],
                                    'username' => ['label' => $username, 'profiles' => ['Admin', 'Super Admin']],
                                    'Password' => ['label' => $Password, 'profiles' => ['Admin', 'Super Admin']],
                                    'status' => ['label' => $status, 'profiles' => ['Super Admin']],
                                    'title_collaborators' => ['label' => $title_collaborators, 'profiles' => ['Admin', 'Super Admin', 'Directeur Groupe', 'Directeur Pièce et Service', 'Directeur des Opérations']]
                                ];

                                ?>

                                <thead>
                                    <tr class="text-start text-black fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2 sorting_disabled"></th>
                                        <?php
                                        // Loop through the columns array and generate headers based on user profile
                                        foreach ($columns as $column) {
                                            if (in_array($_SESSION["profile"], $column['profiles'])) {
                                                echo '<th class="min-w-125px sorting" style="width: 152.719px;">' . $column['label'] . '</th>';
                                            }
                                        }
                                    ?>
                                    </tr>
                                </thead>

                                <?php
                                // End output buffering and flush output
                                ob_end_flush();
                                ?>

                                <tbody class="fw-semibold text-gray-600" id="table">
                                    <?php
                                    $query = [
                                        'active' => true
                                    ];
                                    if ($_SESSION["profile"] !== "Super Admin" || $_SESSION["profile"] !== "Directeur Groupe") {
                                        if ($_SESSION["department"] !== 'Equipment & Motors') {
                                            $query['department'] = $_SESSION["department"];
                                        }
                                    }
                                    $persons = $users->find($query);
                                    foreach ($persons as $user) { ?>
                                    <?php if (
                                        $_SESSION["profile"] == "Admin"
                                    ) { ?>
                                    <?php if (
                                        $user["profile"] != "Admin" && $user["profile"] != "Directeur Pièce et Service" && $user["profile"] != "Directeur des Opérations" && $user["profile"] != "Directeur Groupe" &&
                                        $user["profile"] != "Super Admin" && $_SESSION["subsidiary"] == $user["subsidiary"]
                                    ) { ?>
                                    <tr class="odd" etat="<?php echo $user->active; ?>">
                                        <!-- Optionally include the checkbox -->
                                        <!-- <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" id="checkbox" type="checkbox"
                                                    onclick="enable()" value="<?php echo $user->_id; ?>">
                                            </div>
                                        </td> 
                                        -->
                                        <td></td>
                                        <td data-filter="search">
                                            <?php echo htmlspecialchars($user->firstName . ' ' . $user->lastName); ?>
                                        </td>
                                        <td data-filter="email">
                                            <?php echo htmlspecialchars($user->email); ?>
                                        </td>

                                        <?php if ($user->profile == "Manager" && $user->test) { ?>
                                        <td data-order="subsidiary">
                                            Manager - Technicien
                                        </td>
                                        <?php } else { ?>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->profile); ?>
                                        </td>
                                        <?php } ?>

                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->department); ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->level); ?>
                                        </td>

                                        <td data-order="department">
                                            <?php echo htmlspecialchars($user->username); ?>
                                        </td>

                                        <td data-order="department">
                                            <?php echo htmlspecialchars($user->visiblePassword); ?>
                                        </td>

                                        <?php if ($user->profile == "Manager") { ?>
                                        <td data-order="department">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir les collaborateurs" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details<?php echo $user->_id; ?>">
                                                <?php echo htmlspecialchars($voir_collab); ?>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>

                                    <?php } ?>
                                    <?php } ?>
                                    <?php if (
                                        $_SESSION["profile"] == "Ressource Humaine"
                                    ) { ?>
                                    <?php if (
                                        $user["profile"] != "Ressource Humaine" && $user["profile"] != "Directeur Pièce et Service" && $user["profile"] != "Directeur des Opérations" && $user["profile"] != "Directeur Groupe" &&
                                        $user["profile"] != "Super Admin" && $user["profile"] != "Admin" && $_SESSION["subsidiary"] == $user["subsidiary"]
                                    ) { ?>
                                    <tr class="odd" etat="<?php echo $user->active; ?>">
                                        <!-- Optionally include the checkbox -->
                                        <!-- <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" id="checkbox" type="checkbox"
                                                    onclick="enable()" value="<?php echo $user->_id; ?>">
                                            </div>
                                        </td> 
                                        -->
                                        <td></td>
                                        <td data-filter="search">
                                            <?php echo htmlspecialchars($user->firstName . ' ' . $user->lastName); ?>
                                        </td>
                                        <td data-filter="email">
                                            <?php echo htmlspecialchars($user->email); ?>
                                        </td>

                                        <?php if ($user->profile == "Manager" && $user->test) { ?>
                                        <td data-order="subsidiary">
                                            Manager - Technicien
                                        </td>
                                        <?php } else { ?>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->profile); ?>
                                        </td>
                                        <?php } ?>

                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->department); ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->level); ?>
                                        </td>

                                        <td data-order="department">
                                            <?php echo htmlspecialchars($user->username); ?>
                                        </td>

                                        <td data-order="department">
                                            <?php echo htmlspecialchars($user->visiblePassword); ?>
                                        </td>

                                        <?php if ($user->profile == "Manager") { ?>
                                        <td data-order="department">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir les collaborateurs" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details<?php echo $user->_id; ?>">
                                                <?php echo htmlspecialchars($voir_collab); ?>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>

                                    <?php } ?>
                                    <?php } ?>
                                    <?php if (
                                        $_SESSION["profile"] == "Super Admin"
                                    ) { ?>
                                    <?php if (
                                        $user["profile"] != "Super Admin"
                                    ) {
                                        $userConnected = $connections->findOne([
                                            '$and' => [
                                                [
                                                    "user" => new MongoDB\BSON\ObjectId($user["_id"]),
                                                    "status" => "Online",
                                                ],
                                            ],
                                        ]);
                                        if (isset($userConnected)) {
                                            $status = "Online";
                                            $badge = "badge-light-success";
                                        } else {
                                            $badge = "badge-light-danger";
                                            $status = "Offline";
                                        }
                                        ?>
                                    <tr id="superAdmin" class="odd" etat="<?php echo $user->active; ?>">
                                        <!-- <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" id="checkbox" type="checkbox"
                                                    onclick="enable()" value="<?php echo $user->_id; ?>">
                                            </div>
                                        </td> -->
                                        <td></td>
                                        <td data-filter="search">
                                            <?php echo $user->firstName; ?> <?php echo $user->lastName; ?>
                                        </td>
                                        <td data-filter="email">
                                            <?php echo $user->country; ?>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager" && $user["test"] == true
                                        ) { ?>
                                        <td data-order="subsidiary">
                                            Manager - Technicien
                                        </td>
                                        <?php } else { ?>
                                        <td data-order="subsidiary">
                                            <?php echo $user->profile; ?>
                                        </td>
                                        <?php } ?>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->department); ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo $user->level; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo $user->username; ?>
                                        </td>
                                        <td data-order="department">
                                            <?php echo $user->visiblePassword; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge ?> fs-7 m-1">
                                                <?php echo $status ?>
                                            </span>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager"
                                        ) { ?>
                                        <td data-order="department">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir les collaborateurs" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details<?php echo $user->_id; ?>">
                                                <?php echo $voir_collab ?>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if (
                                        $_SESSION["profile"] == "Directeur Groupe"
                                    ) { ?>
                                    <?php if (
                                        $user["profile"] != "Super Admin" && $user["profile"] != "Directeur Groupe"
                                    ) { ?>
                                    <tr class="odd" etat="<?php echo $user->active; ?>">
                                        <!-- <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" id="checkbox" type="checkbox"
                                                    onclick="enable()" value="<?php echo $user->_id; ?>">
                                            </div>
                                        </td> -->
                                        <td></td>
                                        <td data-filter="search">
                                            <?php echo $user->firstName; ?> <?php echo $user->lastName; ?>
                                        </td>
                                        <td data-filter="email">
                                            <?php echo $user->country; ?>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager" && $user["test"] == true
                                        ) { ?>
                                        <td data-order="subsidiary">
                                            Manager - Technicien
                                        </td>
                                        <?php } else { ?>
                                        <td data-order="subsidiary">
                                            <?php echo $user->profile; ?>
                                        </td>
                                        <?php } ?>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->department); ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo $user->level; ?>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager"
                                        ) { ?>
                                        <td data-order="department">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir les collaborateurs" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details<?php echo $user->_id; ?>">
                                                <?php echo $voir_collab ?>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if (
                                        $user["profile"] == "Directeur Pièce et Service" || $user["profile"] == "Directeur des Opérations"
                                    ) { ?>
                                    <?php if ($user["profile"] != "Directeur Pièce et Service" && $user["profile"] != "Directeur des Opérations" && $user["profile"] != "Directeur Groupe" &&
                                        $user["profile"] != "Super Admin" && 
                                        $_SESSION["subsidiary"] == $user["subsidiary"]
                                    ) { ?>
                                    <tr class="odd" etat="<?php echo $user->active; ?>">
                                        <!-- <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" id="checkbox" type="checkbox"
                                                    onclick="enable()" value="<?php echo $user->_id; ?>">
                                            </div>
                                        </td> -->
                                        <td></td>
                                        <td data-filter="search">
                                            <?php echo $user->firstName; ?> <?php echo $user->lastName; ?>
                                        </td>
                                        <td data-filter="email">
                                            <?php echo $user->country; ?>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager" && $user["test"] == true
                                        ) { ?>
                                        <td data-order="subsidiary">
                                            Manager - Technicien
                                        </td>
                                        <?php } else { ?>
                                        <td data-order="subsidiary">
                                            <?php echo $user->profile; ?>
                                        </td>
                                        <?php } ?>
                                        <td data-order="subsidiary">
                                            <?php echo htmlspecialchars($user->department); ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo $user->level; ?>
                                        </td>
                                        <?php if (
                                            $user["profile"] == "Manager"
                                        ) { ?>
                                        <td data-order="department">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary text-primary fw-bolder btn-sm"
                                                title="Cliquez ici pour voir les collaborateurs" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details<?php echo $user->_id; ?>">
                                                <?php echo $voir_collab ?>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                    <!--begin::Modal - Invite Friends-->
                                    <div class="modal fade" id="kt_modal_update_details<?php echo $user->_id; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog mw-650px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header pb-0 border-0 justify-content-end">
                                                    <!--begin::Close-->
                                                    <div class="btn btn-sm btn-icon btn-active-color-primary"
                                                        data-bs-dismiss="modal">
                                                        <i class="ki-duotone ki-cross fs-1"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <!--end::Close-->
                                                </div>
                                                <!--begin::Modal header-->
                                                <!--begin::Modal body-->
                                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                                    <!--begin::Heading-->
                                                    <div class="text-center mb-13">
                                                        <!--begin::Title-->
                                                        <h1 class="mb-3">
                                                            <?php echo $title_collaborators ?>
                                                        </h1>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Users-->
                                                    <div class="mb-10">
                                                        <!--begin::List-->
                                                        <div class="mh-300px scroll-y me-n7 pe-7">
                                                            <!--begin::User-->
                                                            <?php
                                                            $collaborator = $users->find(
                                                                [
                                                                    '$and' => [
                                                                        [
                                                                            "_id" => [
                                                                                '$in' =>
                                                                                    $user[
                                                                                        "users"
                                                                                    ],
                                                                            ],
                                                                            "active" => true,
                                                                        ],
                                                                    ],
                                                                ]
                                                            );
                                                            foreach (
                                                                $collaborator
                                                                as $collaborator
                                                            ) { ?>
                                                            <div
                                                                class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                                                <!--begin::Details-->
                                                                <div class="d-flex align-items-center">
                                                                    <div class="ms-5">
                                                                        <a href="#"
                                                                            class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">
                                                                            <?php echo $collaborator->firstName; ?>
                                                                            <?php echo $collaborator->lastName; ?>
                                                                        </a>
                                                                    </div>
                                                                    <!--end::Details-->
                                                                </div>
                                                                <!--end::Details-->
                                                                <!--begin::Access menu-->
                                                                <!-- <div data-kt-menu-trigger="click">
                                                                    <form method="POST">
                                                                        <input type="hidden" name="questionID"
                                                                            value="<?php echo $user->_id; ?>">
                                                                        <input type="hidden" name="userID"
                                                                            value="<?php echo $user->_id; ?>">
                                                                        <button
                                                                            class="btn btn-light btn-active-light-primary btn-sm"
                                                                            type="submit" name="retire-question-user"
                                                                            title="Cliquez ici pour enlever la question du questionnaire">Supprimer</button>
                                                                    </form>
                                                                </div> -->
                                                                <!--end::Access menu-->
                                                            </div>
                                                            <!--end::User-->
                                                            <?php }
                                                            ?>
                                                        </div>
                                                        <!--end::List-->
                                                    </div>
                                                    <!--end::Users-->
                                                </div>
                                                <!--end::Modal body-->
                                            </div>
                                            <!--end::Modal content-->
                                        </div>
                                        <!--end::Modal dialog-->
                                    </div>
                                    <!--end::Modal - Invite Friend-->
                                    <?php }
                                    ?>
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
            name: `Users.xlsx`
        })
    });
});
</script>
<?php include_once "partials/footer.php"; ?>
<?php
} ?>