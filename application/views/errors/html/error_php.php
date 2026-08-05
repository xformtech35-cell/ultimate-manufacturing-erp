<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PHP Error | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --bg:#0f172a; --card:#1e293b; --amber:#f59e0b; --amber2:#fb923c; --text:#e2e8f0; --muted:#94a3b8; --border:#334155; }
  body { background:var(--bg); font-family:'Inter',sans-serif; color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; overflow:hidden; }

  .shape1 { position:fixed; width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.18),transparent 70%);top:-120px;right:-100px;animation:slow 8s ease-in-out infinite; }
  .shape2 { position:fixed; width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(251,146,60,.15),transparent 70%);bottom:-80px;left:-80px;animation:slow 10s ease-in-out infinite reverse; }
  @keyframes slow { 0%,100%{transform:scale(1) rotate(0deg)} 50%{transform:scale(1.1) rotate(5deg)} }

  /* code rain */
  .rain-char { position:fixed; font-family:monospace; font-size:13px; color:rgba(245,158,11,.4); pointer-events:none; z-index:1; animation:rain var(--d,8s) linear infinite var(--del,0s); opacity:0; user-select:none; }
  @keyframes rain { 0%{opacity:0;top:-20px} 5%{opacity:.6} 95%{opacity:.4} 100%{opacity:0;top:100vh} }

  .card {
    position:relative; z-index:10;
    background:var(--card); border:1px solid rgba(245,158,11,.25);
    border-radius:24px; padding:52px 44px; max-width:540px; width:90%;
    text-align:center; box-shadow:0 0 0 1px rgba(245,158,11,.08), 0 32px 80px rgba(0,0,0,.55);
    animation:slideUp .7s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

  .php-icon { margin: 4px auto 12px; font-size:72px; animation:shake-icon 3s ease-in-out infinite; display:block; }
  @keyframes shake-icon { 0%,100%{transform:rotate(-4deg)} 50%{transform:rotate(4deg)} }

  .error-code { font-size:58px; font-weight:900; line-height:1; background:linear-gradient(135deg,var(--amber),var(--amber2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-2px; margin-bottom:4px; animation:am-glow 2s ease-in-out infinite; }
  @keyframes am-glow { 0%,100%{filter:drop-shadow(0 0 16px rgba(245,158,11,.5))} 50%{filter:drop-shadow(0 0 32px rgba(251,146,60,.8))} }

  .badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.5px; background:rgba(245,158,11,.1); color:var(--amber); border:1px solid rgba(245,158,11,.25); margin-bottom:10px; }
  h1 { font-size:22px; font-weight:700; margin:12px 0 10px; }
  p  { font-size:14px; color:var(--muted); line-height:1.7; }

  .detail-box { margin:18px 0; padding:14px 18px; background:rgba(245,158,11,.05); border:1px solid rgba(245,158,11,.18); border-radius:12px; font-size:12.5px; color:var(--muted); text-align:left; word-break:break-word; font-family:monospace; }
  .detail-box strong { color:var(--amber); }

  .stack-row { display:flex; align-items:baseline; gap:10px; margin-top:8px; }
  .stack-file { flex:1; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-size:11.5px; }
  .stack-line { font-size:11px; color:var(--amber); white-space:nowrap; }

  .btn-row { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
  .btn-amber { background:linear-gradient(135deg,var(--amber),var(--amber2)); color:#0f172a; }
  .btn-amber:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(245,158,11,.4); }
  .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
  .btn-ghost:hover { border-color:var(--amber); color:var(--amber); transform:translateY(-2px); }
</style>
</head>
<body>
<div class="shape1"></div>
<div class="shape2"></div>
<div id="rain"></div>

<div class="card">
  <div class="badge">&#9881; PHP ERROR</div>
  <span class="php-icon">&#128187;</span>
  <div class="error-code">PHP Error</div>
  <h1><?php echo htmlspecialchars($heading ?? 'A PHP Error Was Encountered'); ?></h1>
  <p>The server encountered a runtime error.<br>This has been logged automatically for review.</p>

  <?php if (!empty($message)): ?>
  <div class="detail-box">
    <strong>// Error Details</strong><br>
    <?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="btn-row">
    <button onclick="history.back()" class="btn btn-amber">&#8592; Go Back</button>
    <a href="<?php echo (function_exists('base_url') ? base_url() : '/'); ?>Home/index" class="btn btn-ghost">&#8962; Dashboard</a>
  </div>
</div>

<script>
(function() {
    // Code rain
    const rain = document.getElementById('rain');
    if (!rain) return;
    const chars = '01{}[];<>/PHP$error';
    for(let i=0;i<40;i++){
      const c = document.createElement('div');
      c.className='rain-char';
      c.textContent = chars[Math.floor(Math.random()*chars.length)];
      c.style.cssText=`left:${Math.random()*100}%;--d:${5+Math.random()*8}s;--del:${Math.random()*8}s;`;
      rain.appendChild(c);
    }
})();
</script>
</body>
</html>