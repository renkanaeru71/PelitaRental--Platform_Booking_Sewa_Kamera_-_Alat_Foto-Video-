<?php
declare(strict_types=1);

$categories = [
    ['id' => 'kamera', 'name' => 'Kamera', 'icon' => 'camera'],
    ['id' => 'lensa', 'name' => 'Lensa', 'icon' => 'lens'],
    ['id' => 'lighting', 'name' => 'Lighting', 'icon' => 'bolt'],
    ['id' => 'aksesori', 'name' => 'Aksesori', 'icon' => 'tools'],
];

$products = [
    ['id' => 1, 'category' => 'kamera', 'type' => 'Kamera Mirrorless', 'name' => 'Sony Alpha A7 III', 'specs' => '24.2MP · 4K HDR · 5-Axis IBIS', 'price' => 350000, 'stock' => 5, 'rating' => '4.9', 'icon' => 'camera', 'label' => 'FULL FRAME', 'description' => 'Kamera mirrorless full-frame untuk foto dan video dengan detail tajam, autofocus cepat, dan stabilisasi 5-axis.', 'included' => 'Body kamera, baterai, charger, strap, dan tas kamera. Lensa disewa terpisah.'],
    ['id' => 2, 'category' => 'lighting', 'type' => 'Lighting Studio', 'name' => 'Godox AD200 Pro', 'specs' => '200Ws Pocket Flash · HSS · TTL', 'price' => 120000, 'stock' => 4, 'rating' => '4.8', 'icon' => 'bolt', 'label' => 'STROBE TTL', 'description' => 'Flash portabel 200Ws dengan dukungan TTL dan high-speed sync untuk kebutuhan foto studio maupun outdoor.', 'included' => 'Unit flash, baterai, charger, flash head, dan carrying case.'],
    ['id' => 3, 'category' => 'lensa', 'type' => 'Lensa Prime', 'name' => 'Sigma 35mm f/1.4 Art', 'specs' => 'Ultra Sharp Bokeh · Sony E', 'price' => 95000, 'stock' => 1, 'rating' => '4.9', 'icon' => 'lens', 'label' => 'SONY E-MOUNT', 'description' => 'Lensa prime 35mm berbukaan f/1.4 untuk portrait, dokumenter, dan kondisi minim cahaya. Kompatibel dengan kamera Sony E-mount.', 'included' => 'Lensa, lens hood, front cap, rear cap, dan pouch.'],
    ['id' => 4, 'category' => 'kamera', 'type' => 'Cinema Line', 'name' => 'Sony FX30 Cinema', 'specs' => 'Dual Base ISO · XLR Top Handle', 'price' => 375000, 'stock' => 0, 'rating' => '5.0', 'icon' => 'video', 'label' => 'SUPER 35MM', 'description' => 'Kamera cinema Super 35 dengan perekaman 4K dan XLR top handle untuk produksi film pendek dan konten profesional.', 'included' => 'Body kamera, XLR top handle, baterai, charger, dan tas kamera.'],
];

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function icon(string $name, string $class = ''): string
{
    return '<svg class="icon ' . escape($class) . '" aria-hidden="true"><use href="#icon-' . escape($name) . '"></use></svg>';
}

function rupiah(int $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}

?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff7718">
    <meta name="description" content="PelitaRental — sewa kamera dan perlengkapan foto video untuk kebutuhan kreatifmu di Yogyakarta.">
    <title>Beranda — PelitaRental</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23ff7718'/%3E%3Cg fill='none' stroke='white' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 14h16l1 17H11l1-17Z'/%3E%3Cpath d='M16 15v-4a4 4 0 0 1 8 0v4m-8 7h8m-4-4v8'/%3E%3C/g%3E%3C/svg%3E">
    <script>
        (() => {
            let theme;
            try { theme = localStorage.getItem('pelita-theme'); } catch { theme = null; }
            document.documentElement.dataset.bsTheme = theme === 'dark' ? 'dark' : 'light';
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        :root,
        [data-bs-theme="light"] {
            --page-bg: #f8f9fb;
            --surface: #ffffff;
            --text: #26303f;
            --muted: #8993a2;
            --faint: #a5aebb;
            --line: #e9ecf1;
            --soft: #f1f4f8;
            --orange: #ff7718;
            --orange-hover: #ea6610;
            --orange-soft: #fff6ee;
            --sidebar-width: 248px;
            --header-height: 73px;
            --bs-body-font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            --bs-body-color: var(--text);
            --bs-body-bg: var(--page-bg);
            --bs-border-color: var(--line);
            --bs-primary: var(--orange);
            --bs-primary-rgb: 255, 119, 24;
        }

        [data-bs-theme="dark"] {
            --page-bg: #141820;
            --surface: #1c222c;
            --text: #e8edf5;
            --muted: #9ba7b8;
            --faint: #7e8ba0;
            --line: #2b3442;
            --soft: #232c39;
            --orange-soft: #34271e;
            --bs-body-bg: var(--page-bg);
            --bs-body-color: var(--text);
            --bs-border-color: var(--line);
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-size: 13px; font-weight: 500; -webkit-font-smoothing: antialiased; }
        button, input { font: inherit; }
        button, a, input { -webkit-tap-highlight-color: transparent; }
        button { touch-action: manipulation; }
        button:focus-visible, a:focus-visible { outline: 3px solid #ff771880; outline-offset: 4px; }
        [hidden] { display: none !important; }
        .svg-sprite { position: absolute; width: 0; height: 0; overflow: hidden; }
        .icon { width: 18px; height: 18px; display: inline-block; fill: none; stroke: currentColor; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
        .skip-link { position: fixed; left: 20px; top: -80px; padding: 12px 20px; background: var(--surface); color: var(--orange); z-index: 2000; border: 1px solid var(--orange); border-radius: 8px; }
        .skip-link:focus { top: 12px; }

        .sidebar { --bs-offcanvas-width: var(--sidebar-width); position: fixed; top: 0; bottom: 0; left: 0; width: var(--sidebar-width); background: var(--surface) !important; border-right: 1px solid var(--line) !important; z-index: 1045; }
        .brand { height: var(--header-height); padding: 0 21px; display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); border-bottom: 1px solid var(--line); }
        .brand-mark { display: grid; place-items: center; width: 32px; height: 32px; background: var(--orange); color: white; border-radius: 8px; box-shadow: 0 3px 8px #ff771817; }
        .brand-mark .icon { width: 19px; height: 19px; stroke-width: 1.7; }
        .brand-copy { display: flex; flex-direction: column; gap: 1px; }
        .brand-copy strong { font-size: 14px; font-weight: 800; letter-spacing: -.4px; }
        .brand-copy small { font-size: 10px; color: var(--muted); }
        .sidebar-nav { padding: 19px 13px; }
        .nav-label { color: var(--muted); font-size: 10px; text-transform: uppercase; letter-spacing: .65px; margin: 0 8px 8px; }
        .category-nav-label { margin-top: 30px; }
        .nav-item { display: flex; width: 100%; gap: 10px; align-items: center; min-height: 37px; padding: 9px 9px; margin-bottom: 3px; text-align: left; color: #697485; border: 0; background: transparent; border-radius: 6px; font-size: 12px; transition: background .15s, color .15s; }
        [data-bs-theme="dark"] .nav-item { color: var(--muted); }
        .nav-item > .icon { width: 15px; height: 15px; color: #a3acb8; stroke-width: 1.4; }
        .nav-item:hover { background: var(--soft); color: var(--text); }
        .nav-item.is-active { background: var(--orange-soft); color: #e67427; }
        .nav-item.is-active > .icon { color: var(--orange); }
        .nav-item.is-active > .icon:has(use[href="#icon-grid"]) { fill: var(--orange); stroke: none; }
        .active-dot { margin-left: auto; height: 5px; width: 5px; border-radius: 50%; background: var(--orange); display: none; }
        .is-active .active-dot { display: inline-block; }
        .sidebar-close { position: absolute; top: 22px; right: 12px; }

        .workspace { margin-left: var(--sidebar-width); min-height: 100dvh; }
        .topbar { height: var(--header-height); background: var(--surface); border-bottom: 1px solid var(--line); display: flex; align-items: center; gap: 20px; padding: 0 32px; }
        .search-wrap { position: relative; width: min(470px, 53%); display: flex; align-items: center; }
        .search-wrap > .icon { position: absolute; left: 12px; width: 15px; height: 15px; color: var(--faint); pointer-events: none; }
        .search-wrap input { background: var(--page-bg); border: 1px solid var(--line); border-radius: 7px; width: 100%; padding: 9px 59px 9px 33px; height: 33px; font-size: 11px; color: var(--text); outline: none; }
        .search-wrap input::placeholder { color: var(--faint); }
        .search-wrap input:focus { border-color: #ffad72; box-shadow: 0 0 0 3px #ff771810; }
        .search-shortcut { color: var(--faint); background: var(--surface); border: 1px solid var(--line); border-radius: 4px; font-size: 8px; padding: 2px 5px; position: absolute; right: 8px; font-family: inherit; }
        .header-actions { display: flex; align-items: center; gap: 12px; margin-left: auto; }
        .icon-button { display: inline-flex; align-items: center; justify-content: center; height: 32px; min-width: 32px; padding: 7px; color: #7e8999; background: var(--surface); border: 1px solid var(--line); border-radius: 6px; transition: background .15s, color .15s; }
        .icon-button .icon { width: 16px; height: 16px; }
        .icon-button:hover { background: var(--soft); color: var(--orange); }
        .cart-toggle { gap: 5px; font-size: 10px; padding-left: 9px; padding-right: 9px; }
        .header-divider { width: 1px; height: 23px; background: var(--line); margin: 0 6px; }
        .user-menu { display: flex; align-items: center; gap: 10px; border: 0; background: none; padding: 0; color: var(--text); text-align: left; }
        .avatar { display: grid; place-items: center; height: 33px; width: 33px; flex-shrink: 0; border-radius: 50%; color: white; background: #5546e9; font-size: 11px; font-weight: 700; }
        .user-copy { display: flex; flex-direction: column; min-width: 72px; }
        .user-copy strong { font-size: 11px; font-weight: 750; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-copy small { font-size: 10px; color: var(--muted); }
        .user-chevron { width: 12px; height: 12px; color: var(--muted); margin-left: 3px; }
        .dropdown-menu { --bs-dropdown-bg: var(--surface); --bs-dropdown-color: var(--text); --bs-dropdown-link-color: var(--text); --bs-dropdown-link-hover-bg: var(--soft); --bs-dropdown-link-hover-color: var(--text); --bs-dropdown-border-color: var(--line); box-shadow: 0 8px 28px #11182712; font-size: 12px; padding: 8px; min-width: 205px; }
        .dropdown-item { padding: 9px 12px; border-radius: 5px; }
        .dropdown-header { color: var(--muted); font-size: 10px; }

        .main-content { padding: 34px 32px 20px; max-width: 1560px; margin: 0 auto; outline: none; }
        .page-heading { margin-bottom: 30px; }
        .page-heading h1 { font-size: 21px; letter-spacing: -.8px; font-weight: 800; margin: 0 0 6px; line-height: 1.4; }
        .page-heading > p { font-size: 11.5px; color: var(--muted); margin: 0; line-height: 1.7; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; margin-bottom: 33px; }
        .stat-card { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 20px 19px 17px; min-width: 0; }
        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .stat-top h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #838d9d; letter-spacing: .55px; margin: 0; line-height: 1.5; }
        .stat-icon { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 7px; flex-shrink: 0; }
        .stat-icon .icon { width: 16px; height: 16px; }
        .blue { background: #f0f6ff; color: #5896ed; }
        .amber { background: #fffbef; color: #ceab55; }
        .green { background: #edfdf5; color: #1fb589; }
        [data-bs-theme="dark"] .blue { background: #233248; }
        [data-bs-theme="dark"] .amber { background: #373125; }
        [data-bs-theme="dark"] .green { background: #1b3930; }
        .stat-value { font-size: 27px; line-height: 1.1; display: block; font-weight: 800; letter-spacing: -.7px; margin: 14px 0 15px; }
        .stat-card > p { border-top: 1px solid var(--line); font-size: 11px; color: var(--muted); margin: 0; padding-top: 12px; line-height: 1.5; }
        .section-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .section-title { display: flex; align-items: center; gap: 9px; }
        .section-title h2 { font-size: 13px; font-weight: 800; letter-spacing: .05px; margin: 0; text-transform: uppercase; line-height: 1.5; }
        .section-note { color: #7c8797; font-size: 10px; background: var(--soft); border-radius: 3px; padding: 3px 6px; }
        .text-button { background: none; border: 0; color: #db6b23; font-size: 11px; white-space: nowrap; padding: 4px 0 4px 8px; display: inline-flex; align-items: center; gap: 3px; }
        .text-button .icon { width: 12px; height: 12px; }
        .text-button:hover { color: var(--orange); text-decoration: underline; }
        .products-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }
        .product-card { min-width: 0; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 15px; transition: border-color .2s, box-shadow .2s, transform .2s; }
        .product-card:hover { border-color: #ffc79e; box-shadow: 0 6px 20px #26303f06; transform: translateY(-2px); }
        .product-visual { position: relative; height: 142px; background: var(--soft); border-radius: 7px; margin-bottom: 14px; display: grid; place-items: center; }
        .stock-badge { position: absolute; right: 10px; top: 10px; border-radius: 3px; border: 1px solid transparent; padding: 3px 7px; line-height: 1.3; font-size: 9px; font-weight: 600; }
        .available { background: #edfbf5; color: #399e7f; border-color: #d2f1e5; }
        .limited { background: #fffae9; color: #b59146; border-color: #f9efc9; }
        .empty { background: #e9eef4; color: #929caa; }
        [data-bs-theme="dark"] .available { background: #1f3a31; border-color: #285441; color: #72d6ac; }
        [data-bs-theme="dark"] .limited { background: #3b3320; border-color: #514323; color: #e6c37d; }
        [data-bs-theme="dark"] .empty { background: #303a4a; color: #a9b4c6; }
        .product-placeholder { color: #a4afbf; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; padding-top: 9px; }
        .product-placeholder > .icon { width: 29px; height: 29px; stroke-width: 1.6; }
        .product-placeholder > span { font-size: 8px; letter-spacing: 1.05px; font-weight: 500; }
        .product-type { display: block; color: var(--faint); font-size: 9.5px; text-transform: uppercase; margin-bottom: 4px; }
        .product-info h3 { font-size: 13px; font-weight: 800; letter-spacing: -.35px; line-height: 1.45; margin: 0 0 3px; }
        .product-specs { color: var(--muted); font-size: 10.5px; line-height: 1.6; margin: 0; min-height: 18px; }
        .product-price-row { display: flex; align-items: center; justify-content: space-between; gap: 3px; border-top: 1px solid var(--line); margin-top: 16px; padding-top: 11px; margin-bottom: 11px; }
        .product-price-row p { margin: 0; white-space: nowrap; }
        .product-price-row strong { font-size: 14px; font-weight: 800; letter-spacing: -.4px; }
        .product-price-row p > span { color: var(--faint); font-size: 9px; margin-left: 2px; }
        .rating { display: flex; align-items: center; gap: 2px; font-size: 9px; color: var(--faint); }
        .rating .icon { width: 11px; height: 11px; fill: #ffc33c; stroke: #ffc33c; }
        .product-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .btn { --bs-btn-font-size: 12px; --bs-btn-font-weight: 600; --bs-btn-border-radius: 6px; --bs-btn-padding-x: 16px; --bs-btn-padding-y: 9px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; }
        .btn-primary { --bs-btn-bg: var(--orange); --bs-btn-border-color: var(--orange); --bs-btn-hover-bg: var(--orange-hover); --bs-btn-hover-border-color: var(--orange-hover); --bs-btn-active-bg: #d75a08; --bs-btn-active-border-color: #d75a08; --bs-btn-focus-shadow-rgb: 255, 119, 24; --bs-btn-disabled-bg: var(--soft); --bs-btn-disabled-color: #a5afbd; --bs-btn-disabled-border-color: var(--line); --bs-btn-disabled-opacity: 1; color: white; }
        .btn-outline-secondary { --bs-btn-color: #657183; --bs-btn-border-color: #dfe4eb; --bs-btn-hover-bg: var(--soft); --bs-btn-hover-border-color: #cbd2dc; --bs-btn-hover-color: var(--text); --bs-btn-active-bg: var(--soft); --bs-btn-active-color: var(--text); --bs-btn-active-border-color: var(--line); --bs-btn-focus-shadow-rgb: 150, 160, 175; background: var(--surface); }
        [data-bs-theme="dark"] .btn-outline-secondary { --bs-btn-color: #b7c1cf; --bs-btn-border-color: var(--line); }
        .product-actions .btn { min-height: 31px; padding: 6px 4px; font-size: 10.5px; line-height: 1.4; gap: 0; }
        .catalog-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .category-tabs { display: flex; align-items: center; gap: 8px; min-width: 0; overflow-x: auto; padding: 3px; margin: -3px; scrollbar-width: thin; scrollbar-color: #ffc79e transparent; }
        .category-tabs::-webkit-scrollbar { height: 4px; }
        .category-tabs::-webkit-scrollbar-thumb { background: #ffc79e; border-radius: 4px; }
        .category-tab { display: inline-flex; align-items: center; justify-content: center; gap: 7px; flex-shrink: 0; padding: 9px 13px; min-height: 39px; color: var(--muted); background: var(--surface); border: 1px solid var(--line); border-radius: 7px; font-size: 11px; font-weight: 650; transition: color .15s, background .15s, border-color .15s, box-shadow .15s; }
        .category-tab > .icon { width: 15px; height: 15px; }
        .category-tab:hover { color: var(--orange); border-color: #ffbf91; }
        .category-tab.is-active { background: var(--orange); border-color: var(--orange); color: #fff; box-shadow: 0 3px 8px #ff77181a; }
        .tab-count { display: inline-flex; align-items: center; justify-content: center; min-width: 19px; height: 19px; padding: 0 4px; font-size: 9px; border-radius: 4px; background: var(--soft); color: var(--muted); }
        .category-tab.is-active .tab-count { background: #ffffff26; color: #fff; }
        .catalog-count { flex-shrink: 0; color: var(--muted); font-size: 10px; }
        #products-heading { scroll-margin-top: 24px; }
        .footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; border-top: 1px solid var(--line); margin-top: 32px; padding: 23px 0 10px; font-size: 10px; color: var(--muted); }
        .footer-links { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer button { background: none; border: 0; color: inherit; font-size: inherit; padding: 0; text-align: left; }
        .footer button:hover { color: var(--orange); }
        .footer-dot { color: #b9c1cd; }
        .system-status { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
        .system-status > span { width: 6px; height: 6px; border-radius: 50%; background: #25b188; }

        .empty-state { text-align: center; padding: 45px 20px; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; }
        .empty-icon { display: grid; place-items: center; width: 65px; height: 65px; margin: 0 auto 20px; background: var(--orange-soft); color: var(--orange); border-radius: 18px; }
        .empty-icon .icon { width: 28px; height: 28px; }
        .empty-state h2, .empty-state h3 { font-size: 18px; font-weight: 800; letter-spacing: -.4px; margin-bottom: 10px; }
        .empty-state p { color: var(--muted); line-height: 1.8; font-size: 12px; margin-bottom: 20px; }
        .page-empty { margin-top: 20px; padding: 80px 20px; }
        .profile-card { max-width: 580px; padding: 28px; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; }
        .profile-intro { display: flex; align-items: center; gap: 15px; margin-bottom: 28px; }
        .avatar-large { width: 52px; height: 52px; font-size: 17px; }
        .profile-intro h2 { font-size: 16px; font-weight: 800; margin: 0 0 4px; }
        .profile-intro p { font-size: 11px; color: var(--muted); margin: 0; }
        .profile-note { color: var(--muted); font-size: 11px; line-height: 1.8; }
        .profile-feedback { margin: 16px 0 0; color: #24a279; font-size: 12px; }
        .form-label { font-size: 12px; font-weight: 700; }
        .form-control { background: var(--surface); color: var(--text); border-color: var(--line); font-size: 13px; min-height: 42px; }
        .form-control:focus { background: var(--surface); color: var(--text); border-color: #ffad72; box-shadow: 0 0 0 3px #ff77181a; }
        .form-control::placeholder { color: var(--faint); }
        .modal-content { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 20px 80px #11182720; }
        .modal-header, .modal-footer { border-color: var(--line); padding: 20px 24px; }
        .modal-title { font-size: 18px; font-weight: 800; }
        .modal-body { padding: 24px; line-height: 1.8; color: var(--muted); }
        .eyebrow { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); margin-bottom: 5px; }
        .detail-visual { background: var(--soft); color: var(--faint); display: grid; place-items: center; height: 160px; border-radius: 9px; margin-bottom: 18px; }
        .detail-visual .icon { width: 54px; height: 54px; stroke-width: 1.2; }
        .detail-specs { font-size: 11px; color: var(--orange); }
        .small-heading { font-size: 12px; color: var(--text); font-weight: 800; }
        .detail-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 22px; gap: 12px; }
        .detail-bottom > strong { color: var(--text); font-size: 19px; }
        .detail-bottom > span { font-size: 11px; }
        .muted-note { font-size: 10.5px; color: var(--muted); margin-top: 10px; margin-bottom: 0; line-height: 1.7; }
        .cart-panel { --bs-offcanvas-width: 445px; --bs-offcanvas-bg: var(--surface); border-color: var(--line); }
        .cart-panel .offcanvas-header { border-bottom: 1px solid var(--line); padding: 24px; }
        .cart-panel h2 { font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -.5px; }
        .cart-panel .offcanvas-body { padding: 24px; }
        .cart-panel .empty-state { border: 0; padding-top: 70px; }
        .cart-item { display: flex; align-items: flex-start; gap: 12px; padding: 16px 0; border-bottom: 1px solid var(--line); }
        .cart-item:first-child { padding-top: 0; }
        .cart-item-visual { display: grid; place-items: center; width: 62px; height: 66px; flex-shrink: 0; border-radius: 8px; background: var(--soft); color: var(--faint); }
        .cart-item-visual .icon { width: 25px; height: 25px; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-info h3 { font-size: 12px; font-weight: 800; margin: 2px 0 5px; }
        .cart-item-info > p { font-size: 11px; color: var(--muted); margin-bottom: 10px; }
        .quantity-control { display: inline-flex; align-items: center; border: 1px solid var(--line); border-radius: 5px; }
        .quantity-control button { width: 29px; height: 27px; background: transparent; color: var(--text); border: 0; font-size: 15px; }
        .quantity-control button:hover:not(:disabled) { background: var(--soft); }
        .quantity-control button:disabled { opacity: .3; cursor: not-allowed; }
        .quantity-control span { display: inline-block; text-align: center; min-width: 26px; font-size: 11px; }
        .remove-item { border: 0; margin-top: -4px; }
        .rental-dates { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px; }
        .rental-dates > div { min-width: 0; }
        .rental-dates input { width: 100%; min-width: 0; padding: 9px; font-size: 11px; }
        .date-error { color: #db4f4f; margin: 10px 0 0; font-size: 11px; line-height: 1.7; }
        .rental-total { margin: 25px 0 20px; }
        .rental-total p { display: flex; justify-content: space-between; color: var(--muted); font-size: 12px; margin-bottom: 13px; }
        .rental-total p strong { color: var(--text); font-weight: 650; }
        .rental-total > div { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--line); padding-top: 17px; margin-top: 19px; font-size: 13px; font-weight: 700; }
        .rental-total > div strong { font-size: 23px; font-weight: 800; color: var(--orange); letter-spacing: -.7px; }
        .notice { display: flex; align-items: flex-start; gap: 10px; background: var(--orange-soft); color: #a97951; padding: 14px; border-radius: 8px; margin-bottom: 20px; }
        .notice .icon { width: 16px; height: 16px; margin-top: 2px; }
        .notice p { font-size: 11px; line-height: 1.8; margin: 0; }
        [data-bs-theme="dark"] .notice { color: #d3a57f; }
        .terms-content li { margin-bottom: 12px; padding-left: 5px; }
        .terms-content ol { padding-left: 20px; }
        .toast { --bs-toast-bg: var(--surface); --bs-toast-border-color: var(--line); --bs-toast-max-width: 380px; box-shadow: 0 8px 35px #11182715; }
        .toast-body { display: flex; align-items: center; gap: 12px; padding: 13px 15px; color: var(--text); font-size: 12px; }
        .toast-icon { color: #24ae85; }
        .toast-body > .icon-button { margin-left: auto; border: 0; }
        #print-sheet { display: none; }
        .noscript-notice { position: fixed; bottom: 0; left: 0; right: 0; padding: 16px; background: #fff3cd; color: #664d03; text-align: center; z-index: 2000; }

        @media (min-width: 1600px) {
            .main-content { padding-top: 42px; }
            .product-visual { height: 190px; }
            .product-info h3 { font-size: 15px; }
            .product-specs { font-size: 12px; }
        }
        @media (max-width: 1250px) and (min-width: 992px) {
            :root { --sidebar-width: 218px; }
            .topbar { padding: 0 26px; }
            .main-content { padding: 30px 26px 18px; }
            .stats-grid, .products-grid { gap: 16px; }
            .category-tab { padding: 8px 10px; font-size: 10px; gap: 5px; }
            .category-tabs { gap: 6px; }
            .product-card { padding: 12px; }
            .product-visual { height: 130px; }
            .product-price-row strong { font-size: 12px; }
            .product-info h3 { font-size: 11.5px; }
            .product-specs { font-size: 9px; min-height: 29px; }
            .product-actions { gap: 5px; }
            .product-actions .btn { font-size: 9.5px; }
            .footer { font-size: 9px; }
            .footer-links { gap: 7px; }
            .search-wrap { width: min(420px, 49%); }
        }
        @media (max-width: 991.98px) {
            :root { --header-height: 68px; }
            .workspace { margin-left: 0; }
            .sidebar { --bs-offcanvas-width: 265px; }
            .brand { padding-right: 48px; }
            .topbar { padding: 0 24px; gap: 16px; }
            .search-wrap { flex: 1; width: auto; max-width: 470px; }
            .header-actions { gap: 9px; }
            .main-content { padding: 30px 24px 20px; }
            .products-grid { gap: 16px; }
            .product-card { padding: 12px; }
            .product-price-row strong { font-size: 12px; }
            .product-info h3 { font-size: 12px; }
            .product-specs { font-size: 10px; min-height: 32px; }
            .product-actions { gap: 6px; }
            .catalog-count { display: none; }
            .footer { flex-wrap: wrap; }
        }
        @media (max-width: 767.98px) {
            .topbar { height: auto; min-height: 108px; padding: 13px 20px; gap: 12px; flex-wrap: wrap; }
            .search-wrap { order: 3; flex-basis: 100%; max-width: none; }
            .search-wrap input { height: 34px; }
            .header-actions { gap: 10px; }
            .header-divider { margin: 0 4px; }
            .main-content { padding: 27px 20px 16px; }
            .page-heading h1 { font-size: 21px; }
            .page-heading p { font-size: 11px; max-width: 430px; }
            .page-heading { margin-bottom: 23px; }
            .stats-grid { gap: 10px; margin-bottom: 28px; }
            .stat-card { padding: 13px 12px; border-radius: 10px; }
            .stat-top { align-items: flex-start; }
            .stat-top h2 { font-size: 8px; letter-spacing: .1px; min-height: 26px; }
            .stat-icon { width: 24px; height: 24px; border-radius: 5px; }
            .stat-icon .icon { width: 12px; height: 12px; }
            .stat-value { font-size: 24px; margin: 12px 0; }
            .stat-card > p { font-size: 9px; padding-top: 10px; min-height: 38px; }
            .products-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .product-card { padding: 14px; }
            .product-visual { height: 160px; }
            .product-info h3 { font-size: 13px; }
            .product-specs { font-size: 10.5px; min-height: 32px; }
            .product-price-row strong { font-size: 14px; }
            .product-actions .btn { min-height: 35px; font-size: 11px; }
            .section-note { display: none; }
            .section-title h2 { font-size: 12px; }
            .section-heading { margin-bottom: 14px; }
            .category-tab { padding: 9px 12px; }
            .catalog-toolbar { margin-bottom: 17px; }
            .footer { margin-top: 28px; padding-top: 20px; gap: 14px; font-size: 9px; }
            .footer-links { gap: 8px; }
            .footer-links .footer-dot:last-of-type { display: none; }
            .footer-links > span:last-child { width: 100%; }
            .page-empty { padding: 60px 20px; }
        }
        @media (max-width: 399.98px) {
            .topbar { padding: 12px 15px; }
            .main-content { padding-left: 15px; padding-right: 15px; }
            .user-copy { min-width: 65px; }
            .user-chevron { display: none; }
            .header-actions { gap: 8px; }
            .header-divider { margin: 0; }
            .page-heading h1 { font-size: 19px; }
            .stats-grid { gap: 7px; }
            .stat-card { padding: 12px 9px; }
            .stat-top { gap: 3px; }
            .stat-top h2 { font-size: 7.5px; }
            .stat-icon { width: 20px; height: 20px; }
            .products-grid { gap: 10px; }
            .product-card { padding: 10px; }
            .product-visual { height: 130px; }
            .product-info h3 { font-size: 11px; }
            .product-specs { font-size: 9px; }
            .product-price-row strong { font-size: 11.5px; }
            .product-price-row p > span, .rating { font-size: 8px; }
            .product-actions .btn { font-size: 9px; }
            .category-tab { padding: 8px 10px; font-size: 10px; }
            .cart-panel .offcanvas-body, .cart-panel .offcanvas-header { padding: 19px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
        @media print {
            @page { margin: 20mm; }
            body { background: white !important; color: #111 !important; }
            body > :not(#print-sheet) { display: none !important; }
            #print-sheet { display: block; font: 12px/1.7 Arial, sans-serif; color: #111; }
            #print-sheet h1 { font-size: 26px; }
            #print-sheet h2 { font-size: 18px; margin-top: 25px; }
            #print-sheet table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            #print-sheet th, #print-sheet td { text-align: left; border-bottom: 1px solid #ddd; padding: 10px 5px; }
            #print-sheet .print-total { text-align: right; font-size: 17px; font-weight: bold; }
            #print-sheet .print-notice { border: 1px solid #ccc; padding: 15px; margin-top: 30px; }
        }
    </style>
    <script id="product-data" type="application/json"><?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="svg-sprite" aria-hidden="true">
    <symbol id="icon-bag" viewBox="0 0 24 24"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2M9 13h6m-3-3v6"/></symbol>
    <symbol id="icon-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
    <symbol id="icon-orders" viewBox="0 0 24 24"><path d="M5 8h14l1 13H4L5 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2m-8 5 3 3 5-5"/></symbol>
    <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="icon-profile" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M18 5h3m-3 6h3m-3 6h3"/></symbol>
    <symbol id="icon-camera" viewBox="0 0 24 24"><path d="m8 5 1.5-2h5L16 5h4a2 2 0 0 1 2 2v12H2V7a2 2 0 0 1 2-2h4Z"/><circle cx="12" cy="12" r="4"/><path d="M5 8h.01"/></symbol>
    <symbol id="icon-lens" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol>
    <symbol id="icon-bolt" viewBox="0 0 24 24"><path d="m14 2-10 12h7l-1 8L20 9h-7l1-7Z"/></symbol>
    <symbol id="icon-video" viewBox="0 0 24 24"><rect x="2" y="10" width="13" height="12" rx="2"/><path d="m15 14 7-4v12l-7-4"/><circle cx="5.5" cy="5.5" r="3.5"/><circle cx="13" cy="5.5" r="3.5"/></symbol>
    <symbol id="icon-tools" viewBox="0 0 24 24"><path d="m14.5 6 3.5 3.5m-6 4-8 8-2-2 8-8M14 3a6 6 0 0 0 7 7l-4-4 1-3a6 6 0 0 0-4 0ZM3 3l4 2 1 3 13 13-2 1L6 9 3 8V3Z"/></symbol>
    <symbol id="icon-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="7"/><path d="m16 16 5 5"/></symbol>
    <symbol id="icon-cart" viewBox="0 0 24 24"><path d="M2 3h3l3 12h11l3-9H6M8 15l-1 3h13"/><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/></symbol>
    <symbol id="icon-moon" viewBox="0 0 24 24"><path d="M20.5 14.5A9 9 0 0 1 9.5 3.5a9 9 0 1 0 11 11Z"/><path d="M18 2v4m-2-2h4"/></symbol>
    <symbol id="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></symbol>
    <symbol id="icon-chevron" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
    <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-6-6 6 6-6 6"/></symbol>
    <symbol id="icon-check" viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"/><path d="m12 2 3 2 4 1 1 4 2 3-2 3-1 4-4 1-3 2-3-2-4-1-1-4-2-3 2-3 1-4 4-1 3-2Z"/></symbol>
    <symbol id="icon-star" viewBox="0 0 24 24"><path d="m12 2 3 6.5 7 1-5 5 1 7-6-3.5L6 21l1-6.5-5-5 7-1L12 2Z"/></symbol>
    <symbol id="icon-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="icon-close" viewBox="0 0 24 24"><path d="m6 6 12 12M6 18 18 6"/></symbol>
    <symbol id="icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6m10-6v6M3 11h18m-14 5h3m4 0h3"/></symbol>
    <symbol id="icon-trash" viewBox="0 0 24 24"><path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/></symbol>
    <symbol id="icon-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v.01"/></symbol>
    <symbol id="icon-print" viewBox="0 0 24 24"><path d="M6 8V2h12v6M6 17H3V8h18v9h-3M6 14h12v8H6v-8Zm11-3h1"/></symbol>
</svg>
<a class="skip-link" href="#main-content">Lewati ke konten utama</a>

<aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Navigasi utama">
    <a class="brand" href="index.php" aria-label="PelitaRental beranda">
        <span class="brand-mark"><?= icon('bag') ?></span>
        <span class="brand-copy"><strong>PelitaRental</strong><small>Sewa Gear Mahasiswa</small></span>
    </a>
    <nav class="sidebar-nav">
        <p class="nav-label">Menu Utama</p>
        <button class="nav-item is-active" type="button" data-view="home" aria-current="page"><?= icon('grid') ?><span>Beranda</span><span class="active-dot"></span></button>
        <button class="nav-item" type="button" data-view="orders"><?= icon('orders') ?><span>Pesanan Saya</span></button>
        <button class="nav-item" type="button" data-view="history"><?= icon('clock') ?><span>Riwayat</span></button>
        <button class="nav-item" type="button" data-view="profile"><?= icon('profile') ?><span>Profil</span></button>
        <p class="nav-label category-nav-label">Kategori Alat</p>
        <?php foreach ($categories as $category): ?>
            <button class="nav-item category-nav" type="button" data-category="<?= escape($category['id']) ?>"><?= icon($category['icon']) ?><span><?= escape($category['name']) ?></span></button>
        <?php endforeach; ?>
    </nav>
    <button class="sidebar-close icon-button d-lg-none" type="button" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup menu"><?= icon('close') ?></button>
</aside>

<div class="workspace">
    <header class="topbar">
        <button class="icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu"><?= icon('menu') ?></button>
        <div class="search-wrap">
            <?= icon('search') ?>
            <input type="search" id="product-search" placeholder="Cari produk (contoh: Sony A7 III, Godox, Lensa 35mm)..." aria-label="Cari produk" autocomplete="off">
            <kbd class="search-shortcut">Ctrl K</kbd>
        </div>
        <div class="header-actions">
            <button class="icon-button" type="button" id="theme-toggle" aria-label="Aktifkan mode gelap" title="Ganti tema"><?= icon('moon') ?></button>
            <button class="icon-button cart-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#cart-panel" aria-controls="cart-panel" aria-label="Buka keranjang, 0 alat"><?= icon('cart') ?><span id="cart-count">0</span></button>
            <span class="header-divider"></span>
            <div class="dropdown">
                <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                    <span class="avatar" id="header-initials">AR</span>
                    <span class="user-copy"><strong id="header-name">Ahmad Rizki</strong><small>Penyewa</small></span>
                    <?= icon('chevron', 'user-chevron') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-header">Akun contoh · Pratinjau</span></li>
                    <li><button class="dropdown-item" type="button" data-view="profile">Profil saya</button></li>
                    <li><button class="dropdown-item" type="button" data-view="orders">Pesanan saya</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#about-modal">Tentang pratinjau</button></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="main-content" id="main-content" tabindex="-1">
        <section id="view-home" class="page-view" aria-labelledby="welcome-heading">
            <div class="page-heading">
                <h1 id="welcome-heading">Selamat Datang, <span id="welcome-name">Kakangkung</span></h1>
                <p>Kelola sewa peralatan foto &amp; video studio di Yogyakarta. Pembayaran &amp; jaminan fisik di toko.</p>
            </div>
            <div class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top"><h2>Booking Aktif</h2><span class="stat-icon blue"><?= icon('camera') ?></span></div>
                    <strong class="stat-value">0</strong><p>Tidak ada unit berjalan</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top"><h2>Pending Bayar</h2><span class="stat-icon amber"><?= icon('clock') ?></span></div>
                    <strong class="stat-value">0</strong><p>Batas ambil 24 jam · Bayar di toko</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top"><h2>Total Transaksi Selesai</h2><span class="stat-icon green"><?= icon('check') ?></span></div>
                    <strong class="stat-value">0</strong><p>Riwayat sewa sukses</p>
                </article>
            </div>

            <section class="products-section" aria-labelledby="products-heading">
                <div class="section-heading">
                    <div class="section-title"><h2 id="products-heading">Produk Pilihan</h2><span class="section-note" id="product-caption">Paling sering disewa mahasiswa</span></div>
                    <button class="text-button" type="button" id="show-all">Lihat Semua <?= icon('arrow') ?></button>
                </div>
                <div class="catalog-toolbar">
                    <div class="category-tabs" role="group" aria-label="Filter kategori produk">
                        <button class="category-tab is-active" type="button" data-category="" aria-pressed="true"><?= icon('grid') ?><span>Semua Alat</span><span class="tab-count"><?= count($products) ?></span></button>
                        <?php foreach ($categories as $category): ?>
                            <button class="category-tab" type="button" data-category="<?= escape($category['id']) ?>" aria-pressed="false"><?= icon($category['icon']) ?><span><?= escape($category['name']) ?></span><span class="tab-count"><?= count(array_filter($products, fn($product) => $product['category'] === $category['id'])) ?></span></button>
                        <?php endforeach; ?>
                    </div>
                    <span class="catalog-count" id="result-count" role="status"><?= count($products) ?> alat</span>
                </div>
                <div class="products-grid" id="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php $status = $product['stock'] === 0 ? 'empty' : ($product['stock'] <= 2 ? 'limited' : 'available'); ?>
                        <article class="product-card" data-product-id="<?= $product['id'] ?>">
                            <div class="product-visual">
                                <span class="stock-badge <?= $status ?>"><?= $status === 'empty' ? 'Habis' : ($status === 'limited' ? 'Stok Terbatas' : 'Tersedia') ?></span>
                                <div class="product-placeholder"><?= icon($product['icon']) ?><span><?= escape($product['label']) ?></span></div>
                            </div>
                            <div class="product-info">
                                <span class="product-type"><?= escape($product['type']) ?></span>
                                <h3><?= escape($product['name']) ?></h3>
                                <p class="product-specs"><?= escape($product['specs']) ?></p>
                                <div class="product-price-row"><p><strong><?= rupiah($product['price']) ?></strong><span>/hari</span></p><span class="rating" aria-label="Rating <?= escape($product['rating']) ?> dari 5"><?= icon('star') ?><?= escape($product['rating']) ?></span></div>
                                <div class="product-actions"><button class="btn btn-outline-secondary detail-button" type="button" data-detail="<?= $product['id'] ?>">Lihat Detail</button><button class="btn btn-primary" type="button" data-add="<?= $product['id'] ?>" <?= $product['stock'] === 0 ? 'disabled aria-label="' . escape($product['name']) . ' stok habis"' : '' ?>>Booking</button></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="empty-state catalog-empty" id="catalog-empty" hidden>
                    <span class="empty-icon"><?= icon('search') ?></span><h3>Belum ada alat yang cocok</h3><p>Coba kata kunci lain atau lihat kategori lainnya.</p><button class="btn btn-primary" type="button" data-reset-catalog>Tampilkan semua alat</button>
                </div>
            </section>
        </section>

        <section id="view-orders" class="page-view" hidden aria-labelledby="orders-heading">
            <div class="page-heading"><h1 id="orders-heading">Pesanan Saya</h1><p>Pantau booking dan pengambilan peralatanmu dalam satu tempat.</p></div>
            <div class="notice"><?= icon('info') ?><p>Mode pratinjau. Keranjang belum membuat booking dan belum terhubung ke database.</p></div>
            <div class="empty-state page-empty"><span class="empty-icon"><?= icon('orders') ?></span><h2>Belum ada pesanan</h2><p>Mulai ide kreatifmu dengan peralatan yang tepat.<br>Temukan gear favoritmu di katalog.</p><button class="btn btn-primary" type="button" data-view="home">Jelajahi Peralatan <?= icon('arrow') ?></button></div>
        </section>

        <section id="view-history" class="page-view" hidden aria-labelledby="history-heading">
            <div class="page-heading"><h1 id="history-heading">Riwayat Sewa</h1><p>Semua cerita kreatif dimulai dari sini. Lihat riwayat penyewaan peralatanmu.</p></div>
            <div class="empty-state page-empty"><span class="empty-icon"><?= icon('clock') ?></span><h2>Belum ada riwayat sewa</h2><p>Transaksi yang sudah selesai akan ditampilkan di sini.<br>Saat ini kamu sedang melihat data contoh.</p><button class="btn btn-primary" type="button" data-view="home">Kembali ke Beranda</button></div>
        </section>

        <section id="view-profile" class="page-view" hidden aria-labelledby="profile-heading">
            <div class="page-heading"><h1 id="profile-heading">Profil Saya</h1><p>Kelola informasi yang ditampilkan pada akunmu.</p></div>
            <form class="profile-card" id="profile-form">
                <div class="profile-intro"><span class="avatar avatar-large" id="profile-initials">AR</span><div><h2>Informasi pribadi</h2><p>Akun penyewa · Mode pratinjau</p></div></div>
                <div class="mb-3"><label class="form-label" for="profile-name">Nama lengkap</label><input class="form-control" id="profile-name" name="name" required minlength="2" maxlength="80" value="Ahmad Rizki" autocomplete="name"></div>
                <div class="mb-3"><label class="form-label" for="profile-email">Email</label><input class="form-control" id="profile-email" name="email" type="email" required maxlength="150" value="ahmad.rizki@example.com" autocomplete="email"></div>
                <div class="mb-4"><label class="form-label" for="profile-phone">Nomor HP</label><input class="form-control" id="profile-phone" name="phone" type="tel" inputmode="tel" pattern="[+0-9 ()\-]{8,20}" maxlength="20" placeholder="Contoh: 081234567890" autocomplete="tel"></div>
                <p class="profile-note">Perubahan hanya berlaku selama pratinjau ini dibuka, bukan akun sungguhan. Jangan gunakan data pribadi asli.</p>
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button><p class="profile-feedback" id="profile-feedback" role="status"></p>
            </form>
        </section>

        <footer class="footer">
            <div class="footer-links"><span>© <?= date('Y') ?> PelitaRental Studio</span><span class="footer-dot">·</span><button type="button" data-bs-toggle="modal" data-bs-target="#terms-modal">Syarat &amp; Ketentuan Sewa Kampus</button><span class="footer-dot">·</span><span>Yogyakarta, Indonesia</span></div>
            <button class="system-status" type="button" data-bs-toggle="modal" data-bs-target="#about-modal"><span></span>Pratinjau Dashboard v1.0</button>
        </footer>
    </main>
</div>

<div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="detail-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><div><span class="eyebrow" id="detail-type"></span><h2 class="modal-title" id="detail-title">Detail alat</h2></div><button class="icon-button" type="button" data-bs-dismiss="modal" aria-label="Tutup detail"><?= icon('close') ?></button></div>
        <div class="modal-body"><div class="detail-visual" id="detail-visual"></div><p class="detail-specs" id="detail-specs"></p><p id="detail-description"></p><h3 class="small-heading">Kelengkapan</h3><p id="detail-included"></p><div class="detail-bottom"><strong id="detail-price"></strong><span id="detail-stock"></span></div><p class="muted-note">Stok contoh, belum berdasarkan ketersediaan tanggal dari database.</p></div>
        <div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Tutup</button><button class="btn btn-primary" type="button" id="detail-add"><?= icon('cart') ?> Tambah ke Keranjang</button></div>
    </div></div>
</div>

<div class="offcanvas offcanvas-end cart-panel" tabindex="-1" id="cart-panel" aria-labelledby="cart-heading">
    <div class="offcanvas-header"><div><span class="eyebrow">Gear untuk ide kreatifmu</span><h2 id="cart-heading">Keranjang Sewa</h2></div><button class="icon-button" type="button" data-bs-dismiss="offcanvas" aria-label="Tutup keranjang"><?= icon('close') ?></button></div>
    <div class="offcanvas-body">
        <div id="cart-empty" class="empty-state"><span class="empty-icon"><?= icon('cart') ?></span><h3>Keranjangmu masih kosong</h3><p>Pilih peralatan yang ingin kamu sewa.</p><button class="btn btn-primary" type="button" data-bs-dismiss="offcanvas">Jelajahi Peralatan</button></div>
        <div id="cart-content" hidden>
            <div id="cart-items"></div>
            <form id="rental-form">
                <div class="rental-dates"><div><label class="form-label" for="start-date">Tanggal mulai</label><input class="form-control" type="date" id="start-date" required></div><div><label class="form-label" for="end-date">Tanggal selesai</label><input class="form-control" type="date" id="end-date" required></div></div>
                <p class="date-error" id="date-error" role="alert" hidden></p>
                <div class="rental-total"><p><span>Durasi sewa</span><strong id="rental-duration">1 hari</strong></p><p><span>Subtotal per hari</span><strong id="daily-subtotal">Rp 0</strong></p><div><span>Estimasi total</span><strong id="rental-total">Rp 0</strong></div></div>
                <div class="notice"><?= icon('info') ?><p>Ini simulasi biaya, bukan booking resmi. Stok belum ditahan. Pembayaran dan penyerahan jaminan dilakukan di toko setelah booking terkonfirmasi.</p></div>
                <button class="btn btn-primary w-100" id="print-summary" type="submit"><?= icon('print') ?> Cetak Estimasi Sewa</button>
                <p class="muted-note text-center mt-3">Tanggal mulai dan selesai ikut dihitung dalam durasi.</p>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="terms-modal" tabindex="-1" aria-labelledby="terms-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h2 class="modal-title" id="terms-title">Syarat &amp; Ketentuan Sewa</h2><button class="icon-button" type="button" data-bs-dismiss="modal" aria-label="Tutup syarat"><?= icon('close') ?></button></div>
    <div class="modal-body terms-content"><p>Peralatan yang tepat untuk setiap karya. Berikut ketentuan penyewaan PelitaRental.</p><ol><li>Login diperlukan sebelum membuat booking resmi.</li><li>Durasi dihitung termasuk tanggal mulai dan selesai. Sewa di hari yang sama dihitung satu hari.</li><li>Total biaya adalah harga per hari × jumlah unit × durasi.</li><li>Ketersediaan alat harus diperiksa sesuai rentang tanggal sewa.</li><li>Booking yang belum dibayar akan hangus setelah 24 jam. Pembayaran dilakukan langsung di toko.</li><li>Bawa identitas untuk jaminan dan konfirmasi dengan admin saat pengambilan.</li><li>Pengembalian alat dikonfirmasi oleh admin.</li></ol></div>
    <div class="modal-footer"><button class="btn btn-primary" type="button" data-bs-dismiss="modal">Saya Mengerti</button></div>
</div></div></div>

<div class="modal fade" id="about-modal" tabindex="-1" aria-labelledby="about-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h2 class="modal-title" id="about-title">Tentang Pratinjau</h2><button class="icon-button" type="button" data-bs-dismiss="modal" aria-label="Tutup informasi"><?= icon('close') ?></button></div>
    <div class="modal-body"><p>Dashboard ini menggunakan data produk dan akun contoh. Pencarian, filter, detail alat, keranjang, perhitungan biaya, dan tema dapat dicoba.</p><p>Login, pengecekan stok berdasarkan tanggal, booking resmi, dan panel admin belum terhubung ke MySQL. Keranjang dan tema disimpan di browser ini. Perubahan profil hanya berlaku selama halaman dibuka.</p></div>
    <div class="modal-footer"><button class="btn btn-primary" type="button" data-bs-dismiss="modal">Mengerti</button></div>
</div></div></div>

<div class="toast-container position-fixed bottom-0 end-0 p-3"><div id="feedback-toast" class="toast" role="status" aria-live="polite" aria-atomic="true"><div class="toast-body"><span class="toast-icon"><?= icon('check') ?></span><span id="toast-message"></span><button class="icon-button" type="button" data-bs-dismiss="toast" aria-label="Tutup pemberitahuan"><?= icon('close') ?></button></div></div></div>
<section id="print-sheet" aria-label="Estimasi sewa untuk dicetak"></section>
<noscript><div class="noscript-notice">Aktifkan JavaScript untuk menggunakan pencarian, keranjang, dan navigasi dashboard.</div></noscript>

<script>
'use strict';

window.addEventListener('DOMContentLoaded', () => {
    const products = JSON.parse(document.getElementById('product-data').textContent);
    const productById = new Map(products.map(product => [product.id, product]));
    const currency = new Intl.NumberFormat('id-ID');
    const money = value => `Rp ${currency.format(value)}`;
    const escapeHTML = value => String(value).replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));
    const icon = name => `<svg class="icon" aria-hidden="true"><use href="#icon-${escapeHTML(name)}"></use></svg>`;
    const search = document.getElementById('product-search');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const cartPanel = document.getElementById('cart-panel');
    const detailModal = document.getElementById('detail-modal');
    const toast = bootstrap.Toast.getOrCreateInstance(document.getElementById('feedback-toast'), { delay: 3000 });
    const cart = new Map();
    let selectedCategory = '';
    let selectedProduct = null;
    let detailTrigger = null;

    document.querySelectorAll('.modal, .offcanvas, .offcanvas-lg').forEach(panel => {
        ['hide.bs.modal', 'hide.bs.offcanvas'].forEach(eventName => {
            panel.addEventListener(eventName, () => {
                if (panel.contains(document.activeElement)) document.activeElement.blur();
            });
        });
    });
    detailModal.addEventListener('hidden.bs.modal', () => detailTrigger?.focus());

    function notify(message) {
        document.getElementById('toast-message').textContent = message;
        toast.show();
    }

    function saveCart() {
        try {
            localStorage.setItem('pelita-cart', JSON.stringify([...cart].map(([id, quantity]) => ({ id, quantity }))));
        } catch {
            notify('Penyimpanan browser tidak tersedia. Keranjang hanya tersimpan selama halaman dibuka.');
        }
    }

    function localToday() {
        const date = new Date();
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    try {
        const saved = JSON.parse(localStorage.getItem('pelita-cart') || '[]');
        if (Array.isArray(saved)) {
            saved.forEach(item => {
                if (!item || typeof item !== 'object') return;
                const product = productById.get(item.id);
                if (product && product.stock > 0 && Number.isInteger(item.quantity) && item.quantity > 0) {
                    cart.set(item.id, Math.min(item.quantity, product.stock));
                }
            });
        }
    } catch {
        cart.clear();
    }

    startDate.min = localToday();
    startDate.value = localToday();
    endDate.min = localToday();
    endDate.value = localToday();

    function hideSidebar() {
        bootstrap.Offcanvas.getInstance(document.getElementById('sidebar'))?.hide();
    }

    function showView(view) {
        const titles = { home: 'Beranda', orders: 'Pesanan Saya', history: 'Riwayat Sewa', profile: 'Profil Saya' };
        document.querySelectorAll('.page-view').forEach(section => {
            section.hidden = section.id !== `view-${view}`;
        });
        document.querySelectorAll('.sidebar-nav [data-view]').forEach(button => {
            const active = button.dataset.view === view && !selectedCategory;
            button.classList.toggle('is-active', active);
            if (active) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
        document.title = `${titles[view]} — PelitaRental`;
        hideSidebar();
    }

    function filterProducts() {
        const query = search.value.trim().toLocaleLowerCase('id-ID');
        let count = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            const product = productById.get(Number(card.dataset.productId));
            const haystack = `${product.name} ${product.type} ${product.category} ${product.specs} ${product.label}`.toLocaleLowerCase('id-ID');
            const matches = (!selectedCategory || product.category === selectedCategory) && query.split(/\s+/).every(word => haystack.includes(word));
            card.hidden = !matches;
            if (matches) count += 1;
        });
        document.getElementById('catalog-empty').hidden = count !== 0;
        document.getElementById('products-grid').hidden = count === 0;
        document.getElementById('result-count').textContent = `${count} alat${query ? ' ditemukan' : ''}`;
        document.getElementById('product-caption').textContent = selectedCategory ? 'Peralatan sesuai kategorimu' : 'Paling sering disewa mahasiswa';
        document.querySelectorAll('[data-category]').forEach(button => {
            const active = button.dataset.category === selectedCategory;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', String(active));
        });
        showView('home');
    }

    function resetCatalog() {
        selectedCategory = '';
        search.value = '';
        filterProducts();
    }

    document.querySelectorAll('[data-view]').forEach(button => {
        button.addEventListener('click', () => {
            selectedCategory = '';
            search.value = '';
            filterProducts();
            showView(button.dataset.view);
            document.getElementById('main-content').focus({ preventScroll: true });
        });
    });

    document.querySelectorAll('[data-category]').forEach(button => {
        button.addEventListener('click', () => {
            selectedCategory = button.dataset.category;
            search.value = '';
            filterProducts();
            if (button.classList.contains('category-nav')) {
                document.getElementById('products-heading').scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start' });
            }
        });
    });
    search.addEventListener('input', filterProducts);
    document.getElementById('show-all').addEventListener('click', resetCatalog);
    document.querySelector('[data-reset-catalog]').addEventListener('click', resetCatalog);
    document.addEventListener('keydown', event => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            if (document.querySelector('.modal.show, .offcanvas.show')) return;
            event.preventDefault();
            search.focus();
            search.select();
        }
    });

    function updateThemeButton() {
        const isDark = document.documentElement.dataset.bsTheme === 'dark';
        const toggle = document.getElementById('theme-toggle');
        toggle.innerHTML = icon(isDark ? 'sun' : 'moon');
        toggle.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        toggle.setAttribute('aria-pressed', String(isDark));
    }
    document.getElementById('theme-toggle').addEventListener('click', () => {
        const theme = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.bsTheme = theme;
        try {
            localStorage.setItem('pelita-theme', theme);
        } catch {
            notify('Tema berubah, tetapi preferensi tidak dapat disimpan di browser ini.');
        }
        updateThemeButton();
    });
    updateThemeButton();

    function getEstimate() {
        const start = startDate.value;
        const end = endDate.value;
        let error = '';
        if (!start || !end) error = 'Pilih tanggal mulai dan selesai terlebih dahulu.';
        else if (start < localToday()) error = 'Tanggal mulai tidak boleh sebelum hari ini.';
        else if (end < start) error = 'Tanggal selesai harus sama atau setelah tanggal mulai.';
        const days = error ? 0 : Math.round((Date.parse(`${end}T00:00:00Z`) - Date.parse(`${start}T00:00:00Z`)) / 86400000) + 1;
        const daily = [...cart].reduce((total, [id, quantity]) => total + productById.get(id).price * quantity, 0);
        return { days, daily, total: days * daily, error };
    }

    function updateEstimate() {
        const { days, daily, total, error } = getEstimate();
        const errorElement = document.getElementById('date-error');
        errorElement.textContent = error;
        errorElement.hidden = !error;
        startDate.setAttribute('aria-invalid', String(Boolean(error)));
        endDate.setAttribute('aria-invalid', String(Boolean(error)));
        startDate.setAttribute('aria-describedby', 'date-error');
        endDate.setAttribute('aria-describedby', 'date-error');
        document.getElementById('rental-duration').textContent = error ? '—' : `${days} hari`;
        document.getElementById('daily-subtotal').textContent = money(daily);
        document.getElementById('rental-total').textContent = error ? '—' : money(total);
        document.getElementById('print-summary').disabled = Boolean(error) || cart.size === 0;
    }

    startDate.addEventListener('input', () => {
        endDate.min = startDate.value >= localToday() ? startDate.value : localToday();
        updateEstimate();
    });
    endDate.addEventListener('input', updateEstimate);

    function renderCart() {
        const quantity = [...cart.values()].reduce((sum, value) => sum + value, 0);
        document.getElementById('cart-count').textContent = quantity;
        document.querySelector('.cart-toggle').setAttribute('aria-label', `Buka keranjang, ${quantity} alat`);
        document.getElementById('cart-empty').hidden = cart.size > 0;
        document.getElementById('cart-content').hidden = cart.size === 0;
        document.getElementById('cart-items').innerHTML = [...cart].map(([id, count]) => {
            const product = productById.get(id);
            return `<article class="cart-item">
                <span class="cart-item-visual">${icon(product.icon)}</span>
                <div class="cart-item-info"><h3>${escapeHTML(product.name)}</h3><p>${money(product.price)} / hari</p>
                    <div class="quantity-control" aria-label="Jumlah ${escapeHTML(product.name)}">
                        <button type="button" data-quantity="${id}" data-delta="-1" aria-label="Kurangi ${escapeHTML(product.name)}" ${count <= 1 ? 'disabled' : ''}>−</button>
                        <span aria-live="polite">${count}</span>
                        <button type="button" data-quantity="${id}" data-delta="1" aria-label="Tambah ${escapeHTML(product.name)}" ${count >= product.stock ? 'disabled' : ''}>+</button>
                    </div>
                </div>
                <button class="icon-button remove-item" type="button" data-remove="${id}" aria-label="Hapus ${escapeHTML(product.name)}">${icon('trash')}</button>
            </article>`;
        }).join('');
        updateEstimate();
    }

    function addToCart(id) {
        const product = productById.get(id);
        const count = cart.get(id) || 0;
        if (count >= product.stock) {
            notify(`Jumlah ${product.name} sudah mencapai stok contoh yang tersedia.`);
            return false;
        }
        cart.set(id, count + 1);
        saveCart();
        renderCart();
        return true;
    }

    document.querySelectorAll('[data-add]').forEach(button => {
        button.addEventListener('click', () => {
            if (addToCart(Number(button.dataset.add))) {
                bootstrap.Offcanvas.getOrCreateInstance(cartPanel).show();
            }
        });
    });

    document.getElementById('cart-items').addEventListener('click', event => {
        const quantityButton = event.target.closest('[data-quantity]');
        const removeButton = event.target.closest('[data-remove]');
        if (quantityButton) {
            const id = Number(quantityButton.dataset.quantity);
            const next = cart.get(id) + Number(quantityButton.dataset.delta);
            if (next < 1 || next > productById.get(id).stock) return;
            cart.set(id, next);
            saveCart();
            renderCart();
            const selector = `[data-quantity="${id}"][data-delta="${quantityButton.dataset.delta}"]`;
            const nextButton = document.querySelector(selector);
            if (!nextButton.disabled) nextButton.focus();
            else document.querySelector(`[data-quantity="${id}"]:not(:disabled)`)?.focus();
        }
        if (removeButton) {
            cart.delete(Number(removeButton.dataset.remove));
            saveCart();
            renderCart();
            const focusTarget = document.querySelector('#cart-items [data-remove]') || document.querySelector('#cart-empty button');
            focusTarget.focus();
        }
    });

    document.querySelectorAll('[data-detail]').forEach(button => {
        button.addEventListener('click', () => {
            detailTrigger = button;
            selectedProduct = productById.get(Number(button.dataset.detail));
            document.getElementById('detail-type').textContent = selectedProduct.type;
            document.getElementById('detail-title').textContent = selectedProduct.name;
            document.getElementById('detail-visual').innerHTML = icon(selectedProduct.icon);
            document.getElementById('detail-specs').textContent = selectedProduct.specs;
            document.getElementById('detail-description').textContent = selectedProduct.description;
            document.getElementById('detail-included').textContent = selectedProduct.included;
            document.getElementById('detail-price').textContent = `${money(selectedProduct.price)} / hari`;
            document.getElementById('detail-stock').textContent = selectedProduct.stock ? `${selectedProduct.stock} unit tersedia` : 'Stok sedang habis';
            const addButton = document.getElementById('detail-add');
            addButton.disabled = selectedProduct.stock === 0;
            addButton.innerHTML = selectedProduct.stock ? `${icon('cart')} Tambah ke Keranjang` : 'Stok Habis';
            bootstrap.Modal.getOrCreateInstance(detailModal).show();
        });
    });
    document.getElementById('detail-add').addEventListener('click', () => {
        if (!selectedProduct || !addToCart(selectedProduct.id)) return;
        detailModal.addEventListener('hidden.bs.modal', () => {
            bootstrap.Offcanvas.getOrCreateInstance(cartPanel).show();
        }, { once: true });
        bootstrap.Modal.getInstance(detailModal).hide();
    });

    document.getElementById('profile-form').addEventListener('submit', event => {
        event.preventDefault();
        const nameInput = document.getElementById('profile-name');
        const name = nameInput.value.trim();
        if (name.length < 2) {
            nameInput.setCustomValidity('Masukkan nama dengan minimal 2 karakter, bukan hanya spasi.');
            nameInput.reportValidity();
            return;
        }
        const initials = name.split(/\s+/).slice(0, 2).map(word => Array.from(word)[0]).join('').toLocaleUpperCase('id-ID');
        nameInput.value = name;
        document.getElementById('header-name').textContent = name;
        document.getElementById('welcome-name').textContent = name.split(/\s+/)[0];
        document.getElementById('header-initials').textContent = initials;
        document.getElementById('profile-initials').textContent = initials;
        document.getElementById('profile-feedback').textContent = 'Profil pratinjau diperbarui. Perubahan tidak disimpan ke database.';
        notify('Profil pratinjau berhasil diperbarui.');
    });
    document.getElementById('profile-name').addEventListener('input', event => event.target.setCustomValidity(''));

    document.getElementById('rental-form').addEventListener('submit', event => {
        event.preventDefault();
        const { days, daily, total, error } = getEstimate();
        if (error || !cart.size) return;
        const formatDate = value => new Intl.DateTimeFormat('id-ID', { dateStyle: 'long', timeZone: 'UTC' }).format(new Date(`${value}T00:00:00Z`));
        document.getElementById('print-sheet').innerHTML = `
            <h1>PelitaRental</h1><p>Sewa Gear Mahasiswa · Yogyakarta</p>
            <h2>Estimasi Sewa Peralatan</h2>
            <p>Periode: ${escapeHTML(formatDate(startDate.value))} – ${escapeHTML(formatDate(endDate.value))}<br>Durasi: ${days} hari</p>
            <table><thead><tr><th>Peralatan</th><th>Unit</th><th>Harga / hari</th><th>Subtotal</th></tr></thead><tbody>
            ${[...cart].map(([id, count]) => {
                const product = productById.get(id);
                return `<tr><td>${escapeHTML(product.name)}</td><td>${count}</td><td>${money(product.price)}</td><td>${money(product.price * count * days)}</td></tr>`;
            }).join('')}
            </tbody></table><p>Subtotal per hari: ${money(daily)}</p><p class="print-total">Estimasi total: ${money(total)}</p>
            <div class="print-notice"><strong>SIMULASI — BUKAN BUKTI BOOKING</strong><br>Dokumen ini hanya estimasi berdasarkan data contoh. Belum ada reservasi stok atau transaksi. Ketersediaan alat perlu diperiksa sesuai tanggal sewa. Pembayaran dan jaminan dilakukan di toko setelah booking resmi.</div>`;
        window.print();
    });

    renderCart();
});
</script>
</body>
</html>
