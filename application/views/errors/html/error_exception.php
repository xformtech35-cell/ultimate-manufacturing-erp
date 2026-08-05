<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exception | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --bg:#0f172a; --card:#1e293b; --purple:#a855f7; --purple2:#ec4899; --text:#e2e8f0; --muted:#94a3b8; --border:#334155; }
  body { background:var(--bg); font-family:'Inter',sans-serif; color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; overflow:hidden; }

  .orb1 { position:fixed; width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(168,85,247,.2),transparent 70%);top:-100px;left:-100px;animation:orb 9s ease-in-out infinite; }
  .orb2 { position:fixed; width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(236,72,153,.18),transparent 70%);bottom:-80px;right:-80px;animation:orb 12s ease-in-out infinite reverse; }
  @keyframes orb { 0%,100%{transform:scale(1)} 50%{transform:scale(1.12)} }

  .card {
    position:relative; z-index:10;
    background:var(--card); border:1px solid rgba(168,85,247,.25);
    border-radius:24px; padding:52px 44px; max-width:520px; width:90%;
    text-align:center; box-shadow:0 0 0 1px rgba(168,85,247,.08), 0 32px 80px rgba(0,0,0,.55);
    animation:slideUp .7s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

  /* animated warning triangle */
  .triangle-wrap { margin: 0 auto 12px; width:120px; height:120px; position:relative; display:flex; align-items:center; justify-content:center; }
  .triangle-svg { animation:tri-pulse 2s ease-in-out infinite; }
  @keyframes tri-pulse { 0%,100%{filter:drop-shadow(0 0 12px rgba(168,85,247,.6));transform:scale(1)} 50%{filter:drop-shadow(0 0 28px rgba(236,72,153,.9));transform:scale(1.06)} }
  .tri-inner { position:absolute; font-size:40px; animation:exc-bounce 2s ease-in-out infinite; }
  @keyframes exc-bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }

  .error-code { font-size:56px; font-weight:900; line-height:1; background:linear-gradient(135deg,var(--purple),var(--purple2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-2px; margin-bottom:4px; animation:pu-glow 2s ease-in-out infinite; }
  @keyframes pu-glow { 0%,100%{filter:drop-shadow(0 0 16px rgba(168,85,247,.5))} 50%{filter:drop-shadow(0 0 32px rgba(236,72,153,.8))} }

  .badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.5px; background:rgba(168,85,247,.1); color:var(--purple); border:1px solid rgba(168,85,247,.25); margin-bottom:10px; }
  h1 { font-size:22px; font-weight:700; margin:12px 0 10px; }
  p  { font-size:14px; color:var(--muted); line-height:1.7; }

  .detail-box { margin:18px 0; padding:14px 18px; background:rgba(168,85,247,.05); border:1px solid rgba(168,85,247,.18); border-radius:12px; font-size:12.5px; color:var(--muted); text-align:left; word-break:break-word; }
  .detail-box strong { color:var(--purple); }

  .divider { height:1px; background:linear-gradient(90deg,transparent,var(--border),transparent); margin:20px 0; }

  .support-box { font-size:12px; color:var(--muted); }
  .support-box a { color:var(--purple); text-decoration:none; }
  .support-box a:hover { text-decoration:underline; }

  .btn-row { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
  .btn-purple { background:linear-gradient(135deg,var(--purple),var(--purple2)); color:#fff; }
  .btn-purple:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(168,85,247,.4); }
  .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
  .btn-ghost:hover { border-color:var(--purple); color:var(--purple); transform:translateY(-2px); }
</style>
</head>
<body>
<div class="orb1"></div>
<div class="orb2"></div>

<div class="card">
  <div class="badge">&#9888; EXCEPTION THROWN</div>

  <!-- Animated triangle -->
  <div class="triangle-wrap">
    <svg class="triangle-svg" width="110" height="98" viewBox="0 0 110 98" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M55 4L106 94H4L55 4Z" fill="#1e3a5f" stroke="url(#tri-grad)" stroke-width="3" stroke-linejoin="round"/>
      <defs>
        <linearGradient id="tri-grad" x1="4" y1="94" x2="106" y2="4" gradientUnits="userSpaceOnUse">
          <stop stop-color="#a855f7"/>
          <stop offset="1" stop-color="#ec4899"/>
        </linearGradient>
      </defs>
    </svg>
    <div class="tri-inner">⚠️</div>
  </div>

  <div class="error-code">Exception</div>
  <h1><?php echo htmlspecialchars($heading ?? 'An Exception Was Encountered'); ?></h1>
  <p>An unhandled exception has been thrown.<br>The error has been captured and logged for our development team.</p>

  <?php if (!empty($message)): ?>
  <div class="detail-box">
    <strong>Exception Details:</strong><br><?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="divider"></div>
  <div class="support-box">
    Need help? Contact your system administrator or<br>
    email <a href="mailto:support@xform.in">support@xform.in</a>
  </div>

  <div class="btn-row">
    <a href="javascript:history.back()" class="btn btn-ghost">&#8592; Go Back</a>
    <a href="<?php echo (function_exists('base_url') ? base_url() : '/'); ?>Home/index" class="btn btn-purple">&#8962; Dashboard</a>
  </div>
</div>
</body>
</html>