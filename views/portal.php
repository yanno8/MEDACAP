<?php
    session_start();
    include_once "language.php";

    if (! isset($_SESSION["profile"])) {
        header("Location: ../");
        exit();
    } else {

        // Initialisation de la langue (par défaut en français)
        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';

        // Texte à afficher selon la langue
        $texts = [
            'en' => [
                'numbers' => ['1.', '2.', '3.', '4.', '5.', '6.', '7.'],
                'labels'  => ['Measure', 'Explore', 'Define', 'Acquire', 'Certify', 'Apply', 'Perform'],
            ],
            'fr' => [
                'numbers' => ['1.', '2.', '3.', '4.', '5.', '6.', '7.'],
                'labels'  => ['Mesurer', 'Exploiter', 'Définir', 'Acquérir', 'Certifier', 'Appliquer', 'Performer'],
            ],
        ];

        // Changer la langue si l'utilisateur clique sur un bouton
        if (isset($_GET['lang'])) {
            $lang             = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        }

        // Récupérer le département de l'utilisateur depuis la session
        $department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

        // Définir les images pour le département "Equipment"
        $equipmentImages = [
            ['url' => '../public/images/renaults.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/truck.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/truck.png.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/tof_dashboard_tech.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/truck2025.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/jcb2.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/renaulttech.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/mercedestruck2.png', 'animation' => 'panLeft'],
        ];

        // Définir les images pour le département "Motors"
        $motorsImages = [
            ['url' => '../public/images/prado3.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/welc_tech.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/labmoteurs_portal.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/peugeot_portal2.png', 'animation' => 'panLeft'],
        ];

        // Définir les images pour les autres départements
        $otherImages = [
            ['url' => '../public/images/mercedestruck2.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/prado3.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/tof_dashboard_tech.png', 'animation' => 'panLeft'],
            ['url' => '../public/images/peugeot_portal2.png', 'animation' => 'panLeft'],
        ];

        // Définir les images du carousel en fonction du département
        if ($department === "Equipment") {
            $carouselImages = $equipmentImages;
        } elseif ($department === "Motors") {
            $carouselImages = $motorsImages;
        } else {
            // Utiliser les images pour les autres départements
            $carouselImages = $otherImages;
        }
        // Définir les brands pour le département "Equipment"
        $equipmentBrands = [
            ['name' => 'FUSO', 'icon' => 'truck'],
            ['name' => 'HINO', 'icon' => 'truck'],
            ['name' => 'JCB', 'icon' => 'truck-monster'],
            ['name' => 'KING LONG', 'icon' => 'bus'],
            ['name' => 'LOVOL', 'icon' => 'truck-pickup'],
            ['name' => 'MERCEDES TRUCK', 'icon' => 'truck'],
            ['name' => 'RENAULT TRUCK', 'icon' => 'truck'],
            ['name' => 'SINOTRUK', 'icon' => 'truck'],
            ['name' => 'TOYOTA BT', 'icon' => 'truck-pickup'],
            ['name' => 'TOYOTA FORKLIFT', 'icon' => 'truck-pickup'],
        ];

        // Définir les brands pour le département "Motors"
        $motorsBrands = [
            ['name' => 'BYD', 'icon' => 'car'],
            ['name' => 'CITROEN', 'icon' => 'car'],
            ['name' => 'MERCEDES', 'icon' => 'car'],
            ['name' => 'MITSUBISHI', 'icon' => 'car'],
            ['name' => 'PEUGEOT', 'icon' => 'car'],
            ['name' => 'SUZUKI', 'icon' => 'car'],
            ['name' => 'TOYOTA', 'icon' => 'car'],
        ];

        // Définir les brands pour les autres départements
        $otherBrands = [
            // Ici tu peux soit mettre un “mix” de tout,
            // soit tout le tableau complet, selon ton besoin
            ['name' => 'BYD', 'icon' => 'car'],
            ['name' => 'CITROEN', 'icon' => 'car'],
            ['name' => 'FUSO', 'icon' => 'truck'],
            ['name' => 'HINO', 'icon' => 'truck'],
            ['name' => 'JCB', 'icon' => 'truck-monster'],
            ['name' => 'KING LONG', 'icon' => 'bus'],
            ['name' => 'LOVOL', 'icon' => 'truck-pickup'],
            ['name' => 'MERCEDES', 'icon' => 'car'],
            ['name' => 'MERCEDES TRUCK', 'icon' => 'truck'],
            ['name' => 'MITSUBISHI', 'icon' => 'car'],
            ['name' => 'PEUGEOT', 'icon' => 'car'],
            ['name' => 'RENAULT TRUCK', 'icon' => 'truck'],
            ['name' => 'SINOTRUK', 'icon' => 'truck'],
            ['name' => 'SUZUKI', 'icon' => 'car'],
            ['name' => 'TOYOTA', 'icon' => 'car'],
            ['name' => 'TOYOTA BT', 'icon' => 'truck-pickup'],
            ['name' => 'TOYOTA FORKLIFT', 'icon' => 'truck-pickup'],
            // Et éventuellement CITROEN, BYD etc., si tu veux vraiment TOUT regrouper
        ];
        if ($department === "Equipment") {
            $brands = $equipmentBrands;
        } elseif ($department === "Motors") {
            $brands = $motorsBrands;
        } else {
            $brands = $otherBrands;
        }

        // On crée un tableau qui représente la pyramide, niveau par niveau.
        $levels = [
            // NIVEAU 1
            [
                [
                    'labelIndex'         => 0, // pour "Measure"
                    'slug'               => 'measure/dashboard',
                    'icon'               => 'fas fa-clipboard-check fs-2',
                    // Profils autorisés
                    'authorizedProfiles' => ["Super Admin", "Trainer", "Technicien", "Manager"],
                ],
            ],

            // NIVEAU 2
            [
                [
                    'labelIndex'         => 1, // "Explore"
                    'slug'               => 'explore/dashboard',
                    'icon'               => 'fas fa-chart-pie fs-2',
                    'authorizedProfiles' => ["Super Admin", "Trainer", "Admin", "Ressource Humaine", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe"],
                ],
                [
                    'labelIndex'         => 2, // "Define"
                    'slug'               => 'define/dashboard',
                    'icon'               => 'fas fa-edit fs-2',
                    'authorizedProfiles' => ["Super Admin", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe", "Admin", "Ressource Humaine", "Manager", "Technicien"],
                ],
            ],

            // NIVEAU 3
            [
                [
                    'labelIndex'         => 3,                   // "Acquire"
                    'slug'               =>'comingsoon', // Ou "404" si tu veux bloquer par défaut
                    'icon'               => 'fas fa-book-reader fs-2',
                    'authorizedProfiles' => ["Super Admin", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe", "Admin", "Ressource Humaine", "Manager", "Technicien"],
                ],
                [
                    'labelIndex'         => 4, // "Certify"
                    'slug'               => 'certify/dashboard',
                    'icon'               => 'fas fa-award fs-2',
                    'authorizedProfiles' => ["Super Admin", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe", "Admin", "Ressource Humaine", "Manager", "Technicien"],
                ],
                [
                    'labelIndex'         => 5, // "Apply"
                    'slug'               => 'apply/dashboard',
                    'icon'               => 'fas fa-tools fs-2',
                    'authorizedProfiles' => ["Super Admin", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe", "Admin", "Ressource Humaine", "Manager", "Technicien"],
                ],
                [
                    'labelIndex'         => 6, // "Perform"
                    'slug'               => 'perform/dashboard',
                    'icon'               => 'fas fa-medal fs-2',
                    'authorizedProfiles' => ["Super Admin", "Directeur Pièce et Service", "Directeur des Opérations", "Directeur Groupe", "Admin", "Ressource Humaine", "Manager", "Technicien"],
                ],
            ],
        ];

        function getStepLinkInfo($step, $texts, $lang, $sessionProfile, $tooltipAttributes)
        {
            // $step contient: ['labelIndex', 'slug', 'icon', 'authorizedProfiles']
            $labelIndex         = $step['labelIndex'];
            $authorizedProfiles = $step['authorizedProfiles'];
            $icon               = $step['icon'];
            $slug               = $step['slug'];

            // Vérifier si le profil du user est dans la liste
            if (in_array($sessionProfile, $authorizedProfiles)) {
                // Autorisé
                $link    = $slug;
                $classes = "card-step authorized";
                $tooltip = '';
            } else {
                // Non autorisé
                $link    = "404";
                $classes = "card-step unauthorized";
                $tooltip = $tooltipAttributes;
            }

            // On construit une structure de retour
            return [
                'link'      => $link,
                'classes'   => $classes,
                'tooltip'   => $tooltip,
                'number'    => $texts[$lang]['numbers'][$labelIndex],
                'label'     => $texts[$lang]['labels'][$labelIndex],
                'iconClass' => $icon,
            ];
        }
    ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($portal) ?> | CFAO Mobility Academy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../public/images/logo-cfao.png" rel="icon">
    <!-- Importation des polices Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Importation de Bootstrap CSS (version 5) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css">
    <!-- Importation de Font Awesome (version 6) pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Importation de Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Importation d'Animate.css pour les animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
    /* Vos styles existants */

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        font-family: 'Orbitron', sans-serif;
        color: #ffffff;
    }

    body {
        background: #000;
        position: relative;
    }

    /* Conteneur principal */
    .main-container {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    /* Overlay pour simuler la brume */
    .mist-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        z-index: -1;
    }

    /* Section du titre */
    .header-section {
        position: relative;
        text-align: center;
        padding: 40px 20px 20px;
        z-index: 2;
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        margin: 20px;
        display: inline-block;
    }

    .header-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 20px;
        overflow: hidden;
        white-space: nowrap;
        border-right: .15em solid #b23a48;
        animation: typing 4s steps(40, end), blink-caret 1.5s step-end infinite;
        display: inline-block;
    }

    @keyframes typing {
        from {
            width: 0
        }

        to {
            width: 100%
        }
    }

    @keyframes blink-caret {

        from,
        to {
            border-color: transparent
        }

        50% {
            border-color: transparent
        }
    }

    .header-section p {
        font-size: 1.8rem;
        color: white;
        animation: focusInContract 2s ease-in-out;
    }

    /* Animation "focus in contract back" pour le slogan */
    @keyframes focusInContract {
        0% {
            letter-spacing: 1em;
            filter: blur(12px);
            opacity: 0;
        }

        100% {
            filter: blur(0);
            opacity: 1;
        }
    }

    /* Icône avec animation */
    .three-d-icon {
        font-size: 2rem;
        color: #d70006;
        animation: bounce 4s infinite;
        margin-left: 10px;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-20px);
        }

        60% {
            transform: translateY(-10px);
        }
    }

    /* Mise en évidence de "MEDACAP" */
    .highlight {
        color: rgb(255, 250, 250);
    }

    /* Section centrale */
    .content-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    /* Conteneur de la pyramide */
    .pyramid-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pyramid-level {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pyramid-level:last-child {
        margin-bottom: 0;
    }

    .card-step {
        background-color: rgba(255, 255, 255, 0.2);
        ;
        backdrop-filter: blur(15px);
        border-radius: 15px;
        padding: 20px;
        color: white;
        text-align: center;
        width: 200px;
        margin: 10px;
        transition: transform 0.3s ease, box-shadow 0.5s ease;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.18);
        position: relative;
    }

    .card-step:nth-child(2) {
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .card-step:hover {
        transform: scale(1.2);
        box-shadow: 0 16px 32px rgba(255, 255, 255, 255);
        text-decoration: none;
    }

    .card-step h3 {
        display: flex;
        align-items: center;
        justify-content: center;
        /* Pour éviter que le texte s'étale sur plusieurs lignes : */
        white-space: nowrap;
        font-size: 1.4rem;
        font-weight: 500;
        margin-bottom: 10px;
        color: inherit;
    }

    /* Style for step numbers */
    .step-number {
        font-size: 2rem;
        /* Larger font size */
        color: white;
        /* Red color */
        font-weight: bold;
        margin-right: 5px;
    }

    /* Icons in red */
    .card-step i {
        font-size: 2.5rem;
        color: #d70006 !important;
        margin-bottom: 5px;
        transition: none;
    }

    /* Active state */
    .card-step.active {
        background-color: #d70006;
        color: #fff;
    }

    .card-step.active h3 {
        color: #fff;
    }

    /* Keep icons red even when active */
    .card-step.active i {
        color: #d70006 !important;
    }

    /* Zoom animation on first letter */
    .card-step h3::first-letter {
        display: inline-block;
        animation: zoomLetter 2s infinite;
    }

    @keyframes zoomLetter {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.5);
        }
    }

    /* Flèches verticales */
    .arrow-container {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
    }

    .arrow {
        width: 30px;
        height: 30px;
        background: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(5px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
        animation: bounce 4s infinite;
    }

    .arrow::before {
        content: '';
        display: block;
        width: 10px;
        height: 10px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        transition: border-color 0.3s;
    }

    /* Flèches horizontales */
    .arrow-horizontal {
        width: 23px;
        height: 3px;
        background: #fff;
        margin: 0 3px;
        position: relative;
        animation: moveArrow 4s infinite;
    }

    .arrow-horizontal::after {
        content: '';
        position: absolute;
        top: -3px;
        right: -3px;
        width: 12px;
        height: 10px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(-45deg);
    }

    /* Arrow animation */
    @keyframes moveArrow {

        0%,
        100% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(5px);
        }
    }

    /* Brand Carousel */
    .brand-carousel {
        overflow: hidden;
        position: relative;
        width: 100%;
        height: 70px;
        margin-bottom: 20px;
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-container {
        display: flex;
        width: auto;
        animation: marquee 100s linear infinite;
        white-space: nowrap;
        position: relative;
    }

    .brand-slide {
        display: flex;
        flex-wrap: nowrap;
    }

    .brand-item {
        display: inline-flex;
        align-items: center;
        margin: 0 20px;
        font-size: 1.2rem;
        color: white;
        white-space: nowrap;
        transition: transform 0.3s ease;
    }

    /* Zoom effect when brand is centered */
    .brand-item.animate {
        animation: brandZoom 10s infinite;
    }

    @keyframes brandZoom {

        0%,
        10%,
        100% {
            transform: scale(1);
        }

        5% {
            transform: scale(1.5);
        }
    }

    .brand-item h4 {
        margin: 0;
        display: flex;
        align-items: center;
    }

    .brand-name {
        margin-right: 10px;
    }

    .icon-zoom {
        animation: zoomInOut 2s infinite;
        color: #d70006;
    }

    @keyframes marquee {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    @keyframes zoomInOut {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    /* Pied de page */
    footer {
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: #ffffff;
        text-align: center;
        padding: 20px;
        font-size: 1rem;
        position: relative;
        z-index: 1;
        border-top: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .pyramid-level {
            margin-bottom: 10px;
            /* Reduced space between levels */
        }

        .arrow-container {
            margin-bottom: 5px;
            /* Reduced space */
        }

        .card-step {
            /* flex: 1 1 calc(33.33% - 20px); */
            margin: 5px;
            /* background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px); */
            /* max-width: calc(33.33% - 20px); */
        }

        .pyramid-level:nth-child(2) .card-step {
            flex: 1 1 calc(50% - 20px);
            max-width: calc(50% - 20px);
        }

        /* .pyramid-level:nth-child(3) .card-step {
            flex: 1 1 calc(25% - 20px);
            max-width: calc(25% - 20px);
        } */
    }

    @media (max-width: 768px) {
        .card-step {
            width: 100%;
            margin: 10px 0;
        }

        .pyramid-level {
            flex-direction: column;
        }

        .arrow-horizontal {
            display: none;
        }

        .header-section h1 {
            font-size: 1.5rem;
        }

        .card-step i {
            font-size: 2.5rem;
        }

        .brand-carousel {
            height: auto;
        }

        .brand-container {
            animation: marquee 40s linear infinite;
        }
    }

    /* Animations pour le panoramique de l'image de fond */
    .background-carousel {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        background-size: cover;
        background-position: center;
        transition: background-image 1s ease-in-out;
    }

    .svg-inline--fa {
        height: 1.5em;
    }

    /* Animations personnalisées pour le panoramique */
    @keyframes panLeft {
        from {
            background-position: right center;
        }

        to {
            background-position: left center;
        }
    }

    .i {
        background-color: red !important;
    }

    /* Nouvelles règles pour les curseurs */
    .card-step.unauthorized {
        cursor: not-allowed;
    }

    .card-step.unauthorized:hover {
        opacity: 0.7;
    }

    .card-step.authorized {
        cursor: pointer;
    }

    /* Fond blanc et texte rouge */
    .tooltip-inner {
        background-color: #fff !important;
        color: #d70006 !important;
        /* Rouge */
        border: 1px solid #d70006 !important;
        /* Optionnel: une bordure rouge */
    }

    /* Couleur de la flèche */
    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #fff !important;
        /* Pour les tooltips en haut */
    }

    .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
        border-bottom-color: #fff !important;
        /* Pour ceux en bas */
    }

    .tooltip.bs-tooltip-left .tooltip-arrow::before {
        border-left-color: #fff !important;
        /* Pour ceux à gauche */
    }

    .tooltip.bs-tooltip-right .tooltip-arrow::before {
        border-right-color: #fff !important;
        /* Pour ceux à droite */
    }
    </style>
</head>

<body>
    <!-- Overlay pour simuler la brume -->
    <div class="mist-overlay"></div>

    <!-- Carrousel d'images en arrière-plan -->
    <div class="background-carousel"></div>

    <div class="main-container">
        <!-- Section du titre -->
        <div class="header-section">
            <h1> Bienvenue sur <span>MEDACAP</span>
                <!-- <i class="fas fa-car three-d-icon fs-2x"></i> -->
            </h1>
            <p>Développez votre expertise professionnelle</p>
        </div>

        <!-- Section centrale -->
        <div class="content-section">
            <!-- Conteneur de la pyramide -->
            <?php
                $tooltipAttributes = 'data-bs-toggle="tooltip" data-bs-placement="top" title="Non applicable pour votre environnement"';
                ?>

            <div class="pyramid-container">
                <?php foreach ($levels as $levelIndex => $stepsInLevel): ?>
                <div class="pyramid-level">
                    <?php foreach ($stepsInLevel as $stepIndex => $step):
                                // On calcule les infos "dynamiques"
                                $info = getStepLinkInfo($step, $texts, $lang, $_SESSION['profile'], $tooltipAttributes);
                            ?>
                    <!-- Lien d'étape -->
                    <a href="<?php echo htmlspecialchars($info['link']); ?>"
                        class="<?php echo htmlspecialchars($info['classes']); ?>" <?php echo $info['tooltip']; ?>>

                        <i style="color : #d70006;" class="<?php echo htmlspecialchars($info['iconClass']); ?>"></i>
                        <h3>
                            <span class="step-number">
                                <?php echo htmlspecialchars($info['number']); ?>
                            </span>
                            <?php echo htmlspecialchars($info['label']); ?>
                        </h3>
                    </a>

                    <?php
                                    // Si ce n’est pas le dernier step du niveau, on affiche la flèche horizontale
                                        if ($stepIndex < count($stepsInLevel) - 1) {
                                            echo '<div class="arrow-horizontal"></div>';
                                        }
                                    ?>
                    <?php endforeach; ?>
                </div>

                <?php
                    // Si ce n’est pas le dernier niveau, on met la flèche verticale
                        if ($levelIndex < count($levels) - 1) {
                            echo '<div class="arrow-container"></div>';
                        }
                    ?>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- Brand Carousel -->
        <div class="brand-carousel">
            <div class="brand-container">
                <div class="brand-slide">
                    <?php
                        // Dupliquer pour un défilement fluide
                            $all_brands = array_merge($brands, $brands);

                            foreach ($all_brands as $brand) {
                                echo '<div class="brand-item">
                                <h4><span class="brand-name">' . htmlspecialchars($brand['name']) . '</span> <i class="fas fa-' . htmlspecialchars($brand['icon']) . ' icon-zoom"></i></h4>
                              </div>';
                            }
                        ?>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <footer>
            <strong><span class="text-muted fw-semibold me-2">2025&copy;</span>
                <a href="#" class="text-white-800 text-hover-primary">
                    CFAO Mobility Academy</a></strong>, All rights reserved
        </footer>
    </div>

    <!-- Scripts nécessaires -->
    <!-- Font Awesome pour les icônes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/js/bootstrap.min.js"></script>

    <!-- Script pour les animations -->
    <script>
    // Récupérer les images du carousel depuis PHP
    const carouselImages = <?php echo json_encode($carouselImages); ?>;

    let currentImageIndex = 0;
    const carouselElement = document.querySelector('.background-carousel');

    function changeBackgroundImage() {
        const image = carouselImages[currentImageIndex];
        carouselElement.style.backgroundImage = `url('${image.url}')`;
        carouselElement.style.animation = `${image.animation} 20s linear forwards`;
        currentImageIndex = (currentImageIndex + 1) % carouselImages.length;
    }

    // Changer l'image toutes les 30 secondes
    changeBackgroundImage();
    setInterval(changeBackgroundImage, 30000);

    // Effet de zoom sur les éléments de marque
    const brandItems = document.querySelectorAll('.brand-item');

    brandItems.forEach((item, index) => {
        const delay = index * 10; // Ajuster le délai si nécessaire
        item.style.animationDelay = `${delay}s`;
        item.classList.add('animate');
    });

    // Gestion des clics sur les étapes du carousel
    const cardSteps = document.querySelectorAll('.card-step');

    cardSteps.forEach(card => {
        card.addEventListener('click', (e) => {
            // Si le lien est non autorisé, empêcher la navigation
            if (card.classList.contains('unauthorized')) {
                e.preventDefault();
                // Optionnel: Afficher un message ou une alerte
                alert("Vous n'êtes pas autorisé à accéder à cette page.");
            } else {
                // Retirer la classe 'active' de toutes les étapes
                cardSteps.forEach(c => c.classList.remove('active'));
                // Ajouter la classe 'active' à l'étape cliquée
                card.classList.add('active');
            }
        });
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            animation: false,
            delay: {
                show: 0,
                hide: 0
            }
        })
    })
    </script>

</body>

</html>
<?php }?>