<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel — Ecoa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --ink: #16231f;
        --paper: #f4f6f2;
        --paper-dim: #eceee8;
        --white: #ffffff;

        --teal-950: #082522;
        --teal-900: #0d3634;
        --teal-850: #103c39;
        --teal-800: #124542;
        --teal-700: #1b5e5a;
        --teal-600: #256f6a;

        --amber: #c6873a;
        --amber-light: #e7c396;

        --sage: #b9cfc0;

        --line: rgba(22, 35, 31, 0.09);
        --line-dark: rgba(244, 246, 242, 0.13);

        --muted: #68736d;
        --muted-light: #8a938c;

        --danger: #b3452f;
        --success: #2f7a4f;

        --shadow-sm:
            0 4px 18px rgba(22, 35, 31, 0.045);

        --shadow-md:
            0 12px 32px rgba(22, 35, 31, 0.07);

        --shadow-lg:
            0 22px 55px rgba(22, 35, 31, 0.11);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        min-height: 100vh;
        background: var(--paper);
        color: var(--ink);
        font-family: 'IBM Plex Sans', system-ui, sans-serif;
        -webkit-font-smoothing: antialiased;
        line-height: 1.5;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button {
        font-family: inherit;
    }

    a:focus-visible,
    button:focus-visible {
        outline: 2px solid var(--amber);
        outline-offset: 3px;
        border-radius: 6px;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 32px;
    }

    h1,
    h2,
    h3 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 500;
        letter-spacing: -0.018em;
    }


    /* =========================================================
           HEADER
        ========================================================= */

    header {
        position: sticky;
        top: 0;
        z-index: 100;

        background: rgba(13, 54, 52, 0.97);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);

        border-bottom: 1px solid var(--line-dark);
    }

    .nav {
        min-height: 72px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 24px;
    }


    /* LOGO */

    .wordmark {
        display: inline-flex;
        align-items: center;

        gap: 10px;

        color: var(--paper);

        font-family: 'Newsreader', Georgia, serif;
        font-size: 25px;
        font-weight: 600;

        flex-shrink: 0;
    }

    .wordmark img {
        width: 32px;
        height: 32px;

        object-fit: contain;
    }

    .wordmark span {
        color: var(--amber);
    }


    /* HEADER RIGHT */

    .nav-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }


    /* SECURITY LINK */

    .security-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 9px 13px;

        border: 1px solid transparent;
        border-radius: 9px;

        color: rgba(244, 246, 242, 0.76);

        font-size: 13.5px;
        font-weight: 500;

        transition:
            background .2s ease,
            color .2s ease,
            border-color .2s ease;
    }

    .security-link:hover {
        color: var(--paper);

        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.10);
    }

    .security-link svg {
        width: 16px;
        height: 16px;
        opacity: .9;
    }


    /* ACCOUNT */

    .account {
        position: relative;
    }

    .account-button {
        display: flex;
        align-items: center;

        gap: 10px;

        padding: 5px 7px 5px 5px;

        background: transparent;
        border: 1px solid transparent;
        border-radius: 11px;

        color: var(--paper);

        cursor: pointer;

        transition:
            background .2s ease,
            border-color .2s ease;
    }

    .account-button:hover {
        background: rgba(255, 255, 255, 0.07);
        border-color: rgba(255, 255, 255, 0.10);
    }

    .account-avatar {
        width: 36px;
        height: 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        background: var(--amber);
        color: #241505;

        font-size: 14px;
        font-weight: 700;

        box-shadow:
            0 0 0 3px rgba(198, 135, 58, 0.13);
    }

    .account-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;

        line-height: 1.15;
    }

    .account-name {
        max-width: 150px;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        color: rgba(244, 246, 242, 0.95);

        font-size: 13px;
        font-weight: 600;
    }

    .account-role {
        margin-top: 3px;

        color: rgba(244, 246, 242, 0.55);

        font-family: 'IBM Plex Mono', monospace;
        font-size: 9.5px;

        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .account-chevron {
        width: 15px;
        height: 15px;

        color: rgba(244, 246, 242, 0.55);

        transition: transform .2s ease;
    }

    .account.open .account-chevron {
        transform: rotate(180deg);
    }


    /* DROPDOWN */

    .account-dropdown {
        position: absolute;

        right: 0;
        top: calc(100% + 10px);

        width: 270px;

        padding: 8px;

        background: var(--white);

        border: 1px solid var(--line);
        border-radius: 14px;

        box-shadow: var(--shadow-lg);

        opacity: 0;
        visibility: hidden;

        transform: translateY(-5px);

        transition:
            opacity .18s ease,
            visibility .18s ease,
            transform .18s ease;
    }

    .account.open .account-dropdown {
        opacity: 1;
        visibility: visible;

        transform: translateY(0);
    }

    .dropdown-user {
        display: flex;
        align-items: center;

        gap: 11px;

        padding: 12px;

        border-bottom: 1px solid var(--line);

        margin-bottom: 6px;
    }

    .dropdown-avatar {
        width: 38px;
        height: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        background: var(--teal-900);
        color: var(--paper);

        font-size: 13px;
        font-weight: 600;
    }

    .dropdown-user strong {
        display: block;

        color: var(--ink);

        font-size: 13px;
        font-weight: 600;

        word-break: break-word;
    }

    .dropdown-user span {
        display: block;

        margin-top: 2px;

        color: var(--muted);

        font-size: 11.5px;

        word-break: break-word;
    }

    .dropdown-link {
        display: flex;
        align-items: center;

        gap: 10px;

        width: 100%;

        padding: 10px 11px;

        border-radius: 8px;

        color: #4e5953;

        font-size: 13px;
        font-weight: 500;

        transition:
            background .18s ease,
            color .18s ease;
    }

    .dropdown-link:hover {
        background: var(--paper);
        color: var(--teal-700);
    }

    .dropdown-link svg {
        width: 16px;
        height: 16px;
    }

    .dropdown-divider {
        height: 1px;

        background: var(--line);

        margin: 6px 4px;
    }

    .dropdown-logout {
        width: 100%;

        display: flex;
        align-items: center;

        gap: 10px;

        padding: 10px 11px;

        background: transparent;
        border: 0;
        border-radius: 8px;

        color: var(--danger);

        font-size: 13px;
        font-weight: 500;

        cursor: pointer;
        text-align: left;

        transition: background .18s ease;
    }

    .dropdown-logout:hover {
        background: rgba(179, 69, 47, 0.07);
    }

    .dropdown-logout svg {
        width: 16px;
        height: 16px;
    }


    /* =========================================================
           HERO
        ========================================================= */

    .hero {
        position: relative;
        overflow: hidden;

        background:
            radial-gradient(720px 420px at 84% -12%,
                rgba(198, 135, 58, 0.17),
                transparent 61%),
            radial-gradient(500px 300px at 15% 100%,
                rgba(37, 111, 106, 0.17),
                transparent 70%),
            linear-gradient(180deg,
                var(--teal-900),
                #0a2b29 92%);

        color: var(--paper);

        padding: 66px 0 76px;
    }

    .hero::before {
        content: "";

        position: absolute;

        width: 320px;
        height: 320px;

        right: -130px;
        top: 70px;

        border: 1px solid rgba(231, 195, 150, .08);

        border-radius: 50%;
    }

    .hero::after {
        content: "";

        position: absolute;

        width: 220px;
        height: 220px;

        right: -80px;
        top: 120px;

        border: 1px solid rgba(231, 195, 150, .06);

        border-radius: 50%;
    }

    .hero-content {
        position: relative;
        z-index: 2;

        max-width: 790px;
    }


    /* EYEBROW */

    .eyebrow {
        display: inline-flex;
        align-items: center;

        gap: 9px;

        margin-bottom: 16px;

        color: var(--sage);

        font-family: 'IBM Plex Mono', monospace;
        font-size: 10.5px;

        letter-spacing: .15em;
        text-transform: uppercase;
    }

    .eyebrow::before {
        content: "";

        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: var(--amber);

        box-shadow:
            0 0 0 3px rgba(198, 135, 58, 0.20);
    }


    /* HERO TITLE */

    .hero h1 {
        margin-bottom: 13px;

        color: var(--paper);

        font-size: clamp(38px, 5vw, 54px);

        line-height: 1.04;
    }

    .hero p {
        max-width: 62ch;

        color: rgba(244, 246, 242, 0.70);

        font-size: 16px;
        line-height: 1.68;
    }


    /* HERO META */

    .hero-meta {
        display: flex;
        align-items: center;

        gap: 20px;

        margin-top: 28px;
    }

    .hero-meta-item {
        display: flex;
        align-items: center;

        gap: 8px;

        color: rgba(244, 246, 242, 0.55);

        font-size: 12px;
    }

    .hero-meta-item svg {
        width: 15px;
        height: 15px;

        color: var(--amber-light);
    }


    /* SECURITY BANNER */

    .banner {
        margin-top: 27px;

        display: flex;
        align-items: center;

        gap: 13px;

        max-width: 690px;

        padding: 13px 16px;

        background: rgba(198, 135, 58, 0.11);

        border: 1px solid rgba(198, 135, 58, 0.28);

        border-radius: 11px;
    }

    .banner-icon {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 8px;

        background: rgba(198, 135, 58, 0.14);

        color: var(--amber-light);
    }

    .banner-icon svg {
        width: 17px;
        height: 17px;
    }

    .banner-content {
        flex: 1;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 16px;
    }

    .banner p {
        color: var(--paper);

        font-size: 13px;
        line-height: 1.4;
    }

    .banner a {
        color: var(--amber-light);

        font-size: 13px;
        font-weight: 600;

        white-space: nowrap;

        border-bottom: 1px solid rgba(231, 195, 150, .35);

        transition: border-color .2s ease;
    }

    .banner a:hover {
        border-color: var(--amber-light);
    }


    /* =========================================================
           DASHBOARD
        ========================================================= */

    .dashboard {
        padding: 55px 0 82px;
    }


    /* SECTION HEADING */

    .section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;

        gap: 24px;

        margin-bottom: 24px;
    }

    .section-heading h2 {
        margin-bottom: 5px;

        color: var(--ink);

        font-size: 30px;
    }

    .section-heading p {
        color: #69746e;

        font-size: 14px;
    }

    .section-note {
        color: var(--muted-light);

        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;

        letter-spacing: .06em;
        text-transform: uppercase;

        white-space: nowrap;
    }


    /* =========================================================
           STAT CARDS
        ========================================================= */

    .cards {
        display: grid;

        grid-template-columns: repeat(4, 1fr);

        gap: 15px;

        margin-bottom: 38px;
    }

    .card {
        position: relative;
        overflow: hidden;

        min-height: 176px;

        display: flex;
        flex-direction: column;

        padding: 21px;

        background: var(--white);

        border: 1px solid var(--line);
        border-radius: 13px;

        box-shadow: var(--shadow-sm);

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .card::after {
        content: "";

        position: absolute;

        width: 95px;
        height: 95px;

        right: -38px;
        bottom: -42px;

        border-radius: 50%;

        background: rgba(27, 94, 90, .045);
    }

    .card:hover {
        transform: translateY(-3px);

        border-color: rgba(27, 94, 90, .18);

        box-shadow: var(--shadow-md);
    }

    .card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-bottom: 17px;
    }

    .card h3 {
        font-family: 'IBM Plex Sans', sans-serif;

        color: #68736d;

        font-size: 11px;
        font-weight: 600;

        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .card-icon {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: var(--paper);

        color: var(--teal-700);
    }

    .card-icon svg {
        width: 17px;
        height: 17px;
    }

    .card .value {
        position: relative;
        z-index: 1;

        margin-top: auto;

        color: var(--teal-700);

        font-family: 'Newsreader', Georgia, serif;

        font-size: 40px;
        line-height: 1;
    }

    .card .hint {
        margin-top: 7px;

        color: var(--muted-light);

        font-size: 11.5px;
    }


    /* =========================================================
           PANELS
        ========================================================= */

    .panels {
        display: grid;

        grid-template-columns: 1.45fr 1fr;

        gap: 18px;
    }

    .panel {
        background: var(--white);

        border: 1px solid var(--line);
        border-radius: 13px;

        padding: 27px;

        box-shadow: var(--shadow-sm);
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding-bottom: 17px;
        margin-bottom: 20px;

        border-bottom: 1px solid var(--line);
    }

    .panel-header h2 {
        color: var(--ink);

        font-size: 24px;
    }

    .panel-label {
        display: inline-flex;
        align-items: center;

        gap: 6px;

        color: var(--teal-600);

        font-family: 'IBM Plex Mono', monospace;

        font-size: 9.5px;

        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .panel-label::before {
        content: "";

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: var(--success);

        box-shadow:
            0 0 0 3px rgba(47, 122, 79, .10);
    }


    /* =========================================================
           PROFILE
        ========================================================= */

    .profile-info {
        display: grid;

        grid-template-columns: repeat(2, 1fr);

        gap: 11px;
    }

    .profile-item {
        min-height: 82px;

        padding: 15px 16px;

        background: var(--paper);

        border: 1px solid transparent;
        border-radius: 10px;

        transition:
            background .18s ease,
            border-color .18s ease;
    }

    .profile-item:hover {
        background: #eef1ec;

        border-color: var(--line);
    }

    .profile-item span {
        display: block;

        margin-bottom: 6px;

        color: #707a74;

        font-size: 10px;
        font-weight: 500;

        text-transform: uppercase;
        letter-spacing: .065em;
    }

    .profile-item strong {
        display: block;

        color: var(--ink);

        font-size: 14px;
        font-weight: 600;

        word-break: break-word;
    }


    /* =========================================================
           SECURITY PANEL
        ========================================================= */

    .security-status {
        display: flex;
        align-items: center;

        gap: 10px;

        margin-bottom: 15px;

        padding: 13px 15px;

        border-radius: 10px;

        font-size: 13px;
        font-weight: 600;
    }

    .security-status.on {
        background: rgba(47, 122, 79, .09);
        color: var(--success);
    }

    .security-status.off {
        background: rgba(224, 161, 92, .13);
        color: #8a5a24;
    }

    .status-dot {
        width: 7px;
        height: 7px;

        flex-shrink: 0;

        border-radius: 50%;
    }

    .status-dot.on {
        background: #5ba976;

        box-shadow:
            0 0 0 3px rgba(91, 169, 118, .15);
    }

    .status-dot.off {
        background: #d49451;

        box-shadow:
            0 0 0 3px rgba(212, 148, 81, .14);
    }

    .security-desc {
        margin-bottom: 19px;

        color: #5f6a64;

        font-size: 13px;
        line-height: 1.6;
    }


    /* SECURITY DETAILS */

    .security-details {
        display: grid;

        grid-template-columns: 1fr 1fr;

        gap: 9px;

        margin-bottom: 18px;
    }

    .security-detail {
        padding: 11px 12px;

        background: var(--paper);

        border-radius: 9px;
    }

    .security-detail span {
        display: block;

        margin-bottom: 3px;

        color: var(--muted);

        font-size: 9.5px;

        text-transform: uppercase;
        letter-spacing: .055em;
    }

    .security-detail strong {
        color: var(--ink);

        font-size: 12px;
        font-weight: 600;
    }


    /* BUTTON */

    .btn-outline {
        width: 100%;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 8px;

        padding: 11px 18px;

        background: transparent;

        border: 1px solid var(--teal-700);
        border-radius: 9px;

        color: var(--teal-700);

        font-size: 13px;
        font-weight: 600;

        transition:
            background .2s ease,
            color .2s ease,
            transform .15s ease;
    }

    .btn-outline:hover {
        background: var(--teal-700);
        color: var(--white);

        transform: translateY(-1px);
    }

    .btn-outline svg {
        width: 16px;
        height: 16px;
    }


    /* =========================================================
           QUICK ACTIONS
        ========================================================= */

    .quick-actions {
        margin-top: 18px;

        background: var(--white);

        border: 1px solid var(--line);
        border-radius: 13px;

        padding: 27px;

        box-shadow: var(--shadow-sm);
    }

    .quick-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        margin-bottom: 19px;
    }

    .quick-header h2 {
        color: var(--ink);

        font-size: 23px;
    }

    .quick-header p {
        margin-top: 3px;

        color: var(--muted);

        font-size: 12.5px;
    }

    .quick-label {
        color: var(--muted-light);

        font-family: 'IBM Plex Mono', monospace;

        font-size: 9.5px;

        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .quick-grid {
        display: grid;

        grid-template-columns: repeat(3, 1fr);

        gap: 11px;
    }

    .quick-action {
        display: flex;
        align-items: center;

        gap: 13px;

        min-height: 70px;

        padding: 12px 14px;

        background: var(--paper);

        border: 1px solid transparent;
        border-radius: 10px;

        transition:
            background .18s ease,
            border-color .18s ease,
            transform .18s ease;
    }

    .quick-action:hover {
        background: #eef1ec;

        border-color: var(--line);

        transform: translateY(-1px);
    }

    .quick-icon {
        width: 37px;
        height: 37px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: var(--white);

        color: var(--teal-700);

        border: 1px solid var(--line);
    }

    .quick-icon svg {
        width: 17px;
        height: 17px;
    }

    .quick-text {
        min-width: 0;
    }

    .quick-text strong {
        display: block;

        color: var(--ink);

        font-size: 13px;
        font-weight: 600;
    }

    .quick-text span {
        display: block;

        margin-top: 2px;

        color: var(--muted);

        font-size: 11px;
    }

    .quick-arrow {
        margin-left: auto;

        color: var(--muted-light);

        font-size: 16px;

        transition:
            color .18s ease,
            transform .18s ease;
    }

    .quick-action:hover .quick-arrow {
        color: var(--teal-700);

        transform: translateX(2px);
    }


    /* =========================================================
           FOOTER
        ========================================================= */

    footer {
        background: #0a2b29;

        color: rgba(244, 246, 242, .50);

        padding: 32px 0;

        font-size: 12px;
    }

    .footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        flex-wrap: wrap;
    }

    .footer-brand {
        display: flex;
        align-items: center;

        gap: 9px;
    }

    .footer-brand img {
        width: 22px;
        height: 22px;

        object-fit: contain;

        opacity: .7;
    }

    .footer-links {
        display: flex;
        gap: 20px;
    }

    .footer-links a {
        color: rgba(244, 246, 242, .50);

        transition: color .2s ease;
    }

    .footer-links a:hover {
        color: var(--paper);
    }


    /* =========================================================
           RESPONSIVE
        ========================================================= */

    @media (max-width: 1000px) {

        .cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .panels {
            grid-template-columns: 1fr;
        }

        .quick-grid {
            grid-template-columns: 1fr 1fr;
        }
    }


    @media (max-width: 680px) {

        .container {
            padding: 0 20px;
        }

        .nav {
            min-height: 64px;
        }

        .wordmark {
            font-size: 23px;
        }

        .wordmark img {
            width: 29px;
            height: 29px;
        }

        .security-link {
            padding: 8px;
        }

        .security-link span {
            display: none;
        }

        .account-info,
        .account-chevron {
            display: none;
        }

        .account-button {
            padding: 4px;
        }

        .hero {
            padding: 50px 0 60px;
        }

        .hero h1 {
            font-size: 36px;
        }

        .hero p {
            font-size: 15px;
        }

        .hero-meta {
            align-items: flex-start;
            flex-direction: column;
            gap: 9px;
        }

        .banner {
            width: 100%;
            align-items: flex-start;
        }

        .banner-content {
            align-items: flex-start;
            flex-direction: column;
            gap: 8px;
        }

        .dashboard {
            padding: 40px 0 58px;
        }

        .section-heading {
            align-items: flex-start;
            flex-direction: column;
            gap: 7px;
        }

        .cards {
            grid-template-columns: 1fr;
        }

        .card {
            min-height: 150px;
        }

        .panel,
        .quick-actions {
            padding: 22px;
        }

        .profile-info {
            grid-template-columns: 1fr;
        }

        .security-details {
            grid-template-columns: 1fr 1fr;
        }

        .quick-grid {
            grid-template-columns: 1fr;
        }

        .footer-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .footer-links {
            flex-wrap: wrap;
        }
    }


    @media (max-width: 420px) {

        .account-dropdown {
            position: fixed;

            top: 70px;
            left: 16px;
            right: 16px;

            width: auto;
        }

        .panel-header {
            align-items: flex-start;

            flex-direction: column;

            gap: 8px;
        }

        .security-details {
            grid-template-columns: 1fr;
        }

        .quick-header {
            align-items: flex-start;

            flex-direction: column;

            gap: 5px;
        }
    }
    </style>
</head>


<body>


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <header>

        <div class="container">

            <div class="nav">


                {{-- LOGO --}}
                <a href="{{ route('landing') }}" class="wordmark" aria-label="Ecoa — Página inicial">
                    <img src="{{ asset('img/ecoa-icone.png') }}" alt="Ecoa">
                </a>
                

                <style>
                .wordmark img {
                    width: 50px;
                    height: 50px;
                    object-fit: contain;
                }
                </style>



                <div class="nav-right">


                    {{-- SEGURANÇA --}}

                    <a href="{{ route('security.index') }}" class="security-link" title="Configurações de segurança">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="4" y="11" width="16" height="9" rx="2"></rect>

                            <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>

                            <circle cx="12" cy="15" r="1"></circle>
                        </svg>

                        <span>Segurança</span>

                    </a>


                    {{-- CONTA --}}

                    <div class="account" id="accountMenu">

                        <button type="button" class="account-button" id="accountButton" aria-expanded="false"
                            aria-haspopup="true">

                            <span class="account-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>

                            <span class="account-info">

                                <span class="account-name">
                                    {{ auth()->user()->name }}
                                </span>

                                <span class="account-role">
                                    {{ ucwords(str_replace('_', ' ', auth()->user()->perfil ?? 'Usuário')) }}
                                </span>

                            </span>

                            <svg class="account-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>

                        </button>


                        {{-- DROPDOWN --}}

                        <div class="account-dropdown" role="menu">

                            <div class="dropdown-user">

                                <span class="dropdown-avatar">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>

                                <div>

                                    <strong>
                                        {{ auth()->user()->name }}
                                    </strong>

                                    <span>
                                        {{ auth()->user()->email }}
                                    </span>

                                </div>

                            </div>


                            {{-- SEGURANÇA --}}

                            <a href="{{ route('security.index') }}" class="dropdown-link" role="menuitem">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>

                                Segurança da conta

                            </a>


                            <div class="dropdown-divider"></div>


                            {{-- LOGOUT --}}

                            <form method="POST" action="{{ route('logout') }}">

                                @csrf

                                <button type="submit" class="dropdown-logout" role="menuitem">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <path d="m16 17 5-5-5-5"></path>
                                        <path d="M21 12H9"></path>
                                    </svg>

                                    Sair da conta

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </header>



    {{-- =========================================================
         HERO
    ========================================================== --}}

    <section class="hero">

        <div class="container">

            <div class="hero-content">


                <span class="eyebrow">
                    Painel Ecoa
                </span>


                <h1>
                    Olá, {{ explode(' ', auth()->user()->name)[0] }}.
                </h1>


                <p>
                    Seu espaço para acompanhar atendimentos, organizar
                    informações e manter o cuidado infantil reunido em
                    um só lugar.
                </p>


                {{-- HERO META --}}

                <div class="hero-meta">

                    <span class="hero-meta-item">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>

                            <path d="M12 7v5l3 2"></path>
                        </svg>

                        Ambiente de acompanhamento

                    </span>


                    <span class="hero-meta-item">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7 10 17l-5-5"></path>
                        </svg>

                        Sessão protegida

                    </span>

                </div>


                {{-- AVISO DE SEGURANÇA --}}

                @unless (auth()->user()->two_factor_confirmed_at)

                <div class="banner">

                    <div class="banner-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>

                            <path d="M12 8v4"></path>

                            <path d="M12 15h.01"></path>
                        </svg>

                    </div>


                    <div class="banner-content">

                        <p>
                            Sua conta ainda não possui a verificação em duas etapas.
                        </p>

                        <a href="{{ route('security.index') }}">
                            Ativar agora →
                        </a>

                    </div>

                </div>

                @endunless

            </div>

        </div>

    </section>



    {{-- =========================================================
         DASHBOARD
    ========================================================== --}}

    <main class="dashboard">

        <div class="container">


            {{-- TÍTULO --}}

            <div class="section-heading">

                <div>

                    <h2>
                        Visão geral
                    </h2>

                    <p>
                        Um resumo rápido do seu ambiente de acompanhamento.
                    </p>

                </div>

                <span class="section-note">
                    Atualizado agora
                </span>

            </div>



            {{-- =================================================
                 CARDS
            ================================================== --}}

            <div class="cards">


                {{-- CRIANÇAS --}}

                <div class="card">

                    <div class="card-top">

                        <h3>
                            Crianças
                        </h3>

                        <div class="card-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="8" r="3"></circle>

                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>

                                <path d="M16 5.5a3 3 0 0 1 0 5.8"></path>

                                <path d="M17 14a5 5 0 0 1 4 5"></path>
                            </svg>

                        </div>

                    </div>


                    <div class="value">
                        0
                    </div>

                    <div class="hint">
                        nenhum acompanhamento cadastrado
                    </div>

                </div>



                {{-- PLANOS --}}

                <div class="card">

                    <div class="card-top">

                        <h3>
                            Planos terapêuticos
                        </h3>

                        <div class="card-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>

                                <path d="M14 2v6h6"></path>

                                <path d="M8 13h8"></path>

                                <path d="M8 17h5"></path>
                            </svg>

                        </div>

                    </div>


                    <div class="value">
                        0
                    </div>

                    <div class="hint">
                        nenhum plano criado
                    </div>

                </div>



                {{-- EXERCÍCIOS --}}

                <div class="card">

                    <div class="card-top">

                        <h3>
                            Exercícios
                        </h3>

                        <div class="card-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 7v10"></path>
                                <path d="M8 5v14"></path>
                                <path d="M16 5v14"></path>
                                <path d="M20 7v10"></path>
                                <path d="M2 9h6"></path>
                                <path d="M16 9h6"></path>
                                <path d="M2 15h6"></path>
                                <path d="M16 15h6"></path>
                            </svg>

                        </div>

                    </div>


                    <div class="value">
                        0
                    </div>

                    <div class="hint">
                        biblioteca ainda vazia
                    </div>

                </div>



                {{-- PROFISSIONAIS --}}

                <div class="card">

                    <div class="card-top">

                        <h3>
                            Profissionais
                        </h3>

                        <div class="card-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="3"></circle>

                                <path d="M5 21a7 7 0 0 1 14 0"></path>
                            </svg>

                        </div>

                    </div>


                    <div class="value">
                        1
                    </div>

                    <div class="hint">
                        você
                    </div>

                </div>

            </div>



            {{-- =================================================
                 PAINÉIS
            ================================================== --}}

            <div class="panels">


                {{-- =================================================
                     PERFIL
                ================================================== --}}

                <div class="panel">

                    <div class="panel-header">

                        <h2>
                            Seu perfil
                        </h2>

                        <span class="panel-label">
                            Sessão ativa
                        </span>

                    </div>


                    <div class="profile-info">


                        <div class="profile-item">

                            <span>
                                Nome
                            </span>

                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>
                                E-mail
                            </span>

                            <strong>
                                {{ auth()->user()->email }}
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>
                                Perfil de acesso
                            </span>

                            <strong>
                                {{ ucwords(str_replace('_', ' ', auth()->user()->perfil ?? 'Não definido')) }}
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>
                                Membro desde
                            </span>

                            <strong>
                                {{ auth()->user()->created_at?->translatedFormat('d \d\e F \d\e Y') ?? 'Não informado' }}
                            </strong>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     SEGURANÇA
                ================================================== --}}

                <div class="panel">

                    <div class="panel-header">

                        <h2>
                            Segurança
                        </h2>

                    </div>


                    @if (auth()->user()->two_factor_confirmed_at)

                    <div class="security-status on">

                        <span class="status-dot on"></span>

                        Verificação em duas etapas ativada

                    </div>


                    <p class="security-desc">

                        Sua conta está protegida por um segundo fator
                        de autenticação.

                    </p>


                    <div class="security-details">

                        <div class="security-detail">

                            <span>
                                Status
                            </span>

                            <strong>
                                Protegida
                            </strong>

                        </div>


                        <div class="security-detail">

                            <span>
                                2FA
                            </span>

                            <strong>
                                Ativo
                            </strong>

                        </div>

                    </div>

                    @else

                    <div class="security-status off">

                        <span class="status-dot off"></span>

                        Verificação em duas etapas desativada

                    </div>


                    <p class="security-desc">

                        Adicione uma camada extra de proteção à sua conta
                        usando um aplicativo autenticador.

                    </p>


                    <div class="security-details">

                        <div class="security-detail">

                            <span>
                                Status
                            </span>

                            <strong>
                                Atenção necessária
                            </strong>

                        </div>


                        <div class="security-detail">

                            <span>
                                2FA
                            </span>

                            <strong>
                                Inativo
                            </strong>

                        </div>

                    </div>

                    @endif


                    <a href="{{ route('security.index') }}" class="btn-outline">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>

                            <path d="m9 12 2 2 4-4"></path>
                        </svg>

                        Gerenciar segurança

                    </a>

                </div>

            </div>



            {{-- =================================================
                 ACESSO RÁPIDO
            ================================================== --}}

            <section class="quick-actions">

                <div class="quick-header">

                    <div>

                        <h2>
                            Acesso rápido
                        </h2>

                        <p>
                            Atalhos para as principais áreas do Ecoa.
                        </p>

                    </div>

                    <span class="quick-label">
                        Próximas ações
                    </span>

                </div>


                <div class="quick-grid">


                    {{-- NOVA CRIANÇA --}}

                    <a href="#" class="quick-action">

                        <span class="quick-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="8" r="3"></circle>

                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>

                                <path d="M19 8v6"></path>

                                <path d="M16 11h6"></path>
                            </svg>

                        </span>


                        <span class="quick-text">

                            <strong>
                                Nova criança
                            </strong>

                            <span>
                                Iniciar acompanhamento
                            </span>

                        </span>


                        <span class="quick-arrow">
                            →
                        </span>

                    </a>



                    {{-- NOVO PLANO --}}

                    <a href="#" class="quick-action">

                        <span class="quick-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>

                                <path d="M14 2v6h6"></path>

                                <path d="M12 12v6"></path>

                                <path d="M9 15h6"></path>
                            </svg>

                        </span>


                        <span class="quick-text">

                            <strong>
                                Novo plano
                            </strong>

                            <span>
                                Criar plano terapêutico
                            </span>

                        </span>


                        <span class="quick-arrow">
                            →
                        </span>

                    </a>



                    {{-- EXERCÍCIOS --}}

                    <a href="#" class="quick-action">

                        <span class="quick-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 7v10"></path>
                                <path d="M8 5v14"></path>
                                <path d="M16 5v14"></path>
                                <path d="M20 7v10"></path>

                                <path d="M2 9h6"></path>
                                <path d="M16 9h6"></path>

                                <path d="M2 15h6"></path>
                                <path d="M16 15h6"></path>
                            </svg>

                        </span>


                        <span class="quick-text">

                            <strong>
                                Exercícios
                            </strong>

                            <span>
                                Consultar biblioteca
                            </span>

                        </span>


                        <span class="quick-arrow">
                            →
                        </span>

                    </a>


                </div>

            </section>


        </div>

    </main>



    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <footer>

        <div class="container">

            <div class="footer-row">


                <div class="footer-brand">

                    <img src="{{ asset('img/ecoa-icone.png') }}" alt="">

                    <span>
                        © {{ date('Y') }} Ecoa — Sistema de Acompanhamento Fonoaudiológico Infantil
                    </span>

                </div>


                <div class="footer-links">

                    <a href="{{ route('landing') }}">
                        Início
                    </a>

                    <a href="{{ route('home') }}">
                        Painel
                    </a>

                    <a href="{{ route('security.index') }}">
                        Segurança
                    </a>

                </div>

            </div>

        </div>

    </footer>



    {{-- =========================================================
         ACCOUNT MENU SCRIPT
    ========================================================== --}}

    <script>
    const accountMenu =
        document.getElementById('accountMenu');

    const accountButton =
        document.getElementById('accountButton');


    accountButton.addEventListener('click', function(event) {

        event.stopPropagation();

        const isOpen =
            accountMenu.classList.toggle('open');

        accountButton.setAttribute(
            'aria-expanded',
            isOpen ? 'true' : 'false'
        );

    });


    document.addEventListener('click', function(event) {

        if (!accountMenu.contains(event.target)) {

            accountMenu.classList.remove('open');

            accountButton.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    });


    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            accountMenu.classList.remove('open');

            accountButton.setAttribute(
                'aria-expanded',
                'false'
            );

            accountButton.focus();

        }

    });
    </script>

</body>

</html>