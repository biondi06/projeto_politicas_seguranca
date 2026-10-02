[1mdiff --git a/resources/css/app.css b/resources/css/app.css[m
[1mindex c4bc246..d7e7eb1 100644[m
[1m--- a/resources/css/app.css[m
[1m+++ b/resources/css/app.css[m
[36m@@ -1,10 +1,1339 @@[m
[31m-@import "bootstrap/dist/css/bootstrap.min.css";[m
[31m-@import 'tailwindcss';[m
[32m+[m[32m/* =========================================================[m
[32m+[m[32m   ECOA — Folha de estilo global[m
[32m+[m[32m   Compartilhada entre landing page, painel, segurança e demais[m
[32m+[m[32m   telas autenticadas. Antes vivia duplicada dentro de cada[m
[32m+[m[32m   arquivo .blade.php como <style> inline — centralizada aqui[m
[32m+[m[32m   para evitar repetição e facilitar manutenção (uma cor/ajuste[m
[32m+[m[32m   muda em um lugar só).[m
[32m+[m[32m========================================================= */[m
 [m
[31m-@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';[m
[31m-@source '../../storage/framework/views/*.php';[m
[32m+[m[32m:root {[m
[32m+[m[32m    --ink: #16231f;[m
[32m+[m[32m    --paper: #f4f6f2;[m
[32m+[m[32m    --paper-dim: #eceee8;[m
[32m+[m[32m    --white: #ffffff;[m
 [m
[31m-@theme {[m
[31m-    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',[m
[31m-        'Segoe UI Symbol', 'Noto Color Emoji';[m
[32m+[m[32m    --teal-950: #082522;[m
[32m+[m[32m    --teal-900: #0d3634;[m
[32m+[m[32m    --teal-850: #103c39;[m
[32m+[m[32m    --teal-800: #124542;[m
[32m+[m[32m    --teal-700: #1b5e5a;[m
[32m+[m[32m    --teal-600: #256f6a;[m
[32m+[m
[32m+[m[32m    --amber: #c6873a;[m
[32m+[m[32m    --amber-light: #e7c396;[m
[32m+[m
[32m+[m[32m    --sage: #b9cfc0;[m
[32m+[m
[32m+[m[32m    --line: rgba(22, 35, 31, 0.09);[m
[32m+[m[32m    --line-dark: rgba(244, 246, 242, 0.13);[m
[32m+[m
[32m+[m[32m    --muted: #68736d;[m
[32m+[m[32m    --muted-light: #8a938c;[m
[32m+[m
[32m+[m[32m    --danger: #b3452f;[m
[32m+[m[32m    --success: #2f7a4f;[m
[32m+[m
[32m+[m[32m    --shadow-sm: 0 4px 18px rgba(22, 35, 31, 0.045);[m
[32m+[m[32m    --shadow-md: 0 12px 32px rgba(22, 35, 31, 0.07);[m
[32m+[m[32m    --shadow-lg: 0 22px 55px rgba(22, 35, 31, 0.11);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m* {[m
[32m+[m[32m    margin: 0;[m
[32m+[m[32m    padding: 0;[m
[32m+[m[32m    box-sizing: border-box;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mhtml {[m
[32m+[m[32m    scroll-behavior: smooth;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mbody {[m
[32m+[m[32m    min-height: 100vh;[m
[32m+[m[32m    background: var(--paper);[m
[32m+[m[32m    color: var(--ink);[m
[32m+[m[32m    font-family: 'IBM Plex Sans', system-ui, sans-serif;[m
[32m+[m[32m    -webkit-font-smoothing: antialiased;[m
[32m+[m[32m    line-height: 1.5;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32ma {[m
[32m+[m[32m    color: inherit;[m
[32m+[m[32m    text-decoration: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mbutton {[m
[32m+[m[32m    font-family: inherit;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32ma:focus-visible,[m
[32m+[m[32mbutton:focus-visible {[m
[32m+[m[32m    outline: 2px solid var(--amber);[m
[32m+[m[32m    outline-offset: 3px;[m
[32m+[m[32m    border-radius: 6px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.container {[m
[32m+[m[32m    width: 100%;[m
[32m+[m[32m    max-width: 1200px;[m
[32m+[m[32m    margin: 0 auto;[m
[32m+[m[32m    padding: 0 32px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mh1,[m
[32m+[m[32mh2,[m
[32m+[m[32mh3 {[m
[32m+[m[32m    font-family: 'Newsreader', Georgia, serif;[m
[32m+[m[32m    font-weight: 500;[m
[32m+[m[32m    letter-spacing: -0.018em;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m/* =========================================================[m
[32m+[m[32m   HEADER[m
[32m+[m[32m========================================================= */[m
[32m+[m
[32m+[m[32mheader {[m
[32m+[m[32m    position: sticky;[m
[32m+[m[32m    top: 0;[m
[32m+[m[32m    z-index: 100;[m
[32m+[m[32m    background: rgba(13, 54, 52, 0.97);[m
[32m+[m[32m    backdrop-filter: blur(16px);[m
[32m+[m[32m    -webkit-backdrop-filter: blur(16px);[m
[32m+[m[32m    border-bottom: 1px solid var(--line-dark);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.nav {[m
[32m+[m[32m    min-height: 72px;[m
[32m+[m[32m    display: flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    justify-content: space-between;[m
[32m+[m[32m    gap: 24px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.wordmark {[m
[32m+[m[32m    display: inline-flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    gap: 10px;[m
[32m+[m[32m    color: var(--paper);[m
[32m+[m[32m    font-family: 'Newsreader', Georgia, serif;[m
[32m+[m[32m    font-size: 25px;[m
[32m+[m[32m    font-weight: 600;[m
[32m+[m[32m    flex-shrink: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.wordmark img {[m
[32m+[m[32m    width: 50px;[m
[32m+[m[32m    height: 50px;[m
[32m+[m[32m    object-fit: contain;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.wordmark span {[m
[32m+[m[32m    color: var(--amber);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.nav-right {[m
[32m+[m[32m    display: flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    gap: 10px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.security-link {[m
[32m+[m[32m    display: inline-flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    gap: 8px;[m
[32m+[m[32m    padding: 9px 13px;[m
[32m+[m[32m    border: 1px solid transparent;[m
[32m+[m[32m    border-radius: 9px;[m
[32m+[m[32m    color: rgba(244, 246, 242, 0.76);[m
[32m+[m[32m    font-size: 13.5px;[m
[32m+[m[32m    font-weight: 500;[m
[32m+[m[32m    transition: background .2s ease, color .2s ease, border-color .2s ease;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.security-link:hover {[m
[32m+[m[32m    color: var(--paper);[m
[32m+[m[32m    background: rgba(255, 255, 255, 0.06);[m
[32m+[m[32m    border-color: rgba(255, 255, 255, 0.10);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.security-link svg {[m
[32m+[m[32m    width: 16px;[m
[32m+[m[32m    height: 16px;[m
[32m+[m[32m    opacity: .9;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m/* ACCOUNT MENU */[m
[32m+[m
[32m+[m[32m.account {[m
[32m+[m[32m    position: relative;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.account-button {[m
[32m+[m[32m    display: flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    gap: 10px;[m
[32m+[m[32m    padding: 5px 7px 5px 5px;[m
[32m+[m[32m    background: transparent;[m
[32m+[m[32m    border: 1px solid transparent;[m
[32m+[m[32m    border-radius: 11px;[m
[32m+[m[32m    color: var(--paper);[m
[32m+[m[32m    cursor: pointer;[m
[32m+[m[32m    transition: background .2s ease, border-color .2s ease;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.account-button:hover {[m
[32m+[m[32m    background: rgba(255, 255, 255, 0.07);[m
[32m+[m[32m    border-color: rgba(255, 255, 255, 0.10);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.account-avatar {[m
[32m+[m[32m    width: 36px;[m
[32m+[m[32m    height: 36px;[m
[32m+[m[32m    display: flex;[m
[32m+[m[32m    align-items: center;[m
[32m+[m[32m    justify-content: center;[m
[32m+[m[32m    border-radius: 50%;[m
[32m+[m[32m    background: var(--amber);[m
[32m+[m[32m    color: #241505;[m
[32m+[m[32m    font-size: 14px;[m
[32m+[m[32m    font-weight: 700;[m
[32m+[m[32m    box-shadow: 0 0 0 3px rgba(198, 135, 58, 0.13);[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.account-info {[m
[32m+[m[32m    display: flex;[m
[32m+[m[32m    flex-direction: column;[m
[32m+[m[32m    align-items: flex-start;[m
[32m+[m[32m    line-height: 1.15;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m.account-name {[m
[32m+[m[32m    max-width: 15