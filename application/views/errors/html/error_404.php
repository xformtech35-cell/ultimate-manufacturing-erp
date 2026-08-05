<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 – Page Not Found | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0f172a;
    --card: #1e293b;
    --accent: #3b82f6;
    --accent2: #8b5cf6;
    --warn: #f59e0b;
    --text: #e2e8f0;
    --muted: #94a3b8;
    --border: #334155;
  }

  body {
    background: var(--bg);
    font-family: 'Inter', sans-serif;
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  /* ── animated starfield ── */
  .stars { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
  .star {
    position: absolute;
    border-radius: 50%;
    background: #fff;
    animation: twinkle var(--d,3s) ease-in-out infinite var(--delay,0s);
  }
  @keyframes twinkle { 0%,100%{opacity:.1;transform:scale(1)} 50%{opacity:.8;transform:scale(1.4)} }

  /* ── floating shapes ── */
  .shape {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .18;
    animation: float var(--fd,12s) ease-in-out infinite alternate;
    pointer-events: none;
    z-index: 0;
  }
  @keyframes float { 0%{transform:translateY(0) scale(1)} 100%{transform:translateY(-40px) scale(1.08)} }

  /* ── card ── */
  .card {
    position: relative;
    z-index: 10;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 56px 48px;
    max-width: 520px;
    width: 90%;
    text-align: center;
    box-shadow: 0 32px 80px rgba(0,0,0,.5);
    animation: slideUp .7s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

  /* ── 404 number ── */
  .error-code {
    font-size: 110px;
    font-weight: 900;
    line-height: 1;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: pulse 2s ease-in-out infinite;
    letter-spacing: -6px;
  }
  @keyframes pulse { 0%,100%{filter:drop-shadow(0 0 24px rgba(59,130,246,.5))} 50%{filter:drop-shadow(0 0 48px rgba(139,92,246,.8))} }

  /* ── astronaut SVG ── */
  .astro-wrap {
    margin: 20px auto 8px;
    width: 140px;
    animation: float2 4s ease-in-out infinite;
  }
  @keyframes float2 { 0%,100%{transform:translateY(0) rotate(-2deg)} 50%{transform:translateY(-14px) rotate(2deg)} }

  h1 { font-size: 22px; font-weight: 700; margin: 16px 0 10px; color: var(--text); }
  p  { font-size: 14px; color: var(--muted); line-height: 1.7; }

  .detail-box {
    margin: 20px 0;
    padding: 14px 18px;
    background: rgba(59,130,246,.08);
    border: 1px solid rgba(59,130,246,.2);
    border-radius: 12px;
    font-size: 13px;
    color: var(--muted);
    text-align: left;
    word-break: break-word;
  }
  .detail-box strong { color: var(--text); }

  .btn-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 28px; }
  .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px; font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all .2s;
  }
  .btn-primary { background: linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; }
  .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(59,130,246,.4); }
  .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
  .btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform:translateY(-2px); }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(245,158,11,.12);
    color: var(--warn);
    border: 1px solid rgba(245,158,11,.25);
    margin-bottom: 12px;
    letter-spacing: .5px;
  }
</style>
</head>
<body>

<!-- stars -->
<div class="stars" id="stars"></div>

<!-- bg glows -->
<div class="shape" style="width:400px;height:400px;background:#3b82f6;top:-100px;left:-100px;--fd:14s"></div>
<div class="shape" style="width:350px;height:350px;background:#8b5cf6;bottom:-80px;right:-80px;--fd:18s"></div>

<div class="card">
  <div class="badge">&#9888; 404 ERROR</div>
  <div class="error-code">404</div>

  <!-- Animated astronaut SVG -->
  <div class="astro-wrap">
    <svg viewBox="0 0 140 160" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- helmet -->
      <circle cx="70" cy="52" r="34" fill="#1e3a5f" stroke="#3b82f6" stroke-width="3"/>
      <circle cx="70" cy="52" r="24" fill="#0ea5e9" opacity=".25"/>
      <!-- visor shine -->
      <ellipse cx="62" cy="44" rx="7" ry="4" fill="white" opacity=".3" transform="rotate(-20 62 44)"/>
      <!-- body -->
      <rect x="44" y="82" width="52" height="50" rx="14" fill="#1e3a5f" stroke="#3b82f6" stroke-width="2"/>
      <!-- backpack -->
      <rect x="36" y="90" width="12" height="28" rx="6" fill="#334155" stroke="#475569" stroke-width="1.5"/>
      <rect x="92" y="90" width="12" height="28" rx="6" fill="#334155" stroke="#475569" stroke-width="1.5"/>
      <!-- arms -->
      <rect x="22" y="86" width="24" height="12" rx="6" fill="#1e3a5f" stroke="#3b82f6" stroke-width="1.5" transform="rotate(10 22 86)"/>
      <rect x="94" y="86" width="24" height="12" rx="6" fill="#1e3a5f" stroke="#3b82f6" stroke-width="1.5" transform="rotate(-10 94 86)"/>
      <!-- legs -->
      <rect x="50" y="128" width="14" height="22" rx="7" fill="#1e3a5f" stroke="#3b82f6" stroke-width="1.5"/>
      <rect x="76" y="128" width="14" height="22" rx="7" fill="#1e3a5f" stroke="#3b82f6" stroke-width="1.5"/>
      <!-- chest badge -->
      <circle cx="70" cy="106" r="7" fill="#3b82f6" opacity=".5"/>
      <text x="70" y="110" text-anchor="middle" font-size="8" fill="white" font-weight="bold">?</text>
    </svg>
  </div>

  <h1>Page Not Found</h1>
  <p>The page you're looking for has drifted into deep space.<br>It may have been moved, deleted, or never existed.</p>

  <?php if (!empty($message)): ?>
  <div class="detail-box">
    <strong>Details:</strong><br><?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="btn-row">
    <a href="javascript:history.back()" class="btn btn-ghost">&#8592; Go Back</a>
    <a href="<?php echo (function_exists('base_url') ? base_url() : '/'); ?>Home/index" class="btn btn-primary">&#8962; Dashboard</a>
  </div>
</div>

<script>
// Generate starfield
const container = document.getElementById('stars');
for(let i=0; i<120; i++){
  const s = document.createElement('div');
  s.className = 'star';
  const sz = Math.random()*2.5+.5;
  s.style.cssText = `width:${sz}px;height:${sz}px;top:${Math.random()*100}%;left:${Math.random()*100}%;--d:${2+Math.random()*4}s;--delay:${Math.random()*4}s;`;
  container.appendChild(s);
}
</script>
</body>
</html>