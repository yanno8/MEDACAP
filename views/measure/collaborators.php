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
require_once "../../vendor/autoload.php"; // Create connection
$conn = new MongoDB\Client("mongodb://localhost:27017");
// Connecting in database
$academy = $conn->academy; // Connecting in collections
$users = $academy->users;
$allocations = $academy->allocations;

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
    $activeSheet->setCellValue("Q1", "Manager");
    $myObj = $users->find([
        '$and' => [
            [
                "manager" => new MongoDB\BSON\ObjectId($_SESSION["id"]),
                "active" => true,
            ],
        ],
    ]);
    $i = 2;
    foreach ($myObj as $row) {
        $manager = $users->findOne(["_id" => $row->manager]);
        if ($manager) {
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
            $activeSheet->setCellValue(
                "Q" . $i,
                $_SESSION["firstName"] . " " . $_SESSION["lastName"]
            );
            $i++;
        }
    }
    $filename = "Collaborateurs.xlsx";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment;filename=" . $filename);
    header("cache-Control: max-age=0");
    $excel_writer->save("php://output");
}
?>

<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $title_collaborators ?> | CFAO Mobility Academy</title>
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
                <h1 class="text-dark fw-bold my-1 fs-1">
                    <?php echo $title_collaborators ?> </h1>
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
            <!--begin::Actions-->
            <!-- <div class="d-flex align-items-center flex-nowrap text-nowrap py-1">
                <div class="d-flex justify-content-end align-items-center" style="margin-left: 10px;">
                    <button type="button" id="users" data-bs-toggle="modal" class="btn btn-primary">
                        Liste subordonnés
                    </button>
                </div>
                <div class="d-flex justify-content-end align-items-center" style="margin-left: 10px;">
                    <button type="button" id="edit" title="Cliquez ici pour modifier le technicien"
                        data-bs-toggle="modal" class="btn btn-primary">
                        Modifier
                    </button>
                </div>
                <div class="d-flex justify-content-end align-items-center" style="margin-left: 10px;">
                    <button type="button" id="password" data-bs-toggle="modal"
                        title="Cliquez ici pour modifier le mot de passe du technicien" class="btn btn-primary">
                        Modifier mot de passe
                    </button>
                </div>
                <div class="d-flex justify-content-end align-items-center" style="margin-left: 10px;">
                    <button type="button" id="delete" title="Cliquez ici pour supprimer le technicien"
                        data-bs-toggle="modal" class="btn btn-danger">
                        Supprimer
                    </button>
                </div>
            </div> -->
            <!--end::Actions-->
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
                <!-- <div class="card-header border-0 pt-6"> -->
                <!--begin::Card title-->
                <!-- <div class="card-title"> -->
                <!--begin::Search-->
                <!-- <div
                            class="d-flex align-items-center position-relative my-1">
                            <i
                                class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span
                                    class="path1"></span><span
                                    class="path2"></span></i>
                            <input type="text" id="search"
                                class="form-control form-control-solid w-250px ps-12"
                                placeholder="Recherche">
                        </div> -->
                <!--end::Search-->
                <!-- </div> -->
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <!-- <div class="card-toolbar"> -->
                <!--begin::Toolbar-->
                <!-- <div class="d-flex justify-content-end"
                            data-kt-customer-table-toolbar="base"> -->
                <!--begin::Filter-->
                <!-- <div class="w-150px me-3" id="etat"> -->
                <!--begin::Select2-->
                <!-- <select id="select"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-hide-search="true"
                                    data-placeholder="Etat"
                                    data-kt-ecommerce-order-filter="etat">
                                    <option></option>
                                    <option value="tous">Tous
                                    </option>
                                    <option value="true">
                                        Active</option>
                                    <option value="false">
                                        Supprimé</option>
                                </select> -->
                <!--end::Select2-->
                <!-- </div> -->
                <!--end::Filter-->
                <!--begin::Export dropdown-->
                <!-- <button type="button" id="excel"
                                class="btn btn-light-primary">
                                <i class="ki-duotone ki-exit-up fs-2"><span
                                        class="path1"></span><span
                                        class="path2"></span></i>
                                Excel
                            </button> -->
                <!--end::Export dropdown-->
                <!--begin::Group actions-->
                <!-- <div class="d-flex justify-content-end align-items-center"
                                style="margin-left: 10px;">
                                <button type="button" id="edit"
                                    data-bs-toggle="modal"
                                    class="btn btn-primary">
                                    Modifier
                                </button>
                            </div> -->
                <!--end::Group actions-->
                <!--begin::Group actions-->
                <!-- <div class="d-flex justify-content-end align-items-center"
                                style="margin-left: 10px;">
                                <button type="button" id="password"
                                    data-bs-toggle="modal"
                                    class="btn btn-primary">
                                    Modifier mot de passe
                                </button>
                            </div> -->
                <!--end::Group actions-->
                <!--begin::Group actions-->
                <!-- <div class="d-flex justify-content-end align-items-center"
                                style="margin-left: 10px;">
                                <button type="button" id="delete"
                                    data-bs-toggle="modal"
                                    class="btn btn-danger">
                                    Supprimer
                                </button>
                            </div> -->
                <!--end::Group actions-->
                <!-- </div> -->
                <!--end::Toolbar-->
                <!-- </div> -->
                <!--end::Card toolbar-->
                <!-- </div> -->
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table aria-describedby=""
                                class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer"
                                id="kt_customers_table">
                                <thead>
                                    <tr class="text-start text-black fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2 sorting_disabled" rowspan="1" colspan="1" aria-label=""
                                            style="width: 29.8906px;">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </div>
                                        </th>
                                        <th class="min-w-225px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Customer Name: activate to sort column ascending"
                                            style="width: 125px;"><?php echo $prenomsNoms ?>
                                        </th>
                                        <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Email: activate to sort column ascending"
                                            style="width: 155.266px;"><?php echo $email ?></th>
                                        <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Created Date: activate to sort column ascending"
                                            style="width: 152.719px;">
                                            <?php echo $phoneNumber ?></th>
                                        <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Created Date: activate to sort column ascending"
                                            style="width: 152.719px;"><?php echo $profil ?>
                                        </th>
                                        <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Created Date: activate to sort column ascending"
                                            style="width: 152.719px;"><?php echo $levelTech ?>
                                        </th>
                                        <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="1" colspan="1"
                                            aria-label="Created Date: activate to sort column ascending"
                                            style="width: 152.719px;">
                                            <?php echo $department ?></th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600" id="table">
                                    <?php
                                    $manager = $users->findOne([
                                        '$and' => [
                                            [
                                                "_id" => new MongoDB\BSON\ObjectId(
                                                    $_SESSION["id"]
                                                ),
                                                "active" => true,
                                            ],
                                        ],
                                    ]);
                                    foreach ($manager->users as $person) {
                                        $user = $users->findOne([
                                            '$and' => [
                                                [
                                                    "_id" => new MongoDB\BSON\ObjectId(
                                                        $person
                                                    ),
                                                    "active" => true,
                                                ],
                                            ],
                                        ]); ?>
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
                                            <?php echo $user->email; ?>
                                        </td>
                                        <td data-order="subsidiary">
                                            <?php echo $user->phone; ?>
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
                                            <?php echo $user->level; ?>
                                        </td>
                                        <td data-order="department">
                                            <?php echo $user->department; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
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
                <form method="post">
                    <button type="submit" name="excel" title="Cliquez ici pour importer la table" class="btn btn-primary">
                        <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                        <?php echo $excel ?>
                    </button>
                </form>
            </div>
            <!--end::Export dropdown-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Body-->
<?php include_once "partials/footer.php"; ?>
<?php
} ?>
