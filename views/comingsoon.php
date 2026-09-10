<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Coming Soon | CFAO Mobility Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../public/images/logo-cfao.png">

    <!-- Police Orbitron -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-3+/ZC4qh6mCJQLktiXw5fOLf7WZtYsEgtS/ZCe2ZljIbsHtG98yTdcptz6hBSVXLM6go1lfyUy3FUWyWZ4qE/Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    /* Reset & base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Police Orbitron (tech/futuriste) */
    body {
        font-family: 'Orbitron', sans-serif;
        background: #000;
        color: #fff;
        overflow: hidden;
        /* Pour gérer le parallax en fond */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* NAVBAR */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* Espace entre logo et titre */
        background-color: #000;
        padding: 1rem 2rem;
        box-shadow: 0 0 5px #111;
        position: relative;
        z-index: 10;
    }

    /* Logo plus gros + marge */
    .navbar .logo-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .navbar .logo-container img {
        height: 70px;
    }

    /* Titre centré dans la navbar */
    .navbar .page-title {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        font-size: 1.2rem;
        color: rgb(248, 247, 247);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* CONTENU PRINCIPAL */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 2rem;
        position: relative;
        z-index: 2;
        /* Au-dessus des icônes de fond */
    }

    /* Icônes Font Awesome en fond (pour le parallax) */
    .parallax-icon {
        position: absolute;
        font-size: 50px;
        /* Taille de l'icône */
        opacity: 0.12;
        /* On les rend un peu transparentes */
        transition: transform 0.1s;
        color: #fff;
        /* ou #d70006 si tu préfères du rouge */
    }

    /* Quelques positions de base */
    .icon1 {
        top: 20%;
        left: 10%;
    }

    .icon2 {
        top: 60%;
        left: 20%;
    }

    .icon3 {
        top: 30%;
        right: 15%;
    }

    .icon4 {
        top: 70%;
        right: 5%;
    }

    /* TITRE COMING SOON */
    .coming-title {
        font-size: 3rem;
        font-weight: 900;
        color: #fff;
        text-transform: uppercase;
        /* Effet de contour rouge (ombre) */
        text-shadow:
            1px 1px 0px #d70006,
            -1px 1px 0px #d70006,
            1px -1px 0px #d70006,
            -1px -1px 0px #d70006;
        margin-bottom: 2rem;
    }

    /* Roues qui tournent (<img>) */
    .rotating-wheel {
        width: 60px;
        height: 60px;
        vertical-align: middle;
        animation: wheelSpin 2s linear infinite;
        margin: 0 0.1em;
    }

    @keyframes wheelSpin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Apparition du compteur (fade + slide) */
    .countdown-container {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 1.2s forwards ease-in-out 0.3s;
        /* 0.3s de délai pour un petit effet */
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* COMPTE À REBOURS */
    .countdown {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        font-size: 1.2rem;
        font-weight: bold;
        justify-content: center;
    }

    .time-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
    }

    .time-box .number {
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 0.2rem;
        text-shadow: 1px 1px #d70006;
    }

    .time-box .label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #bbb;
    }

    /* PIED DE PAGE */
    footer {
        text-align: center;
        font-size: 0.8rem;
        color: #fff;
        padding: 0.8rem 0;
        background: #000;
        z-index: 10;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .coming-title {
            font-size: 2rem;
        }

        .countdown {
            flex-direction: column;
            gap: 1rem;
        }

        .navbar .page-title {
            font-size: 1rem;
        }

        .navbar .logo-container img {
            height: 50px;
        }

        .parallax-icon {
            font-size: 30px;
            /* Icônes plus petites sur mobile */
        }
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo-container">
            <img src="../public/images/logo.png" alt="CFAO Mobility Academy">
        </div>
        <!-- Titre centré -->
        <div class="page-title">CFAO Mobility Academy – Prochainement</div>
    </div>

    <!-- Icônes parallax en fond -->
    <!-- On utilise des icônes Font Awesome pour l'univers auto. Exemples : fa-wrench, fa-gear, fa-car-burst, fa-tools ... -->
    <i class="fa-solid fa-wrench parallax-icon icon1"></i>
    <i class="fa-solid fa-gear   parallax-icon icon2"></i>
    <i class="fa-solid fa-tools  parallax-icon icon3"></i>
    <i class="fa-solid fa-car-burst parallax-icon icon4"></i>

    <!-- Section centrale -->
    <div class="main-content">
        <h1 class="coming-title">
            C
            <img class="rotating-wheel"
                src="https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2023/12/wheel-404-min.png" alt="roue" />
            MING
            S
            <img class="rotating-wheel"
                src="https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2023/12/wheel-404-min.png" alt="roue" />
            <img class="rotating-wheel"
                src="https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2023/12/wheel-404-min.png" alt="roue" />
            N
        </h1>

        <div class="countdown-container">
            <div class="countdown" id="countdown">
                <div class="time-box">
                    <div class="number" id="days">00</div>
                    <div class="label">Days</div>
                </div>
                <div class="time-box">
                    <div class="number" id="hours">00</div>
                    <div class="label">Hours</div>
                </div>
                <div class="time-box">
                    <div class="number" id="minutes">00</div>
                    <div class="label">Minutes</div>
                </div>
                <div class="time-box">
                    <div class="number" id="seconds">00</div>
                    <div class="label">Seconds</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <footer>
        © 2025 CFAO Mobility Academy, All rights reserved
    </footer>

    <!-- Script JS -->
    <script>
    /*
      Ex: “step3” = 1er juillet 2025
      On veut “step4” => +8 mois => 1er mars 2026
    */
    const step3Date = new Date('2025-07-01T00:00:00');
    const monthsToAdd = 8;
    const targetDate = new Date(step3Date);
    targetDate.setMonth(targetDate.getMonth() + monthsToAdd);
    const targetTime = targetDate.getTime();

    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');

    function updateCountdown() {
        const now = Date.now();
        const distance = targetTime - now;

        if (distance < 0) {
            daysEl.textContent = '00';
            hoursEl.textContent = '00';
            minutesEl.textContent = '00';
            secondsEl.textContent = '00';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        daysEl.textContent = String(days).padStart(2, '0');
        hoursEl.textContent = String(hours).padStart(2, '0');
        minutesEl.textContent = String(minutes).padStart(2, '0');
        secondsEl.textContent = String(seconds).padStart(2, '0');
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();

    /* Effet de parallaxe sur la souris 
       => on déplace légèrement les .parallax-icon
       en fonction de la position de la souris. */
    const icons = document.querySelectorAll('.parallax-icon');
    document.addEventListener('mousemove', (e) => {
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        const diffX = e.clientX - centerX;
        const diffY = e.clientY - centerY;

        icons.forEach((icon, index) => {
            // On applique un ratio différent selon l'index
            const ratio = 0.02 * (index + 1);
            const moveX = diffX * ratio;
            const moveY = diffY * ratio;

            icon.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });
    </script>
</body>

</html>