<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["id"])) {
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
    $quizzes = $academy->quizzes;
    $vehicles = $academy->vehicles;
    $questions = $academy->questions;
    $results = $academy->results;
    $exams = $academy->exams;
    $tests = $academy->tests;
    $allocations = $academy->allocations;

    $id = $_GET["user"];
    $manager = $_GET["id"];
    $level = $_GET["level"];
    $test = $_GET["test"];

    $user = $users->findone([
        '$and' => [
            [
                "_id" => new MongoDB\BSON\ObjectId($id),
                "active" => true,
            ],
        ],
    ]);

    $userManager = $users->findone([
        '$and' => [
            [
                "_id" => new MongoDB\BSON\ObjectId($manager),
                "active" => true,
            ],
        ],
    ]);

    $userTest = $tests->findOne([
        '$and' => [
            ["_id" => new MongoDB\BSON\ObjectId($test)],
            ["user" => new MongoDB\BSON\ObjectId($id)],
            ["type" => "Declaratif"],
            ["level" => $level],
            ["active" => true],
        ],
    ]);

    $exam = $exams->findOne([
        '$and' => [
            ["user" => new MongoDB\BSON\ObjectId($id)],
            ["manager" => new MongoDB\BSON\ObjectId($manager)],
            ["test" => new MongoDB\BSON\ObjectId($test)],
            ["type" => "Manager"],
            ["active" => true],
        ],
    ]);
    $arrQuizzes = $userTest["quizzes"];

    if (isset($_POST["save"])) {
        $questionsTag = $_POST["questionsTag"];
        $hr = $_POST["hr"];
        $mn = $_POST["mn"];
        $sc = $_POST["sc"];
        $questionsTags = [];
        $body = $_POST;
        $answers = [];
        $to = [];

        for ($i = 0; $i < count($questionsTag); ++$i) {
            array_push(
                $questionsTags,
                new MongoDB\BSON\ObjectId($questionsTag[$i])
            );
        }
        foreach($body as $key => $value) {
            if (strpos($key, 'answer') === 0) {
                array_push($answers, $value);
                if ($value != "null") {
                    array_push($to, $value);
                }
            }
        }

        if (isset($_POST["quizAssistance"])) {
            $assistanceID = new MongoDB\BSON\ObjectId($_POST["quizAssistance"]);
        }
        if (isset($_POST["quizArbre"])) {
            $arbreID = new MongoDB\BSON\ObjectId($_POST["quizArbre"]);
        }
        if (isset($_POST["quizTransfert"])) {
            $transfertID = new MongoDB\BSON\ObjectId($_POST["quizTransfert"]);
        }
        if (isset($_POST["quizBoite"])) {
            $boiteID = new MongoDB\BSON\ObjectId($_POST["quizBoite"]);
        }
        if (isset($_POST["quizBoiteAuto"])) {
            $boiteAutoID = new MongoDB\BSON\ObjectId($_POST["quizBoiteAuto"]);
        }
        if (isset($_POST["quizBoiteMan"])) {
            $boiteManID = new MongoDB\BSON\ObjectId($_POST["quizBoiteMan"]);
        }
        if (isset($_POST["quizBoiteVc"])) {
            $boiteVcID = new MongoDB\BSON\ObjectId($_POST["quizBoiteVc"]);
        }
        if (isset($_POST["quizClimatisation"])) {
            $climatisationID = new MongoDB\BSON\ObjectId(
                $_POST["quizClimatisation"]
            );
        }
        if (isset($_POST["quizDemi"])) {
            $demiID = new MongoDB\BSON\ObjectId($_POST["quizDemi"]);
        }
        if (isset($_POST["quizDirection"])) {
            $directionID = new MongoDB\BSON\ObjectId($_POST["quizDirection"]);
        }
        if (isset($_POST["quizElectricite"])) {
            $electriciteID = new MongoDB\BSON\ObjectId(
                $_POST["quizElectricite"]
            );
        }
        if (isset($_POST["quizFrei"])) {
            $freiID = new MongoDB\BSON\ObjectId($_POST["quizFrei"]);
        }
        if (isset($_POST["quizFreinageElec"])) {
            $freinageElecID = new MongoDB\BSON\ObjectId(
                $_POST["quizFreinageElec"]
            );
        }
        if (isset($_POST["quizFreinage"])) {
            $freinageID = new MongoDB\BSON\ObjectId($_POST["quizFreinage"]);
        }
        if (isset($_POST["quizFrein"])) {
            $freinID = new MongoDB\BSON\ObjectId($_POST["quizFrein"]);
        }
        if (isset($_POST["quizHydraulique"])) {
            $hydrauliqueID = new MongoDB\BSON\ObjectId(
                $_POST["quizHydraulique"]
            );
        }
        if (isset($_POST["quizMoteurDiesel"])) {
            $moteurDieselID = new MongoDB\BSON\ObjectId(
                $_POST["quizMoteurDiesel"]
            );
        }
        if (isset($_POST["quizMoteurElec"])) {
            $moteurElecID = new MongoDB\BSON\ObjectId($_POST["quizMoteurElec"]);
        }
        if (isset($_POST["quizMoteurEssence"])) {
            $moteurEssenceID = new MongoDB\BSON\ObjectId(
                $_POST["quizMoteurEssence"]
            );
        }
        if (isset($_POST["quizMoteur"])) {
            $moteurID = new MongoDB\BSON\ObjectId($_POST["quizMoteur"]);
        }
        if (isset($_POST["quizMultiplexage"])) {
            $multiplexageID = new MongoDB\BSON\ObjectId(
                $_POST["quizMultiplexage"]
            );
        }
        if (isset($_POST["quizPont"])) {
            $pontID = new MongoDB\BSON\ObjectId($_POST["quizPont"]);
        }
        if (isset($_POST["quizPneumatique"])) {
            $pneumatiqueID = new MongoDB\BSON\ObjectId(
                $_POST["quizPneumatique"]
            );
        }
        if (isset($_POST["quizReducteur"])) {
            $reducteurID = new MongoDB\BSON\ObjectId($_POST["quizReducteur"]);
        }
        if (isset($_POST["quizSuspension"])) {
            $suspensionID = new MongoDB\BSON\ObjectId($_POST["quizSuspension"]);
        }
        if (isset($_POST["quizSuspensionLame"])) {
            $suspensionLameID = new MongoDB\BSON\ObjectId(
                $_POST["quizSuspensionLame"]
            );
        }
        if (isset($_POST["quizSuspensionRessort"])) {
            $suspensionRessortID = new MongoDB\BSON\ObjectId(
                $_POST["quizSuspensionRessort"]
            );
        }
        if (isset($_POST["quizSuspensionPneumatique"])) {
            $suspensionPneumatiqueID = new MongoDB\BSON\ObjectId(
                $_POST["quizSuspensionPneumatique"]
            );
        }
        if (isset($_POST["quizTransversale"])) {
            $transversaleID = new MongoDB\BSON\ObjectId(
                $_POST["quizTransversale"]
            );
        }
        if (!isset($_POST["quizAssistance"])) {
            $assistanceID = null;
        }
        if (!isset($_POST["quizArbre"])) {
            $arbreID = null;
        }
        if (!isset($_POST["quizTransfert"])) {
            $transfertID = null;
        }
        if (!isset($_POST["quizBoite"])) {
            $boiteID = null;
        }
        if (!isset($_POST["quizBoiteAuto"])) {
            $boiteAutoID = null;
        }
        if (!isset($_POST["quizBoiteMan"])) {
            $boiteManID = null;
        }
        if (!isset($_POST["quizBoiteVc"])) {
            $boiteVcID = null;
        }
        if (!isset($_POST["quizClimatisation"])) {
            $climatisationID = null;
        }
        if (!isset($_POST["quizDemi"])) {
            $demiID = null;
        }
        if (!isset($_POST["quizDirection"])) {
            $directionID = null;
        }
        if (!isset($_POST["quizFrei"])) {
            $freiID = null;
        }
        if (!isset($_POST["quizFreinageElec"])) {
            $freinageElecID = null;
        }
        if (!isset($_POST["quizFreinage"])) {
            $freinageID = null;
        }
        if (!isset($_POST["quizFrein"])) {
            $freinID = null;
        }
        if (!isset($_POST["quizHydraulique"])) {
            $hydrauliqueID = null;
        }
        if (!isset($_POST["quizElectricite"])) {
            $electriciteID = null;
        }
        if (!isset($_POST["quizMoteurDiesel"])) {
            $moteurDieselID = null;
        }
        if (!isset($_POST["quizMoteurElec"])) {
            $moteurElecID = null;
        }
        if (!isset($_POST["quizMoteurEssence"])) {
            $moteurEssenceID = null;
        }
        if (!isset($_POST["quizMoteur"])) {
            $moteurID = null;
        }
        if (!isset($_POST["quizMultiplexage"])) {
            $multiplexageID = null;
        }
        if (!isset($_POST["quizPont"])) {
            $pontID = null;
        }
        if (!isset($_POST["quizPneumatique"])) {
            $pneumatiqueID = null;
        }
        if (!isset($_POST["quizReducteur"])) {
            $reducteurID = null;
        }
        if (!isset($_POST["quizSuspension"])) {
            $suspensionID = null;
        }
        if (!isset($_POST["quizSuspensionLame"])) {
            $suspensionLameID = null;
        }
        if (!isset($_POST["quizSuspensionRessort"])) {
            $suspensionRessortID = null;
        }
        if (!isset($_POST["quizSuspensionPneumatique"])) {
            $suspensionPneumatiqueID = null;
        }
        if (!isset($_POST["quizTransversale"])) {
            $transversaleID = null;
        }

        if ($exam) {
            $exam->answers = $answers;
            $exam->hour = $hr;
            $exam->minute = $mn;
            $exam->second = $sc;
            $exam->total = count($to);
            $exam->updated = date("d-m-Y H:i:s");
            $exams->updateOne(
                ["_id" => new MongoDB\BSON\ObjectId($exam->_id)],
                ['$set' => $exam]
            );
        } else {
            $exam = [
                "questions" => $questionsTags,
                "answers" => $answers,
                "user" => new MongoDB\BSON\ObjectId($id),
                "manager" => new MongoDB\BSON\ObjectId($manager),
                "test" => new MongoDB\BSON\ObjectId($test),
                "quizAssistance" => $assistanceID,
                "quizArbre" => $arbreID,
                "quizTransfert" => $transfertID,
                "quizBoite" => $boiteID,
                "quizBoiteAuto" => $boiteAutoID,
                "quizBoiteMan" => $boiteManID,
                "quizBoiteVc" => $boiteVcID,
                "quizClimatisation" => $climatisationID,
                "quizDemi" => $demiID,
                "quizDirection" => $directionID,
                "quizElectricite" => $electriciteID,
                "quizFrei" => $freiID,
                "quizFreinageElec" => $freinageElecID,
                "quizFreinage" => $freinageID,
                "quizFrein" => $freinID,
                "quizHydraulique" => $hydrauliqueID,
                "quizMoteurDiesel" => $moteurDieselID,
                "quizMoteurElec" => $moteurElecID,
                "quizMoteurEssence" => $moteurEssenceID,
                "quizMoteur" => $moteurID,
                "quizMultiplexage" => $multiplexageID,
                "quizPont" => $pontID,
                "quizPneumatique" => $pneumatiqueID,
                "quizReducteur" => $reducteurID,
                "quizSuspension" => $suspensionID,
                "quizSuspensionLame" => $suspensionLameID,
                "quizSuspensionRessort" => $suspensionRessortID,
                "quizSuspensionPneumatique" => $suspensionPneumatiqueID,
                "quizTransversale" => $transversaleID,
                "hour" => $hr,
                "minute" => $mn,
                "second" => $sc,
                "total" => count($to),
                "active" => true,
                "type" => "Manager",
                "created" => date("d-m-Y H:i:s"),
            ];

            $exams->insertOne($exam);
        }
    }

    if (isset($_POST["valid"])) {
        $time = $_POST["timer"];
        if (isset($exam)) {
            $questionsTag = $_POST["questionsTag"];
            $body = $_POST;
            // assuming POST method, you can replace it with $_GET if it's a GET method
            $proposals = array_values($body);
            $userAnswer = $exam->answers;
            $array = [];
            if (count($questionsTag) != $exam->total) {
                foreach ($userAnswer as $key => $answer) {
                    if ($answer == 'null') {
                        array_push($array, $answer);
                        $error_msg =
                            "Vous n'avez pas répondu à " .
                            count($array) .
                            " question(s), Veuillez vérifier la ou les question(s) dont valider est en vert svp.";
                    }
                }
                //var_dump($array);
                //var_dump($o);
                //var_dump($array);
                //var_dump($o);
                //var_dump($r++);
                //var_dump($o);
            } else {
                $scoreF = 0;
                $score = [];
                $scoreAss = [];
                $scoreAr = [];
                $scoreBoi = [];
                $scoreBoiA = [];
                $scoreBoiM = [];
                $scoreBoT = [];
                $scoreBoiV = [];
                $scoreClim = [];
                $scoreDe = [];
                $scoreDir = [];
                $scoreElec = [];
                $scoreMoD = [];
                $scoreMoEl = [];
                $scoreMoE = [];
                $scoreMoT = [];
                $scoreHyd = [];
                $scoreFrei = [];
                $scoreFreiE = [];
                $scoreFreiH = [];
                $scoreFreiP = [];
                $scoreMulti = [];
                $scorePont = [];
                $scorePneu = [];
                $scoreRe = [];
                $scoreSus = [];
                $scoreSusL = [];
                $scoreSusH = [];
                $scoreSusR = [];
                $scoreSusP = [];
                $scoreTran = [];
    
                $quizQuestion = [];
                $proposal = [];
                $proposalAssistance = [];
                $proposalArbre = [];
                $proposalTransfert = [];
                $proposalBoite = [];
                $proposalBoiteAuto = [];
                $proposalBoiteMan = [];
                $proposalBoiteVc = [];
                $proposalClimatisation = [];
                $proposalDemi = [];
                $proposalDirection = [];
                $proposalElectricite = [];
                $proposalFrei = [];
                $proposalFreinageElec = [];
                $proposalFreinage = [];
                $proposalFrein = [];
                $proposalHydraulique = [];
                $proposalMoteurDiesel = [];
                $proposalMoteurElec = [];
                $proposalMoteurEssence = [];
                $proposalMoteurThermique = [];
                $proposalMultiplexage = [];
                $proposalPont = [];
                $proposalPneu = [];
                $proposalReducteur = [];
                $proposalSuspensionLame = [];
                $proposalSuspension = [];
                $proposalSuspensionRessort = [];
                $proposalSuspensionPneumatique = [];
                $proposalTransversale = [];
    
                if (isset($_POST["quizAssistance"])) {
                    $assistanceID = $_POST["quizAssistance"];
                    $quizAssistance = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($assistanceID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Assistance à la Conduite"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Assistance à la Conduite-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreAss,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalAssistance, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalAssistance, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizAssistance->questions,
                                    "answers" => $proposalAssistance,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $assistanceID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreAss),
                                    "speciality" => $quizAssistance->speciality,
                                    "level" => $level,
                                    "type" => $quizAssistance->type,
                                    "typeR" => "Manager",
                                    "total" => $quizAssistance->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizAssistance->speciality],
                            ["type" => $quizAssistance->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizAssistance->questions,
                            "answers" => $proposalAssistance,
                            "quiz" => new MongoDB\BSON\ObjectId($assistanceID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreAss),
                            "speciality" => $quizAssistance->speciality,
                            "level" => $level,
                            "type" => $quizAssistance->type,
                            "typeR" => "Manager",
                            "total" => $quizAssistance->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizArbre"])) {
                    $arbreID = $_POST["quizArbre"];
                    $quizArbre = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($arbreID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Arbre de Transmission"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Arbre de Transmission-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreAr,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalArbre, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalArbre, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizArbre->questions,
                                    "answers" => $proposalArbre,
                                    "quiz" => new MongoDB\BSON\ObjectId($arbreID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreAr),
                                    "speciality" => $quizArbre->speciality,
                                    "level" => $level,
                                    "type" => $quizArbre->type,
                                    "typeR" => "Manager",
                                    "total" => $quizArbre->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizArbre->speciality],
                            ["type" => $quizArbre->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizArbre->questions,
                            "answers" => $proposalArbre,
                            "quiz" => new MongoDB\BSON\ObjectId($arbreID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreAr),
                            "speciality" => $quizArbre->speciality,
                            "level" => $level,
                            "type" => $quizArbre->type,
                            "typeR" => "Manager",
                            "total" => $quizArbre->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizTransfert"])) {
                    $transfertID = $_POST["quizTransfert"];
                    $quizTransfert = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($transfertID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality == "Boite de Transfert"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Boite de Transfert-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreBoT,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalTransfert, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalTransfert, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizTransfert->questions,
                                    "answers" => $proposalTransfert,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $transfertID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreBoT),
                                    "speciality" => $quizTransfert->speciality,
                                    "level" => $level,
                                    "type" => $quizTransfert->type,
                                    "typeR" => "Manager",
                                    "total" => $quizTransfert->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizTransfert->speciality],
                            ["type" => $quizTransfert->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizTransfert->questions,
                            "answers" => $proposalTransfert,
                            "quiz" => new MongoDB\BSON\ObjectId($transfertID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreBoT),
                            "speciality" => $quizTransfert->speciality,
                            "level" => $level,
                            "type" => $quizTransfert->type,
                            "typeR" => "Manager",
                            "total" => $quizTransfert->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizBoite"])) {
                    $boiteID = $_POST["quizBoite"];
                    $quizBoite = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($boiteID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Boite de Vitesse") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Boite de Vitesse-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreBoi,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalBoite, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalBoite, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizBoite->questions,
                                    "answers" => $proposalBoite,
                                    "quiz" => new MongoDB\BSON\ObjectId($boiteID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreBoi),
                                    "speciality" => $quizBoite->speciality,
                                    "level" => $level,
                                    "type" => $quizBoite->type,
                                    "typeR" => "Manager",
                                    "total" => $quizBoite->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizBoite->speciality],
                            ["type" => $quizBoite->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizBoite->questions,
                            "answers" => $proposalBoite,
                            "quiz" => new MongoDB\BSON\ObjectId($boiteID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreBoi),
                            "speciality" => $quizBoite->speciality,
                            "level" => $level,
                            "type" => $quizBoite->type,
                            "typeR" => "Manager",
                            "total" => $quizBoite->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizBoiteAuto"])) {
                    $boiteAutoID = $_POST["quizBoiteAuto"];
                    $quizBoiteAuto = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($boiteAutoID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Boite de Vitesse Automatique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Boite de Vitesse Automatique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreBoiA,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalBoiteAuto, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalBoiteAuto, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizBoiteAuto->questions,
                                    "answers" => $proposalBoiteAuto,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $boiteAutoID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreBoiA),
                                    "speciality" => $quizBoiteAuto->speciality,
                                    "level" => $level,
                                    "type" => $quizBoiteAuto->type,
                                    "typeR" => "Manager",
                                    "total" => $quizBoiteAuto->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizBoiteAuto->speciality],
                            ["type" => $quizBoiteAuto->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizBoiteAuto->questions,
                            "answers" => $proposalBoiteAuto,
                            "quiz" => new MongoDB\BSON\ObjectId($boiteAutoID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreBoiA),
                            "speciality" => $quizBoiteAuto->speciality,
                            "level" => $level,
                            "type" => $quizBoiteAuto->type,
                            "typeR" => "Manager",
                            "total" => $quizBoiteAuto->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizBoiteMan"])) {
                    $boiteManID = $_POST["quizBoiteMan"];
                    $quizBoiteMan = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($boiteManID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Boite de Vitesse Mécanique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Boite de Vitesse Mécanique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreBoiM,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalBoiteMan, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalBoiteMan, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizBoiteMan->questions,
                                    "answers" => $proposalBoiteMan,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $boiteManID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreBoiM),
                                    "speciality" => $quizBoiteMan->speciality,
                                    "level" => $level,
                                    "type" => $quizBoiteMan->type,
                                    "typeR" => "Manager",
                                    "total" => $quizBoiteMan->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizBoiteMan->speciality],
                            ["type" => $quizBoiteMan->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizBoiteMan->questions,
                            "answers" => $proposalBoiteMan,
                            "quiz" => new MongoDB\BSON\ObjectId($boiteManID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreBoiM),
                            "speciality" => $quizBoiteMan->speciality,
                            "level" => $level,
                            "type" => $quizBoiteMan->type,
                            "typeR" => "Manager",
                            "total" => $quizBoiteMan->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizBoiteVc"])) {
                    $boiteVcID = $_POST["quizBoiteVc"];
                    $quizBoiteVc = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($boiteVcID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Boite de Vitesse à Variation Continue"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Boite de Vitesse à Variation Continue-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreBoiV,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalBoiteVc, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalBoiteVc, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizBoiteVc->questions,
                                    "answers" => $proposalBoiteVc,
                                    "quiz" => new MongoDB\BSON\ObjectId($boiteVcID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreBoiV),
                                    "speciality" => $quizBoiteVc->speciality,
                                    "level" => $level,
                                    "type" => $quizBoiteVc->type,
                                    "typeR" => "Manager",
                                    "total" => $quizBoiteVc->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizBoiteVc->speciality],
                            ["type" => $quizBoiteVc->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizBoiteVc->questions,
                            "answers" => $proposalBoiteVc,
                            "quiz" => new MongoDB\BSON\ObjectId($boiteVcID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreBoiV),
                            "speciality" => $quizBoiteVc->speciality,
                            "level" => $level,
                            "type" => $quizBoiteVc->type,
                            "typeR" => "Manager",
                            "total" => $quizBoiteVc->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizClimatisation"])) {
                    $climatisationID = $_POST["quizClimatisation"];
                    $quizClimatisation = $quizzes->findOne([
                        '$and' => [
                            ["_id" => new MongoDB\BSON\ObjectId($climatisationID)],
                            ["active" => true],
                        ],
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Climatisation") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Climatisation-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreClim,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalClimatisation, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalClimatisation, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizClimatisation->questions,
                                    "answers" => $proposalClimatisation,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $climatisationID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreClim),
                                    "speciality" => $quizClimatisation->speciality,
                                    "level" => $level,
                                    "type" => $quizClimatisation->type,
                                    "typeR" => "Manager",
                                    "total" => $quizClimatisation->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizClimatisation->speciality],
                            ["type" => $quizClimatisation->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizClimatisation->questions,
                            "answers" => $proposalClimatisation,
                            "quiz" => new MongoDB\BSON\ObjectId($climatisationID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreClim),
                            "speciality" => $quizClimatisation->speciality,
                            "level" => $level,
                            "type" => $quizClimatisation->type,
                            "typeR" => "Manager",
                            "total" => $quizClimatisation->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizDemi"])) {
                    $demiID = $_POST["quizDemi"];
                    $quizDemi = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($demiID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality == "Demi Arbre de Roue"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Demi Arbre de Roue-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreDe,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalDemi, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalDemi, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizDemi->questions,
                                    "answers" => $proposalDemi,
                                    "quiz" => new MongoDB\BSON\ObjectId($demiID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreDe),
                                    "speciality" => $quizDemi->speciality,
                                    "level" => $level,
                                    "type" => $quizDemi->type,
                                    "typeR" => "Manager",
                                    "total" => $quizDemi->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizDemi->speciality],
                            ["type" => $quizDemi->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizDemi->questions,
                            "answers" => $proposalDemi,
                            "quiz" => new MongoDB\BSON\ObjectId($demiID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreDe),
                            "speciality" => $quizDemi->speciality,
                            "level" => $level,
                            "type" => $quizDemi->type,
                            "typeR" => "Manager",
                            "total" => $quizDemi->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizDirection"])) {
                    $directionID = $_POST["quizDirection"];
                    $quizDirection = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($directionID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Direction") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Direction-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreDir,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalDirection, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalDirection, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizDirection->questions,
                                    "answers" => $proposalDirection,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $directionID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreDir),
                                    "speciality" => $quizDirection->speciality,
                                    "level" => $level,
                                    "type" => $quizDirection->type,
                                    "typeR" => "Manager",
                                    "total" => $quizDirection->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizDirection->speciality],
                            ["type" => $quizDirection->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizDirection->questions,
                            "answers" => $proposalDirection,
                            "quiz" => new MongoDB\BSON\ObjectId($directionID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreDir),
                            "speciality" => $quizDirection->speciality,
                            "level" => $level,
                            "type" => $quizDirection->type,
                            "typeR" => "Manager",
                            "total" => $quizDirection->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizElectricite"])) {
                    $electriciteID = $_POST["quizElectricite"];
                    $quizElectricite = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($electriciteID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Electricité et Electronique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Electricité et Electronique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreElec,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalElectricite, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalElectricite, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizElectricite->questions,
                                    "answers" => $proposalElectricite,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $electriciteID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreElec),
                                    "speciality" => $quizElectricite->speciality,
                                    "level" => $level,
                                    "type" => $quizElectricite->type,
                                    "typeR" => "Manager",
                                    "total" => $quizElectricite->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizElectricite->speciality],
                            ["type" => $quizElectricite->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizElectricite->questions,
                            "answers" => $proposalElectricite,
                            "quiz" => new MongoDB\BSON\ObjectId($electriciteID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreElec),
                            "speciality" => $quizElectricite->speciality,
                            "level" => $level,
                            "type" => $quizElectricite->type,
                            "typeR" => "Manager",
                            "total" => $quizElectricite->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizFrei"])) {
                    $freiID = $_POST["quizFrei"];
                    $quizFrei = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($freiID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Freinage") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Freinage-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreFrei,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalFrei, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalFrei, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizFrei->questions,
                                    "answers" => $proposalFrei,
                                    "quiz" => new MongoDB\BSON\ObjectId($freiID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreFrei),
                                    "speciality" => $quizFrei->speciality,
                                    "level" => $level,
                                    "type" => $quizFrei->type,
                                    "typeR" => "Manager",
                                    "total" => $quizFrei->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizFrei->speciality],
                            ["type" => $quizFrei->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizFrei->questions,
                            "answers" => $proposalFrei,
                            "quiz" => new MongoDB\BSON\ObjectId($freiID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreFrei),
                            "speciality" => $quizFrei->speciality,
                            "level" => $level,
                            "type" => $quizFrei->type,
                            "typeR" => "Manager",
                            "total" => $quizFrei->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizFreinageElec"])) {
                    $freinageElecID = $_POST["quizFreinageElec"];
                    $quizFreinageElec = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($freinageElecID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Freinage Electromagnétique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Freinage Electromagnétique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreFreiE,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalFreinageElec, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalFreinageElec, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizFreinageElec->questions,
                                    "answers" => $proposalFreinageElec,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $freinageElecID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreFreiE),
                                    "speciality" => $quizFreinageElec->speciality,
                                    "level" => $level,
                                    "type" => $quizFreinageElec->type,
                                    "typeR" => "Manager",
                                    "total" => $quizFreinageElec->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizFreinageElec->speciality],
                            ["type" => $quizFreinageElec->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizFreinageElec->questions,
                            "answers" => $proposalFreinageElec,
                            "quiz" => new MongoDB\BSON\ObjectId($freinageElecID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreFreiE),
                            "speciality" => $quizFreinageElec->speciality,
                            "level" => $level,
                            "type" => $quizFreinageElec->type,
                            "typeR" => "Manager",
                            "total" => $quizFreinageElec->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizFreinage"])) {
                    $freinageID = $_POST["quizFreinage"];
                    $quizFreinage = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($freinageID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality == "Freinage Hydraulique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Freinage Hydraulique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreFreiH,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalFreinage, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalFreinage, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizFreinage->questions,
                                    "answers" => $proposalFreinage,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $freinageID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreFreiH),
                                    "speciality" => $quizFreinage->speciality,
                                    "level" => $level,
                                    "type" => $quizFreinage->type,
                                    "typeR" => "Manager",
                                    "total" => $quizFreinage->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizFreinage->speciality],
                            ["type" => $quizFreinage->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizFreinage->questions,
                            "answers" => $proposalFreinage,
                            "quiz" => new MongoDB\BSON\ObjectId($freinageID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreFreiH),
                            "speciality" => $quizFreinage->speciality,
                            "level" => $level,
                            "type" => $quizFreinage->type,
                            "typeR" => "Manager",
                            "total" => $quizFreinage->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizFrein"])) {
                    $freinID = $_POST["quizFrein"];
                    $quizFrein = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($freinID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality == "Freinage Pneumatique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Freinage Pneumatique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreFreiP,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalFrein, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalFrein, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizFrein->questions,
                                    "answers" => $proposalFrein,
                                    "quiz" => new MongoDB\BSON\ObjectId($freinID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreFreiP),
                                    "speciality" => $quizFrein->speciality,
                                    "level" => $level,
                                    "type" => $quizFrein->type,
                                    "typeR" => "Manager",
                                    "total" => $quizFrein->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizFrein->speciality],
                            ["type" => $quizFrein->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizFrein->questions,
                            "answers" => $proposalFrein,
                            "quiz" => new MongoDB\BSON\ObjectId($freinID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreFreiP),
                            "speciality" => $quizFrein->speciality,
                            "level" => $level,
                            "type" => $quizFrein->type,
                            "typeR" => "Manager",
                            "total" => $quizFrein->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizHydraulique"])) {
                    $hydrauliqueID = $_POST["quizHydraulique"];
                    $quizHydraulique = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($hydrauliqueID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Hydraulique") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Hydraulique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreHyd,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalHydraulique, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalHydraulique, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizHydraulique->questions,
                                    "answers" => $proposalHydraulique,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $hydrauliqueID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreHyd),
                                    "speciality" => $quizHydraulique->speciality,
                                    "level" => $level,
                                    "type" => $quizHydraulique->type,
                                    "typeR" => "Manager",
                                    "total" => $quizHydraulique->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizHydraulique->speciality],
                            ["type" => $quizHydraulique->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizHydraulique->questions,
                            "answers" => $proposalHydraulique,
                            "quiz" => new MongoDB\BSON\ObjectId($hydrauliqueID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreHyd),
                            "speciality" => $quizHydraulique->speciality,
                            "level" => $level,
                            "type" => $quizHydraulique->type,
                            "typeR" => "Manager",
                            "total" => $quizHydraulique->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizMoteurDiesel"])) {
                    $moteurDieselID = $_POST["quizMoteurDiesel"];
                    $quizMoteurDiesel = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($moteurDieselID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Moteur Diesel") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Moteur Diesel-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreMoD,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalMoteurDiesel, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalMoteurDiesel, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizMoteurDiesel->questions,
                                    "answers" => $proposalMoteurDiesel,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $moteurDieselID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreMoD),
                                    "speciality" => $quizMoteurDiesel->speciality,
                                    "level" => $level,
                                    "type" => $quizMoteurDiesel->type,
                                    "typeR" => "Manager",
                                    "total" => $quizMoteurDiesel->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizMoteurDiesel->speciality],
                            ["type" => $quizMoteurDiesel->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizMoteurDiesel->questions,
                            "answers" => $proposalMoteurDiesel,
                            "quiz" => new MongoDB\BSON\ObjectId($moteurDieselID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreMoD),
                            "speciality" => $quizMoteurDiesel->speciality,
                            "level" => $level,
                            "type" => $quizMoteurDiesel->type,
                            "typeR" => "Manager",
                            "total" => $quizMoteurDiesel->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizMoteurElec"])) {
                    $moteurElecID = $_POST["quizMoteurElec"];
                    $quizMoteurElec = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($moteurElecID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Moteur Electrique") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Moteur Electrique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreMoEl,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalMoteurElec, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalMoteurElec, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizMoteurElec->questions,
                                    "answers" => $proposalMoteurElec,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $moteurElecID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreMoEl),
                                    "speciality" => $quizMoteurElec->speciality,
                                    "level" => $level,
                                    "type" => $quizMoteurElec->type,
                                    "typeR" => "Manager",
                                    "total" => $quizMoteurElec->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizMoteurElec->speciality],
                            ["type" => $quizMoteurElec->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizMoteurElec->questions,
                            "answers" => $proposalMoteurElec,
                            "quiz" => new MongoDB\BSON\ObjectId($moteurElecID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreMoEl),
                            "speciality" => $quizMoteurElec->speciality,
                            "level" => $level,
                            "type" => $quizMoteurElec->type,
                            "typeR" => "Manager",
                            "total" => $quizMoteurElec->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizMoteurEssence"])) {
                    $moteurEssenceID = $_POST["quizMoteurEssence"];
                    $quizMoteurEssence = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($moteurEssenceID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Moteur Essence") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Moteur Essence-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreMoE,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalMoteurEssence, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalMoteurEssence, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizMoteurEssence->questions,
                                    "answers" => $proposalMoteurEssence,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $moteurEssenceID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreMoE),
                                    "speciality" => $quizMoteurEssence->speciality,
                                    "level" => $level,
                                    "type" => $quizMoteurEssence->type,
                                    "typeR" => "Manager",
                                    "total" => $quizMoteurEssence->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizMoteurEssence->speciality],
                            ["type" => $quizMoteurEssence->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizMoteurEssence->questions,
                            "answers" => $proposalMoteurEssence,
                            "quiz" => new MongoDB\BSON\ObjectId($moteurEssenceID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreMoE),
                            "speciality" => $quizMoteurEssence->speciality,
                            "level" => $level,
                            "type" => $quizMoteurEssence->type,
                            "typeR" => "Manager",
                            "total" => $quizMoteurEssence->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizMoteur"])) {
                    $moteurThermiqueID = $_POST["quizMoteur"];
                    $quizMoteurThermique = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($moteurThermiqueID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Moteur Thermique") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Moteur Thermique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreMoT,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalMoteurThermique, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalMoteurThermique, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizMoteurThermique->questions,
                                    "answers" => $proposalMoteurThermique,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $moteurThermiqueID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreMoT),
                                    "speciality" =>
                                        $quizMoteurThermique->speciality,
                                    "level" => $level,
                                    "type" => $quizMoteurThermique->type,
                                    "typeR" => "Manager",
                                    "total" => $quizMoteurThermique->total,
                                    "numberTest" => 1,
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizMoteurThermique->speciality],
                            ["type" => $quizMoteurThermique->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizMoteurThermique->questions,
                            "answers" => $proposalMoteurThermique,
                            "quiz" => new MongoDB\BSON\ObjectId($moteurThermiqueID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreMoT),
                            "speciality" => $quizMoteurThermique->speciality,
                            "level" => $level,
                            "type" => $quizMoteurThermique->type,
                            "typeR" => "Manager",
                            "total" => $quizMoteurThermique->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    }
                }
                if (isset($_POST["quizMultiplexage"])) {
                    $multiplexageID = $_POST["quizMultiplexage"];
                    $quizMultiplexage = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($multiplexageID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Réseaux de Communication") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Réseaux de Communication-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreMulti,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalMultiplexage, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalMultiplexage, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizMultiplexage->questions,
                                    "answers" => $proposalMultiplexage,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $multiplexageID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreMulti),
                                    "speciality" => $quizMultiplexage->speciality,
                                    "level" => $level,
                                    "type" => $quizMultiplexage->type,
                                    "typeR" => "Manager",
                                    "total" => $quizMultiplexage->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizMultiplexage->speciality],
                            ["type" => $quizMultiplexage->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizMultiplexage->questions,
                            "answers" => $proposalMultiplexage,
                            "quiz" => new MongoDB\BSON\ObjectId($multiplexageID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreMulti),
                            "speciality" => $quizMultiplexage->speciality,
                            "level" => $level,
                            "type" => $quizMultiplexage->type,
                            "typeR" => "Manager",
                            "total" => $quizMultiplexage->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizPont"])) {
                    $pontID = $_POST["quizPont"];
                    $quizPont = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($pontID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Pont") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Pont-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scorePont,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalPont, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalPont, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizPont->questions,
                                    "answers" => $proposalPont,
                                    "quiz" => new MongoDB\BSON\ObjectId($pontID),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scorePont),
                                    "speciality" => $quizPont->speciality,
                                    "level" => $level,
                                    "type" => $quizPont->type,
                                    "typeR" => "Manager",
                                    "total" => $quizPont->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizPont->speciality],
                            ["type" => $quizPont->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizPont->questions,
                            "answers" => $proposalPont,
                            "quiz" => new MongoDB\BSON\ObjectId($pontID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scorePont),
                            "speciality" => $quizPont->speciality,
                            "level" => $level,
                            "type" => $quizPont->type,
                            "typeR" => "Manager",
                            "total" => $quizPont->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizPneumatique"])) {
                    $pneumatiqueID = $_POST["quizPneumatique"];
                    $quizPneumatique = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($pneumatiqueID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Pneumatique") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Pneumatique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scorePneu,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalPneu, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalPneu, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizPneumatique->questions,
                                    "answers" => $proposalPneu,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $pneumatiqueID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scorePneu),
                                    "speciality" => $quizPneumatique->speciality,
                                    "level" => $level,
                                    "type" => $quizPneumatique->type,
                                    "typeR" => "Manager",
                                    "total" => $quizPneumatique->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizPneumatique->speciality],
                            ["type" => $quizPneumatique->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizPneumatique->questions,
                            "answers" => $proposalPneu,
                            "quiz" => new MongoDB\BSON\ObjectId($pneumatiqueID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scorePneu),
                            "speciality" => $quizPneumatique->speciality,
                            "level" => $level,
                            "type" => $quizPneumatique->type,
                            "typeR" => "Manager",
                            "total" => $quizPneumatique->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizReducteur"])) {
                    $reducteurID = $_POST["quizReducteur"];
                    $quizReducteur = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($reducteurID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Reducteur") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Reducteur-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreRe,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalReducteur, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalReducteur, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizReducteur->questions,
                                    "answers" => $proposalReducteur,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $reducteurID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreRe),
                                    "speciality" => $quizReducteur->speciality,
                                    "level" => $level,
                                    "type" => $quizReducteur->type,
                                    "typeR" => "Manager",
                                    "total" => $quizReducteur->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizReducteur->speciality],
                            ["type" => $quizReducteur->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizReducteur->questions,
                            "answers" => $proposalReducteur,
                            "quiz" => new MongoDB\BSON\ObjectId($reducteurID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreRe),
                            "speciality" => $quizReducteur->speciality,
                            "level" => $level,
                            "type" => $quizReducteur->type,
                            "typeR" => "Manager",
                            "total" => $quizReducteur->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizSuspension"])) {
                    $suspensionID = $_POST["quizSuspension"];
                    $quizSuspension = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($suspensionID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Suspension") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Suspension à -" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreSus,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalSuspension, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalSuspension, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizSuspension->questions,
                                    "answers" => $proposalSuspension,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $suspensionID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreSus),
                                    "speciality" => $quizSuspension->speciality,
                                    "level" => $level,
                                    "type" => $quizSuspension->type,
                                    "typeR" => "Manager",
                                    "total" => $quizSuspension->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizSuspension->speciality],
                            ["type" => $quizSuspension->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizSuspension->questions,
                            "answers" => $proposalSuspension,
                            "quiz" => new MongoDB\BSON\ObjectId($suspensionID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreSus),
                            "speciality" => $quizSuspension->speciality,
                            "level" => $level,
                            "type" => $quizSuspension->type,
                            "typeR" => "Manager",
                            "total" => $quizSuspension->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizSuspensionLame"])) {
                    $suspensionLameID = $_POST["quizSuspensionLame"];
                    $quizSuspensionLame = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($suspensionLameID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Suspension à Lame") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Suspension à Lame-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreSusL,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalSuspensionLame, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalSuspensionLame, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizSuspensionLame->questions,
                                    "answers" => $proposalSuspensionLame,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $suspensionLameID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreSusL),
                                    "speciality" => $quizSuspensionLame->speciality,
                                    "level" => $level,
                                    "type" => $quizSuspensionLame->type,
                                    "typeR" => "Manager",
                                    "total" => $quizSuspensionLame->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizSuspensionLame->speciality],
                            ["type" => $quizSuspensionLame->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizSuspensionLame->questions,
                            "answers" => $proposalSuspensionLame,
                            "quiz" => new MongoDB\BSON\ObjectId($suspensionLameID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreSusL),
                            "speciality" => $quizSuspensionLame->speciality,
                            "level" => $level,
                            "type" => $quizSuspensionLame->type,
                            "typeR" => "Manager",
                            "total" => $quizSuspensionLame->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizSuspensionRessort"])) {
                    $suspensionRessortID = $_POST["quizSuspensionRessort"];
                    $quizSuspensionRessort = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($suspensionRessortID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality == "Suspension Ressort"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Suspension Ressort-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreSusR,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalSuspensionRessort, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalSuspensionRessort, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" =>
                                        $quizSuspensionRessort->questions,
                                    "answers" => $proposalSuspensionRessort,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $suspensionRessortID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreSusR),
                                    "speciality" =>
                                        $quizSuspensionRessort->speciality,
                                    "level" => $level,
                                    "type" => $quizSuspensionRessort->type,
                                    "typeR" => "Manager",
                                    "total" => $quizSuspensionRessort->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizSuspensionRessort->speciality],
                            ["type" => $quizSuspensionRessort->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizSuspensionRessort->questions,
                            "answers" => $proposalSuspensionRessort,
                            "quiz" => new MongoDB\BSON\ObjectId($suspensionRessortID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreSusR),
                            "speciality" => $quizSuspensionRessort->speciality,
                            "level" => $level,
                            "type" => $quizSuspensionRessort->type,
                            "typeR" => "Manager",
                            "total" => $quizSuspensionRessort->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizSuspensionPneumatique"])) {
                    $suspensionPneumatiqueID = $_POST["quizSuspensionPneumatique"];
                    $quizSuspensionPneumatique = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId(
                            $suspensionPneumatiqueID
                        ),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if (
                                $questionsData->speciality ==
                                "Suspension Pneumatique"
                            ) {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Suspension Pneumatique-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreSusP,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push(
                                        $proposalSuspensionPneumatique,
                                        "Oui"
                                    );
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push(
                                        $proposalSuspensionPneumatique,
                                        "Non"
                                    );
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" =>
                                        $quizSuspensionPneumatique->questions,
                                    "answers" => $proposalSuspensionPneumatique,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $suspensionPneumatiqueID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreSusP),
                                    "speciality" =>
                                        $quizSuspensionPneumatique->speciality,
                                    "level" => $level,
                                    "type" => $quizSuspensionPneumatique->type,
                                    "typeR" => "Manager",
                                    "total" => $quizSuspensionPneumatique->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizSuspensionPneumatique->speciality],
                            ["type" => $quizSuspensionPneumatique->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizSuspensionPneumatique->questions,
                            "answers" => $proposalSuspensionPneumatique,
                            "quiz" => new MongoDB\BSON\ObjectId($suspensionPneumatiqueID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreSusP),
                            "speciality" => $quizSuspensionPneumatique->speciality,
                            "level" => $level,
                            "type" => $quizSuspensionPneumatique->type,
                            "typeR" => "Manager",
                            "total" => $quizSuspensionPneumatique->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                if (isset($_POST["quizTransversale"])) {
                    $transversaleID = $_POST["quizTransversale"];
                    $quizTransversale = $quizzes->findOne([
                        "_id" => new MongoDB\BSON\ObjectId($transversaleID),
                    ]);
                    for ($i = 0; $i < count($userAnswer); $i++) {
                        $questionsData = $questions->findOne([
                            '$or' => [
                                ["proposal1" => $userAnswer[$i]],
                                ["proposal2" => $userAnswer[$i]],
                                ["proposal3" => $userAnswer[$i]],
                            ],
                        ]);
    
                        if ($questionsData != null) {
                            if ($questionsData->speciality == "Transversale") {
                                if (
                                    $userAnswer[$i] ==
                                    "1-Transversale-" .
                                        $questionsData->level .
                                        "-" .
                                        $questionsData->label .
                                        "-1"
                                ) {
                                    array_push(
                                        $scoreTran,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposalTransversale, "Oui");
                                    array_push(
                                        $score,
                                        "Il maitrise (le technicien réalise cette tâche professionnelle)"
                                    );
                                    array_push($proposal, "Oui");
                                } else {
                                    array_push($proposalTransversale, "Non");
                                    array_push($proposal, "Non");
                                }
    
                                array_push($quizQuestion, $questionsData->_id);
                                $result = [
                                    "questions" => $quizTransversale->questions,
                                    "answers" => $proposalTransversale,
                                    "quiz" => new MongoDB\BSON\ObjectId(
                                        $transversaleID
                                    ),
                                    "user" => new MongoDB\BSON\ObjectId($id),
                                    "manager" => new MongoDB\BSON\ObjectId(
                                        $manager
                                    ),
                                    "score" => count($scoreTran),
                                    "speciality" => $quizTransversale->speciality,
                                    "level" => $quizTransversale->level,
                                    "type" => $quizTransversale->type,
                                    "typeR" => "Manager",
                                    "total" => $quizTransversale->total,
                                    "numberTest" => 1, 
                                    "time" => $time,
                                    "active" => true,
                                    "created" => date("d-m-Y H:i:s"),
                                ];
                            }
                        }
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["speciality" => $quizTransversale->speciality],
                            ["type" => $quizTransversale->type],
                            ["typeR" => "Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $result1 = [
                            "questions" => $quizTransversale->questions,
                            "answers" => $proposalTransversale,
                            "quiz" => new MongoDB\BSON\ObjectId($transversaleID),
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "score" => count($scoreTran),
                            "speciality" => $quizTransversale->speciality,
                            "level" => $level,
                            "type" => $quizTransversale->type,
                            "typeR" => "Manager",
                            "total" => $quizTransversale->total,
                            "numberTest" => count($exist) + 1,
                            "time" => $time,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insertedResult = $results->insertOne($result1);
                    } else {
                        $insertedResult = $results->insertOne($result);
                    } 
                }
                $managerAnswer = [];
                for ($i = 0; $i < count($userAnswer); ++$i) {
                    $data = $questions->findOne([
                        '$or' => [
                            ["proposal1" => $userAnswer[$i]],
                            ["proposal2" => $userAnswer[$i]],
                            ["proposal3" => $userAnswer[$i]],
                        ],
                        "type" => "Declarative",
                    ]);
                    if ($data) {
                        array_push($managerAnswer, $userAnswer[$i]);
                    }
                }
                $exist = $results->find([
                '$and' => [
                    ["user" => new MongoDB\BSON\ObjectId($id)],
                    ["manager" => new MongoDB\BSON\ObjectId($manager)],
                    ["test" => new MongoDB\BSON\ObjectId($test)],
                    ["typeR" => "Managers"],
                    ["level" => $level],
                ],
                ])->toArray();
                if ($exist) {
                    $newResult1 = [
                        "questions" => $quizQuestion,
                        "answers" => $proposal,
                        "managerAnswers" => $managerAnswer,
                        "user" => new MongoDB\BSON\ObjectId($id),
                        "test" => new MongoDB\BSON\ObjectId($test),
                        "manager" => new MongoDB\BSON\ObjectId($manager),
                        "score" => count($score),
                        "level" => $level,
                        "type" => "Declaratif",
                        "subsidiary" => $user['subsidiary'],
                        "typeR" => "Managers",
                        "total" => count($quizQuestion),
                        "numberTest" => count($exist) + 1,
                        "time" => $time,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $resultM = $results->insertOne($newResult1);
                } else {
                    $newResult = [
                        "questions" => $quizQuestion,
                        "answers" => $proposal,
                        "managerAnswers" => $managerAnswer,
                        "user" => new MongoDB\BSON\ObjectId($id),
                        "test" => new MongoDB\BSON\ObjectId($test),
                        "manager" => new MongoDB\BSON\ObjectId($manager),
                        "score" => count($score),
                        "level" => $level,
                        "type" => "Declaratif",
                        "subsidiary" => $user['subsidiary'],
                        "typeR" => "Managers",
                        "total" => count($quizQuestion),
                        "numberTest" => 1,
                        "time" => $time,
                        "active" => true,
                        "created" => date("d-m-Y H:i:s"),
                    ];
                    $resultM = $results->insertOne($newResult);
                } 
    
                $manaResult = $results->findOne([
                    '$and' => [
                        ["_id" => new MongoDB\BSON\ObjectId($resultM->getInsertedId())],
                        ["manager" => new MongoDB\BSON\ObjectId($manager)],
                    ],
                ]);
    
                $allocationData = $allocations->findOne([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($id)],
                        ["test" => new MongoDB\BSON\ObjectId($test)],
                    ],
                ]);
    
                $examData = $exams->findOne([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($id)],
                        ["test" => new MongoDB\BSON\ObjectId($test)],
                        ["active" => true],
                    ],
                ]);
    
                $allocationData->activeManager = true;
                $allocations->updateOne(
                    ["_id" => $allocationData->_id],
                    ['$set' => $allocationData]
                );
    
                $examData->active = false;
                $exams->updateOne(["_id" => $examData->_id], ['$set' => $examData]);
    
                $technicienResult = $results->findOne([
                    '$and' => [
                        ["user" => new MongoDB\BSON\ObjectId($id)],
                        ["typeR" => "Techniciens"],
                        ["numberTest" => $manaResult->numberTest],
                        ["level" => $level],
                    ],
                ]);
    
                if ($technicienResult) {
                    for ($i = 0; $i < count($proposal); $i++) {
                        if (
                            $proposal[$i] == "Oui" &&
                            $technicienResult->answers[$i] == "Oui"
                        ) {
                            $scoreF += 1;
                        } else {
                            $scoreF += 0;
                        }
                        $newResult = [
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "test" => new MongoDB\BSON\ObjectId($test),
                            "score" => $scoreF,
                            "level" => $level,
                            "type" => "Declaratif",
                            "subsidiary" => $user['subsidiary'],
                            "typeR" => "Technicien - Manager",
                            "total" => count($quizQuestion),
                            "numberTest" => 1,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                    }
                    $exist = $results->find([
                        '$and' => [
                            ["user" => new MongoDB\BSON\ObjectId($id)],
                            ["manager" => new MongoDB\BSON\ObjectId($manager)],
                            ["test" => new MongoDB\BSON\ObjectId($test)],
                            ["typeR" => "Technicien - Manager"],
                            ["level" => $level],
                        ],
                    ])->toArray();
                    if ($exist) {
                        $newResult1 = [
                            "user" => new MongoDB\BSON\ObjectId($id),
                            "manager" => new MongoDB\BSON\ObjectId($manager),
                            "test" => new MongoDB\BSON\ObjectId($test),
                            "score" => $scoreF,
                            "level" => $level,
                            "type" => "Declaratif",
                            "subsidiary" => $user['subsidiary'],
                            "typeR" => "Technicien - Manager",
                            "total" => count($quizQuestion),
                            "numberTest" => $manaResult->numberTest,
                            "active" => true,
                            "created" => date("d-m-Y H:i:s"),
                        ];
                        $insert = $results->insertOne($newResult1);
                    } else {
                        $insert = $results->insertOne($newResult);
                    } 
                }
                
                // Assurez-vous que les variables sont définies et valides
                if (isset($userManager['lastName'], $user['firstName'], $user['lastName'], $level)) {
                    // Échapper les données pour éviter les problèmes de sécurité
                    $managerLastName = htmlspecialchars($userManager['lastName']);
                    $technicianFirstName = htmlspecialchars($user['firstName']);
                    $technicianLastName = htmlspecialchars($user['lastName']);
                    $levelEscaped = htmlspecialchars($level);

                    // Créer le message
                    $message = '<p>Bonjour M. ' . $managerLastName . ',</p>
                                <p>Vous avez complété avec succès le <strong>QCM Tâche Professionnelle Manager niveau ' . $levelEscaped . '</strong> 
                                de votre collaborateur <strong>' . $technicianFirstName . ' ' . $technicianLastName . '</strong>, 
                                il est désormais possible pour l\'administrateur de votre filiale de voir son résultat dans l\'espace <strong>Résultats des Techniciens par niveau</strong>.</p>
                                <p style="margin-top: 50px; font-size: 20px; font-weight: 100px">Cordialement | Best Regards | よろしくお願いしま。</p>';

                    // Sujet de l'e-mail
                    $subject = 'Confirmation de réception du QCM Tâche Professionnelle Manager niveau ' . $levelEscaped . ' de ' . $technicianFirstName . ' ' . $technicianLastName;

                    // Envoyer l'e-mail
                    $sendMail = sendMailQCMDone($userManager['email'], $subject, $message);
                }
                header("Location: ./congrat");
            }
        } else {
            header("Location: ./congrat");
        }
    }
    ?>
<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $evaluation_de ?> <?php echo $user->firstName; ?> <?php echo $user->lastName; ?> | CFAO MobIlity Academy</title>
<!--end::Title-->

<link href="https://fonts.googleapis.com/css2?famIly=Montserrat:wght@300;400;500&display=swap" rel="stylesheet" />
<link href="../../public/css/userQuiz.css" rel="stylesheet" type="text/css" />
<link href="../../public/css/userQuiz.css" rel="stylesheet" type="text/css" />

<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content"
    data-select2-id="select2-data-kt_content">
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
        <!--begin::Container-->
        <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
            <div class="container">
    <form class="quiz-form" method="POST">
            <center class="center" style="margin-top: -100px;">
                <div class="timer" style="margin-right: 400px;">
                    <div class="time_left_txt"><?php echo $left_questions ?></div>
                    <div class="timer_sec" id="num" value="1">
                    </div>
                </div>
                <div class="timer" style="margin-top: -45px; margin-left: 400px">
                <div class="time_left_txt"><?php echo $duree ?></div>
                <div class="timer_sec" id="timer_sec"></div>
                </div>
            </center>
            <div class="heading" style="margin-top: 10px;">
              <h3 class="heading__text"><?php echo $evaluer_tache_pro ?> <br> M. <?php echo $user->firstName; ?> <?php echo $user->lastName; ?> <br> <?php echo $Level; ?> <?php echo $level; ?></h3>
            </div>
            
            <?php if (isset($error_msg)) { ?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <center><strong><?php echo $error_msg; ?></strong></center>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;
                    </span>
                </button>
            </div>
            <?php } ?>
                        
            <!-- Quiz section -->
            <div class="quiz" style="margin-bottom: 40px;">
                <p class="list-unstyled text-gray-600 fw-semibold fs-6 p-0 m-0">
                    <?php echo $evaluation_text ?>
                </p>
            <input class="hidden" type="text" name="timer" id="clock" />
            <input class="hidden" type="text" name="hr" id="hr" />
            <input class="hidden" type="text" name="mn" id="mn" />
            <input class="hidden" type="text" name="sc" id="sc" />
            <div class="quiz-form__quiz">
                            <?php if (!isset($exam)) { ?>
                <?php
                $k = 1;
                for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $assistanceDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Assistance à la Conduite"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($assistanceDecla) {
                        $arrQuestions = $assistanceDecla["questions"]; ?>
                    <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $assistanceConduite ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizAssistance" value="<?php echo $assistanceDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerAssistance<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerAssistance<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerAssistance<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerAssistance<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                }
                ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $arbreDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Arbre de Transmission"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($arbreDecla) {
                        $arrQuestions = $arbreDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $arbre ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizArbre" value="<?php echo $arbreDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerArbre<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerArbre<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerArbre<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerArbre<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $transfertDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Boite de Transfert"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($transfertDecla) {
                        $arrQuestions = $transfertDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $transfert ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizTransfert" value="<?php echo $transfertDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransfert<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransfert<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransfert<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerTransfert<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $boiteDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Boite de Vitesse"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($boiteDecla) {
                        $arrQuestions = $boiteDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizBoite" value="<?php echo $boiteDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerBoite<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); ++$j) {

                    $boiteAutoDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Boite de Vitesse Automatique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($boiteAutoDecla) {
                        $arrQuestions = $boiteAutoDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_auto ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); ++$i) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizBoiteAuto" value="<?php echo $boiteAutoDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteAuto<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteAuto<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteAuto<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerBoiteAuto<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); ++$j) {

                    $boiteManDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Boite de Vitesse Mécanique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($boiteManDecla) {
                        $arrQuestions = $boiteManDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_meca ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); ++$i) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizBoiteMan" value="<?php echo $boiteManDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteMan<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteMan<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerboiteMan<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerBoiteMan<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $boiteVcDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            [
                                "speciality" =>
                                    "Boite de Vitesse à Variation Continue",
                            ],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($boiteVcDecla) {
                        $arrQuestions = $boiteVcDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_VC ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizBoiteVc" value="<?php echo $boiteVcDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoiteVc<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoiteVc<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerBoiteVc<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerBoiteVc<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $climatisationDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Climatisation"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($climatisationDecla) {
                        $arrQuestions = $climatisationDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $clim ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizClimatisation"
                    value="<?php echo $climatisationDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerClimatisation<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerClimatisation<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerClimatisation<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerClimatisation<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $demiDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Demi Arbre de Roue"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($demiDecla) {
                        $arrQuestions = $demiDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $demi ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizDemi" value="<?php echo $demiDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDemi<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDemi<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDemi<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerDemi<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $directionDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Direction"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($directionDecla) {
                        $arrQuestions = $directionDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $direction ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizDirection" value="<?php echo $directionDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDirection<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDirection<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerDirection<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerDirection<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $electriciteDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Electricité et Electronique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($electriciteDecla) {
                        $arrQuestions = $electriciteDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $electricite ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizElectricite" value="<?php echo $electriciteDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerElectricite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerElectricite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerElectricite<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerElectricite<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $freiDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Freinage"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($freiDecla) {
                        $arrQuestions = $freiDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinage ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizFrei" value="<?php echo $freiDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrei<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrei<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrei<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerFrei<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $freinageElecDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Freinage Electromagnétique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($freinageElecDecla) {
                        $arrQuestions = $freinageElecDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinageElec ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizFreinageElec" value="<?php echo $freinageElecDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinageElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinageElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinageElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerFreinageElec<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $freinageDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Freinage Hydraulique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($freinageDecla) {
                        $arrQuestions = $freinageDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinageHydro ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizFreinage" value="<?php echo $freinageDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFreinage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerFreinage<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $freinDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Freinage Pneumatique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($freinDecla) {
                        $arrQuestions = $freinDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinagePneu ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizFrein" value="<?php echo $freinDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrein<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrein<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerFrein<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerFrein<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $hydrauliqueDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Hydraulique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($hydrauliqueDecla) {
                        $arrQuestions = $hydrauliqueDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $hydraulique ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizHydraulique" value="<?php echo $hydrauliqueDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerHydraulique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerHydraulique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerHydraulique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerHydraulique<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $moteurDieselDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Moteur Diesel"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($moteurDieselDecla) {
                        $arrQuestions = $moteurDieselDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurDiesel ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizMoteurDiesel"
                    value="<?php echo $moteurDieselDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurDiesel<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurDiesel<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurDiesel<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerMoteurDiesel<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $moteurElecDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Moteur Electrique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($moteurElecDecla) {
                        $arrQuestions = $moteurElecDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurElectrique ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizMoteurElec" value="<?php echo $moteurElecDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurElec<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerMoteurElec<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $moteurEssenceDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Moteur Essence"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($moteurEssenceDecla) {
                        $arrQuestions = $moteurEssenceDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurEssence ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizMoteurEssence"
                    value="<?php echo $moteurEssenceDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurEssence<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurEssence<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurEssence<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerMoteurEssence<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $moteurThermiqueDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Moteur Thermique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($moteurThermiqueDecla) {
                        $arrQuestions = $moteurThermiqueDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurThermique ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizMoteur"
                    value="<?php echo $moteurThermiqueDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurThermique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurThermique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMoteurThermique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerMoteurThermique<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $multiplexageDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Réseaux de Communication"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($multiplexageDecla) {
                        $arrQuestions = $multiplexageDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $multiplexage ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizMultiplexage"
                    value="<?php echo $multiplexageDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMultiplexage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMultiplexage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerMultiplexage<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerMultiplexage<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $pontDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Pont"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($pontDecla) {
                        $arrQuestions = $pontDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $pont ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizPont" value="<?php echo $pontDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPont<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPont<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPont<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerPont<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $pneumatiqueDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Pneumatique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($pneumatiqueDecla) {
                        $arrQuestions = $pneumatiqueDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $pneu ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizPneumatique" value="<?php echo $pneumatiqueDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPneu<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPneu<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerPneu<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerPneu<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $reducteurDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Reducteur"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($reducteurDecla) {
                        $arrQuestions = $reducteurDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $reducteur ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizReducteur" value="<?php echo $reducteurDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerRed<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerRed<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerRed<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerRed<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $suspensionDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            [
                                "speciality" => "Suspension",
                            ],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($suspensionDecla) {
                        $arrQuestions = $suspensionDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspension ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizSuspension"
                    value="<?php echo $suspensionDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspension<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspension<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspension<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerSuspension<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $suspensionLameDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Suspension à Lame"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($suspensionLameDecla) {
                        $arrQuestions = $suspensionLameDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionLame ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizSuspensionLame"
                    value="<?php echo $suspensionLameDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionLame<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionLame<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionLame<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerSuspensionLame<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $suspensionRessortDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Suspension Ressort"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($suspensionRessortDecla) {
                        $arrQuestions = $suspensionRessortDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionRessort ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizSuspensionRessort"
                    value="<?php echo $suspensionRessortDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionRessort<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionRessort<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionRessort<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerSuspensionRessort<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $suspensionPneumatiqueDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Suspension Pneumatique"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($suspensionPneumatiqueDecla) {
                        $arrQuestions =
                            $suspensionPneumatiqueDecla["questions"]; ?>
                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionPneu ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizSuspensionPneumatique"
                    value="<?php echo $suspensionPneumatiqueDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionPneumatique<?php echo $i + 1; ?>"
                        value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionPneumatique<?php echo $i + 1; ?>"
                        value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerSuspensionPneumatique<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerSuspensionPneumatique<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <?php for ($j = 0; $j < count($arrQuizzes); $j++) {

                    $transversaleDecla = $quizzes->findOne([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuizzes[$j]
                                ),
                            ],
                            ["speciality" => "Transversale"],
                            ["type" => "Declaratif"],
                            ["level" => $level],
                            ["active" => true],
                        ],
                    ]);
                    if ($transversaleDecla) {
                        $arrQuestions = $transversaleDecla["questions"]; ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $transversale ?></h1> -->
                <?php for ($i = 0; $i < count($arrQuestions); $i++) {
                    $question = $questions->findone([
                        '$and' => [
                            [
                                "_id" => new MongoDB\BSON\ObjectId(
                                    $arrQuestions[$i]
                                ),
                            ],
                            ["active" => true],
                        ],
                    ]); ?>
                <input class="hidden" type="text" name="quizTransversale"
                    value="<?php echo $transversaleDecla->_id; ?>" />
                <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $question->_id; ?>" />
                <?php if ($level == "Expert") { ?>
                    <p class="fw-bold"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->title; ?>
                    </p>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } else { ?>
                    <p class="quiz-form__question fw-bold" id="question"
                        style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                        <?php echo $k++; ?> - <?php echo $question->label; ?> (<?php echo $question->ref; ?>)
                    </p>
                <?php } ?>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransversale<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal1; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_maitrise ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransversale<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal2; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_ne_maitrise_pas ?>
                    </span>
                </label>
                <label class="quiz-form__ans">
                    <input type="radio" id="proposal<?php echo $i +
                        1; ?>" onclick="checkedRadio()"
                        name="answerTransversale<?php echo $i +
                            1; ?>" value="<?php echo $question->proposal3; ?>" />
                    <span class="design"></span>
                    <span class="text">
                        <?php echo $il_na_jamais_fait ?>
                    </span>
                </label>
                <label class="quiz-form__ans" hidden>
                    <input type="radio" onclick="checkedRadio()"
                        name="answerTransversale<?php echo $i + 1; ?>"
                        value="null" checked/>
                    <span class="design"></span>
                    <span class="text">
                    </span>
                </label>
                <div>
                    <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                </div>
                <?php
                } ?>
                <?php
                    }
                    ?>
                <?php
                } ?>
                <div style="margin-top: 70px; align-items: center; justify-content: space-evenly; display: flex;">
                    <!-- <button type="submit" class="btn btn-secondary btn-lg" name="back">Retour</button> -->
                    <button type="submit" id="button" class="btn btn-primary btn-lg" name="valid"><?php echo $next ?></button>
                </div>
              </div>
        </form>
    </div>
</div>
</div>
<!--end::Container-->
</div>
<!--end::Post-->
</div>
<!--end::Body-->
                            <?php } elseif (isset($exam)) { ?>
                                <php if ($assistanceDecla) { ?>
                                <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $assistanceConduite ?></h1> -->
                                <php } ?>
                        <?php
                        $questionExam = $exam["questions"];
                        $k = 1; ?>
                        <?php for ($j = 0; $j < count($questionExam); ++$j) {
                            $assistanceFac = $questions->findOne([
                                '$and' => [
                                    [
                                        "_id" => new MongoDB\BSON\ObjectId(
                                            $questionExam[$j]
                                        ),
                                    ],
                                    [
                                        "speciality" =>
                                            "Assistance à la Conduite",
                                    ],
                                    ["type" => "Declarative"],
                                    ["level" => $level],
                                    ["active" => true],
                                ],
                            ]);
                            if ($assistanceFac) { ?>
                                                    <input class="hidden" type="text" name="quizAssistance"
                                                        value="<?php echo $exam[
                                                            "quizAssistance"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $assistanceFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $assistanceFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $assistanceFac->label; ?> (<?php echo $assistanceFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $assistanceFac->label; ?> (<?php echo $assistanceFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $assistanceFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                        <?php if (
                                            $exam["answers"][$j] ==
                                            $assistanceFac["proposal1"]
                                        ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                            $exam["answers"][$j] ==
                                            $assistanceFac["proposal2"]
                                        ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                            $exam["answers"][$j] ==
                                            $assistanceFac["proposal3"]
                                        ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                             <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $assistanceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerAssistance<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php }}
                        }
                        ?>
                        <php if ($arbreDecla) { ?>
                        <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $arbre ?></h1> -->
                        <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $arbreFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Arbre de Transmission",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($arbreFac) { ?>
                                                    <input class="hidden" type="text" name="quizArbre" value="<?php echo $exam[
                                                        "quizArbre"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $arbreFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $arbreFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $arbreFac->label; ?> (<?php echo $arbreFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $arbreFac->label; ?> (<?php echo $arbreFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $arbreFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $arbreFac["proposal1"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $arbreFac["proposal2"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $arbreFac["proposal3"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                             <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>" value="<?php echo $arbreFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerArbre<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($transfertDecla) { ?>
                                                    <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $transfert ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $transfertFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Boite de Transfert",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($transfertFac) { ?>
                                                    <input class="hidden" type="text" name="quizTransfert"
                                                        value="<?php echo $exam[
                                                            "quizTransfert"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $transfertFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $transfertFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $transfertFac->label; ?> (<?php echo $transfertFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $transfertFac->label; ?> (<?php echo $transfertFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $transfertFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $transfertFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $transfertFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $transfertFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>" value="<?php echo $transfertFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>" value="<?php echo $transfertFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>" value="<?php echo $transfertFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transfertFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransfert<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($boiteDecla) { ?>
                                                    <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $boiteFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Boite de Vitesse",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($boiteFac) { ?>
                                                    <input class="hidden" type="text" name="quizBoite" value="<?php echo $exam[
                                                        "quizBoite"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $boiteFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteFac->label; ?> (<?php echo $boiteFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $boiteFac->label; ?> (<?php echo $boiteFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $boiteFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $boiteFac["proposal1"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteFac["proposal2"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                             <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoite<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($boiteAutoDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_auto ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $boiteAutoFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Boite de Vitesse Automatique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($boiteAutoFac) { ?>
                                                    <input class="hidden" type="text" name="quizBoiteAuto" value="<?php echo $exam[
                                                        "quizBoiteAuto"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $boiteAutoFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteAutoFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteAutoFac->label; ?> (<?php echo $boiteAutoFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $boiteAutoFac->label; ?> (<?php echo $boiteAutoFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $boiteAutoFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $boiteAutoFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteAutoFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteAutoFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteAutoFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteAuto<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($boiteManDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_meca ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $boiteManFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Boite de Vitesse Mécanique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($boiteManFac) { ?>
                                                    <input class="hidden" type="text" name="quizBoiteMan" value="<?php echo $exam[
                                                        "quizBoiteMan"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $boiteManFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteManFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteManFac->label; ?> (<?php echo $boiteManFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $boiteManFac->label; ?> (<?php echo $boiteManFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $boiteManFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $boiteManFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteManFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteManFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteManFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteMan<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($boiteVcDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $boite_vitesse_VC ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $boiteVcFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Boite de Vitesse à Variation Continue",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($boiteVcFac) { ?>
                                                    <input class="hidden" type="text" name="quizBoiteVc" value="<?php echo $exam[
                                                        "quizBoiteVc"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $boiteVcFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteVcFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $boiteVcFac->label; ?> (<?php echo $boiteVcFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $boiteVcFac->label; ?> (<?php echo $boiteVcFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $boiteVcFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $boiteVcFac["proposal1"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteVcFac["proposal2"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $boiteVcFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>" value="<?php echo $boiteVcFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerBoiteVc<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($climatisationDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $clim ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $climatisationFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Climatisation",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $climatisationFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizClimatisation"
                                                        value="<?php echo $exam[
                                                            "quizClimatisation"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $climatisationFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $climatisationFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $climatisationFac->label; ?> (<?php echo $climatisationFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $climatisationFac->label; ?> (<?php echo $climatisationFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $climatisationFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $climatisationFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $climatisationFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $climatisationFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $climatisationFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerClimatisation<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($demiDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $demi ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $demiFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Demi Arbre de Roue",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($demiFac) { ?>
                                                    <input class="hidden" type="text" name="quizDemi"
                                                        value="<?php echo $exam[
                                                            "quizDemi"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $demiFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $demiFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $demiFac->label; ?> (<?php echo $demiFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $demiFac->label; ?> (<?php echo $demiFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $demiFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $demiFac["proposal1"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $demiFac["proposal2"]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $demiFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $demiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDemi<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($directionDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $direction ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $directionFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Direction",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($directionFac) { ?>
                                                    <input class="hidden" type="text" name="quizDirection"
                                                        value="<?php echo $exam[
                                                            "quizDirection"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $directionFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $directionFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $directionFac->label; ?> (<?php echo $directionFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $directionFac->label; ?> (<?php echo $directionFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $directionFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $directionFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $directionFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $directionFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $directionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerDirection<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($electriciteDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $electricite ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $electriciteFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Electricité et Electronique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $electriciteFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizElectricite"
                                                        value="<?php echo $exam[
                                                            "quizElectricite"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $electriciteFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $electriciteFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $electriciteFac->label; ?> (<?php echo $electriciteFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $electriciteFac->label; ?> (<?php echo $electriciteFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $electriciteFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $electriciteFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $electriciteFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?> 
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $electriciteFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $electriciteFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerElectricite<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($freiDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinage ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $freiFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Freinage",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($freiFac) { ?>
                                                    <input class="hidden" type="text" name="quizFrei"
                                                        value="<?php echo $exam[
                                                            "quizFrei"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $freiFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freiFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freiFac->label; ?> (<?php echo $freiFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $freiFac->label; ?> (<?php echo $freiFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $freiFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $freiFac["proposal1"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freiFac["proposal2"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freiFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freiFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrei<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($freinageElecDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinageElec ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $freinageElecFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Freinage Electromagnétique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $freinageElecFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizFreinageElec"
                                                        value="<?php echo $exam[
                                                            "quizFreinageElec"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $freinageElecFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinageElecFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinageElecFac->label; ?> (<?php echo $freinageElecFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $freinageElecFac->label; ?> (<?php echo $freinageElecFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $freinageElecFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $freinageElecFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinageElecFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinageElecFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerfreinageElec<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $freinageFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Freinage Hydraulique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($freinageFac) { ?>
                                                    <input class="hidden" type="text" name="quizFreinage"
                                                        value="<?php echo $exam[
                                                            "quizFreinage"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $freinageFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinageFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinageFac->label; ?> (<?php echo $freinageFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $freinageFac->label; ?> (<?php echo $freinageFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $freinageFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $freinageFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinageFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal2; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinageFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                           <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $freinageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFreinage<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($freinDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $freinagePneu ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $freinFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Freinage Pneumatique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($freinFac) { ?>
                                                    <input class="hidden" type="text" name="quizFrein" value="<?php echo $exam[
                                                        "quizFrein"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $freinFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $freinFac->label; ?> (<?php echo $freinFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $freinFac->label; ?> (<?php echo $freinFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $freinFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $freinFac["proposal1"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal1; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinFac["proposal2"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $freinFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>" value="<?php echo $freinFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerFrein<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($hydrauliqueDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $hydraulique ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $hydrauliqueFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Hydraulique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $hydrauliqueFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizHydraulique"
                                                        value="<?php echo $exam[
                                                            "quizHydraulique"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $hydrauliqueFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $hydrauliqueFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $hydrauliqueFac->label; ?> (<?php echo $hydrauliqueFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $hydrauliqueFac->label; ?> (<?php echo $hydrauliqueFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $hydrauliqueFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $hydrauliqueFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $hydrauliqueFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $hydrauliqueFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $hydrauliqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerHydraulique<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($moteurDieselDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurDiesel ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $moteurDieselFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Moteur Diesel",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $moteurDieselFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizMoteurDiesel"
                                                        value="<?php echo $exam[
                                                            "quizMoteurDiesel"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $moteurDieselFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurDieselFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurDieselFac->label; ?> (<?php echo $moteurDieselFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $moteurDieselFac->label; ?> (<?php echo $moteurDieselFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $moteurDieselFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $moteurDieselFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurDieselFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurDieselFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurDieselFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurDiesel<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($moteurElecDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurElectrique ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $moteurElecFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Moteur Electrique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($moteurElecFac) { ?>
                                                    <input class="hidden" type="text" name="quizMoteurElec"
                                                        value="<?php echo $exam[
                                                            "quizMoteurElec"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $moteurElecFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurElecFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurElecFac->label; ?> (<?php echo $moteurElecFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $moteurElecFac->label; ?> (<?php echo $moteurElecFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $moteurElecFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $moteurElecFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurElecFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurElecFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurElecFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurElec<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($moteurEssenceDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurEssence ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $moteurEssenceFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Moteur Essence",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $moteurEssenceFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizMoteurEssence"
                                                        value="<?php echo $exam[
                                                            "quizMoteurEssence"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $moteurEssenceFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurEssenceFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurEssenceFac->label; ?> (<?php echo $moteurEssenceFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $moteurEssenceFac->label; ?> (<?php echo $moteurEssenceFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $moteurEssenceFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $moteurEssenceFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurEssenceFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurEssenceFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurEssenceFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteurEssence<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($moteurDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $moteurThermique ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $moteurFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Moteur Thermique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($moteurFac) { ?>
                                                    <input class="hidden" type="text" name="quizMoteur"
                                                        value="<?php echo $exam[
                                                            "quizMoteur"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $moteurFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $moteurFac->label; ?> (<?php echo $moteurFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $moteurFac->label; ?> (<?php echo $moteurFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $question->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $moteurFac["proposal1"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurFac["proposal2"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $moteurFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $moteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMoteur<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($multiplexageDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $multiplexage ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $multiplexageFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Réseaux de Communication",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $multiplexageFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizMultiplexage"
                                                        value="<?php echo $exam[
                                                            "quizMultiplexage"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $multiplexageFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $multiplexageFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $multiplexageFac->label; ?> (<?php echo $multiplexageFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $multiplexageFac->label; ?> (<?php echo $multiplexageFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $multiplexageFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $multiplexageFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $multiplexageFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $multiplexageFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $multiplexageFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerMultiplexage<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($pontDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $pont ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $pontFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Pont",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($pontFac) { ?>
                                                    <input class="hidden" type="text" name="quizPont" value="<?php echo $exam[
                                                        "quizPont"
                                                    ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $pontFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $pontFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $pontFac->label; ?> (<?php echo $pontFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $pontFac->label; ?> (<?php echo $pontFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $pontFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $pontFac["proposal1"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $pontFac["proposal2"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $pontFac["proposal3"]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal3; ?>" checked />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>" value="<?php echo $pontFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPont<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($pneumatiqueDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $pneu ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $pneumatiqueFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Pneumatique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $pneumatiqueFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizPneumatique"
                                                        value="<?php echo $exam[
                                                            "quizPneumatique"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $pneumatiqueFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $pneumatiqueFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $pneumatiqueFac->label; ?> (<?php echo $pneumatiqueFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $pneumatiqueFac->label; ?> (<?php echo $pneumatiqueFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $pneumatiqueFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $pneumatiqueFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $pneumatiqueFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                           <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $pneumatiqueFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>" value="<?php echo $pneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerPneu<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($reducteurDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $reducteur ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $reducteurFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Reducteur",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($reducteurFac) { ?>
                                                    <input class="hidden" type="text" name="quizReducteur"
                                                        value="<?php echo $exam[
                                                            "quizReducteur"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $reducteurFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $reducteurFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $reducteurFac->label; ?> (<?php echo $reducteurFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $reducteurFac->label; ?> (<?php echo $reducteurFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $reducteurFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $reducteurFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $reducteurFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $reducteurFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $reducteurFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerReducteur<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($suspensionDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspension ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $suspensionFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Suspension",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if ($suspensionFac) { ?>
                                                    <input class="hidden" type="text" name="quizSuspension"
                                                        value="<?php echo $exam[
                                                            "quizSuspension"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $suspensionFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionFac->label; ?> (<?php echo $suspensionFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $suspensionFac->label; ?> (<?php echo $suspensionFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $suspensionFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $suspensionFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspension<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($suspensionLameDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionLame ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $suspensionLameFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Suspension à Lame",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $suspensionLameFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizSuspensionLame"
                                                        value="<?php echo $exam[
                                                            "quizSuspensionLame"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $suspensionLameFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionLameFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionLameFac->label; ?> (<?php echo $suspensionLameFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $suspensionLameFac->label; ?> (<?php echo $suspensionLameFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $suspensionLameFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $suspensionLameFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionLameFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionLameFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionLameFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionLame<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($suspensionRessortDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionRessort ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $suspensionRessortFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Suspension Ressort",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $suspensionRessortFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizSuspensionRessort"
                                                        value="<?php echo $exam[
                                                            "quizSuspensionRessort"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $suspensionRessortFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionRessortFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionRessortFac->label; ?> (<?php echo $suspensionRessortFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $suspensionRessortFac->label; ?> (<?php echo $suspensionRessortFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $suspensionRessortFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $suspensionRessortFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionRessortFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionRessortFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionRessortFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionRessort<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($suspensionPneumatiqueDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $suspensionPneu ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $suspensionPneumatiqueFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Suspension Pneumatique",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $suspensionPneumatiqueFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizSuspensionPneumatique"
                                                        value="<?php echo $exam[
                                                            "quizSuspensionPneumatique"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $suspensionPneumatiqueFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionPneumatiqueFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $suspensionPneumatiqueFac->label; ?> (<?php echo $suspensionPneumatiqueFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $suspensionPneumatiqueFac->label; ?> (<?php echo $suspensionPneumatiqueFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $suspensionPneumatiqueFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $suspensionPneumatiqueFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionPneumatiqueFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $suspensionPneumatiqueFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $suspensionPneumatiqueFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerSuspensionPneumatique<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } ?>
                                                    <?php }
                                                        ?>
                                                    <?php
                                                    } ?>
                                                    <php if ($transversaleDecla) { ?>
                                                            <!-- <h1 class="fw-bold"  style="margin-top: 30px"><?php echo $groupe_fonctionnel ?> <?php echo $transversale ?></h1> -->
                                                    <php } ?>
                                                    <?php for (
                                                        $j = 0;
                                                        $j <
                                                        count($questionExam);
                                                        ++$j
                                                    ) {

                                                        $transversaleFac = $questions->findOne(
                                                            [
                                                                '$and' => [
                                                                    [
                                                                        "_id" => new MongoDB\BSON\ObjectId(
                                                                            $questionExam[
                                                                                $j
                                                                            ]
                                                                        ),
                                                                    ],
                                                                    [
                                                                        "speciality" =>
                                                                            "Transversale",
                                                                    ],
                                                                    [
                                                                        "type" =>
                                                                            "Declarative",
                                                                    ],
                                                                    [
                                                                        "level" => $level,
                                                                    ],
                                                                    [
                                                                        "active" => true,
                                                                    ],
                                                                ],
                                                            ]
                                                        );
                                                        if (
                                                            $transversaleFac
                                                        ) { ?>
                                                    <input class="hidden" type="text" name="quizTransversale"
                                                        value="<?php echo $exam[
                                                            "quizTransversale"
                                                        ]; ?>" />
                                                    <input class="hidden" type="text" name="questionsTag[]" value="<?php echo $transversaleFac->_id; ?>" />
                                                    <?php if ($level == "Expert") { ?>
                                                        <p class="fw-bold"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $transversaleFac->title; ?>
                                                        </p>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $transversaleFac->label; ?> (<?php echo $transversaleFac->ref; ?>)
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="quiz-form__question fw-bold" id="question"
                                                            style="margin-top: 50px; font-size: large; margin-bottom: 20px;">
                                                            <?php echo $k++; ?> - <?php echo $transversaleFac->label; ?> (<?php echo $transversaleFac->ref; ?>)
                                                        </p>
                                                    <?php } ?>
                                                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                                                        <img id="image" alt="" src="../../public/files/<?php echo $transversaleFac->image ??
                                                            ""; ?>"> <br>
                                                    </div>
                                                    <?php if (
                                                        $exam["answers"][$j] ==
                                                        $transversaleFac[
                                                            "proposal1"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal1; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $transversaleFac[
                                                            "proposal2"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal2; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } elseif (
                                                        $exam["answers"][$j] ==
                                                        $transversaleFac[
                                                            "proposal3"
                                                        ]
                                                    ) { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal3; ?>" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-secondary btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>
                                                    <?php } else { ?>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal1; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_maitrise ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal2; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_ne_maitrise_pas ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans">
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="<?php echo $transversaleFac->proposal3; ?>" />
                                                        <span class="design"></span>
                                                        <span class="text">
                                                            <?php echo $il_na_jamais_fait ?>
                                                        </span>
                                                    </label>
                                                    <label class="quiz-form__ans" hidden>
                                                        <input type="radio" onclick="checkedRadio()"
                                                            name="answerTransversale<?php echo $j +
                                                                1; ?>"
                                                            value="null" checked/>
                                                        <span class="design"></span>
                                                        <span class="text">
                                                        </span>
                                                    </label>
                                                    <div >
                                                        <button type="submit" class="btn btn-success btn-lg" name="save"><?php echo $valider ?></button>
                                                    </div>   
                            <?php } ?>
                            <?php }
                                                        ?>
                            <?php
                                                    } ?>
                                                    <div style="margin-top: 70px; align-items: center; justify-content: space-evenly; display: flex;">
                                                        <!-- <button type="submit" class="btn btn-secondary btn-lg" name="back">Retour</button> -->
                                                        <button type="submit" id="button" class="btn btn-primary btn-lg" name="valid"><?php echo $next ?></button>
                                                    </div>
                                                 </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Container-->
                            </div>
                            <!--end::Post-->
                        </div>
                        <!--end::Body-->
                            <?php } ?>
<script>
let hr = <?php echo $exam["hour"] ?? "01"; ?>;
let mn = <?php echo $exam["minute"] ?? "00"; ?>;
let sc = <?php echo $exam["second"] ?? "00"; ?> ; 
// const startingMinutes = document
//     .getElementById("timer_sec")
//     .getAttribute("value");
let time = Number(hr) * 3600 + Number(mn) *60 + Number(sc);

const countDown = document.getElementById("timer_sec");

setInterval(updateCountDown, 1000);

function updateCountDown() {
    let hour = Math.floor(time / 3600);
    let minutes = Math.floor((time / 60) - (hour * 60));
    let seconds = time - ((hour * 3600) + (minutes * 60));
    time--;
    if (time > 0) {
        hour = hour < 10 ? "0" + hour : hour;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        countDown.innerHTML = `${hour}:${minutes}:${seconds}`;
        document.getElementById("clock").value = `${hour}:${minutes}:${seconds}`;
        document.getElementById("hr").value = `${hour}`;
        document.getElementById("mn").value = `${minutes}`;
        document.getElementById("sc").value = `${seconds}`;
    } else if (time < 0) {
        clearInterval(updateCountDown);
        hour = "00";
        minutes = "00";
        seconds = "00";
        countDown.innerHTML = `${hour}:${minutes}:${seconds}`;
        document.getElementById("clock").value = `${hour}:${minutes}:${seconds}`;
        document.getElementById("hr").value = `${hour}`;
        document.getElementById("mn").value = `${minutes}`;
        document.getElementById("sc").value = `${seconds}`;
        // document.getElementById(".submit").addEventListener("click")
    }
}

// var timer = setInterval(countTimer, 1000);
// var totalSecond = 0;

// function countTimer() {
//     totalSecond++;

//     var hour = Math.floor(totalSecond / 3600);
//     var minutes = Math.floor((totalSecond - hour * 3600) / 60);
//     var seconds = totalSecond - (hour * 3600 + minutes * 60);

//     if (minutes <= 9 && hour > 9) {
//         document.getElementById("timer_sec").innerHTML = hour + ":" + "0" + minutes;
//         document.getElementById("clock").value = hour + ":" + "0" + minutes + ":" + seconds;
//     }
//     if (hour <= 9 && minutes > 9) {
//         document.getElementById("timer_sec").innerHTML = "0" + hour + ":" + minutes;
//         document.getElementById("clock").value = "0" + hour + ":" + minutes + ":" + seconds;
//     }
//     if (hour <= 9 && minutes <= 9) {
//         document.getElementById("timer_sec").innerHTML = "0" + hour + ":" + "0" + minutes;
//         document.getElementById("clock").value = "0" + hour + ":" + "0" + minutes + ":" + seconds;
//     }
//     if (hour == 9 && minutes == 9) {
//         document.getElementById("timer_sec").innerHTML = "0" + hour + ":" + "0" + minutes;
//         document.getElementById("clock").value = "0" + hour + ":" + "0" + minutes + ":" + seconds;
//     }
//     Et
// }

let radio;
const ques = document.querySelectorAll("#question");
const submitBtn = document.querySelector("#button")
// submitBtn.classList.add("disabled")
const num = document.querySelector("#num").getAttribute('value');
const score = document.querySelector("#num");
const cal = (num * ques.length) - <?php echo $exam["total"] ?? 0; ?>;
score.innerHTML = `${cal}`;
// score.innerHTML = `100`;
// if (ques.length == <?php echo $exam["total"] ?? 0; ?>) {
//     submitBtn.classList.remove("disabled");
// }

// function checkedRadio() {
//     const radios = document.querySelectorAll("input[type='radio']:checked");
//     radios.forEach(async (rad, i) => {
//         radio = i + 1;
//     })
//     if (ques.length == radio) {
//         submitBtn.classList.remove("disabled");
//     }
//     const cal = (num * ques.length) - radio;
//     score.innerHTML = `${cal}`;
// }

$(window).scroll(function() {
  sessionStorage.scrollTop = $(this).scrollTop();
});

$(document).ready(function() {
  if (sessionStorage.scrollTop != "undefined") {
    $(window).scrollTop(sessionStorage.scrollTop);
  }
});
</script>
<?php include_once "partials/footer.php"; ?>
<?php
} ?>
<?php 
// Output buffering to prevent incomplete or malformed output
ob_start(); 

// Include the chatbox HTML
include_once 
'chat.php'; 

// Flush the buffer to ensure complete output
ob_end_flush(); 
?>