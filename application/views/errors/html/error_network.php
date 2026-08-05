<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>No Internet Connection | UWS ERP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --bg:#0f172a; --card:#1e293b; --cyan:#06b6d4; --cyan2:#3b82f6; --text:#e2e8f0; --muted:#94a3b8; --border:#334155; }
  body { background:var(--bg); font-family:'Inter',sans-serif; color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; overflow:hidden; }

  /* animated bg waves */
  .wave { position:fixed; bottom:0; left:0; width:200%; height:220px; pointer-events:none; }
  .wave1 { animation: wave-move 6s linear infinite; opacity:.08; }
  .wave2 { animation: wave-move 9s linear infinite reverse; opacity:.05; bottom:20px; }
  @keyframes wave-move { from{transform:translateX(0)} to{transform:translateX(-50%)} }

  .orb1 { position:fixed; width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.18),transparent 70%);top:-120px;left:-120px;animation:orb 10s ease-in-out infinite; }
  .orb2 { position:fixed; width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.15),transparent 70%);bottom:-80px;right:-80px;animation:orb 13s ease-in-out infinite reverse; }
  @keyframes orb { 0%,100%{transform:scale(1)} 50%{transform:scale(1.12)} }

  .card {
    position:relative; z-index:10;
    background:var(--card); border:1px solid rgba(6,182,212,.25);
    border-radius:24px; padding:52px 44px; max-width:520px; width:90%;
    text-align:center; box-shadow:0 0 0 1px rgba(6,182,212,.08), 0 32px 80px rgba(0,0,0,.55);
    animation:slideUp .7s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

  /* wifi animation */
  .wifi-wrap { margin:0 auto 16px; width:120px; height:100px; position:relative; display:flex; align-items:flex-end; justify-content:center; }
  .wifi-arc {
    position:absolute; border:3px solid var(--cyan); border-radius:50%;
    border-bottom-color: transparent; border-left-color: transparent;
    animation: wifi-pulse 2s ease-in-out infinite;
    bottom:0;
  }
  .arc1 { width:24px;height:24px; animation-delay:.0s; }
  .arc2 { width:50px;height:50px; animation-delay:.15s; }
  .arc3 { width:78px;height:78px; animation-delay:.3s; }
  .arc4 { width:108px;height:108px; animation-delay:.45s; opacity:.4; }
  @keyframes wifi-pulse { 0%,100%{opacity:1;border-color:var(--cyan) transparent transparent var(--cyan);} 50%{opacity:.2;} }
  .wifi-dot { width:10px;height:10px;border-radius:50%;background:var(--cyan);position:absolute;bottom:0;left:50%;transform:translateX(-50%); box-shadow:0 0 14px var(--cyan); animation:dot-blink 2s ease-in-out infinite; }
  @keyframes dot-blink { 0%,100%{opacity:1;transform:translateX(-50%) scale(1)} 50%{opacity:.2;transform:translateX(-50%) scale(.6)} }

  /* red cross over wifi */
  .no-signal {
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    font-size:48px; animation: cross-shake 3s ease-in-out infinite;
    filter: drop-shadow(0 0 10px rgba(239,68,68,.6));
  }
  @keyframes cross-shake { 0%,100%{transform:rotate(-5deg)} 50%{transform:rotate(5deg)} }

  .error-code { font-size:52px; font-weight:900; line-height:1; background:linear-gradient(135deg,var(--cyan),var(--cyan2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-2px; margin-bottom:4px; animation:cn-glow 2s ease-in-out infinite; }
  @keyframes cn-glow { 0%,100%{filter:drop-shadow(0 0 16px rgba(6,182,212,.5))} 50%{filter:drop-shadow(0 0 32px rgba(59,130,246,.8))} }

  .badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.5px; background:rgba(6,182,212,.1); color:var(--cyan); border:1px solid rgba(6,182,212,.25); margin-bottom:10px; }
  h1 { font-size:22px; font-weight:700; margin:12px 0 10px; }
  p  { font-size:14px; color:var(--muted); line-height:1.7; }

  /* signal strength bars */
  .signal-bars { display:flex; gap:5px; align-items:flex-end; justify-content:center; margin:20px 0 6px; }
  .bar { width:10px; border-radius:3px; background:var(--border); transition:background .4s; }
  .bar.active { background:linear-gradient(to top, var(--cyan2), var(--cyan)); box-shadow:0 0 6px var(--cyan); }
  .b1 { height:10px; } .b2 { height:18px; } .b3 { height:28px; } .b4 { height:38px; }
  .signal-label { font-size:11px; color:var(--muted); }

  /* retry countdown */
  .retry-ring { width:60px;height:60px; margin:16px auto 6px; position:relative; }
  .retry-ring svg { transform:rotate(-90deg); }
  .ring-track { fill:none; stroke:var(--border); stroke-width:4; }
  .ring-fill  { fill:none; stroke:var(--cyan); stroke-width:4; stroke-linecap:round; stroke-dasharray:163; stroke-dashoffset:163; animation:ring-count 10s linear forwards; }
  @keyframes ring-count { from{stroke-dashoffset:163} to{stroke-dashoffset:0} }
  .retry-num { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:var(--cyan); }

  .btn-row { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:20px; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
  .btn-cyan { background:linear-gradient(135deg,var(--cyan),var(--cyan2)); color:#fff; }
  .btn-cyan:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(6,182,212,.4); }
  .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
  .btn-ghost:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }

  /* online banner */
  .online-banner {
    display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%);
    background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff;
    padding:12px 28px; border-radius:99px; font-weight:600; font-size:14px;
    box-shadow:0 8px 24px rgba(34,197,94,.4); z-index:999;
    animation:banner-in .4s cubic-bezier(.16,1,.3,1) both;
  }
  @keyframes banner-in { from{opacity:0;transform:translateX(-50%) translateY(-20px)} to{opacity:1;transform:translateX(-50%) translateY(0)} }
</style>
</head>
<body>
<div class="orb1"></div>
<div class="orb2"></div>
<svg class="wave wave1" viewBox="0 0 1440 200" preserveAspectRatio="none"><path fill="#06b6d4" d="M0,100 C360,180 720,20 1080,100 C1260,140 1350,60 1440,100 L1440,200 L0,200Z"/></svg>
<svg class="wave wave2" viewBox="0 0 1440 200" preserveAspectRatio="none"><path fill="#3b82f6" d="M0,120 C480,40 960,200 1440,80 L1440,200 L0,200Z"/></svg>

<div class="online-banner" id="onlineBanner">&#10003; Connection Restored! Refreshing...</div>

<div class="card">
  <div class="badge">&#127758; NETWORK ERROR</div>

  <!-- Wifi / no signal -->
  <div class="wifi-wrap">
    <div class="wifi-arc arc4"></div>
    <div class="wifi-arc arc3"></div>
    <div class="wifi-arc arc2"></div>
    <div class="wifi-arc arc1"></div>
    <div class="wifi-dot"></div>
    <div class="no-signal">&#10060;</div>
  </div>

  <div class="error-code">Offline</div>
  <h1>No Internet Connection</h1>
  <p>It looks like you're offline.<br>Check your network connection and try again.</p>

  <!-- Signal bars -->
  <div class="signal-bars">
    <div class="bar b1" id="sb1"></div>
    <div class="bar b2" id="sb2"></div>
    <div class="bar b3" id="sb3"></div>
    <div class="bar b4" id="sb4"></div>
  </div>
  <div class="signal-label" id="signal-label">Checking signal...</div>

  <!-- Retry ring countdown -->
  <div class="retry-ring">
    <svg width="60" height="60" viewBox="0 0 60 60">
      <circle class="ring-track" cx="30" cy="30" r="26"/>
      <circle class="ring-fill"  cx="30" cy="30" r="26"/>
    </svg>
    <div class="retry-num" id="retryNum">10</div>
  </div>

  <div class="btn-row">
    <button onclick="location.reload()" class="btn btn-cyan">&#8635; Retry Now</button>
    <button onclick="window.history.back()" class="btn btn-ghost">&#8592; Go Back</button>
  </div>
</div>

<script>
// Countdown
let secs = 10;
const numEl = document.getElementById('retryNum');
const interval = setInterval(()=>{
  secs--;
  numEl.textContent = secs;
  if(secs <= 0){ clearInterval(interval); location.reload(); }
}, 1000);

// Signal bar animation
let bars = 0;
const barEls = [document.getElementById('sb1'),document.getElementById('sb2'),document.getElementById('sb3'),document.getElementById('sb4')];
const lbl = document.getElementById('signal-label');
const barInt = setInterval(()=>{
  bars = (bars+1) % 5;
  barEls.forEach((b,i)=>{ if(i<bars){b.classList.add('active')} else {b.classList.remove('active')} });
  lbl.textContent = bars===0?'No signal':bars===1?'Very weak':bars===2?'Weak':bars===3?'Fair':'Searching...';
}, 700);

// Online detection
window.addEventListener('online', ()=>{
  clearInterval(interval);
  clearInterval(barInt);
  const banner = document.getElementById('onlineBanner');
  banner.style.display = 'block';
  barEls.forEach(b=>b.classList.add('active'));
  lbl.textContent = 'Connected!';
  setTimeout(()=>location.reload(), 1500);
});
</script>
</body>
</html>
