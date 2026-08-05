<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Database Error | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --bg:#0f172a; --card:#1e293b; --green:#22c55e; --green2:#06b6d4; --text:#e2e8f0; --muted:#94a3b8; --border:#334155; }
  body { background:var(--bg); font-family:'Inter',sans-serif; color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; overflow:hidden; }

  .bg-grid {
    position:fixed; inset:0; pointer-events:none;
    background-image: linear-gradient(rgba(34,197,94,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(34,197,94,.04) 1px, transparent 1px);
    background-size: 40px 40px;
    animation: grid-move 20s linear infinite;
  }
  @keyframes grid-move { to { background-position: 40px 40px; } }
  .glow1 { position:fixed; width:450px;height:450px;border-radius:50%;background:radial-gradient(circle,rgba(34,197,94,.2),transparent 70%);top:-80px;left:-80px;animation:pglow 5s ease-in-out infinite; }
  .glow2 { position:fixed; width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.2),transparent 70%);bottom:-60px;right:-60px;animation:pglow 7s ease-in-out infinite reverse; }
  @keyframes pglow { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

  .card {
    position:relative; z-index:10;
    background:var(--card); border:1px solid rgba(34,197,94,.25);
    border-radius:24px; padding:52px 44px; max-width:520px; width:90%;
    text-align:center; box-shadow: 0 0 0 1px rgba(34,197,94,.08), 0 32px 80px rgba(0,0,0,.55);
    animation: slideUp .7s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

  /* DB cylinder */
  .db-wrap { margin: 4px auto 16px; width:120px; height:120px; position:relative; }
  .db-icon {
    position: absolute; inset: 0; display:flex; align-items:center; justify-content:center;
  }

  /* animated scan line */
  .scan-line {
    position:absolute; width:100%; height:2px;
    background: linear-gradient(90deg, transparent, var(--green), transparent);
    animation: scan 2.5s linear infinite; opacity:.8;
    box-shadow: 0 0 12px var(--green);
  }
  @keyframes scan { 0%{top:0;opacity:0} 10%{opacity:.8} 90%{opacity:.8} 100%{top:100%;opacity:0} }

  .error-code {
    font-size: 64px; font-weight:900; line-height:1;
    background: linear-gradient(135deg,var(--green),var(--green2));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    letter-spacing:-2px; margin-bottom:4px;
    animation: db-glow 2.5s ease-in-out infinite;
  }
  @keyframes db-glow { 0%,100%{filter:drop-shadow(0 0 16px rgba(34,197,94,.5))} 50%{filter:drop-shadow(0 0 32px rgba(6,182,212,.8))} }

  .badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.5px; background:rgba(34,197,94,.1); color:var(--green); border:1px solid rgba(34,197,94,.25); margin-bottom:10px; }
  h1 { font-size:22px; font-weight:700; margin:12px 0 10px; }
  p  { font-size:14px; color:var(--muted); line-height:1.7; }

  .detail-box { margin:18px 0; padding:14px 18px; background:rgba(34,197,94,.05); border:1px solid rgba(34,197,94,.18); border-radius:12px; font-size:12.5px; color:var(--muted); text-align:left; word-break:break-word; }
  .detail-box strong { color:var(--text); }

  /* terminal loader */
  .terminal { margin:18px 0; background:#0a0f1a; border:1px solid rgba(34,197,94,.2); border-radius:10px; padding:14px 16px; text-align:left; font-family:monospace; font-size:12px; }
  .t-line { color:var(--green); margin-bottom:4px; }
  .t-line::before { content:'> '; color:#475569; }
  .t-cursor { display:inline-block; width:8px; height:14px; background:var(--green); vertical-align:middle; animation:blink .8s step-end infinite; margin-left:2px; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

  .btn-row { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
  .btn-green { background:linear-gradient(135deg,var(--green),var(--green2)); color:#0f172a; }
  .btn-green:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(34,197,94,.4); }
  .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
  .btn-ghost:hover { border-color:var(--green); color:var(--green); transform:translateY(-2px); }
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="glow1"></div>
<div class="glow2"></div>

<div class="card">
  <div class="badge">&#128190; DATABASE ERROR</div>

  <!-- DB cylinder SVG with scan -->
  <div class="db-wrap">
    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" width="120" height="120" overflow="visible">
      <defs>
        <clipPath id="db-clip"><rect x="20" y="20" width="80" height="80"/></clipPath>
      </defs>
      <!-- cylinder body -->
      <rect x="22" y="42" width="76" height="52" rx="4" fill="#1e3a5f" stroke="#22c55e" stroke-width="2"/>
      <!-- disk layers -->
      <ellipse cx="60" cy="42" rx="38" ry="10" fill="#1e3a5f" stroke="#22c55e" stroke-width="2"/>
      <ellipse cx="60" cy="60" rx="38" ry="10" fill="#1e3a5f" stroke="#22c55e" stroke-width="1.5"/>
      <ellipse cx="60" cy="78" rx="38" ry="10" fill="#1e3a5f" stroke="#22c55e" stroke-width="1.5"/>
      <ellipse cx="60" cy="94" rx="38" ry="10" fill="#163a2a" stroke="#22c55e" stroke-width="2"/>
      <!-- connecting lines -->
      <line x1="22" y1="42" x2="22" y2="94" stroke="#22c55e" stroke-width="2"/>
      <line x1="98" y1="42" x2="98" y2="94" stroke="#22c55e" stroke-width="2"/>
      <!-- scan line clipped -->
      <g clip-path="url(#db-clip)">
        <rect class="scan-line" x="22" y="0" width="76" height="2"/>
      </g>
      <!-- warning cross -->
      <circle cx="60" cy="68" r="14" fill="#0f172a" stroke="#22c55e" stroke-width="1.5"/>
      <text x="60" y="73" text-anchor="middle" font-size="16" fill="#ef4444" font-weight="bold">!</text>
    </svg>
  </div>

  <div class="error-code">DB Error</div>
  <h1><?php echo htmlspecialchars($heading ?? 'Database Connection Failed'); ?></h1>
  <p>Unable to connect to the database.<br>The system will automatically attempt to reconnect.</p>

  <?php if (!empty($message)): ?>
  <div class="detail-box">
    <strong>Error Details:</strong><br><?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="terminal">
    <div class="t-line">Checking database connection...</div>
    <div class="t-line" id="tl2" style="opacity:0">Attempting to reconnect (1/3)...</div>
    <div class="t-line" id="tl3" style="opacity:0">Waiting for DB server response...</div>
    <span class="t-cursor"></span>
  </div>

  <div class="btn-row">
    <button onclick="location.reload()" class="btn btn-green">&#8635; Retry Connection</button>
    <a href="<?php echo (function_exists('base_url') ? base_url() : '/'); ?>Home/index" class="btn btn-ghost">&#8962; Dashboard</a>
  </div>
</div>

<script>
setTimeout(()=>{ document.getElementById('tl2').style.opacity=1; },900);
setTimeout(()=>{ document.getElementById('tl3').style.opacity=1; },1800);
</script>
</body>
</html>