<?php
session_start();
include_once "language.php";

require_once "vendor/autoload.php";

// Create connection
$conn = new MongoDB\Client("mongodb://localhost:27017");

// Connecting in database
$academy = $conn->academy;

// Connecting in collections
$users = $academy->users;
$connections = $academy->connections;

if (isset($_POST["login"])) {
    $userName = $_POST["username"];
    $password = sha1($_POST["password"]);

    $data = [
      "username" => $userName,
        "password" => $password,
    ];

    $login = $users->findOne($data);
    if (empty($login)) {
        $error_msg = $error_username_pass;
    } elseif ($login->active == false) {
        $error_msg = $error_authentication;
    } else {
        $_SESSION["username"] = $login["username"];
        $_SESSION["profile"] = $login["profile"];
        $_SESSION["lastName"] = $login["lastName"];
        $_SESSION["firstName"] = $login["firstName"];
        $_SESSION["email"] = $login["email"];
        $_SESSION["test"] = $login["test"];
        $_SESSION["id"] = $login["_id"];
        $_SESSION["subsidiary"] = $login["subsidiary"];
        $_SESSION["agency"] = $login["agency"];
        $_SESSION["department"] = $login["department"];
        $_SESSION["country"] = $login["country"];
        
        $userConnected = $connections->findOne([
            '$and' => [
                [
                    "user" => new MongoDB\BSON\ObjectId($_SESSION["id"]),
                    "active" => true,
                ],
            ],
        ]);
        if ($userConnected) {
            $userConnected->status = "Online";
            $userConnected->start = date("d-m-Y H:i:s");
            $connections->updateOne(
                ["_id" => new MongoDB\BSON\ObjectId($userConnected->_id)],
                ['$set' => $userConnected]
            );
        } else {
            $connection = [
                "user" => new MongoDB\BSON\ObjectId($_SESSION["id"]),
                "status" => "Online",
                "start" => date("d-m-Y H:i:s"),
                "end" => "",
                "active" => true
            ];

            $connections->insertOne($connection);
        }

        header("Location: views/portal");
    }
}
?>

<!DOCTYPE html>
<html lang="en-US">
<!-- Mirrored from demo.bravisthemes.com/ducatibox/home-03/?color=v-dark by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Oct 2024 12:04:47 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <title>CFAO Mobility Academy</title>
  <meta name='robots' content='max-image-preview:large' />
  <link rel='dns-prefetch' href='http://fonts.googleapis.com/' />
  <link rel="alternate" type="application/rss+xml" title="Ducatibox &raquo; Feed" href="https://demo.bravisthemes.com/ducatibox/feed/" />
  <link rel="alternate" type="application/rss+xml" title="Ducatibox &raquo; Comments Feed" href="https://demo.bravisthemes.com/ducatibox/comments/feed/" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" 
    integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icomoon@1.0.0/style.min.css">
  <script type="text/javascript">
    /* <![CDATA[ */
    window._wpemojiSettings = {
      "baseUrl": "https:\/\/s.w.org\/images\/core\/emoji\/15.0.3\/72x72\/",
      "ext": ".png",
      "svgUrl": "https:\/\/s.w.org\/images\/core\/emoji\/15.0.3\/svg\/",
      "svgExt": ".svg",
      "source": {
        "concatemoji": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-includes\/js\/wp-emoji-release.min.js?ver=6.6.2"
      }
    };
    /*! This file is auto-generated */
    ! function(i, n) {
      var o, s, e;

      function c(e) {
        try {
          var t = {
            supportTests: e,
            timestamp: (new Date).valueOf()
          };
          sessionStorage.setItem(o, JSON.stringify(t))
        } catch (e) {}
      }

      function p(e, t, n) {
        e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(t, 0, 0);
        var t = new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data),
          r = (e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(n, 0, 0), new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data));
        return t.every(function(e, t) {
          return e === r[t]
        })
      }

      function u(e, t, n) {
        switch (t) {
          case "flag":
            return n(e, "\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f", "\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f") ? !1 : !n(e, "\ud83c\uddfa\ud83c\uddf3", "\ud83c\uddfa\u200b\ud83c\uddf3") && !n(e, "\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f", "\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f");
          case "emoji":
            return !n(e, "\ud83d\udc26\u200d\u2b1b", "\ud83d\udc26\u200b\u2b1b")
        }
        return !1
      }

      function f(e, t, n) {
        var r = "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope ? new OffscreenCanvas(300, 150) : i.createElement("canvas"),
          a = r.getContext("2d", {
            willReadFrequently: !0
          }),
          o = (a.textBaseline = "top", a.font = "600 32px Arial", {});
        return e.forEach(function(e) {
          o[e] = t(a, e, n)
        }), o
      }

      function t(e) {
        var t = i.createElement("script");
        t.src = e, t.defer = !0, i.head.appendChild(t)
      }
      "undefined" != typeof Promise && (o = "wpEmojiSettingsSupports", s = ["flag", "emoji"], n.supports = {
        everything: !0,
        everythingExceptFlag: !0
      }, e = new Promise(function(e) {
        i.addEventListener("DOMContentLoaded", e, {
          once: !0
        })
      }), new Promise(function(t) {
        var n = function() {
          try {
            var e = JSON.parse(sessionStorage.getItem(o));
            if ("object" == typeof e && "number" == typeof e.timestamp && (new Date).valueOf() < e.timestamp + 604800 && "object" == typeof e.supportTests) return e.supportTests
          } catch (e) {}
          return null
        }();
        if (!n) {
          if ("undefined" != typeof Worker && "undefined" != typeof OffscreenCanvas && "undefined" != typeof URL && URL.createObjectURL && "undefined" != typeof Blob) try {
            var e = "postMessage(" + f.toString() + "(" + [JSON.stringify(s), u.toString(), p.toString()].join(",") + "));",
              r = new Blob([e], {
                type: "text/javascript"
              }),
              a = new Worker(URL.createObjectURL(r), {
                name: "wpTestEmojiSupports"
              });
            return void(a.onmessage = function(e) {
              c(n = e.data), a.terminate(), t(n)
            })
          } catch (e) {}
          c(n = f(s, u, p))
        }
        t(n)
      }).then(function(e) {
        for (var t in e) n.supports[t] = e[t], n.supports.everything = n.supports.everything && n.supports[t], "flag" !== t && (n.supports.everythingExceptFlag = n.supports.everythingExceptFlag && n.supports[t]);
        n.supports.everythingExceptFlag = n.supports.everythingExceptFlag && !n.supports.flag, n.DOMReady = !1, n.readyCallback = function() {
          n.DOMReady = !0
        }
      }).then(function() {
        return e
      }).then(function() {
        var e;
        n.supports.everything || (n.readyCallback(), (e = n.source || {}).concatemoji ? t(e.concatemoji) : e.wpemoji && e.twemoji && (t(e.twemoji), t(e.wpemoji)))
      }))
    }((window, document), window._wpemojiSettings);
    /* ]]> */
  </script>
  <link rel='stylesheet' id='pxl-main-css-css' href='public/wp-content/plugins/bravis-addons/assets/css/pxl-main-css.min8a54.css?ver=1.0.0' type='text/css' media='all' />
  <link rel='stylesheet' id='font-awesome-pro-css' href='public/wp-content/plugins/bravis-addons/assets/libs/font-awesome-pro/css/all.min04f2.css?ver=5.15.4-pro' type='text/css' media='all' />
  <style id='wp-emoji-styles-inline-css' type='text/css'>
    img.wp-smiley,
    img.emoji {
      display: inline !important;
      border: none !important;
      box-shadow: none !important;
      height: 1em !important;
      width: 1em !important;
      margin: 0 0.07em !important;
      vertical-align: -0.1em !important;
      background: none !important;
      padding: 0 !important;
    }
  </style>
  <style id='classic-theme-styles-inline-css' type='text/css'>
    /*! This file is auto-generated */
    .wp-block-button__link {
      color: #fff;
      background-color: #32373c;
      border-radius: 9999px;
      box-shadow: none;
      text-decoration: none;
      padding: calc(.667em + 2px) calc(1.333em + 2px);
      font-size: 1.125em
    }

    .wp-block-file__button {
      background: #32373c;
      color: #fff;
      text-decoration: none
    }
  </style>
  <style id='global-styles-inline-css' type='text/css'>
    :root {
      --wp--preset--aspect-ratio--square: 1;
      --wp--preset--aspect-ratio--4-3: 4/3;
      --wp--preset--aspect-ratio--3-4: 3/4;
      --wp--preset--aspect-ratio--3-2: 3/2;
      --wp--preset--aspect-ratio--2-3: 2/3;
      --wp--preset--aspect-ratio--16-9: 16/9;
      --wp--preset--aspect-ratio--9-16: 9/16;
      --wp--preset--color--black: #000000;
      --wp--preset--color--cyan-bluish-gray: #abb8c3;
      --wp--preset--color--white: #ffffff;
      --wp--preset--color--pale-pink: #f78da7;
      --wp--preset--color--vivid-red: #cf2e2e;
      --wp--preset--color--luminous-vivid-orange: #ff6900;
      --wp--preset--color--luminous-vivid-amber: #fcb900;
      --wp--preset--color--light-green-cyan: #7bdcb5;
      --wp--preset--color--vivid-green-cyan: #00d084;
      --wp--preset--color--pale-cyan-blue: #8ed1fc;
      --wp--preset--color--vivid-cyan-blue: #0693e3;
      --wp--preset--color--vivid-purple: #9b51e0;
      --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgba(6, 147, 227, 1) 0%, rgb(155, 81, 224) 100%);
      --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
      --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgba(252, 185, 0, 1) 0%, rgba(255, 105, 0, 1) 100%);
      --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgba(255, 105, 0, 1) 0%, rgb(207, 46, 46) 100%);
      --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
      --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
      --wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
      --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
      --wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
      --wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
      --wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
      --wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
      --wp--preset--font-size--small: 13px;
      --wp--preset--font-size--medium: 20px;
      --wp--preset--font-size--large: 36px;
      --wp--preset--font-size--x-large: 42px;
      --wp--preset--font-family--inter: "Inter", sans-serif;
      --wp--preset--font-family--cardo: Cardo;
      --wp--preset--spacing--20: 0.44rem;
      --wp--preset--spacing--30: 0.67rem;
      --wp--preset--spacing--40: 1rem;
      --wp--preset--spacing--50: 1.5rem;
      --wp--preset--spacing--60: 2.25rem;
      --wp--preset--spacing--70: 3.38rem;
      --wp--preset--spacing--80: 5.06rem;
      --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
      --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
      --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
      --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);
      --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
    }

    :where(.is-layout-flex) {
      gap: 0.5em;
    }

    :where(.is-layout-grid) {
      gap: 0.5em;
    }

    body .is-layout-flex {
      display: flex;
    }

    .is-layout-flex {
      flex-wrap: wrap;
      align-items: center;
    }

    .is-layout-flex> :is(*, div) {
      margin: 0;
    }

    body .is-layout-grid {
      display: grid;
    }

    .is-layout-grid> :is(*, div) {
      margin: 0;
    }

    :where(.wp-block-columns.is-layout-flex) {
      gap: 2em;
    }

    :where(.wp-block-columns.is-layout-grid) {
      gap: 2em;
    }

    :where(.wp-block-post-template.is-layout-flex) {
      gap: 1.25em;
    }

    :where(.wp-block-post-template.is-layout-grid) {
      gap: 1.25em;
    }

    .has-black-color {
      color: var(--wp--preset--color--black) !important;
    }

    .has-cyan-bluish-gray-color {
      color: var(--wp--preset--color--cyan-bluish-gray) !important;
    }

    .has-white-color {
      color: var(--wp--preset--color--white) !important;
    }

    .has-pale-pink-color {
      color: var(--wp--preset--color--pale-pink) !important;
    }

    .has-vivid-red-color {
      color: var(--wp--preset--color--vivid-red) !important;
    }

    .has-luminous-vivid-orange-color {
      color: var(--wp--preset--color--luminous-vivid-orange) !important;
    }

    .has-luminous-vivid-amber-color {
      color: var(--wp--preset--color--luminous-vivid-amber) !important;
    }

    .has-light-green-cyan-color {
      color: var(--wp--preset--color--light-green-cyan) !important;
    }

    .has-vivid-green-cyan-color {
      color: var(--wp--preset--color--vivid-green-cyan) !important;
    }

    .has-pale-cyan-blue-color {
      color: var(--wp--preset--color--pale-cyan-blue) !important;
    }

    .has-vivid-cyan-blue-color {
      color: var(--wp--preset--color--vivid-cyan-blue) !important;
    }

    .has-vivid-purple-color {
      color: var(--wp--preset--color--vivid-purple) !important;
    }

    .has-black-background-color {
      background-color: var(--wp--preset--color--black) !important;
    }

    .has-cyan-bluish-gray-background-color {
      background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
    }

    .has-white-background-color {
      background-color: var(--wp--preset--color--white) !important;
    }

    .has-pale-pink-background-color {
      background-color: var(--wp--preset--color--pale-pink) !important;
    }

    .has-vivid-red-background-color {
      background-color: var(--wp--preset--color--vivid-red) !important;
    }

    .has-luminous-vivid-orange-background-color {
      background-color: var(--wp--preset--color--luminous-vivid-orange) !important;
    }

    .has-luminous-vivid-amber-background-color {
      background-color: var(--wp--preset--color--luminous-vivid-amber) !important;
    }

    .has-light-green-cyan-background-color {
      background-color: var(--wp--preset--color--light-green-cyan) !important;
    }

    .has-vivid-green-cyan-background-color {
      background-color: var(--wp--preset--color--vivid-green-cyan) !important;
    }

    .has-pale-cyan-blue-background-color {
      background-color: var(--wp--preset--color--pale-cyan-blue) !important;
    }

    .has-vivid-cyan-blue-background-color {
      background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
    }

    .has-vivid-purple-background-color {
      background-color: var(--wp--preset--color--vivid-purple) !important;
    }

    .has-black-border-color {
      border-color: var(--wp--preset--color--black) !important;
    }

    .has-cyan-bluish-gray-border-color {
      border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
    }

    .has-white-border-color {
      border-color: var(--wp--preset--color--white) !important;
    }

    .has-pale-pink-border-color {
      border-color: var(--wp--preset--color--pale-pink) !important;
    }

    .has-vivid-red-border-color {
      border-color: var(--wp--preset--color--vivid-red) !important;
    }

    .has-luminous-vivid-orange-border-color {
      border-color: var(--wp--preset--color--luminous-vivid-orange) !important;
    }

    .has-luminous-vivid-amber-border-color {
      border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
    }

    .has-light-green-cyan-border-color {
      border-color: var(--wp--preset--color--light-green-cyan) !important;
    }

    .has-vivid-green-cyan-border-color {
      border-color: var(--wp--preset--color--vivid-green-cyan) !important;
    }

    .has-pale-cyan-blue-border-color {
      border-color: var(--wp--preset--color--pale-cyan-blue) !important;
    }

    .has-vivid-cyan-blue-border-color {
      border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
    }

    .has-vivid-purple-border-color {
      border-color: var(--wp--preset--color--vivid-purple) !important;
    }

    .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
      background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;
    }

    .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
      background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;
    }

    .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
      background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;
    }

    .has-luminous-vivid-orange-to-vivid-red-gradient-background {
      background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;
    }

    .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
      background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;
    }

    .has-cool-to-warm-spectrum-gradient-background {
      background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;
    }

    .has-blush-light-purple-gradient-background {
      background: var(--wp--preset--gradient--blush-light-purple) !important;
    }

    .has-blush-bordeaux-gradient-background {
      background: var(--wp--preset--gradient--blush-bordeaux) !important;
    }

    .has-luminous-dusk-gradient-background {
      background: var(--wp--preset--gradient--luminous-dusk) !important;
    }

    .has-pale-ocean-gradient-background {
      background: var(--wp--preset--gradient--pale-ocean) !important;
    }

    .has-electric-grass-gradient-background {
      background: var(--wp--preset--gradient--electric-grass) !important;
    }

    .has-midnight-gradient-background {
      background: var(--wp--preset--gradient--midnight) !important;
    }

    .has-small-font-size {
      font-size: var(--wp--preset--font-size--small) !important;
    }

    .has-medium-font-size {
      font-size: var(--wp--preset--font-size--medium) !important;
    }

    .has-large-font-size {
      font-size: var(--wp--preset--font-size--large) !important;
    }

    .has-x-large-font-size {
      font-size: var(--wp--preset--font-size--x-large) !important;
    }

    :where(.wp-block-post-template.is-layout-flex) {
      gap: 1.25em;
    }

    :where(.wp-block-post-template.is-layout-grid) {
      gap: 1.25em;
    }

    :where(.wp-block-columns.is-layout-flex) {
      gap: 2em;
    }

    :where(.wp-block-columns.is-layout-grid) {
      gap: 2em;
    }

    :root :where(.wp-block-pullquote) {
      font-size: 1.5em;
      line-height: 1.6;
    }
  </style>
  <link rel='stylesheet' id='contact-form-7-css' href='public/wp-content/plugins/contact-form-7/includes/css/contact-form-7.mine2db.css?ver=5.9.8' type='text/css' media='all' />
  <link rel='stylesheet' id='woocommerce-layout-css' href='public/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.minc60b.css?ver=9.3.3' type='text/css' media='all' />
  <link rel='stylesheet' id='woocommerce-smallscreen-css' href='public/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.minc60b.css?ver=9.3.3' type='text/css' media='only screen and (max-width: 768px)' />
  <link rel='stylesheet' id='woocommerce-general-css' href='public/wp-content/plugins/woocommerce/assets/css/woocommerce-general.minc60b.css?ver=9.3.3' type='text/css' media='all' />
  <style id='woocommerce-inline-inline-css' type='text/css'>
    .woocommerce form .form-row .required {
      visibility: visible;
    }
  </style>
  <link rel='stylesheet' id='elementor-icons-css' href='public/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min0fd8.css?ver=5.31.0' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-frontend-css' href='public/wp-content/uploads/elementor/css/custom-frontend.min5e6a.css?ver=1728436204' type='text/css' media='all' />
  <link rel='stylesheet' id='swiper-css' href='public/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min94a4.css?ver=8.4.5' type='text/css' media='all' />
  <link rel='stylesheet' id='e-swiper-css' href='public/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.minf9f0.css?ver=3.24.5' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-7-css' href='public/wp-content/uploads/elementor/css/post-75e6a.css?ver=1728436204' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-global-css' href='public/wp-content/uploads/elementor/css/global2e63.css?ver=1728436205' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-409-css' href='public/wp-content/uploads/elementor/css/post-4094e9c.css?ver=1728436225' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-419-css' href='public/wp-content/uploads/elementor/css/post-4194e9c.css?ver=1728436225' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-6938-css' href='public/wp-content/uploads/elementor/css/post-69384e9c.css?ver=1728436225' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-494-css' href='public/wp-content/uploads/elementor/css/post-49459c3.css?ver=1728436206' type='text/css' media='all' />
  <link rel='stylesheet' id='hint-css' href='public/wp-content/plugins/woo-smart-compare/assets/libs/hint/hint.min109c.css?ver=6.6.2' type='text/css' media='all' />
  <link rel='stylesheet' id='perfect-scrollbar-css' href='public/wp-content/plugins/woo-smart-compare/assets/libs/perfect-scrollbar/css/perfect-scrollbar.min109c.css?ver=6.6.2' type='text/css' media='all' />
  <link rel='stylesheet' id='perfect-scrollbar-wpc-css' href='public/wp-content/plugins/woo-smart-compare/assets/libs/perfect-scrollbar/css/perfect-scrollbar-wpc.min109c.css?ver=6.6.2' type='text/css' media='all' />
  <link rel='stylesheet' id='woosc-frontend-css' href='public/wp-content/plugins/woo-smart-compare/assets/css/woosc-frontend.mina086.css?ver=6.3.0' type='text/css' media='all' />
  <link rel='stylesheet' id='woosw-icons-css' href='public/wp-content/plugins/woo-smart-wishlist/assets/css/woosw-icons.min20fd.css?ver=4.9.2' type='text/css' media='all' />
  <link rel='stylesheet' id='woosw-frontend-css' href='public/wp-content/plugins/woo-smart-wishlist/assets/css/woosw-frontend.min20fd.css?ver=4.9.2' type='text/css' media='all' />
  <style id='woosw-frontend-inline-css' type='text/css'>
    .woosw-popup .woosw-popup-inner .woosw-popup-content .woosw-popup-content-bot .woosw-notice {
      background-color: #ffffff;
    }

    .woosw-popup .woosw-popup-inner .woosw-popup-content .woosw-popup-content-bot .woosw-popup-content-bot-inner a:hover {
      color: #ffffff;
      border-color: #ffffff;
    }
  </style>
  <link rel='stylesheet' id='jquery-ui-css' href='public/wp-content/themes/ducatibox/assets/css/jquery-ui.min8a54.css?ver=1.0.0' type='text/css' media='all' />
  <link rel='stylesheet' id='magnific-popup-css' href='public/wp-content/themes/ducatibox/assets/css/libs/magnific-popup.minf488.css?ver=1.1.0' type='text/css' media='all' />
  <link rel='stylesheet' id='wow-animate-css' href='public/wp-content/themes/ducatibox/assets/css/libs/animate.minf488.css?ver=1.1.0' type='text/css' media='all' />
  <link rel='stylesheet' id='pxl-bravisicon-css' href='public/wp-content/themes/ducatibox/assets/css/pxl-bravisicon.min20b9.css?ver=1.0.2' type='text/css' media='all' />
  <link rel='stylesheet' id='bootstrap-icons-css' href='public/wp-content/themes/ducatibox/assets/fonts/bootstrap-icons/css/bootstrap-icons.min109c.css?ver=6.6.2' type='text/css' media='all' />
  <link rel='stylesheet' id='icomoon-css' href='public/wp-content/themes/ducatibox/assets/fonts/icomoon/css/icomoon.min20b9.css?ver=1.0.2' type='text/css' media='all' />
  <link rel='stylesheet' id='pxl-grid-css' href='public/wp-content/themes/ducatibox/assets/css/pxl-grid.min20b9.css?ver=1.0.2' type='text/css' media='all' />
  <link rel='stylesheet' id='pxl-style-css' href='public/wp-content/themes/ducatibox/assets/css/pxl-style.min20b9.css?ver=1.0.2' type='text/css' media='all' />
  <style id='pxl-style-inline-css' type='text/css'>
    :root {
      --primary-color: #D70006;
      --secondary-color: #000;
      --third-color: #4E4E4E;
      --fourth-color: #FFFFFF;
      --fifth-color: #999999;
      --sixth-color: #1B1B1B;
      --bglight-color: #FFFFFF;
      --bgdark-color: #110E10;
      --gridlight-color: #E6E6E6;
      --griddark-color: #2e2e2e;
      --primary-color-rgb: 215, 0, 6;
      --secondary-color-rgb: 0, 0, 0;
      --third-color-rgb: 78, 78, 78;
      --fourth-color-rgb: 255, 255, 255;
      --fifth-color-rgb: 153, 153, 153;
      --sixth-color-rgb: 27, 27, 27;
      --bglight-color-rgb: 255, 255, 255;
      --bgdark-color-rgb: 17, 14, 16;
      --gridlight-color-rgb: 230, 230, 230;
      --griddark-color-rgb: 46, 46, 46;
      --link-color: #D70006;
      --link-color-hover: #D70006;
      --link-color-active: #D70006;
      --gradient-color-from: #D70006;
      --gradient-color-to: #540A0D;
    }
  </style>
  <link rel='stylesheet' id='pxl-base-css' href='public/wp-content/themes/ducatibox/pxl-base.min20b9.css?ver=1.0.2' type='text/css' media='all' />
  <link rel='stylesheet' id='pxl-google-fonts-css' href='http://fonts.googleapis.com/css2?family=Bai%20Jamjuree:ital,wght@0,300;0,400;0,500;0,600;0,700&amp;family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&amp;subset=latin%2Clatin-ext' type='text/css' media='all' />
  <link rel='stylesheet' id='google-fonts-1-css' href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CBai+Jamjuree%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CMontserrat%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;ver=6.6.2' type='text/css' media='all' />
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <script type="text/javascript" src="public/wp-includes/js/jquery/jquery.minf43b.js?ver=3.7.1" id="jquery-core-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/jquery/jquery-migrate.min5589.js?ver=3.4.1" id="jquery-migrate-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/bravis-addons/assets/js/libs/waypoints.minc1b4.js?ver=2.0.5" id="waypoints-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.mina7df.js?ver=2.7.0-wc.9.3.3" id="jquery-blockui-js" data-wp-strategy="defer"></script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.mine91a.js?ver=2.1.4-wc.9.3.3" id="js-cookie-js" data-wp-strategy="defer"></script>
  <script type="text/javascript" id="woocommerce-js-extra">
    /* <![CDATA[ */
    var woocommerce_params = {
      "ajax_url": "\/ducatibox\/wp-admin\/admin-ajax.php",
      "wc_ajax_url": "\/ducatibox\/?wc-ajax=%%endpoint%%"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.minc60b.js?ver=9.3.3" id="woocommerce-js" defer="defer" data-wp-strategy="defer"></script>
  <script type="text/javascript" src="public/wp-content/plugins/bravis-addons/assets/js/libs/isotope.pkgd.min7c45.js?ver=3.0.6" id="isotope-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/bravis-addons/assets/js/libs/counter.min109c.js?ver=6.6.2" id="pxl-counter-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/bravis-addons/assets/js/libs/progressbar.minceb2.js?ver=0.7.1" id="pxl-progressbar-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/bravis-addons/assets/js/libs/swiper/swiper.min48f5.js?ver=5.3.6" id="swiper-js"></script>
  <link rel="https://api.w.org/" href="https://demo.bravisthemes.com/ducatibox/wp-json/" />
  <link rel="alternate" title="JSON" type="application/json" href="https://demo.bravisthemes.com/ducatibox/wp-json/wp/v2/pages/409" />
  <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://demo.bravisthemes.com/ducatibox/xmlrpc.php?rsd" />
  <meta name="generator" content="WordPress 6.6.2" />
  <meta name="generator" content="WooCommerce 9.3.3" />
  <link rel="canonical" href="index.html" />
  <link rel='shortlink' href='https://demo.bravisthemes.com/ducatibox/?p=409' />
  <link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed" href="https://demo.bravisthemes.com/ducatibox/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fdemo.bravisthemes.com%2Fducatibox%2Fhome-03%2F" />
  <link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed" href="https://demo.bravisthemes.com/ducatibox/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fdemo.bravisthemes.com%2Fducatibox%2Fhome-03%2F&amp;format=xml" />
  <meta name="generator" content="Redux 4.4.18" />
  <link rel="icon" type="image/png" href="public/images/logo-cfao.png" /> <noscript>
    <style>
      .woocommerce-product-gallery {
        opacity: 1 !important;
      }
    </style>
  </noscript>
  <meta name="generator" content="Elementor 3.24.5; features: additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap">
  <style>
    .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload),
    .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * {
      background-image: none !important;
    }

    @media screen and (max-height: 1024px) {

      .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload),
      .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload) * {
        background-image: none !important;
      }
    }

    @media screen and (max-height: 640px) {

      .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload),
      .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload) * {
        background-image: none !important;
      }
    }
  </style>
  <style id='wp-fonts-local' type='text/css'>
    @font-face {
      font-family: Inter;
      font-style: normal;
      font-weight: 300 900;
      font-display: fallback;
      src: url('https://demo.bravisthemes.com/ducatibox/wp-content/plugins/woocommerce/assets/fonts/Inter-VariableFont_slnt,wght.woff2') format('woff2');
      font-stretch: normal;
    }

    @font-face {
      font-family: Cardo;
      font-style: normal;
      font-weight: 400;
      font-display: fallback;
      src: url('https://demo.bravisthemes.com/ducatibox/wp-content/plugins/woocommerce/assets/fonts/cardo_normal_400.woff2') format('woff2');
    }
  </style>
  <style id="pxl-page-dynamic-css" data-type="redux-output-css">
    #pxl-wapper #pxl-main {
      padding-top: 0px;
      padding-bottom: 0px;
    }
    .highlight {
        color: #D70006;
        background-size: 400%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0%;
        }

        100% {
            background-position: 100%;
        }
    }
  </style>
</head>

<body class="page-template-default page page-id-409 theme-ducatibox woocommerce-no-js  pxl-redux-page  bd-px-header--default dark-mode elementor-default elementor-kit-7 elementor-page elementor-page-409">
  <div id="pxl-wapper" class="pxl-wapper">
    <div id="pxl-loadding" class="pxl-loader style-car">
      <div class="pxl-loader-effect">
        <div class="loader-car">
          <div class="pxl-gears"> <img class="big" src="public/wp-content/themes/ducatibox/assets/img/logo-ld.png" alt="Ducatibox" />
            <div class="spin-small spin-small1"> <img src="public/wp-content/themes/ducatibox/assets/img/round-ld-1.png" alt="Ducatibox" /></div>
            <div class="spin-small spin-small2"> <img src="public/wp-content/themes/ducatibox/assets/img/round-ld-1.png" alt="Ducatibox" /></div>
            <div class="spin-big"> <img src="public/wp-content/themes/ducatibox/assets/img/round-ld-big.png" alt="Ducatibox" /></div> <img class="small" src="public/wp-content/themes/ducatibox/assets/img/cl.png" alt="Ducatibox" />
          </div>
          <div class="logo-loader"> <img style="margin-top:-80px;" src="public/images/logo.png" alt="Ducatibox" /></div>
        </div>
      </div>
    </div>
    <header id="pxl-header-elementor" class="is-sticky">
      <div class="pxl-header-elementor-main px-header--default">
        <div class="pxl-header-content">
          <div class="row">
            <div class="col-12">
              <div data-elementor-type="wp-post" data-elementor-id="419" class="elementor elementor-419">
                <section class="elementor-section elementor-top-section elementor-element elementor-element-9357e68 elementor-section-full_width elementor-section-content-middle elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="9357e68" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                  <div class="elementor-container elementor-column-gap-extended ">
                    <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-0a0912c pxl-column-element-default pxl-column-none" data-id="0a0912c" data-element_type="column">
                      <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-element-c5ca21a elementor-widget__width-auto ml-50 elementor-widget elementor-widget-pxl_logo" data-id="c5ca21a" data-element_type="widget" data-widget_type="pxl_logo.default">
                          <div class="elementor-widget-container">
                            <img style="width:180px; height:100px" class="fa fa-map-marker-alt me-2" src="public/images/logo.png" alt="Logo">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-d4de476 pxl-column-element-default pxl-column-none" data-id="d4de476" data-element_type="column">
                      <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-element-7a825bf elementor-widget__width-auto elementor-widget elementor-widget-pxl_menu" data-id="7a825bf" data-element_type="widget" data-widget_type="pxl_menu.default">
                          <div class="elementor-widget-container">
                            <div class="pxl-nav-menu pxl-nav-menu1 line-style-1">
                              <div class="menu-primary-menu-container">
                                <a href="#connexion" style="color:white !important;" class="btn btn-primary rounded-pill py-2 px-4"><?php echo $connexion ?></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pxl-header-elementor-sticky pxl-sticky-stb">
        <div class="row">
          <div class="col-12">
            <div data-elementor-type="wp-post" data-elementor-id="1450" class="elementor elementor-1450">
              <section class="elementor-section elementor-top-section elementor-element elementor-element-9357e68 elementor-section-full_width elementor-section-content-middle elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="9357e68" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                <div class="elementor-container elementor-column-gap-extended ">
                  <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-0a0912c pxl-column-element-default pxl-column-none" data-id="0a0912c" data-element_type="column">
                    <div class="elementor-widget-wrap elementor-element-populated">
                      <div class="elementor-element elementor-element-c5ca21a elementor-widget__width-auto ml-50 elementor-widget elementor-widget-pxl_logo" data-id="c5ca21a" data-element_type="widget" data-widget_type="pxl_logo.default">
                        <div class="elementor-widget-container">
                        <img style="width:180px; height:100px" class="fa fa-map-marker-alt me-2" src="public/images/logo.png" alt="Logo">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-d4de476 pxl-column-element-default pxl-column-none" data-id="d4de476" data-element_type="column">
                    <div class="elementor-widget-wrap elementor-element-populated">
                      <div class="elementor-element elementor-element-7a825bf elementor-widget__width-auto elementor-widget elementor-widget-pxl_menu" data-id="7a825bf" data-element_type="widget" data-widget_type="pxl_menu.default">
                        <div class="elementor-widget-container">
                          <div class="pxl-nav-menu pxl-nav-menu1 line-style-1">
                            <div class="menu-primary-menu-container">
                              <a href="#connexion" style="color:white !important;" class="btn btn-primary rounded-pill py-2 px-4"><?php echo $connexion ?></a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div id="pxl-main">
      <div class="elementor-container">
        <div class="row pxl-content-wrap no-sidebar">
          <div id="pxl-content-area" class="pxl-content-area pxl-content-page col-12">
            <main id="pxl-content-main">
              <article id="pxl-post-409" class="post-409 page type-page status-publish hentry">
                <div class="pxl-entry-content clearfix">
                  <div data-elementor-type="wp-page" data-elementor-id="409" class="elementor elementor-409">
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-7c33425 elementor-section-full_width elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="7c33425" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                      <div class="pxl-section-bg-parallax" data-parallax="{&quot;x&quot;:50}"></div>
                      <div class="elementor-container elementor-column-gap-no ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-4f8277f pxl-column-element-default pxl-column-none" data-id="4f8277f" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-4e14b99 elementor-widget elementor-widget-pxl_heading" data-id="4e14b99" data-element_type="widget" data-widget_type="pxl_heading.default">
                              <div class="elementor-widget-container">
                                <div id="pxl-pxl_heading-4e14b99-4864" class="pxl-heading px-sub-title-default-style">
                                  <div class="pxl-heading--inner">
                                    <div class="pxl-item--subtitle px-sub-title-default " data-wow-delay="ms"> <span class="pxl-item--subtext"> CFAO MOBILITY ACADEMY <span class="pxl-item--subdivider"></span> </span></div>
                                    <h4 class="pxl-item--title pxl-split-text split-in-down " data-wow-delay="ms"><?php echo str_replace('CFAO Mobility Academy', '<cite>CFAO Mobility Academy</cite>', $index); ?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="elementor-element elementor-element-1e5405c elementor-widget elementor-widget-pxl_banner_box" data-id="1e5405c" data-element_type="widget" data-widget_type="pxl_banner_box.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-banner pxl-banner3 wow fadeInDown" data-wow-delay="200ms">
                                  <div class="pxl-banner-inner">
                                    <!-- <div class="pxl-box-main"> <img fetchpriority="high" decoding="async" width="1920" height="593" src="public/wp-content/uploads/2024/01/car-banner.png" class="no-lazyload attachment-full" alt="" srcset="https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner.png 1920w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-300x93.png 300w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-1024x316.png 1024w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-768x237.png 768w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-1536x474.png 1536w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-1170x361.png 1170w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-600x185.png 600w" sizes="(max-width: 1920px) 100vw, 1920px" loading="eager" /></div>
                                    <div class="box-image-hover"> <img decoding="async" width="1920" height="593" src="public/wp-content/uploads/2024/01/car-banner-light.png" class="no-lazyload attachment-full" alt="" srcset="https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light.png 1920w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-300x93.png 300w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-1024x316.png 1024w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-768x237.png 768w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-1536x474.png 1536w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-1170x361.png 1170w, https://demo.bravisthemes.com/ducatibox/wp-content/uploads/2024/01/car-banner-light-600x185.png 600w" sizes="(max-width: 1920px) 100vw, 1920px" loading="eager" /></div> -->
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-6dbe494 elementor-section-full_width elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="6dbe494" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-extended ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-890554b pxl-column-element-default pxl-column-none" data-id="890554b" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-e9b0824 elementor-widget__width-auto elementor-absolute elementor-widget elementor-widget-pxl_horizontal_scroll" data-id="e9b0824" data-element_type="widget" data-settings="{&quot;_position&quot;:&quot;absolute&quot;}" data-widget_type="pxl_horizontal_scroll.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-horizontal-scroll ">
                                  <div class="scroll-trigger gals-wrap">
                                    <div class="gal-item"><img decoding="async" width="1920" height="485" src="public/wp-content/uploads/2024/01/bg-scoll-1.png" class="attachment-full" alt="" loading="eager" /></div>
                                    <div class="gal-item"><img decoding="async" width="1920" height="485" src="public/wp-content/uploads/2024/01/bg-scoll2.png" class="attachment-full" alt="" loading="eager" /></div>
                                    <div class="gal-item"><img decoding="async" width="1920" height="485" src="public/wp-content/uploads/2024/01/bg-scoll3.png" class="attachment-full" alt="" loading="eager" /></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-944f1db elementor-section-full_width elementor-section-stretched elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="944f1db" data-element_type="section" data-settings="{&quot;stretch_section&quot;:&quot;section-stretched&quot;,&quot;background_background&quot;:&quot;classic&quot;}">
                      <div class="elementor-container elementor-column-gap-no ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-f5f93f4 pxl-column-element-default pxl-column-none" data-id="f5f93f4" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-7b6c83e e-transform elementor-widget elementor-widget-pxl_text_slip" data-id="7b6c83e" data-element_type="widget" data-settings="{&quot;_transform_rotateZ_effect&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:-8,&quot;sizes&quot;:[]},&quot;_transform_rotateZ_effect_mobile_extra&quot;:{&quot;unit&quot;:&quot;deg&quot;,&quot;size&quot;:10,&quot;sizes&quot;:[]},&quot;_transform_rotateZ_effect_laptop&quot;:{&quot;unit&quot;:&quot;deg&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_rotateZ_effect_tablet_extra&quot;:{&quot;unit&quot;:&quot;deg&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_rotateZ_effect_tablet&quot;:{&quot;unit&quot;:&quot;deg&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_rotateZ_effect_mobile&quot;:{&quot;unit&quot;:&quot;deg&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}" data-widget_type="pxl_text_slip.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-text-slip pxl-text-slip1 pxl-slide-to-right  pxl-text-white-shadow" data-wow-delay="ms">
                                  <div class="pxl-item--container">
                                    <div class="pxl-item--inner">
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">BYD</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">CITROEN</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">FUSO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">HINO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">JCB</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">KING LONG</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">LOVOL</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MITSUBISHI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">PEUGEOT</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">RENAULT TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SINOTRUK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SUZUKI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA BT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA FORKLIFT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                    </div>
                                    <div class="pxl-item--inner">
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">BYD</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">CITROEN</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">FUSO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">HINO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">JCB</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">KING LONG</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">LOVOL</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MITSUBISHI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">PEUGEOT</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">RENAULT TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SINOTRUK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SUZUKI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA BT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA FORKLIFT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="elementor-element elementor-element-dfed84d elementor-absolute elementor-widget elementor-widget-pxl_text_slip" data-id="dfed84d" data-element_type="widget" data-settings="{&quot;_position&quot;:&quot;absolute&quot;}" data-widget_type="pxl_text_slip.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-text-slip pxl-text-slip1 pxl-slide-to-left  pxl-text-white-shadow" data-wow-delay="ms">
                                  <div class="pxl-item--container">
                                    <div class="pxl-item--inner">
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">BYD</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">CITROEN</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">FUSO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">HINO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">JCB</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">KING LONG</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">LOVOL</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MITSUBISHI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">PEUGEOT</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">RENAULT TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SINOTRUK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SUZUKI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA BT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA FORKLIFT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                    </div>
                                    <div class="pxl-item--inner">
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">BYD</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">CITROEN</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">FUSO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">HINO</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">JCB</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">KING LONG</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">LOVOL</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MERCEDES TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">MITSUBISHI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">PEUGEOT</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">RENAULT TRUCK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SINOTRUK</span> <i aria-hidden="true" class="bi bi-truck"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">SUZUKI</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA</span> <i aria-hidden="true" class="bi bi-car-front-fill"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA BT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                      <h4 class="pxl-item--text"> <span class="pxl-text-backdrop">TOYOTA FORKLIFT</span> <i aria-hidden="true" class="bi bi-truck-flatbed"></i></h4>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section id="connexion" class="elementor-section elementor-top-section elementor-element elementor-element-7570e74 elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="7570e74" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-extended ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-007d85f pxl-column-element-default pxl-column-none" data-id="007d85f" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-670d2c6 elementor-widget elementor-widget-pxl_heading" data-id="670d2c6" data-element_type="widget" data-widget_type="pxl_heading.default">
                              <div class="elementor-widget-container">
                                <div id="pxl-pxl_heading-670d2c6-5940" class="pxl-heading px-sub-title-default-style">
                                  <div class="pxl-heading--inner">
                                    <div class="pxl-item--subtitle px-sub-title-default " data-wow-delay="ms"> <span class="pxl-item--subtext"> <?php echo $connexion ?> <span class="pxl-item--subdivider"></span> </span></div>
                                    <h3 class="pxl-item--title  " data-wow-delay="ms"> <?php echo $connectez_vous ?></h3>
                                    <div class="px-divider--wrap ">
                                      <div class="px-title--divider px-title--divider6"><span></span></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <?php if (isset($error_msg)) { ?>
                    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <center><strong><?php echo $error_msg; ?></strong></center>
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;
                            </span>
                        </button>
                    </div>
                    <?php } ?>
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-a3231e5 elementor-section-stretched elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="a3231e5" data-element_type="section" data-settings="{&quot;stretch_section&quot;:&quot;section-stretched&quot;,&quot;background_background&quot;:&quot;classic&quot;}" style="margin-top: 100px; margin-bottom: 100px">
                      <div class="pxl-section-bg-parallax" data-parallax="{&quot;x&quot;:50}"></div>
                      <div class="elementor-container elementor-column-gap-extended ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-3b5574e pxl-column-element-default pxl-column-none" data-id="3b5574e" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <section class="elementor-section elementor-inner-section elementor-element elementor-element-1f0e94f elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="1f0e94f" data-element_type="section">
                              <div class="elementor-container elementor-column-gap-no ">
                                <div style="margin-left:50px;" class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-3780654 pxl-column-element-default pxl-column-none" data-id="3780654" data-element_type="column">
                                  <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-7afa7e6 elementor-widget__width-inherit elementor-widget elementor-widget-pxl_image" data-id="7afa7e6" data-element_type="widget" data-widget_type="pxl_image.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-image-single  " data-wow-delay="ms">
                                          <div class="pxl-item--inner"> <img width="70" height="35" src="public/wp-content/uploads/2023/12/Icon-right.png" class="no-lazyload attachment-full" alt="" loading="eager" /></div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="elementor-element elementor-element-e9b9dc0 elementor-widget elementor-widget-pxl_heading" data-id="e9b9dc0" data-element_type="widget" data-widget_type="pxl_heading.default">
                                      <div class="elementor-widget-container">
                                        <div id="pxl-pxl_heading-e9b9dc0-6562" class="pxl-heading px-sub-title-default-style">
                                          <div class="pxl-heading--inner">
                                            <h3 class="pxl-item--title" data-wow-delay="ms"> Entrez vos informations de connexion pour acceder à votre espace <cite>MEDACAP</cite></h3>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-54f38f3 pxl-column-element-default pxl-column-none">
                                  <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-87e9d32 contact-form-custom2 elementor-widget elementor-widget-pxl_contact_form">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-contact-form pxl-contact-form1 btn-w-auto ">
                                          <div class="wpcf7 no-js">
                                            <form method="post">
                                              <div class="wp-row-ctf7 contact-form-custom1">
                                                <div class="row pxl-group--items">
                                                  <div class="col-lg-12">
                                                    <div class="pxl--item your-subject">
                                                      <p>
                                                        <label for="subject"><?php echo $username ?></label>
                                                        <span class="wpcf7-form-control-wrap">
                                                          <input style="border-radius: 15px;" size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="<?php echo $username ?>" type="text" name="username" />
                                                        </span>
                                                      </p>
                                                    </div>
                                                  </div>
                                                  <div class="col-lg-12">
                                                    <div class="pxl--item your-subject">
                                                      <p>
                                                        <label for="subject"><?php echo $Password ?></label>
                                                        <span class="wpcf7-form-control-wrap">
                                                          <input style="border-radius: 15px;" id="password" size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"  placeholder="<?php echo $Password ?>" type="password" name="password" />
                                                        </span>
                                                        <span style="cursor: pointer;" class="password-viewer" onclick="togglePasswordVisibility()">
                                                            <i class="bi bi-eye"></i> Afficher le mot de passe.
                                                        </span>
                                                      </p>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-lg-12"><br><br>
                                                  <div class="row pxl--item input-filled-btn">
                                                    <button class="wpcf7-form-control wpcf7-submit has-spinner" type="submit" name="login"><?php echo $acceder ?></button>
                                                  </div>
                                                </div>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </section>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section style="margin-top:50px;" class="elementor-section elementor-top-section elementor-element elementor-element-7570e74 elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="7570e74" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-extended ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-007d85f pxl-column-element-default pxl-column-none" data-id="007d85f" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-670d2c6 elementor-widget elementor-widget-pxl_heading" data-id="670d2c6" data-element_type="widget" data-widget_type="pxl_heading.default">
                              <div class="elementor-widget-container">
                                <div id="pxl-pxl_heading-670d2c6-5940" class="pxl-heading px-sub-title-default-style">
                                  <div class="pxl-heading--inner">
                                    <div class="pxl-item--subtitle px-sub-title-default " data-wow-delay="ms"> <span class="pxl-item--subtext"> <?php echo $our_brands ?> <span class="pxl-item--subdivider"></span> </span></div>
                                    <h3 class="pxl-item--title  " data-wow-delay="ms"> <?php echo $some_brands ?></h3>
                                    <div class="px-divider--wrap ">
                                      <div class="px-title--divider px-title--divider6"><span></span></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-4c1e379 elementor-section-full_width elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="4c1e379" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-no ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-06e9da0 pxl-column-element-default pxl-column-none" data-id="06e9da0" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-5895d1b elementor-widget elementor-widget-pxl_partner_carousel" data-id="5895d1b" data-element_type="widget" data-widget_type="pxl_partner_carousel.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-swiper-sliders pxl-partner-carousel pxl-partner-carousel2">
                                  <div class="pxl-carousel-inner">
                                    <div class="pxl-swiper-container" dir="ltr" data-settings="{&quot;slide_direction&quot;:&quot;horizontal&quot;,&quot;slide_percolumn&quot;:&quot;1&quot;,&quot;slide_mode&quot;:&quot;slide&quot;,&quot;slides_to_show&quot;:&quot;6&quot;,&quot;slides_to_show_xxl&quot;:&quot;6&quot;,&quot;slides_to_show_lg&quot;:&quot;4&quot;,&quot;slides_to_show_md&quot;:&quot;3&quot;,&quot;slides_to_show_sm&quot;:&quot;2&quot;,&quot;slides_to_show_xs&quot;:&quot;1&quot;,&quot;slides_to_scroll&quot;:&quot;1&quot;,&quot;arrow&quot;:&quot;false&quot;,&quot;pagination&quot;:&quot;false&quot;,&quot;pagination_type&quot;:&quot;bullets&quot;,&quot;autoplay&quot;:&quot;&quot;,&quot;pause_on_hover&quot;:&quot;&quot;,&quot;pause_on_interaction&quot;:&quot;true&quot;,&quot;delay&quot;:5000,&quot;loop&quot;:&quot;false&quot;,&quot;speed&quot;:500}">
                                      <div class="pxl-swiper-wrapper">
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" style="width:150px; height:100px" src="public/wp-content/uploads/2023/12/logo-toyota.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/logo-chevolet.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" style="width:150px; height:150px" src="public/wp-content/uploads/2023/12/logo-citroen.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/bmw_logo.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" style="" src="public/wp-content/uploads/2023/12/logo-renault_trucks.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/fordlogo.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" style="width:180px; height:150px" src="public/wp-content/uploads/2023/12/logo-mu-fu.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/honda.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" width="280" height="200" src="public/wp-content/uploads/2023/12/logo-byd.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/logo-clent.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                        <div class="pxl-swiper-slide">
                                          <div class="pxl-item--inner wow pulse" data-wow-delay="ms">
                                            <div class="pxl-item--logo"> <img decoding="async" width="298" height="250" src="public/wp-content/uploads/2023/12/mercedes_ben.png" class="no-lazyload logo-dark attachment-full" alt="" loading="eager" /> <img decoding="async" width="298" height="200" src="wp-content/uploads/2023/12/mercedes_ben.png" class="no-lazyload pxl-logo-light attachment-full" alt="" loading="eager" /></div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section style="margin-top: 100px; margin-bottom: 50px" class="elementor-section elementor-top-section elementor-element elementor-element-7570e74 elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="7570e74" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-extended ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-007d85f pxl-column-element-default pxl-column-none" data-id="007d85f" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-670d2c6 elementor-widget elementor-widget-pxl_heading" data-id="670d2c6" data-element_type="widget" data-widget_type="pxl_heading.default">
                              <div class="elementor-widget-container">
                                <div id="pxl-pxl_heading-670d2c6-5940" class="pxl-heading px-sub-title-default-style">
                                  <div class="pxl-heading--inner">
                                    <div class="pxl-item--subtitle px-sub-title-default " data-wow-delay="ms"> <span class="pxl-item--subtext"> <?php echo $levelTechs ?> <span class="pxl-item--subdivider"></span> </span></div>
                                    <h3 class="pxl-item--title  " data-wow-delay="ms"> <?php echo $levelTechs ?></h3>
                                    <div class="px-divider--wrap ">
                                      <div class="px-title--divider px-title--divider6"><span></span></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                    <section class="elementor-section elementor-top-section elementor-element elementor-element-a3d324e elementor-section-full_width elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-offset-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="a3d324e" data-element_type="section">
                      <div class="elementor-container elementor-column-gap-no ">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-3226e8f pxl-column-element-default pxl-column-none" data-id="3226e8f" data-element_type="column">
                          <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-2841a6d elementor-widget__width-auto elementor-absolute light-none e-transform elementor-widget elementor-widget-pxl_image" data-id="2841a6d" data-element_type="widget" data-settings="{&quot;_position&quot;:&quot;absolute&quot;,&quot;_transform_translateX_effect&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:-50,&quot;sizes&quot;:[]},&quot;_transform_translateX_effect_laptop&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateX_effect_tablet_extra&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateX_effect_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateX_effect_mobile_extra&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateX_effect_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:-50,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect_laptop&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect_tablet_extra&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect_mobile_extra&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_translateY_effect_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}" data-widget_type="pxl_image.default">
                              <div class="elementor-widget-container">
                                <div class="pxl-image-single pxl-bottom-to-top " data-wow-delay="ms">
                                  <div class="pxl-item--inner"> <img decoding="async" width="1304" height="1235" src="public/wp-content/uploads/2023/12/Ellipse-holder.png" class="no-lazyload attachment-full" alt="" loading="eager" /></div>
                                </div>
                              </div>
                            </div>
                            <section class="elementor-section elementor-inner-section elementor-element elementor-element-e2a0c59 elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="e2a0c59" data-element_type="section">
                              <div class="elementor-container elementor-column-gap-extended ">
                                <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-84f56e3 pxl-column-element-default pxl-column-none" data-id="84f56e3" data-element_type="column">
                                  <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-64772b9 elementor-widget elementor-widget-pxl_icon_box" data-id="64772b9" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInRight" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-64"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Niveau Junior</span></h3>
                                              <div class="pxl-item--description"> Premier niveau de compétences des techniciens, concernant toutes les tâches de <cite style="color: #D70006;">Maintenances</cite> dans les ateliers.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="elementor-element elementor-element-e03cff6 elementor-widget elementor-widget-pxl_icon_box" data-id="e03cff6" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInRight" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-75"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Niveau Senior</span></h3>
                                              <div class="pxl-item--description"> Deuxième niveau de compétences des techniciens, concernant toutes les tâches de <cite style="color: #D70006;">Réparations</cite> dans les ateliers.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="elementor-element elementor-element-700a352 elementor-widget elementor-widget-pxl_icon_box" data-id="700a352" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInRight" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-65"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Niveau Expert</span></h3>
                                              <div class="pxl-item--description"> Troisième niveau de compétences des techniciens, concernant toutes les tâches de <cite style="color: #D70006;">Diagnostics</cite> dans les ateliers.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-7391e19 pxl-column-element-default pxl-column-none" data-id="7391e19" data-element_type="column">
                                  <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-bd978df elementor-widget elementor-widget-pxl_image" data-id="bd978df" data-element_type="widget" data-widget_type="pxl_image.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-image-single pxl-bottom-to-top " data-wow-delay="ms">
                                          <div class="pxl-item--inner"> <img decoding="async" width="396" height="905" src="public/wp-content/uploads/2023/12/gtr-1.png" class="no-lazyload attachment-full" alt="" loading="eager" /></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-fd85066 pxl-column-element-default pxl-column-none" data-id="fd85066" data-element_type="column">
                                  <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-3c20ef6 elementor-widget elementor-widget-pxl_icon_box" data-id="3c20ef6" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInLeft" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-66"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Junior Level</span></h3>
                                              <div class="pxl-item--description"> First level of technicians' skills, concerning all <cite style="color: #D70006;">Maintenance</cite> tasks in the workshops.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="elementor-element elementor-element-e8e89ae elementor-widget elementor-widget-pxl_icon_box" data-id="e8e89ae" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInLeft" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-79"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Senior Level</span></h3>
                                              <div class="pxl-item--description"> Second level of technicians' skills, concerning all <cite style="color: #D70006;">Repair</cite> tasks in the workshops.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="elementor-element elementor-element-e527851 elementor-widget elementor-widget-pxl_icon_box" data-id="e527851" data-element_type="widget" data-widget_type="pxl_icon_box.default">
                                      <div class="elementor-widget-container">
                                        <div class="pxl-icon-box pxl-icon-box1 wow fadeInLeft" data-wow-delay="ms">
                                          <div class="pxl-item--inner bg-off">
                                            <div class="pxl-item--icon"> <i aria-hidden="true" class="icomoon icomoon-icon-80"></i></div>
                                            <div class="pxl-item--content">
                                              <h3 class="pxl-item--title el-empty"> <span>Expert Level</span></h3>
                                              <div class="pxl-item--description"> Third level of technicians' skills, concerning all <cite style="color: #D70006;">Diagnostics</cite> tasks in the workshops.</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </section>
                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </article>
            </main>
          </div>
        </div>
      </div>
    </div><!-- #main -->
    <footer id="pxl-footer-elementor">
      <div class="footer-elementor-inner">
        <div class="row">
          <div class="col-12">
            <div data-elementor-type="wp-post" data-elementor-id="6938" class="elementor elementor-6938">
              <section class="elementor-section elementor-top-section elementor-element elementor-element-315747a elementor-section-content-middle elementor-section-boxed elementor-section-height-default elementor-section-height-default pxl-type-header-none pxl-section-smoke_particles-no pxl-row-scroll-none" data-id="315747a" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                <div class="footer py-4 d-flex flex-lg-column " id="kt_footer">
                    <!--begin::Container-->
                    <div class=" container-fluid  d-flex flex-column flex-md-row flex-stack">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                        <strong><span class="text-muted fw-semibold me-2" id="currentYear"></span>
                            <a href="#" class="text-gray-800 text-hover-danger">CFAO Mobility Academy</a></strong>
                            <span class="text-muted fw-semibold me-2">, All rights reserved</span>
                        </div>
                        <!--end::Copyright-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
              </section>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <a class="pxl-scroll-top" href="#"> <i class="pxl-image-effect2 bravisicon bravisicon-top"></i> </a>
  </div>
  <div id="woosw_wishlist" class="woosw-popup woosw-popup-center"></div>
  <script type='text/javascript'>
    // Obtenir l'année courante
    const currentYear = new Date().getFullYear();
    // Afficher l'année dans l'élément avec l'ID 'currentYear'
    document.getElementById('currentYear').textContent = '© ' + currentYear;
    const lazyloadRunObserver = () => {
      const lazyloadBackgrounds = document.querySelectorAll(`.e-con.e-parent:not(.e-lazyloaded)`);
      const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            let lazyloadBackground = entry.target;
            if (lazyloadBackground) {
              lazyloadBackground.classList.add('e-lazyloaded');
            }
            lazyloadBackgroundObserver.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '200px 0px 200px 0px'
      });
      lazyloadBackgrounds.forEach((lazyloadBackground) => {
        lazyloadBackgroundObserver.observe(lazyloadBackground);
      });
    };
    const events = [
      'DOMContentLoaded',
      'elementor/lazyload/observe',
    ];
    events.forEach((event) => {
      document.addEventListener(event, lazyloadRunObserver);
    });
  </script>
  <script type='text/javascript'>
    (function() {
      var c = document.body.className;
      c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
      document.body.className = c;
    })();
  </script>
  <link rel='stylesheet' id='wc-blocks-style-css' href='public/wp-content/plugins/woocommerce/assets/client/blocks/wc-blockse2cc.css?ver=wc-9.3.3' type='text/css' media='all' />
  <link rel='stylesheet' id='widget-divider-css' href='public/wp-content/plugins/elementor/assets/css/widget-divider.minf9f0.css?ver=3.24.5' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-1450-css' href='public/wp-content/uploads/elementor/css/post-1450fc80.css?ver=1728436226' type='text/css' media='all' />
  <link rel='stylesheet' id='elementor-post-6164-css' href='public/wp-content/uploads/elementor/css/post-6164fc80.css?ver=1728436226' type='text/css' media='all' />
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-core-main.min8a54.js?ver=1.0.0" id="pxl-core-main-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/dist/hooks.min2757.js?ver=2810c76e705dd1a53b18" id="wp-hooks-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/dist/i18n.minc33c.js?ver=5e580eb46a90c2b997e6" id="wp-i18n-js"></script>
  <script type="text/javascript" id="wp-i18n-js-after">
    /* <![CDATA[ */
    wp.i18n.setLocaleData({
      'text direction\u0004ltr': ['ltr']
    });
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/swv.mine2db.js?ver=5.9.8" id="swv-js"></script>
  <script type="text/javascript" id="contact-form-7-js-extra">
    /* <![CDATA[ */
    var wpcf7 = {
      "api": {
        "root": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-json\/",
        "namespace": "contact-form-7\/v1"
      }
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/contact-form-7.mine2db.js?ver=5.9.8" id="contact-form-7-js"></script>
  <script type="text/javascript" id="wc-add-to-cart-js-extra">
    /* <![CDATA[ */
    var wc_add_to_cart_params = {
      "ajax_url": "\/ducatibox\/wp-admin\/admin-ajax.php",
      "wc_ajax_url": "\/ducatibox\/?wc-ajax=%%endpoint%%",
      "i18n_view_cart": "View cart",
      "cart_url": "https:\/\/demo.bravisthemes.com\/ducatibox\/cart\/",
      "is_cart": "",
      "cart_redirect_after_add": "no"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart.minc60b.js?ver=9.3.3" id="wc-add-to-cart-js" data-wp-strategy="defer"></script>
  <script type="text/javascript" src="public/wp-includes/js/imagesloaded.minbb93.js?ver=5.0.0" id="imagesloaded-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/elementor/assets/lib/jquery-numerator/jquery-numerator.min3958.js?ver=0.2.1" id="jquery-numerator-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/gsap.min3b71.js?ver=3.5.0" id="gsap-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-splitText.mina7a0.js?ver=3.6.1" id="pxl-splitText-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/axand-elementor-edit.min20b9.js?ver=1.0.2" id="axand-elementor-edit-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-elementor.min20b9.js?ver=1.0.2" id="ducatibox-elementor-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-scroll-trigger.min2039.js?ver=3.10.5" id="pxl-scroll-trigger-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-elements.min20b9.js?ver=1.0.2" id="ducatibox-elements-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/wow.min8a54.js?ver=1.0.0" id="wow-animate-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-swiper-slider.min20b9.js?ver=1.0.2" id="ducatibox-swiper-slider-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-parallax-scroll.min20b9.js?ver=1.0.2" id="pxl-parallax-scroll-js"></script>
  <script type="text/javascript" id="pxl-post-grid-js-extra">
    /* <![CDATA[ */
    var main_data = {
      "ajax_url": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-admin\/admin-ajax.php"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-post-grid.min20b9.js?ver=1.0.2" id="pxl-post-grid-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-swiper.min20b9.js?ver=1.0.2" id="pxl-swiper-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/pie-chart.min20b9.js?ver=1.0.2" id="pxl-pie-chart-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-pie-chart.min20b9.js?ver=1.0.2" id="ducatibox-pie-chart-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-counter.min20b9.js?ver=1.0.2" id="ducatibox-counter-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-accordion.min20b9.js?ver=1.0.2" id="ducatibox-accordion-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-progressbar.min20b9.js?ver=1.0.2" id="ducatibox-progressbar-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/ducatibox-smoke.min20b9.js?ver=1.0.2" id="ducatibox-smoke-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/print.mina086.js?ver=6.3.0" id="print-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/table-head-fixer.mina086.js?ver=6.3.0" id="table-head-fixer-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/woo-smart-compare/assets/libs/perfect-scrollbar/js/perfect-scrollbar.jquery.mina086.js?ver=6.3.0" id="perfect-scrollbar-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/jquery/ui/core.minb37e.js?ver=1.13.3" id="jquery-ui-core-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/jquery/ui/mouse.minb37e.js?ver=1.13.3" id="jquery-ui-mouse-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/jquery/ui/sortable.minb37e.js?ver=1.13.3" id="jquery-ui-sortable-js"></script>
  <script type="text/javascript" id="woosc-frontend-js-extra">
    /* <![CDATA[ */
    var woosc_vars = {
      "wc_ajax_url": "\/ducatibox\/?wc-ajax=%%endpoint%%",
      "nonce": "24965d6c60",
      "hash": "uiyh",
      "user_id": "0cdb64fab32a05bd393b20c8c351de9f",
      "page_url": "#",
      "open_button": "",
      "hide_empty_row": "yes",
      "reload_count": "no",
      "variations": "yes",
      "open_button_action": "open_popup",
      "menu_action": "open_popup",
      "button_action": "show_table",
      "sidebar_position": "right",
      "message_position": "right-top",
      "message_added": "{name} has been added to Compare list.",
      "message_removed": "{name} has been removed from the Compare list.",
      "message_exists": "{name} is already in the Compare list.",
      "open_bar": "no",
      "bar_bubble": "no",
      "adding": "prepend",
      "click_again": "no",
      "hide_empty": "no",
      "click_outside": "yes",
      "freeze_column": "yes",
      "freeze_row": "yes",
      "scrollbar": "yes",
      "limit": "100",
      "remove_all": "Do you want to remove all products from the compare?",
      "limit_notice": "You can add a maximum of {limit} products to the comparison table.",
      "copied_text": "Share link %s was copied to clipboard!",
      "button_text": "Compare",
      "button_text_added": "Compare",
      "button_normal_icon": "woosc-icon-1",
      "button_added_icon": "woosc-icon-74"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/woosc-frontend.mina086.js?ver=6.3.0" id="woosc-frontend-js"></script>
  <script type="text/javascript" id="woosw-frontend-js-extra">
    /* <![CDATA[ */
    var woosw_vars = {
      "wc_ajax_url": "\/ducatibox\/?wc-ajax=%%endpoint%%",
      "nonce": "6fd8592257",
      "menu_action": "open_page",
      "reload_count": "no",
      "perfect_scrollbar": "yes",
      "wishlist_url": "https:\/\/demo.bravisthemes.com\/ducatibox\/wishlist\/",
      "button_action": "list",
      "message_position": "right-top",
      "button_action_added": "popup",
      "empty_confirm": "This action cannot be undone. Are you sure?",
      "delete_confirm": "This action cannot be undone. Are you sure?",
      "copied_text": "Copied the wishlist link:",
      "menu_text": "Wishlist",
      "button_text": "Add to wishlist",
      "button_text_added": "Browse wishlist",
      "button_normal_icon": "woosw-icon-5",
      "button_added_icon": "woosw-icon-8",
      "button_loading_icon": "woosw-icon-4"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/woosw-frontend.min20fd.js?ver=4.9.2" id="woosw-frontend-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/magnific-popup.minf488.js?ver=1.1.0" id="magnific-popup-js"></script>
  <script type="text/javascript" src="public/wp-includes/js/jquery/ui/slider.minb37e.js?ver=1.13.3" id="jquery-ui-slider-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/counter-slide.min8a54.js?ver=1.0.0" id="pxl-counter-slide-js"></script>
  <script type="text/javascript" src="public/wp-content/themes/ducatibox/assets/js/libs/nice-select.mindc98.js?ver=all" id="nice-select-js"></script>
  <script type="text/javascript" id="pxl-main-js-extra">
    /* <![CDATA[ */
    var main_data = {
      "ajax_url": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-admin\/admin-ajax.php"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-main.min20b9.js?ver=1.0.2" id="pxl-main-js"></script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/pxl-woocommerce.min20b9.js?ver=1.0.2" id="pxl-woocommerce-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/sourcebuster/sourcebuster.minc60b.js?ver=9.3.3" id="sourcebuster-js-js"></script>
  <script type="text/javascript" id="wc-order-attribution-js-extra">
    /* <![CDATA[ */
    var wc_order_attribution = {
      "params": {
        "lifetime": 1.0000000000000000818030539140313095458623138256371021270751953125e-5,
        "session": 30,
        "base64": false,
        "ajaxurl": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-admin\/admin-ajax.php",
        "prefix": "wc_order_attribution_",
        "allowTracking": true
      },
      "fields": {
        "source_type": "current.typ",
        "referrer": "current_add.rf",
        "utm_campaign": "current.cmp",
        "utm_source": "current.src",
        "utm_medium": "current.mdm",
        "utm_content": "current.cnt",
        "utm_id": "current.id",
        "utm_term": "current.trm",
        "utm_source_platform": "current.plt",
        "utm_creative_format": "current.fmt",
        "utm_marketing_tactic": "current.tct",
        "session_entry": "current_add.ep",
        "session_start_time": "current_add.fd",
        "session_pages": "session.pgs",
        "session_count": "udata.vst",
        "user_agent": "udata.uag"
      }
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/plugins/woocommerce/assets/js/frontend/order-attribution.minc60b.js?ver=9.3.3" id="wc-order-attribution-js"></script>
  <script type="text/javascript" id="wooaa-frontend-js-extra">
    /* <![CDATA[ */
    var wooaa_vars = {
      "wc_ajax_url": "\/ducatibox\/?wc-ajax=%%endpoint%%",
      "nonce": "d546cabca9",
      "product_types": "all",
      "ignore_btn_class": ".disabled,.wpc-disabled,.wooaa-disabled,.wooco-disabled,.woosb-disabled,.woobt-disabled,.woosg-disabled,.woofs-disabled,.woopq-disabled,.wpcpo-disabled,.wpcbn-btn,.wpcev-btn,.wpcuv-update",
      "ignore_form_data": "",
      "cart_url": "https:\/\/demo.bravisthemes.com\/ducatibox\/cart\/",
      "cart_redirect_after_add": "no"
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/uploads/siteground-optimizer-assets/wooaa-frontend.mine1fc.js?ver=2.1.1" id="wooaa-frontend-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/elementor/assets/js/webpack.runtime.minf9f0.js?ver=3.24.5" id="elementor-webpack-runtime-js"></script>
  <script type="text/javascript" src="public/wp-content/plugins/elementor/assets/js/frontend-modules.minf9f0.js?ver=3.24.5" id="elementor-frontend-modules-js"></script>
  <script type="text/javascript" id="elementor-frontend-js-before">
    /* <![CDATA[ */
    var elementorFrontendConfig = {
      "environmentMode": {
        "edit": false,
        "wpPreview": false,
        "isScriptDebug": false
      },
      "i18n": {
        "shareOnFacebook": "Share on Facebook",
        "shareOnTwitter": "Share on Twitter",
        "pinIt": "Pin it",
        "download": "Download",
        "downloadImage": "Download image",
        "fullscreen": "Fullscreen",
        "zoom": "Zoom",
        "share": "Share",
        "playVideo": "Play Video",
        "previous": "Previous",
        "next": "Next",
        "close": "Close",
        "a11yCarouselWrapperAriaLabel": "Carousel | Horizontal scrolling: Arrow Left & Right",
        "a11yCarouselPrevSlideMessage": "Previous slide",
        "a11yCarouselNextSlideMessage": "Next slide",
        "a11yCarouselFirstSlideMessage": "This is the first slide",
        "a11yCarouselLastSlideMessage": "This is the last slide",
        "a11yCarouselPaginationBulletMessage": "Go to slide"
      },
      "is_rtl": false,
      "breakpoints": {
        "xs": 0,
        "sm": 480,
        "md": 576,
        "lg": 992,
        "xl": 1440,
        "xxl": 1600
      },
      "responsive": {
        "breakpoints": {
          "mobile": {
            "label": "Mobile Portrait",
            "value": 575,
            "default_value": 767,
            "direction": "max",
            "is_enabled": true
          },
          "mobile_extra": {
            "label": "Mobile Landscape",
            "value": 767,
            "default_value": 880,
            "direction": "max",
            "is_enabled": true
          },
          "tablet": {
            "label": "Tablet Portrait",
            "value": 991,
            "default_value": 1024,
            "direction": "max",
            "is_enabled": true
          },
          "tablet_extra": {
            "label": "Tablet Landscape",
            "value": 1199,
            "default_value": 1200,
            "direction": "max",
            "is_enabled": true
          },
          "laptop": {
            "label": "Laptop",
            "value": 1599,
            "default_value": 1366,
            "direction": "max",
            "is_enabled": true
          },
          "widescreen": {
            "label": "Widescreen",
            "value": 2400,
            "default_value": 2400,
            "direction": "min",
            "is_enabled": false
          }
        },
        "hasCustomBreakpoints": true
      },
      "version": "3.24.5",
      "is_static": false,
      "experimentalFeatures": {
        "additional_custom_breakpoints": true,
        "e_swiper_latest": true,
        "e_nested_atomic_repeaters": true,
        "e_onboarding": true,
        "home_screen": true,
        "landing-pages": true,
        "link-in-bio": true,
        "floating-buttons": true
      },
      "urls": {
        "assets": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-content\/plugins\/elementor\/assets\/",
        "ajaxurl": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-admin\/admin-ajax.php",
        "uploadUrl": "https:\/\/demo.bravisthemes.com\/ducatibox\/wp-content\/uploads"
      },
      "nonces": {
        "floatingButtonsClickTracking": "3ce5a4937d"
      },
      "swiperClass": "swiper",
      "settings": {
        "page": [],
        "editorPreferences": []
      },
      "kit": {
        "active_breakpoints": ["viewport_mobile", "viewport_mobile_extra", "viewport_tablet", "viewport_tablet_extra", "viewport_laptop"],
        "viewport_mobile": 575,
        "viewport_tablet": 991,
        "viewport_tablet_extra": 1199,
        "viewport_laptop": 1599,
        "viewport_mobile_extra": 767,
        "global_image_lightbox": "yes",
        "lightbox_enable_counter": "yes",
        "lightbox_enable_fullscreen": "yes",
        "lightbox_enable_zoom": "yes",
        "lightbox_enable_share": "yes",
        "lightbox_title_src": "title",
        "lightbox_description_src": "description"
      },
      "post": {
        "id": 409,
        "title": "Home%20%E2%80%93%20Car%20Repair%20%E2%80%93%20Ducatibox",
        "excerpt": "",
        "featuredImage": false
      }
    };
    /* ]]> */
  </script>
  <script type="text/javascript" src="public/wp-content/plugins/elementor/assets/js/frontend.minf9f0.js?ver=3.24.5" id="elementor-frontend-js"></script>
  <script>
    // Function to handle closing of the alert message
    document.addEventListener('DOMContentLoaded', function() {
        const closeButtons = document.querySelectorAll('.alert .close');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                alert.remove();
            });
        });
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.querySelector(".password-viewer i");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        }
    }
  </script>
</body>
<!-- Mirrored from demo.bravisthemes.com/ducatibox/home-03/?color=v-dark by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Oct 2024 12:05:33 GMT -->

</html>