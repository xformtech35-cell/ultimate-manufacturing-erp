<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Something Went Wrong | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0f172a; --card: #1e293b; --danger: #ef4444; --danger2: #f97316;
    --text: #e2e8f0; --muted: #94a3b8; --border: #334155;
  }
  body {
    background: var(--bg); font-family: 'Inter', sans-serif; color: var(--text);
    min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden;
  }
  .bg-layer { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
  .glow1 { position: fixed; width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(239,68,68,.25),transparent 70%); top:-100px;left:-100px; animation: pulse-bg 4s ease-in-out infinite; }
  .glow2 { position: fixed; width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(249,115,22,.2),transparent 70%); bottom:-80px;right:-80px; animation: pulse-bg 5s ease-in-out infinite reverse; }
  @keyframes pulse-bg { 0%,100%{transform:scale(1);opacity:.6} 50%{transform:scale(1.2);opacity:1} }

  /* particle sparks */
  .spark { position:fixed; width:4px; height:4px; border-radius:50%; pointer-events:none; z-index:1; animation: sparkfly var(--d,6s) linear infinite var(--delay,0s); opacity:0; }
  @keyframes sparkfly {
    0%{opacity:0;transform:translateY(100vh) translateX(0)}
    10%{opacity:1}
    90%{opacity:.6}
    100%{opacity:0;transform:translateY(-20px) translateX(var(--dx,20px))}
  }

  .card {
    position: relative; z-index: 10;
    background: var(--card); border: 1px solid rgba(239,68,68,.3);
    border-radius: 24px; padding: 52px 44px; max-width: 520px; width: 90%;
    text-align: center; box-shadow: 0 0 0 1px rgba(239,68,68,.1), 0 32px 80px rgba(0,0,0,.6);
    animation: shakeIn .8s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes shakeIn {
    0%{opacity:0;transform:translateY(40px) rotate(-1deg)}
    60%{transform:translateY(-4px) rotate(.5deg)}
    100%{opacity:1;transform:translateY(0) rotate(0)}
  }

  /* broken gear SVG animation */
  .gear-wrap { margin: 0 auto 12px; width: 130px; height: 130px; position: relative; }
  .gear-main { animation: spin 6s linear infinite; transform-origin: center; }
  .gear-small { animation: spin-rev 4s linear infinite; transform-origin: center; }
  @keyframes spin { to{transform:rotate(360deg)} }
  @keyframes spin-rev { to{transform:rotate(-360deg)} }
  .error-bolt {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    font-size: 32px; animation: bolt-flicker 1.5s ease-in-out infinite;
  }
  @keyframes bolt-flicker { 0%,100%{opacity:1;filter:drop-shadow(0 0 8px #ef4444)} 40%,60%{opacity:.3;filter:none} }

  .error-code {
    font-size: 72px; font-weight: 900; line-height: 1;
    background: linear-gradient(135deg, var(--danger), var(--danger2));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    animation: err-glow 2s ease-in-out infinite;
    letter-spacing: -3px; margin-bottom: 4px;
  }
  @keyframes err-glow { 0%,100%{filter:drop-shadow(0 0 16px rgba(239,68,68,.6))} 50%{filter:drop-shadow(0 0 32px rgba(249,115,22,.9))} }

  .badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.5px; background:rgba(239,68,68,.12); color:#ef4444; border:1px solid rgba(239,68,68,.3); margin-bottom: 10px; }
  h1 { font-size: 22px; font-weight: 700; margin: 12px 0 10px; }
  p  { font-size: 14px; color: var(--muted); line-height: 1.7; }

  .detail-box {
    margin: 18px 0; padding: 14px 18px;
    background: rgba(239,68,68,.06); border: 1px solid rgba(239,68,68,.2);
    border-radius: 12px; font-size: 12.5px; color: var(--muted); text-align: left; word-break: break-word;
  }
  .detail-box strong { color: var(--text); }

  .progress-bar { height: 3px; background: rgba(239,68,68,.15); border-radius: 99px; margin: 20px 0 4px; overflow:hidden; }
  .progress-fill { height: 100%; background: linear-gradient(90deg,var(--danger),var(--danger2)); border-radius:99px; animation: load-bar 3s ease-out forwards; }
  @keyframes load-bar { from{width:0} to{width:100%} }
  .progress-label { font-size: 11px; color: var(--muted); text-align: right; }

  .btn-row { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
  .btn-danger  { background: linear-gradient(135deg,var(--danger),var(--danger2)); color:#fff; }
  .btn-danger:hover  { transform:translateY(-2px); box-shadow:0 8px 24px rgba(239,68,68,.4); }
  .btn-ghost  { background:transparent; border:1px solid var(--border); color:var(--muted); }
  .btn-ghost:hover { border-color:var(--danger); color:var(--danger); transform:translateY(-2px); }
</style>
</head>
<body>
<div class="glow1"></div>
<div class="glow2"></div>
<div class="bg-layer" id="sparks"></div>

<div class="card">
  <div class="badge">&#9888; SYSTEM ERROR</div>

  <!-- Animated gear illustration -->
  <div class="gear-wrap">
    <svg viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg" width="130" height="130">
      <!-- big gear -->
      <g class="gear-main" style="transform-origin:55px 55px">
        <path d="M55 18l4-12 8 2-1 12a37 37 0 0 1 10 6l11-5 6 6-5 11a37 37 0 0 1 6 10l12 1 2 8-12 4a37 37 0 0 1-6 10l5 11-6 6-11-5a37 37 0 0 1-10 6l-1 12-8 2-4-12a37 37 0 0 1-10-6l-11 5-6-6 5-11a37 37 0 0 1-6-10l-12-1-2-8 12-4a37 37 0 0 1 6-10l-5-11 6-6 11 5a37 37 0 0 1 10-6z" fill="#1e3a5f" stroke="#ef4444" stroke-width="2"/>
        <circle cx="55" cy="55" r="18" fill="#0f172a" stroke="#ef4444" stroke-width="2"/>
      </g>
      <!-- small gear -->
      <g class="gear-small" style="transform-origin:98px 82px">
        <path d="M98 68l2-7 5 1v7a22 22 0 0 1 6 4l7-3 4 4-3 7a22 22 0 0 1 3 6l7 1 1 5-7 2a22 22 0 0 1-3 6l3 7-4 4-7-3a22 22 0 0 1-6 3l-1 7-5 1-2-7a22 22 0 0 1-6-3l-7 3-4-4 3-7a22 22 0 0 1-3-6l-7-1-1-5 7-2a22 22 0 0 1 3-6l-3-7 4-4 7 3a22 22 0 0 1 6-4z" fill="#1e3a5f" stroke="#f97316" stroke-width="1.5"/>
        <circle cx="98" cy="82" r="9" fill="#0f172a" stroke="#f97316" stroke-width="1.5"/>
      </g>
    </svg>
    <div class="error-bolt">⚡</div>
  </div>

  <div class="error-code">Error</div>
  <h1><?php echo htmlspecialchars($heading ?? 'Something Went Wrong'); ?></h1>
  <p>An unexpected error occurred in the system.<br>Our team has been notified. Please try again shortly.</p>

  <?php if (!empty($message)): ?>
  <div class="detail-box">
    <strong>Error Details:</strong><br><?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="progress-bar"><div class="progress-fill"></div></div>
  <div class="progress-label">Auto-retrying in 3 seconds...</div>

  <div class="btn-row">
    <button onclick="location.reload()" class="btn btn-danger">&#8635; Retry Now</button>
    <a href="<?php echo (function_exists('base_url') ? base_url() : '/'); ?>Home/index" class="btn btn-ghost">&#8962; Dashboard</a>
  </div>
</div>

<script>
// Generate spark particles
const sp = document.getElementById('sparks');
const colors = ['#ef4444','#f97316','#fbbf24'];
for(let i=0;i<30;i++){
  const s = document.createElement('div');
  s.className='spark';
  const c = colors[Math.floor(Math.random()*colors.length)];
  s.style.cssText=`background:${c};left:${Math.random()*100}%;--d:${4+Math.random()*6}s;--delay:${Math.random()*6}s;--dx:${(Math.random()-.5)*100}px;box-shadow:0 0 6px ${c};`;
  sp.appendChild(s);
}
// Auto reload
setTimeout(()=>location.reload(), 3000);
</script>
</body>
</html>