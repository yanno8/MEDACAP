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
    $tests = $academy->tests;
    $vehicles = $academy->vehicles;
    $allocations = $academy->allocations;

    $id = $_SESSION["id"];

    $user = $users->findOne([
        '$and' => [
            ["_id" => new MongoDB\BSON\ObjectId($id)],
            ["active" => true],
        ],
    ]);

if (isset($_POST["update"])) {
  $id = $_POST["userID"];
  $firstName = $_POST["firstName"];
  $lastName = $_POST["lastName"];
  $email = $_POST["email"];
  $phone = $_POST["phone"];
  $matriculation = $_POST["matricule"];
  $userName = $_POST["username"];
  $subsidiary = $_POST["subsidiary"];
  $fonction = $_POST["role"];
  $sex = $_POST["gender"];
  $pays = $_POST["country"];
  $certificate = $_POST["certificate"];
  $birthDate = date("d-m-Y", strtotime($_POST["birthdate"]));
  $recrutementDate = date("d-m-Y", strtotime($_POST["recrutmentDate"]));
  $person = [
      "username" => $userName,
      "matricule" => $matriculation,
      "firstName" => ucfirst($firstName),
      "lastName" => strtoupper($lastName),
      "email" => $email,
      "phone" => $phone,
      "gender" => $sex,
      "level" => $level,
      "country" => $pays,
      "birthdate" => $birthDate,
      "recrutmentDate" => $recrutementDate,
      "certificate" => ucfirst($certificate),
      "subsidiary" => ucfirst($subsidiary),
      "role" => ucfirst($fonction),
      "updated" => date("d-m-Y H:I:S"),
  ];
  $users->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      ['$set' => $person]
  );
  $success_msg = $success_user_edit;
}

if (isset($_POST["specialitySenior"])) {
$id = $_POST["userID"];
$specialitySenior = $_POST["specialitySenior"];

$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  ['$set' => ['specialitySenior' => $specialitySenior] ]
);
$success_msg = $success_user_edit;
}

if (isset($_POST["specialityExpert"])) {
$id = $_POST["userID"];
$specialityExpert = $_POST["specialityExpert"];

$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  ['$set' => ['specialityExpert' => $specialityExpert] ]
);
$success_msg = $success_user_edit;
}

if (isset($_POST["department"])) {
$id = $_POST["userID"];
$department = $_POST["department"];

$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  ['$set' => ['department' => $department] ]
);
$success_msg = $success_user_edit;
}

if (isset($_POST["manager"])) {
$id = $_POST["userID"];
$manager = $_POST["manager"];

$users->updateOne(
    ["_id" => new MongoDB\BSON\ObjectId($id)],
    ['$set' => ['manager' => new MongoDB\BSON\ObjectId($manager)] ]
);
$user = $users->findOne([
    '$and' => [
        ["users" => new MongoDB\BSON\ObjectId($id)],
        ["active" => true],
    ],
]);
$users->updateOne(
    ["_id" => new MongoDB\BSON\ObjectId($user['_id'])],
    ['$pull' => ['users' => new MongoDB\BSON\ObjectId($id)] ]
);
$users->updateOne(
    ["_id" => new MongoDB\BSON\ObjectId($manager)],
    ['$push' => ['users' => new MongoDB\BSON\ObjectId($id)] ]
);
$success_msg = $success_user_edit;
}

if (isset($_POST["brandJu"])) {
$id = $_POST["userID"];

$brandJu = $_POST["brandJu"];
$resultJu = $results->find([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
if (isset($resultJu)) {
  foreach ($resultJu as $result) {
      $results->updateOne(
          ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
          [
              '$set' => [
                  "active" => false
              ],
          ]
      );
  }
}
$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  [
      '$set' => [
          "brandJunior" => $brandJu,
      ],
  ]
);
$testFac = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$testDecla = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($testFac["_id"])],
  [
      '$set' => [
          "quizzes" => [],
          "brand" => $brandJu,
      ],
  ]
);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($testDecla["_id"])],
  [
      '$set' => [
          "quizzes" => [],
          "brand" => $brandJu,
      ],
  ]
);
for ($n = 0; $n < count($brandJu); ++$n) {
  $vehicleFac = $vehicles->findOne([
      '$and' => [
          ["brand" => $brandJu[$n]],
          ["type" => "Factuel"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  $vehicleDecla = $vehicles->findOne([
      '$and' => [
          ["brand" => $brandJu[$n]],
          ["type" => "Declaratif"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  if ($vehicleFac) {
      for (
          $a = 0;
          $a < count($vehicleFac->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testFac["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleFac->quizzes[$a],
                  ],
              ]
          );
      }
  }
  if ($vehicleDecla) {
      for (
          $a = 0;
          $a < count($vehicleDecla->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testDecla["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleDecla->quizzes[$a],
                  ],
              ]
          );
      }
  }
}
$saveTestFac = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testFac["_id"]
  ),
]);
$saveTestDecla = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testDecla["_id"]
  ),
]);

$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestFac["_id"])],
  [
      '$set' => [
          "total" => count($saveTestFac["quizzes"])
      ],
  ]
);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestDecla["_id"])],
  [
      '$set' => [
          "total" => count($saveTestDecla["quizzes"])
      ],
  ]
);
$success_msg = $success_user_edit;
}

if (isset($_POST["brandSe"])) {
$id = $_POST["userID"];

$brandSe = $_POST["brandSe"];
$resultJu = $results->find([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$resultSe = $results->find([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
      ["level" => "Senior"],
      ["active" => true],
  ],
]);
if (isset($resultJu)) {
  foreach ($resultJu as $result) {
      $results->updateOne(
          ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
          [
              '$set' => [
                  "active" => false
              ],
          ]
      );
  }
}
if (isset($resultSe)) {
  foreach ($resultSe as $result) {
      $results->updateOne(
          ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
          [
              '$set' => [
                  "active" => false
              ],
          ]
      );
  }
}
$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  [
      '$set' => [
          "brandSenior" => $brandSe,
      ],
  ]
);
foreach ($brandSe as $bran) {
  $users->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      [
          '$addToSet' => [
              "brandJunior" => $bran,
          ],
      ]
  );
}
$user = $users->findOne([
  '$and' => [
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      ["active" => true],
  ],
]);
$testFac = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$testDecla = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$testFacSe = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Senior"],
      ["active" => true],
  ],
]);
$testDeclaSe = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Senior"],
      ["active" => true],
  ],
]);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($testFac["_id"])],
  [
      '$set' => [
          "quizzes" => [],
          "brand" => $user['brandJunior'],
      ],
  ]
);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($testDecla["_id"])],
  [
      '$set' => [
          "quizzes" => [],
          "brand" => $user['brandJunior'],
      ],
  ]
);

for ($n = 0; $n < count($user['brandJunior']); ++$n) {
  $vehicleFac = $vehicles->findOne([
      '$and' => [
          ["brand" => $user['brandJunior'][$n]],
          ["type" => "Factuel"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  $vehicleDecla = $vehicles->findOne([
      '$and' => [
          ["brand" => $user['brandJunior'][$n]],
          ["type" => "Declaratif"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  if ($vehicleFac) {
      for (
          $a = 0;
          $a < count($vehicleFac->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testFac["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleFac->quizzes[$a],
                  ],
              ]
          );
      }
  }
  if ($vehicleDecla) {
      for (
          $a = 0;
          $a < count($vehicleDecla->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testDecla["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleDecla->quizzes[$a],
                  ],
              ]
          );
      }
  }
}
$saveTestFacJu = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testFac["_id"]
  ),
]);
$saveTestDeclaJu = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testDecla["_id"]
  ),
]);

$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestFacJu["_id"])],
  [
      '$set' => [
          "total" => count($saveTestFacJu["quizzes"])
      ],
  ]
);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestDeclaJu["_id"])],
  [
      '$set' => [
          "total" => count($saveTestDeclaJu["quizzes"])
      ],
  ]
);

if ($testFacSe) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testFacSe["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $brandSe,
          ],
      ]
  );
  for ($n = 0; $n < count($brandSe); ++$n) {
      $vehicleFacSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandSe[$n]],
              ["type" => "Factuel"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacSe) {
          for (
              $a = 0;
              $a < count($vehicleFacSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testFacSe["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestFacSe = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testFacSe["_id"]
      ),
  ]);
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestFacSe["_id"])],
      [
          '$set' => [
              "total" => count($saveTestFacSe["quizzes"])
          ],
      ]
  );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $brandSe,
      "type" => "Factuel",
      "level" =>"Senior",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($brandSe); ++$n) {
      $vehicleFacSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandSe[$n]],
              ["type" => "Factuel"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacSe) {
          for (
              $a = 0;
              $a < count($vehicleFacSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Factuel',
      'level' => "Senior",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}

if ($testDeclaSe) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testDeclaSe["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $brandSe,
          ],
      ]
  );
  
  for ($n = 0; $n < count($brandSe); ++$n) {
      $vehicleDeclaSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandSe[$n]],
              ["type" => "Declaratif"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
  
      if ($vehicleDeclaSe) {
          for (
              $a = 0;
              $a < count($vehicleDeclaSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testDeclaSe["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestDeclaSe = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testDeclaSe["_id"]
      ),
  ]);
  
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestDeclaSe["_id"])],
      [
          '$set' => [
              "total" => count($saveTestDeclaSe["quizzes"])
          ],
          ]
      );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $brandSe,
      "type" => "Declaratif",
      "level" =>"Senior",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($brandSe); ++$n) {
      $vehicleDeclaSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandSe[$n]],
              ["type" => "Declaratif"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleDeclaSe) {
          for (
              $a = 0;
              $a < count($vehicleDeclaSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Declaratif',
      'level' => "Senior",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}
  $success_msg = $success_user_edit;
}

if (isset($_POST["brandEx"])) {
  $id = $_POST["userID"];
  $brandEx = $_POST["brandEx"];

  $resultJu = $results->find([
      '$and' => [
          ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  $resultSe = $results->find([
      '$and' => [
          ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
          ["level" => "Senior"],
          ["active" => true],
      ],
  ]);
  $resultEx = $results->find([
      '$and' => [
          ["user" => new MongoDB\BSON\ObjectId($user['_id'])],
          ["level" => "Expert"],
          ["active" => true],
      ],
  ]);
  if (isset($resultJu)) {
      foreach ($resultJu as $result) {
          $results->updateOne(
              ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
              [
                  '$set' => [
                      "active" => false
                  ],
              ]
          );
      }
  }
  if (isset($resultSe)) {
      foreach ($resultSe as $result) {
          $results->updateOne(
              ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
              [
                  '$set' => [
                      "active" => false
                  ],
              ]
          );
      }
  }
  if (isset($resultEx)) {
      foreach ($resultEx as $result) {
          $results->updateOne(
              ["_id" => new MongoDB\BSON\ObjectId($result['_id'])],
              [
                  '$set' => [
                      "active" => false
                  ],
              ]
          );
      }
  }

$users->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($id)],
  [
      '$set' => [
          "brandExpert" => $brandEx,
      ],
  ]
);
foreach ($brandEx as $bran) {
  $users->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      [
          '$addToSet' => [
              "brandJunior" => $bran,
          ],
      ]
  );
  $users->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      [
          '$addToSet' => [
              "brandSenior" => $bran,
          ],
      ]
  );
}
$user = $users->findOne([
  '$and' => [
      ["_id" => new MongoDB\BSON\ObjectId($id)],
      ["active" => true],
  ],
]);
$testFac = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$testDecla = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Junior"],
      ["active" => true],
  ],
]);
$testFacSe = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Senior"],
      ["active" => true],
  ],
]);
$testDeclaSe = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Senior"],
      ["active" => true],
  ],
]);
$testFacEx = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Factuel"],
      ["level" => "Expert"],
      ["active" => true],
  ],
]);
$testDeclaEx = $tests->findOne([
  '$and' => [
      ["user" => new MongoDB\BSON\ObjectId($id)],
      ["type" => "Declaratif"],
      ["level" => "Expert"],
      ["active" => true],
  ],
]);
for ($n = 0; $n < count($user['brandJunior']); ++$n) {
  $vehicleFac = $vehicles->findOne([
      '$and' => [
          ["brand" => $user['brandJunior'][$n]],
          ["type" => "Factuel"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  $vehicleDecla = $vehicles->findOne([
      '$and' => [
          ["brand" => $user['brandJunior'][$n]],
          ["type" => "Declaratif"],
          ["level" => "Junior"],
          ["active" => true],
      ],
  ]);
  if ($vehicleFac) {
      for (
          $a = 0;
          $a < count($vehicleFac->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testFac["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleFac->quizzes[$a],
                  ],
              ]
          );
      }
  }
  if ($vehicleDecla) {
      for (
          $a = 0;
          $a < count($vehicleDecla->quizzes);
          ++$a
      ) {
          $tests->updateOne(
              [
                  "_id" => new MongoDB\BSON\ObjectId(
                      $testDecla["_id"]
                  ),
              ],
              [
                  '$addToSet' => [
                      "quizzes" =>
                          $vehicleDecla->quizzes[$a],
                  ],
              ]
          );
      }
  }
}
$saveTestFacJu = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testFac["_id"]
  ),
]);
$saveTestDeclaJu = $tests->findOne([
  "_id" => new MongoDB\BSON\ObjectId(
      $testDecla["_id"]
  ),
]);

$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestFacJu["_id"])],
  [
      '$set' => [
          "total" => count($saveTestFacJu["quizzes"])
      ],
  ]
);
$tests->updateOne(
  ["_id" => new MongoDB\BSON\ObjectId($saveTestDeclaJu["_id"])],
  [
      '$set' => [
          "total" => count($saveTestDeclaJu["quizzes"])
      ],
  ]
);

if ($testFacSe) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testFacSe["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $user['brandSenior'],
          ],
      ]
  );
  for ($n = 0; $n < count($user['brandSenior']); ++$n) {
      $vehicleFacSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $user['brandSenior'][$n]],
              ["type" => "Factuel"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacSe) {
          for (
              $a = 0;
              $a < count($vehicleFacSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testFacSe["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestFacSe = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testFacSe["_id"]
      ),
  ]);
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestFacSe["_id"])],
      [
          '$set' => [
              "total" => count($saveTestFacSe["quizzes"])
          ],
      ]
  );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $user['brandSenior'],
      "type" => "Factuel",
      "level" =>"Senior",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($user['brandSenior']); ++$n) {
      $vehicleFacSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $user['brandSenior'][$n]],
              ["type" => "Factuel"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacSe) {
          for (
              $a = 0;
              $a < count($vehicleFacSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Factuel',
      'level' => "Senior",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}

if ($testDeclaSe) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testDeclaSe["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $user['brandSenior'],
          ],
      ]
  );
  
  for ($n = 0; $n < count($user['brandSenior']); ++$n) {
      $vehicleDeclaSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $user['brandSenior'][$n]],
              ["type" => "Declaratif"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
  
      if ($vehicleDeclaSe) {
          for (
              $a = 0;
              $a < count($vehicleDeclaSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testDeclaSe["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestDeclaSe = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testDeclaSe["_id"]
      ),
  ]);
  
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestDeclaSe["_id"])],
      [
          '$set' => [
              "total" => count($saveTestDeclaSe["quizzes"])
          ],
          ]
      );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $user['brandSenior'],
      "type" => "Declaratif",
      "level" =>"Senior",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($user['brandSenior']); ++$n) {
      $vehicleDeclaSe = $vehicles->findOne([
          '$and' => [
              ["brand" => $user['brandSenior'][$n]],
              ["type" => "Declaratif"],
              ["level" => "Senior"],
              ["active" => true],
          ],
      ]);
      if ($vehicleDeclaSe) {
          for (
              $a = 0;
              $a < count($vehicleDeclaSe->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaSe->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Declaratif',
      'level' => "Senior",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}

if ($testFacEx) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testFacEx["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $brandEx,
          ],
      ]
  );
  for ($n = 0; $n < count($brandEx); ++$n) {
      $vehicleFacEx = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandEx[$n]],
              ["type" => "Factuel"],
              ["level" => "Expert"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacEx) {
          for (
              $a = 0;
              $a < count($vehicleFacEx->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testFacEx["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacEx->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestFacEx = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testFacEx["_id"]
      ),
  ]);
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestFacEx["_id"])],
      [
          '$set' => [
              "total" => count($saveTestFacEx["quizzes"])
          ],
      ]
  );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $brandEx,
      "type" => "Factuel",
      "level" =>"Expert",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($brandEx); ++$n) {
      $vehicleFacEx = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandEx[$n]],
              ["type" => "Factuel"],
              ["level" => "Expert"],
              ["active" => true],
          ],
      ]);
      if ($vehicleFacEx) {
          for (
              $a = 0;
              $a < count($vehicleFacEx->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleFacEx->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Factuel',
      'level' => "Expert",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}

if ($testDeclaEx) {
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($testDeclaEx["_id"])],
      [
          '$set' => [
              "quizzes" => [],
              "brand" => $brandEx,
          ],
      ]
  );
  
  for ($n = 0; $n < count($brandEx); ++$n) {
      $vehicleDeclaEx = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandEx[$n]],
              ["type" => "Declaratif"],
              ["level" => "Expert"],
              ["active" => true],
          ],
      ]);
  
      if ($vehicleDeclaEx) {
          for (
              $a = 0;
              $a < count($vehicleDeclaEx->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $testDeclaEx["_id"]
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaEx->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $saveTestDeclaEx = $tests->findOne([
      "_id" => new MongoDB\BSON\ObjectId(
          $testDeclaEx["_id"]
      ),
  ]);
  
  
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($saveTestDeclaEx["_id"])],
      [
          '$set' => [
              "total" => count($saveTestDeclaEx["quizzes"])
          ],
          ]
      );
} else {
  $newTest = [
      "quizzes" => [],
      "user" => new MongoDB\BSON\ObjectId($id),
      "brand" => $brandEx,
      "type" => "Declaratif",
      "level" =>"Expert",
      "total" => 0,
      "active" => true,
      "created" => date("d-m-y h:i:s"),
  ];
  $insert = $tests->insertOne($newTest);

  for ($n = 0; $n < count($brandEx); ++$n) {
      $vehicleDeclaEx = $vehicles->findOne([
          '$and' => [
              ["brand" => $brandEx[$n]],
              ["type" => "Declaratif"],
              ["level" => "Expert"],
              ["active" => true],
          ],
      ]);
      if ($vehicleDeclaEx) {
          for (
              $a = 0;
              $a < count($vehicleDeclaEx->quizzes);
              ++$a
          ) {
              $tests->updateOne(
                  [
                      "_id" => new MongoDB\BSON\ObjectId(
                          $insert->getInsertedId()
                      ),
                  ],
                  [
                      '$addToSet' => [
                          "quizzes" =>
                              $vehicleDeclaEx->quizzes[$a],
                      ],
                  ]
              );
          }
      }
  }
  $test = $tests->findOne([
      '$and' => [
          ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
          ["active" => true],
      ],
  ]);
  $test['total'] = count($test['quizzes']);
  $tests->updateOne(
      ["_id" => new MongoDB\BSON\ObjectId($insert->getInsertedId())],
      [
          '$set' => $test
      ]
  );
  $allocate = [
      'user' => new MongoDB\BSON\ObjectId($user['_id']),
      'test' => new MongoDB\BSON\ObjectId($insert->getInsertedId()),
      'type' => 'Declaratif',
      'level' => "Expert",
      'activeTest' => false,
      'active' => false,
      'created' => date("d-m-y h:i:s"),
  ];
  $allocations->insertOne($allocate);
}
$success_msg = $success_user_edit;
}
if (isset($_POST["excel"])) {
  $spreadsheet = new Spreadsheet();
  $excel_writer = new Xlsx($spreadsheet);
  $spreadsheet->setActiveSheetIndex(0);
  $activeSheet = $spreadsheet->getActiveSheet();
  $activeSheet->setCellValue("A1", "Prénoms");
  $activeSheet->setCellValue("B1", "Noms");
  $myObj = $users->find();
  $i = 2;
  foreach ($myObj as $row) {
      $activeSheet->setCellValue("A" . $i, $row->lastName);
      $activeSheet->setCellValue("A" . $i, $row->firstName);
      $i++;
  }
  $filename = "collaborateurs.xlsx";
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment;filename=" . $filename);
  header("cache-Control: max-age=0");
  $excel_writer->save("php://output");
}
if (isset($_POST["password"])) {
  // Password modification
  $id = $_POST["userID"];
  $passWord = $_POST["password"]; // Check if the password contains at least 8 characters, including at least one uppercase letter, one lowercase letter, and one special character.
  if (
      preg_match(
          '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{6,}$/',
          $passWord
      )
  ) {
      $error =
          "Le mot de passe doit être au moins de six caractères contenir au moins un chiffre, une lettre majiscule";
  } else {
      $password_hash = sha1($passWord);
      $users->updateOne(
          ["_id" => new MongoDB\BSON\ObjectId($id)],
          ['$set' => ["password" => $password_hash, "updated" => date("d-m-Y H:I:S"), "visiblePassword" => $passWord]]
      );
      $success_msg = $success_user_edit;
  }
}
if (isset($_POST["delete"])) {
    $id = $_POST["userID"];
    $member = $users->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
    $member["active"] = false;
    $users->updateOne(
        ["_id" => new MongoDB\BSON\ObjectId($id)],
        ['$set' => $member]
    );
    $success_msg = $success_user_delet;
}
?>
<?php include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $my_info ?> | CFAO Mobility Academy</title>
<!--end::Title-->


<!--begin::Content-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content">
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
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="container-fluid d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bolder my-1 fs-2">
                    <?php echo $my_info ?>
                </h1>
                <!--end::Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Post-->
    <div class="post fs-6 d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div class=" container-xxl ">
        <!--begin::Layout Builder Notice-->
        <div class="card mb-10">
        <!--begin::Navbar-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin: Pic-->
                <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <img src="../../public/assets/media/avatars/300-1.png" alt="image" />
                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                </div>
                </div>
                <!--end::Pic-->

                <!--begin::Info-->
                <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                    <!--begin::Name-->
                    <div class="d-flex align-items-center mb-2">
                        <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"><?php echo $user->firstName; ?> <?php echo $user->lastName; ?></a>
                        <a href="#"><i class="ki-duotone ki-verify fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i></a>

                        <a href="#" class="btn btn-sm btn-light-success fw-bold ms-2 fs-8 py-1 px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_upgrade_plan" data-bs-toggle="modal" data-bs-target="#kt_modal_upgrade_plan"><?php echo $user->profile; ?></a>
                    </div>
                    <!--end::Name-->

                    <!--begin::Info-->
                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                        <i class="ki-duotone ki-sms fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> <?php echo $user->email; ?>
                        </a>
                    </div>
                    <!--end::Info-->
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->

                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Details-->
            </div>
        </div>
        <!--end::Navbar-->
        <!--begin::details View-->
        <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
            <!--begin::Card header-->
            <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <!-- <h3 class="fw-bold m-0">Mes informations</h3> -->
            </div>
            <!--end::Card title-->

            <?php if (
                $_SESSION["profile"] != "Super Admin"
            ) { ?>
            <!--begin::Action-->
            <a href="#" style="background: #225e41;" 
            data-bs-toggle="modal" data-bs-target="#kt_modal_update_details" 
            class="btn btn-sm btn-success align-self-center"><?php echo $edit ?></a>
            <!--end::Action-->
            <?php } ?>
            </div>
            <!--begin::Card header-->

            <!--begin::Card body-->
            <div class="card-body p-9">
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $username ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->username; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $matricule ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->matricule; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted">
                <?php echo $phoneNumber ?>

                <span class="ms-1" data-bs-toggle="tooltip" title="Le numéro de téléphone doit être actif">
                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> </span>
                </label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8 d-flex align-items-center">
                <span class="fw-bold fs-6 text-gray-800 me-2"><?php echo $user->phone; ?></span>
                <!-- <span class="badge badge-success">Verifié</span> -->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $birthdate ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->birthdate; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $gender ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->gender; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted">
                <?php echo $pays ?>

                <span class="ms-1" data-bs-toggle="tooltip" title="Country of origination">
                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> </span>
                </label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->country; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $certificat ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->certificate; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $filiale ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->subsidiary; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $department ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->department; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $role ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->role; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <?php if (isset($user->specialitySenior)) { ?>
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $speciality ?> <?php echo $senior ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['specialitySenior'] as $specialitySenior) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $specialitySenior ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <?php } ?>
            <?php if (isset($user->specialitySenior)) { ?>
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $speciality ?> <?php echo $expert ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['specialityExpert'] as $specialityExpert) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $specialityExpert ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <?php } ?>

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $recrutmentDate ?></label>
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800"><?php echo $user->recrutmentDate; ?></span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <?php if ($_SESSION["profile"] == "Technicien") { ?>
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $junior ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandJunior'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $senior ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandSenior'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $expert ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandExpert'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <?php } ?>

            <?php if ($_SESSION["profile"] == "Manager" && $_SESSION["test"] == true) { ?>
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $junior ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandJunior'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $senior ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandSenior'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted"><?php echo $brand ?> <?php echo $expert ?></label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                <?php foreach ($user['brandExpert'] as $userBrands) { ?>
                <span class="fw-bold fs-6 text-gray-800"><?php echo $userBrands ?>,</span>
                <?php } ?>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <?php } ?>

            </div>
            <!--end::Card body-->
            <!--begin::Modal - Update user details-->
            <div class="modal" id="kt_modal_update_details" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                <!--begin::Form-->
                <form class="form" method="POST" id="kt_modal_update_user_form">
                    <input type="hidden" name="userID" value="<?php echo $user->_id; ?>">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_update_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fs-2 fw-bolder"><?php echo $editer_data ?></h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" data-bs-dismiss="modal" data-kt-menu-dismiss="true">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-10 px-lg-17">
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y me-n7 pe-7"
                            id="kt_modal_update_user_scroll" data-kt-scroll="true"
                            data-kt-scroll-activate="{default: false, lg: true}"
                            data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#kt_modal_update_user_header"
                            data-kt-scroll-wrappers="#kt_modal_update_user_scroll"
                            data-kt-scroll-offset="300px">
                            <!--begin::User toggle-->
                            <div class="fw-boldest fs-3 rotate collapsible mb-7">
                                <?php echo $data ?>
                            </div>
                            <!--end::User toggle-->
                            <!--begin::User form-->
                            <div id="kt_modal_update_user_user_info"
                                class="collapse show">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $username ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="username"
                                        value="<?php echo $user->username; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $matricule ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="matricule"
                                        value="<?php echo $user->matricule; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row g-9 mb-7">
                                    <!--begin::Col-->
                                    <div class="col-md-6 fv-row">
                                        <!--begin::Label-->
                                        <label
                                            class="form-label fw-bolder text-dark fs-6"><?php echo $prenoms ?></label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input class="form-control form-control-solid"
                                            placeholder="" name="firstName"
                                            value="<?php echo $user->firstName; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 fv-row">
                                        <!--begin::Label-->
                                        <label
                                            class="form-label fw-bolder text-dark fs-6"><?php echo $noms ?></label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input class="form-control form-control-solid"
                                            placeholder="" name="lastName"
                                            value="<?php echo $user->lastName; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span><?php echo $Email ?></span>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email"
                                        class="form-control form-control-solid"
                                        placeholder="" name="email"
                                        value="<?php echo $user->email; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-7 fv-row">
                                <!--begin::Label-->
                                <label class="form-label fw-bolder text-dark fs-6">
                                    <span class=""><?php echo $gender ?></span> <span class='ms-1' data-bs-toggle='tooltip' title="Choississez le sexe de l' utilisateur">
                                    <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select name="gender" aria-label="Select a Country" data-control="select2" data-placeholder="<?php echo $user->gender ?>" class="form-select form-select-solid fw-bold">
                                    <option value="<?php echo $user->gender ?>"><?php echo $user->gender ?></option>
                                    <option value='Feminin'>
                                        <?php echo $female ?>
                                    </option>
                                    <option value='Masculin'>
                                        <?php echo $male ?>
                                    </option>
                                </select>
                                <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $phoneNumber ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="phone"
                                        value="<?php echo $user->phone; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $birthdate ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="birthdate"
                                        value="<?php echo $user->birthdate; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class='d-flex flex-column mb-7 fv-row'>
                                <!--begin::Label-->
                                <label class='form-label fw-bolder text-dark fs-6'>
                                    <span><?php echo $pays ?></span> <span class='ms-1' data-bs-toggle='tooltip' title="Votre pays d'origine">
                                    <i class='ki-duotone ki-information fs-7'><span class='path1'></span><span class='path2'></span><span class='path3'></span></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select name='country' aria-label='Select a Country' data-control='select2' data-placeholder='<?php echo $select_country ?>' class='form-select form-select-solid fw-bold'>
                                    <option value='<?php echo $user->country; ?>'><?php echo $user->country; ?></option>
                                    <option value='Afghanistan'>Afghanistan</option>
                                    <option value='Albania'>Albania</option>
                                    <option value='Algeria'>Algeria</option>
                                    <option value='American Samoa'>American Samoa</option>
                                    <option value='Andorra'>Andorra</option>
                                    <option value='Angola'>Angola</option>
                                    <option value='Anguilla'>Anguilla</option>
                                    <option value='Antartica'>Antarctica</option>
                                    <option value='Antigua and Barbuda'>Antigua and Barbuda</option>
                                    <option value='Argentina'>Argentina</option>
                                    <option value='Armenia'>Armenia</option>
                                    <option value='Aruba'>Aruba</option>
                                    <option value='Australia'>Australia</option>
                                    <option value='Austria'>Austria</option>
                                    <option value='Azerbaijan'>Azerbaijan</option>
                                    <option value='Bahamas'>Bahamas</option>
                                    <option value='Bahrain'>Bahrain</option>
                                    <option value='Bangladesh'>Bangladesh</option>
                                    <option value='Barbados'>Barbados</option>
                                    <option value='Belarus'>Belarus</option>
                                    <option value='Belgium'>Belgium</option>
                                    <option value='Belize'>Belize</option>
                                    <option value='Benin'>Benin</option>
                                    <option value='Bermuda'>Bermuda</option>
                                    <option value='Bhutan'>Bhutan</option>
                                    <option value='Bolivia'>Bolivia</option>
                                    <option value='Bosnia and Herzegowina'>Bosnia and Herzegowina</option>
                                    <option value='Botswana'>Botswana</option>
                                    <option value='Bouvet Island'>Bouvet Island</option>
                                    <option value='Brazil'>Brazil</option>
                                    <option value='British Indian Ocean Territory'>British Indian Ocean Territory</option>
                                    <option value='Brunei Darussalam'>Brunei Darussalam</option>
                                    <option value='Bulgaria'>Bulgaria</option>
                                    <option value='Burkina Faso'>Burkina Faso</option>
                                    <option value='Burundi'>Burundi</option>
                                    <option value='Cambodia'>Cambodia</option>
                                    <option value='Cameroon'>Cameroon</option>
                                    <option value='Canada'>Canada</option>
                                    <option value='Cape Verde'>Cape Verde</option>
                                    <option value='Cayman Islands'>Cayman Islands</option>
                                    <option value='Central African Republic'>Central African Republic</option>
                                    <option value='Chad'>Chad</option>
                                    <option value='Chile'>Chile</option>
                                    <option value='China'>China</option>
                                    <option value='Christmas Island'>Christmas Island</option>
                                    <option value='Cocos Islands'>Cocos ( Keeling ) Islands</option>
                                    <option value='Colombia'>Colombia</option>
                                    <option value='Comoros'>Comoros</option>
                                    <option value='Congo'>Congo</option>
                                    <option value='RD Congo'>Congo, the Democratic Republic of the</option>
                                    <option value='Cook Islands'>Cook Islands</option>
                                    <option value='Costa Rica'>Costa Rica</option>
                                    <option value="Cote D'Ivoire">Cote d'Ivoire</option>
                                    <option value="Croatia">Croatia (Hrvatska)</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="Cyprus">Cyprus</option>
                                    <option value="Czech Republic">Czech Republic</option>
                                    <option value="Denmark">Denmark</option>
                                    <option value="Djibouti">Djibouti</option>
                                    <option value="Dominica">Dominica</option>
                                    <option value="Dominican Republic">Dominican Republic</option>
                                    <option value="East Timor">East Timor</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Egypt">Egypt</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                    <option value="Eritrea">Eritrea</option>
                                    <option value="Estonia">Estonia</option>
                                    <option value="Ethiopia">Ethiopia</option>
                                    <option value="Falkland Islands">Falkland Islands (Malvinas)</option>
                                    <option value="Faroe Islands">Faroe Islands</option>
                                    <option value="Fiji">Fiji</option>
                                    <option value="Finland">Finland</option>
                                    <option value="France">France</option>
                                    <option value="France Metropolitan">France, Metropolitan</option>
                                    <option value="French Guiana">French Guiana</option>
                                    <option value="French Polynesia">French Polynesia</option>
                                    <option value="French Southern Territories">French Southern Territories</option>
                                    <option value="Gabon">Gabon</option>
                                    <option value="Gambia">Gambia</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Gibraltar">Gibraltar</option>
                                    <option value="Greece">Greece</option>
                                    <option value="Greenland">Greenland</option>
                                    <option value="Grenada">Grenada</option>
                                    <option value="Guadeloupe">Guadeloupe</option>
                                    <option value="Guam">Guam</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Guinea">Guinea</option>
                                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                                    <option value="Guyana">Guyana</option>
                                    <option value="Haiti">Haiti</option>
                                    <option value="Heard and McDonald Islands">Heard and Mc Donald Islands</option>
                                    <option value="Holy See">Holy See (Vatican City State)</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="Hong Kong">Hong Kong</option>
                                    <option value="Hungary">Hungary</option>
                                    <option value="Iceland">Iceland</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Iran">Iran (Islamic Republic of)</option>
                                    <option value="Iraq">Iraq</option>
                                    <option value="Ireland">Ireland</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Jamaica">Jamaica</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Jordan">Jordan</option>
                                    <option value="Kazakhstan">Kazakhstan</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Kiribati">Kiribati</option>
                                    <option value="Democratic People's Republic of Korea">Korea, Democratic People's
                                    Republic of
                                    </option>
                                    <option value="Korea">Korea, Republic of</option>
                                    <option value="Kuwait">Kuwait</option>
                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                    <option value="Lao">Lao People's Democratic Republic</option>
                                    <option value="Latvia">Latvia</option>
                                    <option value="Lebanon">Lebanon</option>
                                    <option value="Lesotho">Lesotho</option>
                                    <option value="Liberia">Liberia</option>
                                    <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                    <option value="Liechtenstein">Liechtenstein</option>
                                    <option value="Lithuania">Lithuania</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                    <option value="Macau">Macau</option>
                                    <option value="Macedonia">Macedonia, The Former Yugoslav Republic of</option>
                                    <option value="Madagascar">Madagascar</option>
                                    <option value="Malawi">Malawi</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Maldives">Maldives</option>
                                    <option value="Mali">Mali</option>
                                    <option value="Malta">Malta</option>
                                    <option value="Marshall Islands">Marshall Islands</option>
                                    <option value="Martinique">Martinique</option>
                                    <option value="Mauritania">Mauritania</option>
                                    <option value="Mauritius">Mauritius</option>
                                    <option value="Mayotte">Mayotte</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Micronesia">Micronesia, Federated States of</option>
                                    <option value="Moldova">Moldova, Republic of</option>
                                    <option value="Monaco">Monaco</option>
                                    <option value="Mongolia">Mongolia</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Morocco">Morocco</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Myanmar">Myanmar</option>
                                    <option value="Namibia">Namibia</option>
                                    <option value="Nauru">Nauru</option>
                                    <option value="Nepal">Nepal</option>
                                    <option value="Netherlands">Netherlands</option>
                                    <option value="Netherlands Antilles">Netherlands Antilles</option>
                                    <option value="New Caledonia">New Caledonia</option>
                                    <option value="New Zealand">New Zealand</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Niger">Niger</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="Niue">Niue</option>
                                    <option value="Norfolk Island">Norfolk Island</option>
                                    <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                    <option value="Norway">Norway</option>
                                    <option value="Oman">Oman</option>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="Palau">Palau</option>
                                    <option value="Panama">Panama</option>
                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Peru">Peru</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Pitcairn">Pitcairn</option>
                                    <option value="Poland">Poland</option>
                                    <option value="Portugal">Portugal</option>
                                    <option value="Puerto Rico">Puerto Rico</option>
                                    <option value="Qatar">Qatar</option>
                                    <option value="Reunion">Reunion</option>
                                    <option value="Romania">Romania</option>
                                    <option value="Russia">Russian Federation</option>
                                    <option value="Rwanda">Rwanda</option>
                                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                    <option value="Saint LUCIA">Saint LUCIA</option>
                                    <option value="Saint Vincent">Saint Vincent and the Grenadines</option>
                                    <option value="Samoa">Samoa</option>
                                    <option value="San Marino">San Marino</option>
                                    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Senegal">Senegal</option>
                                    <option value="Seychelles">Seychelles</option>
                                    <option value="Sierra">Sierra Leone</option>
                                    <option value="Singapore">Singapore</option>
                                    <option value="Slovakia">Slovakia (Slovak Republic)</option>
                                    <option value="Slovenia">Slovenia</option>
                                    <option value="Solomon Islands">Solomon Islands</option>
                                    <option value="Somalia">Somalia</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="South Georgia">South Georgia and the South Sandwich Islands</option>
                                    <option value="Span">Spain</option>
                                    <option value="SriLanka">Sri Lanka</option>
                                    <option value="St. Helena">St. Helena</option>
                                    <option value="St. Pierre and Miguelon">St. Pierre and Miquelon</option>
                                    <option value="Sudan">Sudan</option>
                                    <option value="Suriname">Suriname</option>
                                    <option value="Svalbard">Svalbard and Jan Mayen Islands</option>
                                    <option value="Swaziland">Swaziland</option>
                                    <option value="Sweden">Sweden</option>
                                    <option value="Switzerland">Switzerland</option>
                                    <option value="Syria">Syrian Arab Republic</option>
                                    <option value="Taiwan">Taiwan, Province of China</option>
                                    <option value="Tajikistan">Tajikistan</option>
                                    <option value="Tanzania">Tanzania, United Republic of</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Togo">Togo</option>
                                    <option value="Tokelau">Tokelau</option>
                                    <option value="Tonga">Tonga</option>
                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                    <option value="Tunisia">Tunisia</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Turkmenistan">Turkmenistan</option>
                                    <option value="Turks and Caicos">Turks and Caicos Islands</option>
                                    <option value="Tuvalu">Tuvalu</option>
                                    <option value="Uganda">Uganda</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="United States Minor Outlying Islands">United States Minor Outlying
                                    Islands</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Uzbekistan">Uzbekistan</option>
                                    <option value="Vanuatu">Vanuatu</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="Vietnam">Viet Nam</option>
                                    <option value="Virgin Islands ( British )">Virgin Islands (British)</option>
                                    <option value="Virgin Islands ( U.S )">Virgin Islands (U.S.)</option>
                                    <option value="Wallis and Futana Islands">Wallis and Futuna Islands</option>
                                    <option value="Western Sahara">Western Sahara</option>
                                    <option value="Yemen">Yemen</option>
                                    <option value="Serbia">Serbia</option>
                                    <option value="Zambia">Zambia</option>
                                    <option value="Zimbabwe">Zimbabwe</option>
                                </select>
                                <!--end::Input-->
                                <?php if (isset($error)) { ?>
                                <span class='text-danger'>
                                    <?php echo $error; ?>
                                </span>
                                <?php } ?>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $certificat ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="certificate"
                                        value="<?php echo $user->certificate; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $role ?>
                                        </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="role"
                                        value="<?php echo $user->role; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $recrutmentDate ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid"
                                        placeholder="" name="recrutmentDate"
                                        value="<?php echo $user->recrutmentDate; ?>" />
                                    <!--end::Input-->
                                </div>
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2"><?php echo $Password ?></label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="password"
                                        class="form-control form-control-solid"
                                        placeholder="" name="password"  value="<?php echo $user->visiblePassword; ?>"/>
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::User form-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-dismiss="true" data-bs-dismiss="modal" data-kt-users-modal-action="cancel">Annuler</button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" name="update" class="btn btn-primary">
                        <?php echo $valider ?>
                    </button>
                    <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
                <!--end::Form-->
                </div>
            </div>
            </div>
            <!--end::Modal - Update user details-->
        </div>
        </div>
        <!--end::Wrapper-->
        </div>
        <!--end::Layout Builder Notice-->
    </div>
    <!--end::Container-->
    </div>
    <!--end::Post-->
<?php include_once "partials/footer.php"; ?>
<?php
} ?>
