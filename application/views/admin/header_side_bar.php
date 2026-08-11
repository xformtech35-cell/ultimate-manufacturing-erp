<?php
//get user role
$session_data_head = $this->session->userdata('session_data_head');
if (isset($session_data_head['result']['user_id'])) {
    if ($this->session->userdata('INFOMSG') === 'Username or Password is wrong!!') {
        $this->session->unset_userdata('INFOMSG');
    }
}
$res = (isset($session_data_head['result']) && is_array($session_data_head['result'])) ? $session_data_head['result'] : array();

$user_role  = $res['role_name'] ?? 'Admin';
$user_email = $res['user_email'] ?? 'admin@uwsenvirotech.com';
$username   = $res['username'] ?? 'Admin';
$user_id    = $res['user_id'] ?? 1;
$settings   = (isset($session_data_head['settings']) && is_array($session_data_head['settings'])) ? $session_data_head['settings'] : array();

$default_brand_name = 'UWS ENVIRO-TECH PVT LTD';
$default_brand_logo = 'uploads/UWS_private_limited.png';

$brand_full_name = !empty($settings['company_name']) ? trim($settings['company_name']) : $default_brand_name;
$brand_short_name = substr($brand_full_name, 0, 30);
$sidebar_brand_name = substr($brand_full_name, 0, 24);

$brand_logo_banner = !empty($settings['company_logo']) ? ltrim($settings['company_logo'], './') : $default_brand_logo;
$brand_logo_icon = $brand_logo_banner;


// var_dump($session_data_head['permission']);
// die();

$password = $session_data_head['password_str'] ?? '';


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $brand_full_name; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url() ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url() ?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo base_url() ?>bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url() ?>dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url() ?>dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style.css">

    <!-- bootstrap wysihtml5 - text editor -->
    <!--        <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">-->

    <!-- Google Font -->
    <!--        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
    <!-- jQuery UI CSS for datepicker -->
<link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/jquery-ui/themes/base/jquery-ui.min.css">

    <!-- Google Font for loader -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════
           PREMIUM PAGE LOADER  v2
        ═══════════════════════════════════════════════════ */

        /* Full-screen overlay */
        #erp-page-loader {
            position: fixed; inset: 0; z-index: 99999;
            background: linear-gradient(135deg,#060d1a 0%,#0b1628 50%,#060d1a 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            transition: opacity .5s ease, visibility .5s ease;
            overflow: hidden;
        }
        #erp-page-loader.hidden { opacity:0; visibility:hidden; pointer-events:none; }

        /* ── Background particles ── */
        .epl-particle {
            position: absolute; border-radius: 50%; pointer-events: none;
            animation: epl-float var(--d,8s) ease-in-out infinite var(--dl,0s);
            opacity: 0;
        }
        @keyframes epl-float {
            0%   { opacity:0; transform: translateY(100px) scale(.5); }
            20%  { opacity:.6; }
            80%  { opacity:.3; }
            100% { opacity:0; transform: translateY(-120px) scale(1.2); }
        }

        /* ── Glow blobs ── */
        .epl-blob {
            position: absolute; border-radius: 50%;
            filter: blur(80px); pointer-events: none; opacity: .15;
            animation: epl-blob-pulse var(--bp,8s) ease-in-out infinite alternate;
        }
        @keyframes epl-blob-pulse { 0%{transform:scale(1)} 100%{transform:scale(1.3)} }

        /* ── Center logo + rings ── */
        .epl-center {
            position: relative; display:flex;
            flex-direction:column; align-items:center;
            z-index: 2;
        }

        /* Outer ring */
        .epl-ring-outer {
            position: absolute;
            width: 130px; height: 130px;
            border-radius: 50%;
            border: 2px solid transparent;
            background: conic-gradient(from 0deg, #06b6d4, #3b82f6, #8b5cf6, #06b6d4) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            animation: epl-spin-cw 2.5s linear infinite;
            top: 50%; left: 50%; transform: translate(-50%,-50%);
        }
        /* Inner ring */
        .epl-ring-inner {
            position: absolute;
            width: 104px; height: 104px;
            border-radius: 50%;
            border: 1.5px solid transparent;
            background: conic-gradient(from 180deg, #f59e0b, #ef4444, #8b5cf6, #f59e0b) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            animation: epl-spin-ccw 3.5s linear infinite;
            top: 50%; left: 50%; transform: translate(-50%,-50%);
        }
        @keyframes epl-spin-cw  { to { transform: translate(-50%,-50%) rotate(360deg);  } }
        @keyframes epl-spin-ccw { to { transform: translate(-50%,-50%) rotate(-360deg); } }

        /* Logo circle */
        .epl-logo-wrap {
            width: 82px; height: 82px; border-radius: 50%;
            background: #0f1f38;
            border: 2px solid rgba(59,130,246,.25);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 32px rgba(6,182,212,.25), 0 0 60px rgba(59,130,246,.12);
            position: relative; z-index: 3;
            animation: epl-logo-pulse 2s ease-in-out infinite;
        }
        @keyframes epl-logo-pulse {
            0%,100% { box-shadow: 0 0 20px rgba(6,182,212,.2), 0 0 40px rgba(59,130,246,.1); }
            50%     { box-shadow: 0 0 40px rgba(6,182,212,.5), 0 0 80px rgba(59,130,246,.25); }
        }
        .epl-logo-wrap img {
            width: 52px; height: 52px; object-fit: contain;
            border-radius: 50%;
        }
        /* fallback initials if logo fails */
        .epl-logo-initials {
            font-family: 'Inter','Segoe UI',sans-serif;
            font-size: 22px; font-weight: 800;
            background: linear-gradient(135deg,#06b6d4,#3b82f6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Ring container */
        .epl-rings {
            width: 130px; height: 130px;
            position: relative; margin-bottom: 28px;
        }

        /* ── Brand text ── */
        .epl-brand {
            font-family: 'Inter','Segoe UI',sans-serif;
            font-size: 18px; font-weight: 700;
            color: #e2e8f0;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
            animation: epl-fade-up .6s ease both .3s;
        }
        .epl-brand span { color: #38bdf8; }
        .epl-sub {
            font-family: 'Inter','Segoe UI',sans-serif;
            font-size: 11px; color: #475569;
            letter-spacing: 1.5px; text-transform: uppercase;
            margin-bottom: 28px;
            animation: epl-fade-up .6s ease both .5s;
        }
        @keyframes epl-fade-up {
            from { opacity:0; transform:translateY(10px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Shimmer progress strip ── */
        .epl-progress-wrap {
            width: 200px; height: 2px;
            background: rgba(255,255,255,.06);
            border-radius: 99px; overflow: hidden;
            animation: epl-fade-up .6s ease both .6s;
        }
        .epl-progress-fill {
            height: 100%;
            width: 45%;
            border-radius: 99px;
            background: linear-gradient(90deg,transparent,#06b6d4,#3b82f6,#8b5cf6,transparent);
            animation: epl-shimmer 1.6s ease-in-out infinite;
        }
        @keyframes epl-shimmer {
            0%   { transform: translateX(-120%); }
            100% { transform: translateX(350%); }
        }

        /* ── Loading text ── */
        .epl-loading-text {
            font-family: 'Inter','Segoe UI',sans-serif;
            font-size: 11px; color: #334155;
            letter-spacing: 1px; text-transform: uppercase;
            margin-top: 12px;
            animation: epl-fade-up .6s ease both .7s;
        }
        .epl-loading-text::after {
            content: '';
            animation: epl-dots 1.5s steps(3,end) infinite;
        }
        @keyframes epl-dots {
            0%   { content: ''; }
            33%  { content: '.'; }
            66%  { content: '..'; }
            100% { content: '...'; }
        }

        /* ── Slim top bar ── */
        #erp-top-bar {
            position: fixed; top:0; left:0;
            height: 3px; width: 0;
            z-index: 999999;
            background: linear-gradient(90deg,#06b6d4,#3b82f6,#8b5cf6);
            transition: width .3s ease;
            box-shadow: 0 0 12px rgba(56,189,248,.7);
            pointer-events: none;
        }
    /* ─── Sidebar menu: smooth, reliable treeview ─── */
        .sidebar {
            height: calc(100vh - 50px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: #2c3b4a; }
        .sidebar::-webkit-scrollbar-thumb { background: #546475; border-radius: 4px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: #6e8090; }
        .sidebar { scrollbar-width: thin; scrollbar-color: #546475 #2c3b4a; }

        /* Main menu scroll */
        .sidebar-menu {
            overflow: visible !important;
            max-height: none !important;
            padding-right: 0 !important;
        }

        /* Treeview submenu — hidden by default, shown via jQuery slideDown */
        .sidebar-menu li.treeview > .treeview-menu {
            display: none;
        }
        .sidebar-menu li.treeview.menu-open > .treeview-menu {
            display: block;
        }
        /* Remove the CSS animation conflict — jQuery handles the animation */
        .sidebar-menu .treeview-menu {
            overflow: hidden;
        }

        /* Arrow rotation on open */
        .sidebar-menu li.treeview > a > .pull-right-container > .fa-angle-left {
            transition: transform 0.25s ease;
        }
        .sidebar-menu li.treeview.menu-open > a > .pull-right-container > .fa-angle-left {
            transform: rotate(-90deg);
        }

        /* Hover highlight */
        .sidebar-menu li > a {
            transition: background 0.15s ease;
        }

        /* Search box */
        #myInput {
            margin: 6px 8px;
            width: calc(100% - 16px);
            border-radius: 4px;
            border: 1px solid #3a4a5a;
            background: #1a2633;
            color: #c0cdd8;
            padding: 5px 10px;
            font-size: 12px;
        }
        #myInput::placeholder { color: #6e8090; }
        #myInput:focus { outline: none; border-color: #4a90d9; background: #1f2f3f; }

        /* ─── Header / brand ─── */
        .main-header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 0 10px;
        }
        .brand-mini-logo { max-width: 34px; max-height: 34px; }
        .brand-header-text {
            display: block;
            width: 100%;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            line-height: 50px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* User panel logo */
        .sidebar .user-panel { min-height: auto; padding: 0; margin: 0; background: #fff; }
        .sidebar .user-panel > .image,
        .sidebar .user-panel > .info { float: none; position: static; width: 100%; padding-left: 0; }
        .sidebar .user-panel > .image { text-align: center; }
        .user-brand-image { width: 45px; height: 45px; object-fit: contain; background: #fff; border-radius: 50%; padding: 2px; }
        .user-brand-image-large { width: 90px; height: 90px; object-fit: contain; background: #fff; border-radius: 50%; padding: 4px; }

        /* ─── Popup (apps switcher) ─── */
        .popup { position: relative; display: inline-block; cursor: pointer; user-select: none; }
        .popup .popuptext {
            visibility: hidden;
            width: auto;
            background-color: white;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 9999;
            top: 150%;
            left: 50%;
            margin-left: -80px;
            box-shadow: 0 4px 16px rgba(0,0,0,.2);
        }
        .popup .popuptext::after {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent black transparent;
        }
        .popup .show { visibility: visible; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

        /* ─── Calculator ─── */
        .calculator-trigger { margin-top: 10px; margin-right: 10px; }
        .calculator-call-window {
            position: fixed; right: 28px; bottom: 28px;
            width: 360px; max-width: calc(100vw - 24px);
            z-index: 10000; border-radius: 14px;
            background: #eef2f7; box-shadow: 0 16px 40px rgba(0,0,0,.25);
            overflow: hidden; border: 1px solid #d8dde5; display: none;
        }
        .calculator-call-window.show { display: block; }
        .calculator-call-header {
            height: 46px; background: #1f2d3d; color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 12px; cursor: move; user-select: none;
        }
        .calculator-call-title { font-size: 14px; font-weight: 600; }
        .calculator-call-actions { display: flex; gap: 8px; }
        .calculator-call-action {
            width: 22px; height: 22px; border: 0; border-radius: 50%;
            font-size: 13px; line-height: 22px; text-align: center;
            padding: 0; cursor: pointer; color: #fff;
            background: rgba(255,255,255,.2);
        }
        .calculator-call-action:hover { background: rgba(255,255,255,.35); }
        .calculator-call-body { padding: 16px; }
        .calculator-shell { background: #fff; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,.12); padding: 16px; }
        .calculator-title { margin: 0 0 12px; font-size: 20px; font-weight: 700; color: #1a2226; text-align: center; }
        .calculator-display {
            width: 100%; box-sizing: border-box; height: 52px; margin-bottom: 12px;
            padding: 10px 12px; border: 1px solid #d2d6de; border-radius: 6px;
            background: #f7f7f7; color: #111; font-size: 24px; text-align: right;
        }
        .calculator-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; }
        .calculator-grid button {
            height: 46px; border: 0; border-radius: 6px;
            font-size: 17px; font-weight: 600; cursor: pointer;
            background: #dfe6ee; color: #1a2226;
        }
        .calculator-grid button.operator { background: #f39c12; color: #fff; }
        .calculator-grid button.equal   { background: #00a65a; color: #fff; }
        .calculator-grid button.clear   { background: #dd4b39; color: #fff; }
        .calculator-call-window.minimized .calculator-call-body { display: none; }
        @media (max-width: 600px) {
            .calculator-call-window { width: calc(100vw - 20px); right: 10px; bottom: 10px; }
        }
        /* Global Fixed Header & Sidebar Layout */
        .main-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
        }
        .main-sidebar {
            position: fixed !important;
            top: 5px !important;
            /* top: 50px !important; */
            bottom: 0 !important;
            height: calc(100vh - 50px) !important;
            z-index: 1020 !important;
            overflow-y: auto !important;
        }
        @media (min-width: 768px) {
            .main-sidebar {
                left: 0 !important;
            }
        }
        .sidebar {
            padding-top: 0 !important;
            margin-top: 0 !important;
            padding-bottom: 50px !important;
        }
        /* Style sidebar scrollbar globally */
        .main-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .main-sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .main-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.12);
            border-radius: 3px;
        }
        .main-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.25);
        }
        .content-wrapper {
            margin-top: 50px !important;
        }
        
        /* Sticky Sidebar Header (Logo + Search) */
        .sidebar-sticky-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            background: #222d32 !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .sidebar-sticky-header .user-panel {
            background: #fff !important;
            border-bottom: 1px solid #eee !important;
            padding: 0 !important;
        }
        .sidebar-sticky-header .sidebar-search-container {
            padding: 10px !important;
        }
        .sidebar-sticky-header #myInput {
            margin: 0 !important;
            width: 100% !important;
            background: #374850 !important;
            color: #fff !important;
            border: 1px solid #2c3b41 !important;
            border-radius: 4px !important;
        }
        .sidebar-sticky-header #myInput::placeholder {
            color: #8aa4af !important;
        }
        .sidebar-collapse .sidebar-sticky-header .sidebar-search-container {
            display: none !important;
        }
        .sidebar-collapse .sidebar-sticky-header .user-panel {
            display: none !important;
        }
        /* Custom table responsive wrapper that isolates scrolling only to the table */
        .table-responsive-container {
            overflow-x: auto !important;
            display: block !important;
            width: 100% !important;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<!-- ══════════════════════════════════════════
     PREMIUM PAGE LOADER v2
═══════════════════════════════════════════ -->
<div id="erp-top-bar"></div>
<div id="erp-page-loader">

    <!-- Background glow blobs -->
    <div class="epl-blob" style="width:500px;height:500px;background:#1e40af;top:-150px;left:-150px;--bp:10s"></div>
    <div class="epl-blob" style="width:400px;height:400px;background:#0e7490;bottom:-100px;right:-100px;--bp:13s"></div>
    <div class="epl-blob" style="width:300px;height:300px;background:#7c3aed;top:60%;left:60%;--bp:8s"></div>

    <!-- Floating particles -->
    <div id="epl-particles"></div>

    <!-- Center content -->
    <div class="epl-center">
        <!-- Dual rings + logo -->
        <div class="epl-rings">
            <div class="epl-ring-outer"></div>
            <div class="epl-ring-inner"></div>
            <div class="epl-logo-wrap" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)">
                <img src="<?php echo base_url() . $brand_logo_icon; ?>" alt="<?php echo htmlspecialchars($brand_short_name); ?>"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="epl-logo-initials" style="display:none"><?php
                    // Auto-generate initials from brand name e.g. "Xformtech" → "XF"
                    $words = preg_split('/\s+/', trim($brand_full_name));
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $w) { $initials .= strtoupper(substr($w, 0, 1)); }
                    echo htmlspecialchars($initials ?: 'ERP');
                ?></div>
            </div>
        </div>

        <!-- Brand text (dynamic from settings) -->
        <div class="epl-brand"><?php echo htmlspecialchars($sidebar_brand_name); ?></div>
        <div class="epl-sub"><?php echo htmlspecialchars($brand_full_name); ?></div>

        <!-- Shimmer progress -->
        <div class="epl-progress-wrap">
            <div class="epl-progress-fill"></div>
        </div>
        <div class="epl-loading-text">Loading</div>
    </div>
</div>
<!-- legacy loader (kept for compatibility) -->
<div id="loader" class="center" style="display:none;"></div>

<script>
// Generate floating particles for loader
(function(){
    var c=document.getElementById('epl-particles');
    if(!c)return;
    var cols=['#06b6d4','#3b82f6','#8b5cf6','#f59e0b','#10b981'];
    for(var i=0;i<25;i++){
        var p=document.createElement('div');
        p.className='epl-particle';
        var sz=Math.random()*4+2;
        p.style.cssText='width:'+sz+'px;height:'+sz+'px;background:'+cols[Math.floor(Math.random()*cols.length)]+';left:'+Math.random()*100+'%;bottom:-20px;--d:'+(6+Math.random()*8)+'s;--dl:'+(Math.random()*8)+'s;box-shadow:0 0 6px currentColor;';
        c.appendChild(p);
    }
})();
</script>
<div class="wrapper">
<header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">

        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="<?php echo base_url() . $brand_logo_icon; ?>" class="brand-mini-logo" alt="<?php echo $brand_short_name; ?>"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><span class="brand-header-text"><?php echo $sidebar_brand_name; ?></span></span>

    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>



        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li style="padding-top: 10px; padding-right: 10px;">
                    <?php
                    $cur_m = (int)date('m');
                    $cur_y = (int)date('Y');
                    $def_fy = ($cur_m >= 4) ? $cur_y : $cur_y - 1;
                    $sel_fy = $this->session->userdata('fy_year');
                    if ($sel_fy === null) {
                        $sel_fy = 'all';
                    }
                    ?>
                    <form method="get" action="" style="margin: 0; display: inline-block;">
                        <?php
                        if (!empty($_GET)) {
                            foreach ($_GET as $gk => $gv) {
                                if ($gk !== 'fy' && is_string($gv)) {
                                    echo '<input type="hidden" name="' . htmlspecialchars($gk) . '" value="' . htmlspecialchars($gv) . '">';
                                }
                            }
                        }
                        ?>
                        <div class="input-group input-group-sm" style="width: 175px;">
                            <span class="input-group-addon" style="background-color: #1a6496; color: #fff; border-color: #1a6496; font-size: 11px; padding: 4px 8px;">
                                <i class="fa fa-calendar"></i> FY
                            </span>
                            <select name="fy" onchange="this.form.submit()" class="form-control input-sm" style="background-color: #1a6496; color: #fff; font-weight: 700; border-color: #1a6496; cursor: pointer; height: 30px; padding: 3px 6px;">
                                <option value="all" <?= ($sel_fy === 'all' || empty($sel_fy)) ? 'selected' : ''; ?>>All FY (All Data)</option>
                                <?php
                                for ($y = $def_fy + 1; $y >= 2020; $y--) {
                                    $lbl = 'FY ' . $y . '-' . substr($y + 1, -2);
                                    $s = ((string)$y === (string)$sel_fy && $sel_fy !== 'all') ? 'selected' : '';
                                    echo "<option value=\"$y\" $s>$lbl</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </li>


                <!-- Approval Notifications Bell (Deletion + Inventory Updates) -->
                <li class="dropdown notifications-menu" id="del-approval-notif-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Approval Requests" onclick="loadPendingDelRequests();">
                        <i class="fa fa-bell-o" style="font-size:18px;"></i>
                        <span class="label label-danger" id="del-approval-badge" style="display:none;position:absolute;top:9px;right:7px;font-size:10px;padding:2px 5px;border-radius:50%;">0</span>
                    </a>
                    <ul class="dropdown-menu" style="width:340px;padding:0;">
                        <li class="header" style="background:#f9f9f9;padding:10px 15px;font-weight:700;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;">
                            <span><i class="fa fa-bell text-orange"></i> Approval Requests</span>
                            <span class="label label-danger" id="del-approval-header-count">0 Pending</span>
                        </li>
                        <li>
                            <ul class="menu" id="del-approval-list" style="max-height:320px;overflow-y:auto;list-style:none;padding:0;margin:0;">
                                <li style="text-align:center;padding:15px;color:#999;"><i class="fa fa-spinner fa-spin"></i> Loading...</li>
                            </ul>
                        </li>
                        <li class="footer" style="background:#f9f9f9;padding:8px;border-top:1px solid #eee;display:flex;justify-content:space-around;">
                            <a href="<?php echo base_url('InventoryApprovalController/index'); ?>" style="color:#3c8dbc;font-weight:600;font-size:12px;"><i class="fa fa-cubes"></i> Inventory Approvals</a>
                            <a href="<?php echo base_url('DeleteApprovalController/panel'); ?>" style="color:#d9534f;font-weight:600;font-size:12px;"><i class="fa fa-trash"></i> Delete Approvals</a>
                        </li>
                    </ul>
                </li>
                <script>
                function updateDelBadgeCount() {
                    if (typeof jQuery !== 'undefined') {
                        jQuery.ajax({
                            url: '<?php echo base_url("InventoryApprovalController/get_pending_count_ajax"); ?>',
                            type: 'GET',
                            dataType: 'json',
                            success: function(res) {
                                var total = (res && res.count) ? parseInt(res.count) : 0;
                                if (total > 0) {
                                    jQuery('#del-approval-badge').text(total).show();
                                    jQuery('#del-approval-header-count').text(total + ' Pending');
                                } else {
                                    jQuery('#del-approval-badge').hide();
                                    jQuery('#del-approval-header-count').text('0 Pending');
                                }
                            }
                        });
                    }
                }
                function loadPendingDelRequests() {
                    if (typeof jQuery !== 'undefined') {
                        jQuery.ajax({
                            url: '<?php echo base_url("InventoryApprovalController/get_pending_html_ajax"); ?>',
                            type: 'GET',
                            success: function(html) {
                                jQuery('#del-approval-list').html(html);
                            }
                        });
                    }
                }
                // Use DOMContentLoaded or direct window.onload to defer checking until scripts are loaded
                window.addEventListener('load', function() {
                    if (typeof jQuery !== 'undefined') {
                        updateDelBadgeCount();
                        setInterval(updateDelBadgeCount, 30000); // refresh every 30 sec
                    }
                });
                </script>

                <li>
                    <button type="button" class="btn btn-primary pull-right calculator-trigger" onclick="openCalculatorWindow();">
                        <i class="fa fa-calculator"></i> Calculator
                    </button>
                </li>

                <li>
                    <button type="button" style="margin-top: 10px;margin-right:10px;"
                        class="btn btn-danger pull-right"
                        onclick="history.back();">
                        <i class="fa fa-arrow-left"></i> Back Button
                    </button>
                </li>
                <li>
                    <button type="button" style="margin-top: 10px;margin-right:10px;"
                        class="btn btn-success pull-right"
                        onclick="location.reload();">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </li>
                <li class="">
                    <div class="popup" onclick="myFunction()"><i class="fa fa-arrows-alt fa-lg" style="margin-top: 20px;margin-right:20px;  color: white "></i>
                        <span class="popuptext" id="myPopup">

                            <form style="margin:10px" method="post" action="<?php echo base_url(); ?>LoginController/login_user" role="login" class="<?php if (
                                                                                                                                                                            $user_role == 'Accounts' || $user_role == 'Sales' ||
                                                                                                                                                                            $user_role == 'Purchase' || $user_role == 'Admin'
                                                                                                                                                                        ) {
                                                                                                                                                                        } else {
                                                                                                                                                                            echo 'hide';
                                                                                                                                                                        }
                                                                                                                                                                        ?>">
                                <input type="hidden" name="user_email" value="<?php echo $user_email ?>" required="" readonly="">
                                <input type="hidden" name="password" value="<?php echo $password ?>" required="" readonly="">

                                <button type="submit" class="btn btn-success btn-block btn-flat" style="background-color: #00a65a !important">View Accounting <i class="fa fa-arrow-circle-right"></i></button>

                            </form>


                            <br>


                            <form style="margin-left:10px; margin-right:10px" method="post" action="<?php echo base_url(); ?>LoginController/login_admin/admin" role="login"
                                class="<?php if ($user_role == 'HR' || $user_role == 'Admin') {
                                        } else {
                                            echo 'hide';
                                        } ?>">
                                <input type="hidden" name="username" value="<?php echo $user_email ?>" required="" readonly="">
                                <input type="hidden" name="password" value="<?php echo $password ?>" required="" readonly="">

                                <button type="submit" class="btn btn-success btn-block btn-flat" style="background-color: #00c0ef !important;">View Payroll <i class="fa fa-arrow-circle-right"></i></button>


                            </form>


                            <br>


                            <form style="margin:10px" method="post" action="<?php echo base_url(); ?>LoginController/login_user" role="login"
                                class="<?php if ($user_role == 'Sales' || $user_role == 'Admin') {
                                        } else {
                                            echo 'hide';
                                        } ?>">
                                <input type="hidden" name="username" value="<?php echo $user_email ?>" required="" readonly="">
                                <input type="hidden" name="password" value="<?php echo $password ?>" required="" readonly="">

                                <button type="submit" class="btn btn-success btn-block btn-flat" style="background-color: #ff851b !important">View CRM <i class="fa fa-arrow-circle-right"></i></button>

                            </form>






                        </span>
                    </div>


                </li>
            </ul>

            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo base_url() . $brand_logo_icon; ?>" class="user-image user-brand-image" alt="<?php echo $brand_short_name; ?>">
                        <span class="hidden-xs"> <?php echo $username; ?></span>

                    </a>

                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?php echo base_url() . $brand_logo_icon; ?>" class="img-circle user-brand-image-large" alt="<?php echo $brand_short_name; ?>">
                            <p>
                                <?php echo $username . "(" . $user_role . ")"; ?>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a class="btn btn-default btn-flat" data-toggle="modal" data-target="#modal_change">Change Password</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo base_url(); ?>LoginController/logout" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>

<?php
$currentPage = $this->router->fetch_class();
$page = $this->router->fetch_method();


// Determine expense_mode_nav based on current page
$expense_mode_nav = '';
if ($currentPage == 'InventoryController') {
    if ($page == 'direct_expense_master' || $page == 'add_expense_data' || $page == 'get_expense_by_id' || $page == 'direct_individual_master' || $page == 'add_direct_individual' || $page == 'edit_direct_individual') {
        $expense_mode_nav = 'direct';
    } elseif ($page == 'indirect_expense_master' || $page == 'indirect_individual_master' || $page == 'add_indirect_individual' || $page == 'edit_indirect_individual') {
        $expense_mode_nav = 'indirect';
    }
} elseif ($currentPage == 'ReportController') {
    if ($page == 'create_expenditure_item_report' || $page == 'get_expenditure_item_report_by_date') {
        // Check query parameters for expense_mode
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        if (strpos($query_string, 'expense_mode=direct') !== false) {
            $expense_mode_nav = 'direct';
        } elseif (strpos($query_string, 'expense_mode=indirect') !== false) {
            $expense_mode_nav = 'indirect';
        }
    }
}
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sticky Sidebar Header (Logo + Search Panel) -->
        <div class="sidebar-sticky-header">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image" style="width:100%;display:flex;align-items:center;justify-content:flex-start;height:auto;padding:0;margin:0;overflow:hidden;line-height:0;background:#fff;">
                    <img src="<?php echo base_url() . $brand_logo_banner; ?>" alt="<?php echo $brand_full_name; ?>" style="width:100%;max-width:100%;height:auto;display:block;margin:0;padding:0;object-fit:contain;object-position:left center;line-height:0;">
                </div>
            </div> 
            <div class="sidebar-search-container">
                <input class="form-control" id="myInput" type="text" placeholder="Search..">
            </div>
        </div>
        <ul class="sidebar-menu" data-widget="tree" id="myList">

            <?php
            // Dynamic Database sidebar menu loader
            $ci =& get_instance();
            if (!function_exists('build_menu_tree')) {
                function build_menu_tree(array $elements, $parentId = null) {
                    $branch = array();
                    foreach ($elements as $element) {
                        $elemParent = (empty($element['parent_id']) || $element['parent_id'] === '0' || $element['parent_id'] === 0) ? null : $element['parent_id'];
                        if ($elemParent == $parentId) {
                            $active_cond = !empty($element['active_cond']) ? json_decode($element['active_cond'], true) : null;
                            $item = array(
                                'title' => $element['title'],
                                'icon' => $element['icon'],
                                'url' => $element['url'],
                                'permission' => $element['permission'],
                            );
                            if ($active_cond !== null) {
                                $item['active_cond'] = $active_cond;
                            }
                            
                            // Skip adding submenus for top-level Dashboard to remove the dropdown
                            if (!(strtolower($element['title']) === 'dashboard' && empty($elemParent))) {
                                $children = build_menu_tree($elements, $element['id']);
                                if ($children) {
                                    $item['submenu'] = $children;
                                }
                            }
                            $branch[] = $item;
                        }
                    }
                    return $branch;
                }
            }

            // Auto-ensure Settings menu item exists in sidebar_menu DB table
            if ($ci->db->table_exists('sidebar_menu')) {
                $ci->db->where('permission', 'Settings')->where_in('parent_id', ['0', 0])->update('sidebar_menu', ['parent_id' => NULL]);

                $has_settings = $ci->db->where('permission', 'Settings')->get('sidebar_menu')->row();
                if (!$has_settings) {
                    $ci->db->insert('sidebar_menu', [
                        'title' => 'Company Settings',
                        'icon' => 'fa fa-cogs',
                        'url' => 'LoginController/get_settings',
                        'permission' => 'Settings',
                        'parent_id' => NULL,
                        'sort_order' => 99,
                        'active_cond' => json_encode([
                            'controllers' => ['LoginController'],
                            'pages' => ['settings', 'get_settings']
                        ])
                    ]);
                }
                
                // Also ensure permission entry exists for Admin role in 'permission' table
                if ($ci->db->table_exists('permission')) {
                    $admin_perm = $ci->db->where('role_id_fk', 1)->where('grp_perm', 'Settings')->get('permission')->row();
                    if (!$admin_perm) {
                        $ci->db->insert('permission', [
                            'role_id_fk' => 1,
                            'grp_perm' => 'Settings'
                        ]);
                    }
                }

                // Auto-ensure SO Approval parent & submenus (SO Approval Dashboard + Order Acceptance) exist in sidebar_menu
                $so_app_parent = $ci->db->group_start()
                    ->where('title', 'SO Approval')
                    ->or_where('permission', 'SO_Approval')
                    ->group_end()
                    ->where('parent_id', 2)
                    ->get('sidebar_menu')->row();

                if (!$so_app_parent) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => 2, // Sales
                        'title'       => 'SO Approval',
                        'icon'        => 'fa fa-check-square-o',
                        'url'         => NULL,
                        'permission'  => NULL,
                        'sort_order'  => 3,
                        'active_cond' => json_encode([
                            'controllers' => ['SalesOrderController', 'OrderConfirmationController']
                        ])
                    ]);
                    $so_app_parent_id = $ci->db->insert_id();
                } else {
                    $so_app_parent_id = $so_app_parent->id;
                    if (!empty($so_app_parent->url) || !empty($so_app_parent->permission)) {
                        $ci->db->where('id', $so_app_parent_id)->update('sidebar_menu', [
                            'url'         => NULL,
                            'permission'  => NULL,
                            'active_cond' => json_encode([
                                'controllers' => ['SalesOrderController', 'OrderConfirmationController']
                            ])
                        ]);
                    }
                }

                // Ensure "SO Approval Dashboard" child menu item exists under SO Approval
                $has_so_dash = $ci->db->where('parent_id', $so_app_parent_id)
                    ->where('permission', 'SO_Approval')
                    ->get('sidebar_menu')->row();

                if (!$has_so_dash) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => $so_app_parent_id,
                        'title'       => 'SO Approval Dashboard',
                        'icon'        => 'fa fa-check-square-o',
                        'url'         => 'SalesOrderController/so_approval_dashboard',
                        'permission'  => 'SO_Approval',
                        'sort_order'  => 0,
                        'active_cond' => json_encode([
                            'controllers' => ['SalesOrderController'],
                            'pages'       => ['so_approval_dashboard']
                        ])
                    ]);
                }

                // Ensure "Order Acceptance" child menu item exists under SO Approval
                $has_oc = $ci->db->where('permission', 'OrderConfirmation')->get('sidebar_menu')->row();
                if (!$has_oc) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => $so_app_parent_id,
                        'title'       => 'Order Acceptance',
                        'icon'        => 'fa fa-file-text-o',
                        'url'         => 'OrderConfirmationController/index',
                        'permission'  => 'OrderConfirmation',
                        'sort_order'  => 1,
                        'active_cond' => json_encode([
                            'controllers' => ['OrderConfirmationController']
                        ])
                    ]);
                } else if ($has_oc->parent_id != $so_app_parent_id) {
                    $ci->db->where('id', $has_oc->id)->update('sidebar_menu', [
                        'parent_id'  => $so_app_parent_id,
                        'title'      => 'Order Acceptance',
                        'sort_order' => 1
                    ]);
                }

                // Auto-ensure Engineering parent & submenus (BOM, Datasheet Upload, Budget Sheet Upload) exist in sidebar_menu
                $eng_parent = $ci->db->group_start()
                    ->where('title', 'Engineering')
                    ->or_where('permission', 'Engineering')
                    ->group_end()
                    ->group_start()
                    ->where('parent_id', 0)
                    ->or_where('parent_id', NULL)
                    ->or_where('parent_id', '')
                    ->group_end()
                    ->get('sidebar_menu')->row();

                if (!$eng_parent) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => NULL,
                        'title'       => 'Engineering',
                        'icon'        => 'fa fa-sitemap',
                        'url'         => NULL,
                        'permission'  => 'Engineering',
                        'sort_order'  => 4,
                        'active_cond' => json_encode([
                            'controllers' => ['BomController', 'EngineeringController']
                        ])
                    ]);
                    $eng_parent_id = $ci->db->insert_id();
                } else {
                    $eng_parent_id = $eng_parent->id;
                    $ci->db->where('id', $eng_parent_id)->update('sidebar_menu', [
                        'active_cond' => json_encode([
                            'controllers' => ['BomController', 'EngineeringController']
                        ])
                    ]);
                }

                // Ensure "BOM" child menu item exists under Engineering
                $has_bom = $ci->db->where('permission', 'Bom')->get('sidebar_menu')->row();
                if (!$has_bom) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => $eng_parent_id,
                        'title'       => 'BOM',
                        'icon'        => 'fa fa-list-alt',
                        'url'         => 'BomController/index',
                        'permission'  => 'Bom',
                        'sort_order'  => 0,
                        'active_cond' => json_encode([
                            'controllers' => ['BomController']
                        ])
                    ]);
                } else if ($has_bom->parent_id != $eng_parent_id) {
                    $ci->db->where('id', $has_bom->id)->update('sidebar_menu', [
                        'parent_id' => $eng_parent_id,
                        'sort_order' => 0
                    ]);
                }

                // Ensure "Datasheet Upload" child menu item exists under Engineering
                $has_datasheet = $ci->db->where('permission', 'Datasheet_Upload')->get('sidebar_menu')->row();
                if (!$has_datasheet) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => $eng_parent_id,
                        'title'       => 'Datasheet Upload',
                        'icon'        => 'fa fa-file-text-o',
                        'url'         => 'EngineeringController/datasheets',
                        'permission'  => 'Datasheet_Upload',
                        'sort_order'  => 1,
                        'active_cond' => json_encode([
                            'controllers' => ['EngineeringController'],
                            'pages'       => ['datasheets']
                        ])
                    ]);
                }

                // Ensure "Budget Sheet Upload" child menu item exists under Engineering
                $has_budget = $ci->db->where('permission', 'Budget_Sheet_Upload')->get('sidebar_menu')->row();
                if (!$has_budget) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => $eng_parent_id,
                        'title'       => 'Budget Sheet Upload',
                        'icon'        => 'fa fa-line-chart',
                        'url'         => 'EngineeringController/budget_sheets',
                        'permission'  => 'Budget_Sheet_Upload',
                        'sort_order'  => 2,
                        'active_cond' => json_encode([
                            'controllers' => ['EngineeringController'],
                            'pages'       => ['budget_sheets']
                        ])
                    ]);
                }

                // Ensure "Inventory Approval" menu item exists under Store / Inventory (parent_id = 19)
                $has_inv_approval = $ci->db->where('url', 'InventoryApprovalController/index')->get('sidebar_menu')->row();
                if (!$has_inv_approval) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => 19,
                        'title'       => 'Inventory Approval',
                        'icon'        => 'fa fa-check-square-o',
                        'url'         => 'InventoryApprovalController/index',
                        'permission'  => 'Inventory_Approval',
                        'sort_order'  => 25,
                        'active_cond' => json_encode([
                            'controllers' => ['InventoryApprovalController'],
                            'pages'       => ['index']
                        ])
                    ]);
                }

                // Ensure "Item Deletion Requests" menu item exists under Store / Inventory (parent_id = 19)
                $has_del_requests = $ci->db->where('url', 'DeleteApprovalController/panel')->get('sidebar_menu')->row();
                if (!$has_del_requests) {
                    $ci->db->insert('sidebar_menu', [
                        'parent_id'   => 19,
                        'title'       => 'Deletion Requests',
                        'icon'        => 'fa fa-trash',
                        'url'         => 'DeleteApprovalController/panel',
                        'permission'  => NULL, // accessible to everyone who has Store / Inventory access
                        'sort_order'  => 50,
                        'active_cond' => json_encode([
                            'controllers' => ['DeleteApprovalController'],
                            'pages'       => ['panel']
                        ])
                    ]);
                }

                // Auto-assign permissions to Admin role (role_id 1)
                if ($ci->db->table_exists('permission')) {
                    foreach (['SO_Approval', 'OrderConfirmation', 'Engineering', 'Bom', 'Datasheet_Upload', 'Budget_Sheet_Upload', 'Inventory_Approval'] as $perm_key) {
                        $admin_perm = $ci->db->where('role_id_fk', 1)->where('grp_perm', $perm_key)->get('permission')->row();
                        if (!$admin_perm) {
                            $ci->db->insert('permission', [
                                'role_id_fk' => 1,
                                'grp_perm'   => $perm_key
                            ]);
                        }
                    }
                }
            }

            $db_menu_rows = $ci->db->order_by('sort_order', 'ASC')->get('sidebar_menu')->result_array();
            $menu_items = build_menu_tree($db_menu_rows);

            if (!function_exists('is_menu_active')) {
                function is_menu_active($item, $currentPage, $page) {
                    if (isset($item['active_cond'])) {
                        $cond = $item['active_cond'];
                        
                        // Check currentPage if specified
                        if (isset($cond['currentPage']) && $currentPage != $cond['currentPage']) {
                            return false;
                        }
                        
                        // Check controllers list if specified
                        if (isset($cond['controllers']) && !in_array($currentPage, $cond['controllers'])) {
                            return false;
                        }
                        
                        // Check page if specified
                        if (isset($cond['page']) && $page != $cond['page']) {
                            return false;
                        }
                        
                        // Check pages list if specified
                        if (isset($cond['pages']) && !in_array($page, $cond['pages'])) {
                            return false;
                        }
                        
                        // Check custom_page_match if specified
                        if (isset($cond['custom_page_match']) && strpos($page, $cond['custom_page_match']) === false) {
                            return false;
                        }
                        
                        // Check custom_check for accounts if specified
                        if (isset($cond['custom_check']) && $cond['custom_check'] == 'accounts') {
                            if ($currentPage == 'SupplierController' && !in_array($page, ['view_purchase_bill', 'purchase_bill'])) {
                                return false;
                            }
                        }
                        
                        // If it passed all specified conditions, it is active
                        return true;
                    }
                    if (isset($item['submenu']) && !empty($item['submenu'])) {
                        foreach ($item['submenu'] as $sub) {
                            if (is_menu_active($sub, $currentPage, $page)) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            }

            if (!function_exists('has_menu_permission')) {
                function has_menu_permission($item, $permissions, $is_admin = false) {
                    // Safe guard: Always keep critical security/management menus open for Admin
                    if ($is_admin && !empty($item['permission'])) {
                        $perm_lower = strtolower($item['permission']);
                        if (in_array($perm_lower, ['role', 'groups', 'settings', 'users', 'permission', 'engineering', 'datasheet_upload', 'budget_sheet_upload'])) {
                            return true;
                        }
                    }

                    // 1. If the item requires a specific permission, user must have it
                    if (!empty($item['permission'])) {
                        return in_array($item['permission'], $permissions);
                    }
                    
                    // 2. If no permission is required, check if submenus have permission
                    if (!empty($item['submenu'])) {
                        foreach ($item['submenu'] as $sub) {
                            if (has_menu_permission($sub, $permissions, $is_admin)) {
                                return true;
                            }
                        }
                        return false;
                    }
                    
                    // 3. If no permission and no submenu, it's public (e.g. Dashboard)
                    return true;
                }
            }

            if (!function_exists('render_sidebar_menu')) {
                function render_sidebar_menu($items, $permissions, $currentPage, $page, $ci, $is_admin = false) {
                    foreach ($items as $item) {
                        if (!has_menu_permission($item, $permissions, $is_admin)) {
                            continue;
                        }
                        
                        $has_submenu = isset($item['submenu']) && !empty($item['submenu']);
                        $is_active = is_menu_active($item, $currentPage, $page);
                        
                        $active_class = $is_active ? ($has_submenu ? 'active menu-open' : 'active') : '';
                        $li_class = $has_submenu ? "treeview $active_class" : $active_class;
                        
                        echo '<li class="' . $li_class . '">';
                        if ($has_submenu) {
                            echo '<a href="#">';
                            echo '<i class="' . $item['icon'] . '"></i> ';
                            echo '<span>' . $item['title'] . '</span>';
                            echo '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
                            echo '</a>';
                            echo '<ul class="treeview-menu">';
                            render_sidebar_menu($item['submenu'], $permissions, $currentPage, $page, $ci, $is_admin);
                            echo '</ul>';
                        } else {
                            $url = base_url() . $item['url'];
                            echo '<a href="' . $url . '">';
                            echo '<i class="' . $item['icon'] . '"></i> ';
                            echo '<span>' . $item['title'] . '</span>';
                            
                            if (isset($item['badge'])) {
                                $pending_count = 0;
                                if ($item['badge']['type'] == 'po_approvals') {
                                    $session_user_email = $ci->session->userdata('session_data_head')['result']['user_email'] ?? '';
                                    $session_role_name  = $ci->session->userdata('session_data_head')['result']['role_name'] ?? '';
                                    if (strtolower($session_role_name) === 'admin') {
                                        $pending_count = $ci->db->where('status', 'pending')->count_all_results('po_approvals');
                                    } else {
                                        $ci->load->model('purchase_model');
                                        $pending_count = $ci->purchase_model->get_pending_count($session_user_email);
                                    }
                                } elseif ($item['badge']['type'] == 'grn_approvals') {
                                    $ci->load->model('grn');
                                    $pending_approvals = $ci->grn->get_pending_grn_approvals($ci->session->userdata('session_data_head')['result']['user_email'] ?? '');
                                    $pending_count = is_array($pending_approvals) ? count($pending_approvals) : 0;
                                }
                                if ($pending_count > 0) {
                                    echo '<span class="pull-right-container"><small class="label pull-right bg-red">' . $pending_count . '</small></span>';
                                }
                            }
                            echo '</a>';
                        }
                        echo '</li>';
                    }
                }
            }

            $user_role_id = $session_data_head['result']['role'] ?? null;
            $role_name_session = $session_data_head['result']['role_name'] ?? '';
            $is_admin_user = (strtolower($role_name_session) === 'admin' || $user_role_id == 1);

            $db_permissions = array();
            if ($user_role_id) {
                // Get permissions directly from the permission table (live)
                $perms = $ci->db->select('grp_perm')->where('role_id_fk', $user_role_id)->get('permission')->result_array();
                foreach ($perms as $p) {
                    $db_permissions[] = $p['grp_perm'];
                }
            }

            // Fallback to session permissions if DB lookup failed
            if (empty($db_permissions)) {
                $db_permissions = isset($session_data_head['permission']) ? $session_data_head['permission'] : array();
            }

            render_sidebar_menu($menu_items, $db_permissions, $currentPage, $page, $ci, $is_admin_user);
            ?>
</ul>
    </section>
    <!-- /.sidebar -->
</aside>
<div class="modal fade" id="modal_change" role="dialog">
    <div class="modal-dialog">

        <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>LoginController/change_password" enctype="multipart/form-data" onsubmit="return checkForm(this);">
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Change Password
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>

                        </h4>
                    </center>
                </div>
                <div class="modal-body form">
                    <input type="hidden" value="" id="id" name="id" />
                    <div class="form-body">

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Email ID<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control input-sm" name="email_id" id="email_id" required=""
                                    pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">New Password<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control input-sm" name="new_password" id="new_password" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 control-label">Confirm Password<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control input-sm" name="confirm_password" id="confirm_password" required="">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" id="changepassword" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>

                </div>
            </div><!-- /.modal-content -->


        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <div id="calculatorCallWindow" class="calculator-call-window" aria-hidden="true">
        <div id="calculatorCallHeader" class="calculator-call-header">
            <span class="calculator-call-title">Calculator</span>
            <div class="calculator-call-actions">
                <button type="button" class="calculator-call-action" onclick="toggleCalculatorMinimize();" title="Minimize">-</button>
                <button type="button" class="calculator-call-action" onclick="closeCalculatorWindow();" title="Close">x</button>
            </div>
        </div>
        <div class="calculator-call-body">
            <div class="calculator-shell">
                <h2 class="calculator-title">Calculator</h2>
                <input type="text" id="calculator_display" class="calculator-display" value="0" readonly>
                <div class="calculator-grid">
                    <button type="button" class="clear" onclick="appendValue('C')">C</button>
                    <button type="button" class="operator" onclick="appendValue('(')">(</button>
                    <button type="button" class="operator" onclick="appendValue(')')">)</button>
                    <button type="button" class="operator" onclick="appendValue('/')">/</button>

                    <button type="button" onclick="appendValue('7')">7</button>
                    <button type="button" onclick="appendValue('8')">8</button>
                    <button type="button" onclick="appendValue('9')">9</button>
                    <button type="button" class="operator" onclick="appendValue('*')">*</button>

                    <button type="button" onclick="appendValue('4')">4</button>
                    <button type="button" onclick="appendValue('5')">5</button>
                    <button type="button" onclick="appendValue('6')">6</button>
                    <button type="button" class="operator" onclick="appendValue('-')">-</button>

                    <button type="button" onclick="appendValue('1')">1</button>
                    <button type="button" onclick="appendValue('2')">2</button>
                    <button type="button" onclick="appendValue('3')">3</button>
                    <button type="button" class="operator" onclick="appendValue('+')">+</button>

                    <button type="button" onclick="appendValue('0')">0</button>
                    <button type="button" onclick="appendValue('00')">00</button>
                    <button type="button" onclick="appendValue('.')">.</button>
                    <button type="button" class="equal" onclick="calculateResult()">=</button>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- jQuery 3 -->
<script src="<?php echo base_url() ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url() ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables with Buttons (from CDN) -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url() ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url() ?>dist/js/demo.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/ckeditor/ckeditor.js"></script>

<script src="<?php echo base_url(); ?>assets/js/custom.js?v=<?php echo time(); ?>"></script>

<!-- Search and Dropdown -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<!-- ./Search and Dropdown -->



<script>
    var calculatorWindow = null;
    var calculatorDragged = false;
    var calculatorDragOffsetX = 0;
    var calculatorDragOffsetY = 0;

    function openCalculatorWindow() {
        var panel = document.getElementById('calculatorCallWindow');
        if (!panel) {
            return;
        }

        panel.classList.add('show');
        panel.classList.remove('minimized');
        panel.style.right = '28px';
        panel.style.bottom = '28px';
        panel.style.left = '';
        panel.style.top = '';
        panel.setAttribute('aria-hidden', 'false');

        calculatorWindow = panel;
    }

    function closeCalculatorWindow() {
        var panel = document.getElementById('calculatorCallWindow');
        if (!panel) {
            return;
        }

        panel.classList.remove('show');
        panel.setAttribute('aria-hidden', 'true');
    }

    function toggleCalculatorMinimize() {
        var panel = document.getElementById('calculatorCallWindow');
        if (!panel) {
            return;
        }

        panel.classList.toggle('minimized');
    }

    function appendValue(value) {
        var display = document.getElementById('calculator_display');
        if (!display) {
            return;
        }

        if (value === 'C') {
            clearDisplay();
            return;
        }

        if (display.value === '0' || display.value === 'Error') {
            display.value = '';
        }
        display.value += value;
    }

    function clearDisplay() {
        var display = document.getElementById('calculator_display');
        if (display) {
            display.value = '0';
        }
    }

    function calculateResult() {
        var display = document.getElementById('calculator_display');
        if (!display) {
            return;
        }

        var expression = display.value;

        if (!/^[0-9+\-*/().\s]+$/.test(expression)) {
            display.value = 'Error';
            return;
        }

        try {
            var result = Function('return (' + expression + ')')();
            if (typeof result === 'undefined' || result === null || !isFinite(result)) {
                display.value = 'Error';
                return;
            }
            display.value = result;
        } catch (error) {
            display.value = 'Error';
        }
    }

    function initCalculatorDrag() {
        var panel = document.getElementById('calculatorCallWindow');
        var header = document.getElementById('calculatorCallHeader');
        if (!panel || !header) {
            return;
        }

        header.addEventListener('mousedown', function(event) {
            calculatorDragged = true;
            var rect = panel.getBoundingClientRect();
            calculatorDragOffsetX = event.clientX - rect.left;
            calculatorDragOffsetY = event.clientY - rect.top;
            panel.style.right = 'auto';
            panel.style.bottom = 'auto';
            event.preventDefault();
        });

        document.addEventListener('mousemove', function(event) {
            if (!calculatorDragged) {
                return;
            }

            var left = event.clientX - calculatorDragOffsetX;
            var top = event.clientY - calculatorDragOffsetY;

            var maxLeft = window.innerWidth - panel.offsetWidth;
            var maxTop = window.innerHeight - panel.offsetHeight;

            left = Math.max(0, Math.min(left, maxLeft));
            top = Math.max(0, Math.min(top, maxTop));

            panel.style.left = left + 'px';
            panel.style.top = top + 'px';
        });

        document.addEventListener('mouseup', function() {
            calculatorDragged = false;
        });
    }

    // When the user clicks on div, open the popup
    function myFunction() {
        var popup = document.getElementById("myPopup");
        popup.classList.toggle("show");
    }

(function($) {
    'use strict';

    /* ── Wait for DOM ─────────────────────────────────────────────── */
    $(function() {

        /* 1. SEARCH FILTER */
        $('#myInput').on('input', function() {
            var val = $(this).val().toLowerCase().trim();
            if (val === '') {
                $('#myList li').show();
                $('#myList .treeview-menu').css('display', '');
                $('#myList li.treeview').removeClass('menu-open');
                
                // Restore active menu
                $('.sidebar-menu li.active').parents('.treeview').addClass('menu-open').children('.treeview-menu').show();
                return;
            }

            var words = val.split(/\s+/);

            $('#myList li').hide();
            $('#myList li.treeview').removeClass('menu-open');
            $('#myList .treeview-menu').hide();

            $('#myList a').each(function() {
                var text = $(this).text().toLowerCase().trim();
                if (!text) return;

                var match = true;
                for (var i = 0; i < words.length; i++) {
                    if (text.indexOf(words[i]) === -1) {
                        match = false;
                        break;
                    }
                }

                if (match) {
                    var $li = $(this).parent();
                    $li.show();
                    
                    // Expand parents
                    $li.parents('li').show().addClass('menu-open');
                    $li.parents('.treeview-menu').show();
                    
                    // Show children if this is a parent category
                    $li.find('li').show();
                    $li.find('.treeview-menu').show();
                    $li.find('li.treeview').addClass('menu-open');
                }
            });
        });

        /* 4. APPS SWITCHER POPUP */
        window.myFunction = function() {
            document.getElementById('myPopup').classList.toggle('show');
        };
        /* Close popup when clicking outside */
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.popup').length) {
                var popup = document.getElementById('myPopup');
                if (popup) popup.classList.remove('show');
            }
        });

        /* 5. BOOTSTRAP DROPDOWNS */
        $('[data-toggle="dropdown"]').dropdown();
        $('.user-menu').on('click', function(e) { e.stopPropagation(); });

        /* 6. CALCULATOR DRAG */
        initCalculatorDrag();
    });

    /* ── Calculator drag ─────────────────────────────────────────── */
    window.initCalculatorDrag = function() {
        var panel  = document.getElementById('calculatorWindow');
        var header = document.getElementById('calculatorHeader');
        if (!panel || !header) return;
        var ox = 0, oy = 0, dragging = false;
        header.addEventListener('mousedown', function(e) {
            dragging = true;
            panel.style.bottom = 'auto';
            panel.style.right  = 'auto';
            ox = e.clientX - panel.getBoundingClientRect().left;
            oy = e.clientY - panel.getBoundingClientRect().top;
        });
        document.addEventListener('mousemove', function(e) {
            if (!dragging) return;
            var l = Math.max(0, Math.min(e.clientX - ox, window.innerWidth  - panel.offsetWidth));
            var t = Math.max(0, Math.min(e.clientY - oy, window.innerHeight - panel.offsetHeight));
            panel.style.left = l + 'px';
            panel.style.top  = t + 'px';
        });
        document.addEventListener('mouseup', function() { dragging = false; });
    };

})(jQuery);

/* ── PAGE LOADER CONTROLLER ───────────────────────────── */
(function() {
    'use strict';

    var loader   = document.getElementById('erp-page-loader');
    var topBar   = document.getElementById('erp-top-bar');
    var hideTimer = null;

    /* Hide loader once page fully ready */
    function hideLoader() {
        console.log("hideLoader called. Loader element:", loader);
        if (loader) {
            loader.classList.add('hidden');
            console.log("Added hidden class. Classes:", loader.className);
        } else {
            console.warn("Loader element not found!");
        }
        if (topBar) {
            topBar.style.width = '100%';
            setTimeout(function() {
                topBar.style.width = '0';
            }, 300);
        }
    }

    function showTopBar() {
        if (!topBar) return;
        topBar.style.transition = 'none';
        topBar.style.width = '0';
        setTimeout(function() {
            topBar.style.transition = 'width 1.5s ease-out';
            topBar.style.width = '85%';
        }, 20);
        clearTimeout(hideTimer);
        hideTimer = setTimeout(function() {
            if (topBar) topBar.style.width = '100%';
            setTimeout(function() { if (topBar) topBar.style.width = '0'; }, 300);
        }, 2500);
    }

    /* Hide full-screen overlay when DOM ready */
    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(hideLoader, 50);
        });
    }

    /* Show top progress bar on internal link clicks (without screen blocking) */
    document.addEventListener('click', function(e) {
        var anchor = e.target.closest('a');
        if (!anchor) return;
        var href = anchor.getAttribute('href');
        if (!href) return;
        if (anchor.target === '_blank') return;
        if (href === '#' || href === '' || href.indexOf('javascript') === 0) return;
        if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;
        if (href.indexOf('http') === 0 && href.indexOf(window.location.hostname) === -1) return;
        
        showTopBar();
    });

    /* Show top bar on form submits */
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (form && form.getAttribute('data-ajax') === 'true') return;
        showTopBar();
    });

    /* Show on browser back/forward */
    window.addEventListener('popstate', function() {
        showLoader();
    });

    /* Hide if jQuery AJAX completes a full page load accidentally */
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxStop(function() {
            /* Only hide if page is fully loaded */
            if (document.readyState === 'complete') {
                setTimeout(hideLoader, 300);
            }
        });
    }

})();
</script>

<!-- ═══════════════════════════════════════════════════════════
     OFFLINE / NETWORK ERROR OVERLAY
     Shows when the browser loses internet connection
════════════════════════════════════════════════════════════ -->
<style>
/* ── Offline overlay ── */
#erp-offline-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(10, 15, 30, 0.97);
    backdrop-filter: blur(6px);
    align-items: center; justify-content: center;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    animation: overlay-in .35s ease both;
}
#erp-offline-overlay.active { display: flex; }
@keyframes overlay-in { from{opacity:0} to{opacity:1} }

.erp-offline-card {
    background: #1e293b; border: 1px solid rgba(6,182,212,.3);
    border-radius: 24px; padding: 48px 44px; max-width: 460px; width: 90%;
    text-align: center; box-shadow: 0 32px 80px rgba(0,0,0,.6);
    animation: card-in .5s cubic-bezier(.16,1,.3,1) both;
    position: relative;
}
@keyframes card-in { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }

.erp-offline-card .wifi-wrap {
    width: 100px; height: 84px; margin: 0 auto 12px;
    position: relative; display: flex; align-items: flex-end; justify-content: center;
}
.erp-offline-card .wifi-arc {
    position: absolute; border: 2.5px solid #06b6d4; border-radius: 50%;
    border-bottom-color: transparent; border-left-color: transparent;
    animation: wf-pulse 1.8s ease-in-out infinite; bottom: 0;
}
.wfa1 { width:20px;height:20px; animation-delay:.0s; }
.wfa2 { width:44px;height:44px; animation-delay:.15s; }
.wfa3 { width:70px;height:70px; animation-delay:.3s; }
@keyframes wf-pulse { 0%,100%{opacity:1} 50%{opacity:.15} }
.erp-offline-card .wifi-dot { width:8px;height:8px;border-radius:50%;background:#06b6d4;position:absolute;bottom:0;left:50%;transform:translateX(-50%);box-shadow:0 0 10px #06b6d4;animation:dot-blink2 1.8s ease-in-out infinite; }
@keyframes dot-blink2 { 0%,100%{opacity:1} 50%{opacity:.2} }
.erp-offline-card .wifi-cross { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:38px;filter:drop-shadow(0 0 8px rgba(239,68,68,.7));animation:cross-pulse 2s ease-in-out infinite; }
@keyframes cross-pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.12)} }

.erp-offline-title { font-size: 20px; font-weight: 700; color: #e2e8f0; margin: 10px 0 6px; }
.erp-offline-sub { font-size: 13px; color: #94a3b8; line-height: 1.6; margin-bottom: 20px; }

.erp-offline-retry-ring { width: 56px; height: 56px; margin: 0 auto 8px; position: relative; }
.erp-offline-retry-ring svg { transform: rotate(-90deg); }
.ort { fill:none; stroke:#334155; stroke-width:4; }
.orf { fill:none; stroke:#06b6d4; stroke-width:4; stroke-linecap:round; stroke-dasharray:150; stroke-dashoffset:150; }
.orf.counting { animation: ring-cnt 10s linear forwards; }
@keyframes ring-cnt { from{stroke-dashoffset:150} to{stroke-dashoffset:0} }
.erp-offline-retry-num { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#06b6d4; }

.erp-offline-btns { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:16px; }
.erp-offline-btn { display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:all .2s;text-decoration:none; }
.erp-offline-btn-primary { background: linear-gradient(135deg,#06b6d4,#3b82f6); color:#fff; }
.erp-offline-btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(6,182,212,.4); }
.erp-offline-btn-ghost { background:transparent; border:1px solid #334155; color:#94a3b8; }
.erp-offline-btn-ghost:hover { border-color:#06b6d4; color:#06b6d4; }

/* ── Online toast ── */
#erp-online-toast {
    display: none;
    position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg,#22c55e,#16a34a);
    color: #fff; padding: 12px 28px; border-radius: 99px;
    font-weight: 600; font-size: 14px; font-family: 'Inter','Segoe UI',sans-serif;
    box-shadow: 0 8px 24px rgba(34,197,94,.45); z-index: 999999;
    animation: toast-in .4s cubic-bezier(.16,1,.3,1) both;
    white-space: nowrap;
}
@keyframes toast-in { from{opacity:0;transform:translateX(-50%) translateY(-20px)} to{opacity:1;transform:translateX(-50%) translateY(0)} }

/* ── AJAX error toast ── */
#erp-ajax-toast {
    display: none;
    position: fixed; bottom: 24px; right: 24px;
    background: #1e293b; border: 1px solid rgba(239,68,68,.4);
    color: #e2e8f0; padding: 14px 20px; border-radius: 14px;
    font-family: 'Inter','Segoe UI',sans-serif; font-size: 13px;
    box-shadow: 0 8px 32px rgba(0,0,0,.4); z-index: 99998; max-width: 320px;
    animation: ajax-in .4s cubic-bezier(.16,1,.3,1) both;
}
@keyframes ajax-in { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.erp-ajax-icon { font-size:18px; margin-right:8px; }
.erp-ajax-close { float:right; cursor:pointer; color:#94a3b8; font-size:16px; margin-left:12px; line-height:1; }
.erp-ajax-close:hover { color:#e2e8f0; }
</style>

<!-- Offline overlay HTML -->
<div id="erp-offline-overlay">
    <div class="erp-offline-card">
        <div class="wifi-wrap">
            <div class="wifi-arc wfa3"></div>
            <div class="wifi-arc wfa2"></div>
            <div class="wifi-arc wfa1"></div>
            <div class="wifi-dot"></div>
            <div class="wifi-cross">&#10060;</div>
        </div>
        <div class="erp-offline-title">No Internet Connection</div>
        <div class="erp-offline-sub">You appear to be offline.<br>Check your network and we'll retry automatically.</div>

        <div class="erp-offline-retry-ring">
            <svg width="56" height="56" viewBox="0 0 56 56">
                <circle class="ort" cx="28" cy="28" r="24"/>
                <circle class="orf" id="erp-orf" cx="28" cy="28" r="24"/>
            </svg>
            <div class="erp-offline-retry-num" id="erp-retry-num">10</div>
        </div>

        <div class="erp-offline-btns">
            <button class="erp-offline-btn erp-offline-btn-primary" onclick="location.reload()">&#8635; Retry Now</button>
            <button class="erp-offline-btn erp-offline-btn-ghost" onclick="history.back()">&#8592; Go Back</button>
        </div>
    </div>
</div>

<!-- Online toast -->
<div id="erp-online-toast">&#10003;&nbsp; Connection Restored! Refreshing...</div>

<!-- AJAX error toast -->
<div id="erp-ajax-toast">
    <span class="erp-ajax-close" id="erp-ajax-close">&#10005;</span>
    <span class="erp-ajax-icon">&#9888;</span>
    <span id="erp-ajax-msg">Server error. Please try again.</span>
</div>

<script>
(function() {
    'use strict';

    var overlay    = document.getElementById('erp-offline-overlay');
    var toast      = document.getElementById('erp-online-toast');
    var ajaxToast  = document.getElementById('erp-ajax-toast');
    var ajaxMsg    = document.getElementById('erp-ajax-msg');
    var retryNum   = document.getElementById('erp-retry-num');
    var orf        = document.getElementById('erp-orf');
    var retryTimer = null;
    var countTimer = null;
    var ajaxHideTimer = null;

    function showOffline() {
        if (!overlay) return;
        overlay.classList.add('active');
        startCountdown();
    }

    function hideOffline() {
        if (!overlay) return;
        overlay.classList.remove('active');
        clearCountdown();
    }

    function startCountdown() {
        clearCountdown();
        var secs = 10;
        if (retryNum) retryNum.textContent = secs;
        if (orf) {
            orf.classList.remove('counting');
            void orf.offsetWidth; // reflow
            orf.classList.add('counting');
        }
        countTimer = setInterval(function() {
            secs--;
            if (retryNum) retryNum.textContent = secs;
            if (secs <= 0) {
                clearInterval(countTimer);
                location.reload();
            }
        }, 1000);
    }

    function clearCountdown() {
        if (countTimer) { clearInterval(countTimer); countTimer = null; }
        if (retryNum) retryNum.textContent = '10';
        if (orf) { orf.classList.remove('counting'); }
    }

    function showOnlineToast() {
        if (!toast) return;
        toast.style.display = 'block';
        setTimeout(function() { location.reload(); }, 1800);
    }

    // Initial check
    if (!navigator.onLine) { showOffline(); }

    window.addEventListener('offline', showOffline);
    window.addEventListener('online',  function() {
        hideOffline();
        showOnlineToast();
    });

    // ── AJAX error interceptor ──────────────────────────────────
    var closeBtn = document.getElementById('erp-ajax-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (ajaxToast) ajaxToast.style.display = 'none';
        });
    }

    function showAjaxError(msg) {
        if (!ajaxToast || !ajaxMsg) return;
        ajaxMsg.textContent = msg || 'Server error. Please try again.';
        ajaxToast.style.display = 'block';
        if (ajaxHideTimer) clearTimeout(ajaxHideTimer);
        ajaxHideTimer = setTimeout(function() { ajaxToast.style.display = 'none'; }, 6000);
    }

    // jQuery AJAX global error handler
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxError(function(event, xhr, settings, error) {
            if (!navigator.onLine) { return; } // handled by offline overlay
            var status = xhr && xhr.status ? xhr.status : 0;
            if (status === 0) { return; } // aborted / navigation
            var msg = 'Request failed';
            if      (status === 404) { msg = '404 – Page not found.'; }
            else if (status === 403) { msg = '403 – Access denied.'; }
            else if (status === 500) { msg = '500 – Server error. Please retry.'; }
            else if (status === 503) { msg = 'Service temporarily unavailable.'; }
            else if (status >= 400)  { msg = 'Error ' + status + ' – Something went wrong.'; }
            showAjaxError(msg);
        });
    }

})();
</script>

<!-- Delete Request Reason Modal -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" role="dialog" aria-labelledby="deleteRequestModalLabel" style="z-index: 999999;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #d9534f; color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteRequestModalLabel" style="font-weight: 600;"><i class="fa fa-trash"></i> Request Deletion Approval</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="alert alert-warning" style="margin-bottom: 15px; border-radius: 4px; padding: 10px 15px; font-weight: 600;">
                    <i class="fa fa-exclamation-triangle"></i> This action requires approval from the Admin. The item will not be deleted until the request is approved.
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #333;">Item Code / Name</label>
                    <input type="text" id="delModalItemDisplay" class="form-control" readonly style="background-color: #eee; font-weight: bold; height: 38px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #333;">Reason for Deletion <span class="text-danger">*</span></label>
                    <textarea id="delModalReason" class="form-control" rows="4" placeholder="Please enter a detailed reason for deleting this item..." style="resize: none; border-radius: 4px;"></textarea>
                    <span class="help-block text-danger" id="delModalError" style="display: none; font-weight: 600; margin-top: 5px;">Reason is required.</span>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f9f9f9; border-top: 1px solid #eee; padding: 15px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600;">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitDeleteModalRequest();" style="font-weight: 600;"><i class="fa fa-paper-plane"></i> Send Request</button>
            </div>
        </div>
    </div>
</div>


<script>
var currentDelModalItemId = '';
var currentDelModalModule = '';

function openDeleteRequestModal(itemId, itemCode, module) {
    currentDelModalItemId = itemId;
    currentDelModalModule = module;
    document.getElementById('delModalItemDisplay').value = itemCode;
    document.getElementById('delModalReason').value = '';
    document.getElementById('delModalError').style.display = 'none';
    jQuery('#deleteRequestModal').modal('show');
}

function submitDeleteModalRequest() {
    var reason = document.getElementById('delModalReason').value.trim();
    if (reason === '') {
        document.getElementById('delModalError').style.display = 'block';
        return;
    }
    
    var baseUrl = '<?php echo base_url(); ?>';
    var redirectUrl = window.location.href.replace(baseUrl, '');
    
    window.location.href = baseUrl + 'DeleteApprovalController/request_delete?item_id=' + encodeURIComponent(currentDelModalItemId) + 
                          '&module=' + encodeURIComponent(currentDelModalModule) + 
                          '&reason=' + encodeURIComponent(reason) + 
                          '&redirect_url=' + encodeURIComponent(redirectUrl);
}

// ── Edit Item Approval Request Modal ──
var currentEditItemId = '';
var currentEditItemCode = '';
var currentEditItemName = '';

function openEditRequestModal(itemId, itemCode, itemName) {
    currentEditItemId   = itemId;
    currentEditItemCode = itemCode;
    currentEditItemName = itemName;
    document.getElementById('editModalItemDisplay').value = itemCode + ' — ' + itemName;
    document.getElementById('editModalReason').value = '';
    document.getElementById('editModalError').style.display = 'none';
    jQuery('#editRequestModal').modal('show');
}

function submitEditModalRequest() {
    var reason = document.getElementById('editModalReason').value.trim();
    if (reason === '') {
        document.getElementById('editModalError').style.display = 'block';
        return;
    }
    var baseUrl = '<?php echo base_url(); ?>';
    window.location.href = baseUrl + 'InventoryController/get_inventory_by_id/' + encodeURIComponent(currentEditItemId) + '?edit_reason=' + encodeURIComponent(reason);
    jQuery('#editRequestModal').modal('hide');
}
</script>

<!-- Edit Request Reason Modal -->
<div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" style="z-index: 999999;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #3c8dbc; color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editRequestModalLabel" style="font-weight: 600;"><i class="fa fa-pencil-square"></i> Request Item Edit Approval</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="alert alert-info" style="margin-bottom: 15px; border-radius: 4px; padding: 10px 15px; font-weight: 600;">
                    <i class="fa fa-info-circle"></i> Changes to this item require Admin approval before they take effect.
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #333;">Item Code / Name</label>
                    <input type="text" id="editModalItemDisplay" class="form-control" readonly style="background-color: #eee; font-weight: bold; height: 38px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #333;">Reason for Edit Request <span class="text-danger">*</span></label>
                    <textarea id="editModalReason" class="form-control" rows="3" placeholder="Enter reason why this item needs to be edited..." style="resize: none; border-radius: 4px;"></textarea>
                    <span class="help-block text-danger" id="editModalError" style="display: none; font-weight: 600; margin-top: 5px;">Reason is required.</span>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f9f9f9; border-top: 1px solid #eee; padding: 15px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditModalRequest();" style="font-weight: 600;"><i class="fa fa-pencil"></i> Continue to Edit</button>
            </div>
        </div>
    </div>
</div>

