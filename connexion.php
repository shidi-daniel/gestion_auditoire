<?php
session_start();
if (isset($_SESSION['utilisateur_connecte']) && $_SESSION['utilisateur_connecte']) { header('Location: index.php'); exit; }
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — SGA · UPC</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--bg:#0d0c1d;--card:rgba(13,12,29,.96);--bdr:rgba(255,255,255,.09);--txt:#e2e8f0;--muted:#94a3b8}
html,body{min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);overflow-x:hidden}
body{display:flex;align-items:center;justify-content:center;padding:20px;position:relative}
/* ANIMATED BG */
.bg{position:fixed;inset:0;z-index:0;overflow:hidden}
.bg-circle{position:absolute;border-radius:50%;filter:blur(80px);animation:float 18s ease-in-out infinite alternate}
.bg-c1{width:600px;height:600px;background:rgba(79,70,229,.28);top:-150px;left:-120px;animation-duration:20s}
.bg-c2{width:500px;height:500px;background:rgba(124,58,237,.20);bottom:-100px;right:-80px;animation-duration:15s;animation-delay:-5s}
.bg-c3{width:300px;height:300px;background:rgba(16,185,129,.08);top:40%;left:40%;animation-duration:12s;animation-delay:-8s}
@keyframes float{from{transform:translate(0,0) scale(1)}to{transform:translate(40px,30px) scale(1.1)}}
/* SPLIT LAYOUT */
.wrap{position:relative;z-index:1;display:grid;grid-template-columns:1fr 1fr;max-width:960px;width:100%;min-height:580px;
  background:var(--card);border:1px solid var(--bdr);border-radius:28px;
  box-shadow:0 40px 90px rgba(0,0,0,.75),0 0 0 1px rgba(79,70,229,.12);
  backdrop-filter:blur(30px);overflow:hidden;animation:sUp .6s cubic-bezier(.16,1,.3,1)}
@keyframes sUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:none}}
/* LEFT PANEL — BRANDING */
.left{background:linear-gradient(155deg,rgba(79,70,229,.22) 0%,rgba(124,58,237,.14) 50%,rgba(16,185,129,.06) 100%);
  border-right:1px solid var(--bdr);padding:48px 40px;display:flex;flex-direction:column;justify-content:space-between}
.brand-top{display:flex;align-items:center;gap:14px;margin-bottom:40px}
.brand-icon{width:52px;height:52px;background:linear-gradient(135deg,var(--pri),var(--acc));border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:#fff;
  box-shadow:0 8px 24px rgba(79,70,229,.5);flex-shrink:0}
.brand-text h2{font-size:17px;font-weight:800;letter-spacing:-.3px}
.brand-text p{font-size:10px;color:var(--muted);margin-top:1px}
.brand-title{margin-bottom:auto}
.brand-title h1{font-size:30px;font-weight:900;line-height:1.15;margin-bottom:14px;
  background:linear-gradient(135deg,#e0e7ff,#a5b4fc,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.brand-title p{font-size:13px;color:var(--muted);line-height:1.7;max-width:280px}
.features{display:flex;flex-direction:column;gap:10px;margin-top:32px}
.feat{display:flex;align-items:center;gap:10px;padding:10px 12px;background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);border-radius:10px;font-size:12px;color:var(--muted)}
.feat-icon{font-size:18px;flex-shrink:0}
.feat strong{color:var(--txt);display:block;font-size:12px;margin-bottom:1px}
.brand-footer{margin-top:32px;font-size:10.5px;color:rgba(148,163,184,.5);border-top:1px solid var(--bdr);padding-top:16px}
/* RIGHT PANEL — FORM */
.right{padding:48px 40px;display:flex;flex-direction:column;justify-content:center}
.right h3{font-size:20px;font-weight:800;margin-bottom:4px}
.right .sub{font-size:12.5px;color:var(--muted);margin-bottom:28px}
/* TABS */
.tabs{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:24px}
.tab{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px 12px;
  background:rgba(255,255,255,.04);border:1.5px solid var(--bdr);border-radius:12px;
  cursor:pointer;transition:all .22s;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.tab:hover{background:rgba(79,70,229,.08);border-color:rgba(79,70,229,.35)}
.tab.active{background:rgba(79,70,229,.14);border-color:rgba(79,70,229,.55);color:#a5b4fc;box-shadow:0 0 0 3px rgba(79,70,229,.14)}
.tab-ico{font-size:26px;line-height:1}
/* PANELS */
.mpanel{display:none}.mpanel.active{display:block;animation:sIn .3s ease}
@keyframes sIn{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:none}}
/* CAM */
.cam-box{position:relative;width:100%;aspect-ratio:4/3;background:#000;border-radius:14px;overflow:hidden;margin-bottom:12px;border:2px solid var(--bdr)}
#video{width:100%;height:100%;object-fit:cover;transform:scaleX(-1)}
.cam-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center}
.face-ring{width:150px;height:185px;border:2.5px solid rgba(79,70,229,.65);border-radius:50% 50% 46% 46%;
  position:relative;box-shadow:0 0 0 2000px rgba(0,0,0,.4);animation:rPulse 2.5s ease-in-out infinite}
@keyframes rPulse{0%,100%{border-color:rgba(79,70,229,.65)}50%{border-color:rgba(79,70,229,1)}}
.face-ring.ok{border-color:rgba(16,185,129,.9)!important;animation:none}
.scan-line{position:absolute;left:0;right:0;top:0;height:2.5px;
  background:linear-gradient(90deg,transparent,rgba(79,70,229,.9),transparent);
  animation:scan 2s linear infinite}
@keyframes scan{from{top:0;opacity:0}10%{opacity:1}90%{opacity:1}to{top:100%;opacity:0}}
.scan-line.ok{background:linear-gradient(90deg,transparent,rgba(16,185,129,.9),transparent)}
.res-overlay{position:absolute;inset:0;display:none;align-items:center;justify-content:center;
  flex-direction:column;gap:8px;background:rgba(0,0,0,.72);border-radius:12px}
.res-overlay.show{display:flex;animation:fIn .3s ease}
@keyframes fIn{from{opacity:0}to{opacity:1}}
.res-icon{font-size:48px}
.res-txt{font-size:13px;font-weight:700;text-align:center;padding:0 14px}
.res-txt.ok{color:#34d399}.res-txt.err{color:#fca5a5}
/* INSTR */
.instr{text-align:center;margin-bottom:10px}
.badge{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:18px;font-size:12.5px;font-weight:600;
  background:rgba(79,70,229,.12);border:1px solid rgba(79,70,229,.3);color:#a5b4fc;transition:all .3s}
.badge.ok{background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);color:#6ee7b7}
.badge.err{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.3);color:#fca5a5}
.badge.warn{background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.3);color:#fde68a}
/* FP */
.fp-zone{display:flex;flex-direction:column;align-items:center;padding:16px 0 10px}
.fp-rings{position:relative;width:140px;height:140px;margin-bottom:14px}
.fpr{position:absolute;border:2.5px solid rgba(124,58,237,.28);border-radius:50%;transition:all .4s}
.fpr:nth-child(1){inset:50px}.fpr:nth-child(2){inset:33px}.fpr:nth-child(3){inset:16px}.fpr:nth-child(4){inset:1px}
.fp-center{position:absolute;inset:50px;background:rgba(124,58,237,.10);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px}
.fp-rings.scanning .fpr{animation:fpP 1.5s ease-in-out infinite}
.fp-rings.scanning .fpr:nth-child(2){animation-delay:.12s}.fp-rings.scanning .fpr:nth-child(3){animation-delay:.24s}.fp-rings.scanning .fpr:nth-child(4){animation-delay:.36s}
@keyframes fpP{0%,100%{opacity:.2;transform:scale(.97)}50%{opacity:1;transform:scale(1.04)}}
.fp-rings.ok .fpr{border-color:rgba(16,185,129,.65)!important;animation:none}
.fp-rings.err .fpr{border-color:rgba(239,68,68,.55)!important;animation:none}
/* BTN */
.btn{width:100%;padding:12px;background:linear-gradient(135deg,var(--pri),var(--acc));color:#fff;border:none;
  border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;
  transition:transform .13s,box-shadow .13s;box-shadow:0 5px 18px rgba(79,70,229,.40)}
.btn:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 9px 24px rgba(79,70,229,.52)}
.btn:disabled{opacity:.38;cursor:not-allowed}
/* ALERT */
.alert{display:flex;gap:9px;padding:12px 14px;border-radius:10px;margin-bottom:14px;font-size:12.5px;line-height:1.5}
.al-ok{background:rgba(16,185,129,.10);border:1px solid rgba(16,185,129,.25);color:#6ee7b7}
.al-err{background:rgba(239,68,68,.10);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
.link{color:#a5b4fc;text-decoration:none;font-size:12.5px}
.link:hover{text-decoration:underline}
.sf{margin-top:18px;padding-top:14px;border-top:1px solid var(--bdr);text-align:center;font-size:10.5px;color:rgba(148,163,184,.5);display:flex;align-items:center;justify-content:center;gap:6px}
.sf .dot{width:5px;height:5px;background:var(--ok);border-radius:50%}
@media(max-width:720px){.wrap{grid-template-columns:1fr}.left{display:none}.right{padding:34px 26px}}
</style>
</head>
<body>
<div class="bg"><div class="bg-circle bg-c1"></div><div class="bg-circle bg-c2"></div><div class="bg-circle bg-c3"></div></div>

<div class="wrap">
  <!-- LEFT: BRANDING -->
  <div class="left">
    <div>
      <div class="brand-top">
        <div class="brand-icon">SGA</div>
        <div class="brand-text"><h2>Gestion des Auditoires</h2><p>Université Protestante au Congo</p></div>
      </div>
      <div class="brand-title">
        <h1>Système de Gestion des Auditoires</h1>
        <p>Plateforme académique dédiée à la planification automatique des créneaux horaires pour la Faculté des Sciences Informatiques.</p>
        <div class="features">
          <div class="feat"><span class="feat-icon">📅</span><div><strong>Planning automatique</strong>Sans conflits, respecte les capacités</div></div>
          <div class="feat"><span class="feat-icon">👤</span><div><strong>Authentification faciale</strong>Reconnaissance biométrique sécurisée</div></div>
          <div class="feat"><span class="feat-icon">👆</span><div><strong>Empreinte digitale</strong>WebAuthn — capteur natif</div></div>
          <div class="feat"><span class="feat-icon">📊</span><div><strong>Rapport d'occupation</strong>Analyse et export JSON/TXT</div></div>
        </div>
      </div>
    </div>
    <div class="brand-footer">UPC · FCI · Année académique 2025–2026 · PHP Procédural</div>
  </div>

  <!-- RIGHT: AUTH -->
  <div class="right">
    <h3>Connexion Biométrique</h3>
    <p class="sub">Choisissez votre méthode d'authentification</p>
    <div id="alertZone"></div>

    <div class="tabs">
      <div class="tab active" id="tab-face" onclick="switchTab('face')">
        <span class="tab-ico">👤</span>Visage
      </div>
      <div class="tab" id="tab-fp" onclick="switchTab('fp')">
        <span class="tab-ico">👆</span>Empreinte
      </div>
    </div>

    <!-- FACE PANEL -->
    <div class="mpanel active" id="panel-face">
      <div class="instr"><span class="badge" id="faceInstr">🔄 Chargement IA…</span></div>
      <div class="cam-box">
        <video id="video" autoplay muted playsinline></video>
        <div class="cam-overlay">
          <div class="face-ring" id="faceRing"><div class="scan-line" id="scanLine"></div></div>
        </div>
        <div class="res-overlay" id="faceResult">
          <div class="res-icon" id="resIcon">✅</div>
          <div class="res-txt ok" id="resTxt">Connexion réussie !</div>
        </div>
      </div>
      <button class="btn" id="faceBtn" disabled onclick="verifyFace()">🔍 Vérifier mon visage</button>
    </div>

    <!-- FP PANEL -->
    <div class="mpanel" id="panel-fp">
      <div class="fp-zone">
        <div class="fp-rings" id="fpRings">
          <div class="fpr"></div><div class="fpr"></div><div class="fpr"></div><div class="fpr"></div>
          <div class="fp-center" id="fpIcon">👆</div>
        </div>
        <div class="instr"><span class="badge" id="fpInstr">Prêt pour la lecture</span></div>
      </div>
      <button class="btn" id="fpBtn" onclick="verifyFP()">👆 Vérifier mon empreinte</button>
    </div>

    <div style="text-align:center;margin-top:14px">
      <a class="link" href="inscription.php">Pas encore inscrit ? → S'inscrire</a>
    </div>
    <div class="sf"><div class="dot"></div><span>Chiffrement TLS · Biométrie locale · Aucune donnée externe</span></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js"></script>
<script>
const MODEL='https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model';
let modelsOk=false,stream=null;

function switchTab(t){
  ['face','fp'].forEach(k=>{
    document.getElementById('tab-'+k).classList.toggle('active',k===t);
    document.getElementById('panel-'+k).classList.toggle('active',k===t);
  });
  clearAlert();
  if(t==='face') initFace();
  else{stopCam();initFP();}
}
function showAlert(type,msg){
  const c=type==='ok'?'al-ok':'al-err',i=type==='ok'?'✅':'⚠️';
  document.getElementById('alertZone').innerHTML=`<div class="alert ${c}"><span>${i}</span><div>${msg}</div></div>`;
}
function clearAlert(){document.getElementById('alertZone').innerHTML='';}
function stopCam(){if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}}

async function initFace(){
  const instr=document.getElementById('faceInstr'),btn=document.getElementById('faceBtn');
  instr.className='badge';instr.textContent='🔄 Chargement des modèles IA…';btn.disabled=true;
  try{
    if(!modelsOk){
      await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL);
      await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL);
      await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL);
      modelsOk=true;
    }
    stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:640,height:480},audio:false});
    const v=document.getElementById('video');v.srcObject=stream;await v.play();
    instr.textContent='😊 Positionnez votre visage dans le cadre';
    btn.disabled=false;detectLoop();
  }catch(e){instr.className='badge err';instr.textContent='❌ Caméra : '+e.message;}
}

let lastDet=false;
async function detectLoop(){
  const v=document.getElementById('video'),r=document.getElementById('faceRing'),s=document.getElementById('scanLine');
  if(!stream)return;
  if(v.readyState>=2){
    const d=await faceapi.detectSingleFace(v,new faceapi.TinyFaceDetectorOptions({inputSize:160,scoreThreshold:.4})).catch(()=>null);
    const ok=!!d;
    if(ok!==lastDet){r.classList.toggle('ok',ok);s.classList.toggle('ok',ok);}
    lastDet=ok;
  }
  if(stream)requestAnimationFrame(detectLoop);
}

async function verifyFace(){
  const btn=document.getElementById('faceBtn'),instr=document.getElementById('faceInstr');
  btn.disabled=true;instr.className='badge';instr.textContent='🔍 Analyse biométrique…';
  try{
    const v=document.getElementById('video');
    const r=await faceapi.detectSingleFace(v,new faceapi.TinyFaceDetectorOptions({inputSize:224,scoreThreshold:.5})).withFaceLandmarks(true).withFaceDescriptor();
    if(!r){instr.className='badge err';instr.textContent='❌ Aucun visage détecté. Réessayez.';btn.disabled=false;return;}
    instr.textContent='📡 Comparaison en cours…';
    const res=await fetch('api/face_verify.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({descriptor:Array.from(r.descriptor)})});
    const data=await res.json();
    const ov=document.getElementById('faceResult');ov.classList.add('show');
    document.getElementById('resIcon').textContent=data.ok?'✅':'❌';
    document.getElementById('resTxt').className='res-txt '+(data.ok?'ok':'err');
    document.getElementById('resTxt').textContent=data.ok?data.msg:'Visage non reconnu';
    if(data.ok){instr.className='badge ok';instr.textContent='✅ Connexion réussie !';stopCam();window.location.href='index.php';}
    else{instr.className='badge err';instr.textContent='❌ '+data.msg;showAlert('err',data.msg+' <a href="inscription.php" style="color:#a5b4fc">→ S\'inscrire</a>');setTimeout(()=>{ov.classList.remove('show');btn.disabled=false;},2200);}
  }catch(e){instr.className='badge err';instr.textContent='❌ '+e.message;btn.disabled=false;}
}

async function initFP(){
  const b=document.getElementById('fpInstr'),btn=document.getElementById('fpBtn');
  if(!window.PublicKeyCredential){b.className='badge err';b.textContent='❌ WebAuthn non supporté';btn.disabled=true;return;}
  const ok=await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().catch(()=>false);
  if(!ok){b.className='badge warn';b.textContent='⚠️ Capteur non détecté — PIN possible';}
  else{b.className='badge';b.textContent='👆 Prêt pour la lecture';}
}

async function verifyFP(){
  const b=document.getElementById('fpInstr'),rings=document.getElementById('fpRings'),icon=document.getElementById('fpIcon'),btn=document.getElementById('fpBtn');
  btn.disabled=true;rings.classList.add('scanning');b.className='badge';b.textContent='🔄 Obtention du challenge…';clearAlert();
  try{
    const chRes=await fetch('api/fp_challenge.php');const ch=await chRes.json();
    const chArr=Uint8Array.from(atob(ch.challenge.replace(/-/g,'+').replace(/_/g,'/')),c=>c.charCodeAt(0));
    b.textContent='👆 Posez votre doigt sur le capteur…';
    const assertion=await navigator.credentials.get({publicKey:{challenge:chArr,rpId:window.location.hostname||'localhost',userVerification:'required',timeout:60000}});
    b.textContent='📡 Vérification…';
    function b64(buf){return btoa(String.fromCharCode(...new Uint8Array(buf)))}
    const res=await fetch('api/fp_verify.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({credentialId:btoa(String.fromCharCode(...new Uint8Array(assertion.rawId))),counter:new DataView(assertion.response.authenticatorData).getUint32(33),clientDataJSON:b64(assertion.response.clientDataJSON),authenticatorData:b64(assertion.response.authenticatorData),signature:b64(assertion.response.signature)})});
    const data=await res.json();
    rings.classList.remove('scanning');
    if(data.ok){rings.classList.add('ok');icon.textContent='✅';b.className='badge ok';b.textContent='✅ Connexion réussie !';showAlert('ok',data.msg);setTimeout(()=>{window.location.href='index.php';},1400);}
    else{rings.classList.add('err');icon.textContent='❌';b.className='badge err';b.textContent='❌ Non reconnu';showAlert('err',data.msg+' <a href="inscription.php" style="color:#a5b4fc">→ S\'inscrire</a>');btn.disabled=false;}
  }catch(e){
    rings.classList.remove('scanning');rings.classList.add('err');icon.textContent='❌';
    b.className='badge err';b.textContent='❌ '+(e.name==='NotAllowedError'?'Lecture refusée.':e.message);btn.disabled=false;
  }
}
document.addEventListener('DOMContentLoaded',()=>{initFace();initFP();});
</script>
</body></html>
