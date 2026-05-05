<?php
session_start();
if(isset($_SESSION['utilisateur_connecte'])&&$_SESSION['utilisateur_connecte']){header('Location: index.php');exit;}
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription Biométrique — SGA</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--warn:#f59e0b;--bg:#0d0c1d;--card:rgba(13,12,29,.96);--bdr:rgba(255,255,255,.09);--txt:#e2e8f0;--muted:#94a3b8}
html,body{min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt)}
body{display:flex;align-items:flex-start;justify-content:center;padding:30px 20px;position:relative}
.bg{position:fixed;inset:0;z-index:0;overflow:hidden}
.bg-c{position:absolute;border-radius:50%;filter:blur(80px);animation:fl 18s ease-in-out infinite alternate}
.bg-c1{width:500px;height:500px;background:rgba(79,70,229,.25);top:-100px;left:-80px}
.bg-c2{width:400px;height:400px;background:rgba(124,58,237,.18);bottom:-80px;right:-60px;animation-duration:14s;animation-delay:-6s}
@keyframes fl{from{transform:translate(0,0)}to{transform:translate(30px,25px)}}
.card{position:relative;z-index:1;width:100%;max-width:560px;background:var(--card);border:1px solid var(--bdr);
  border-radius:26px;box-shadow:0 36px 80px rgba(0,0,0,.7);backdrop-filter:blur(28px);
  animation:sUp .55s cubic-bezier(.16,1,.3,1)}
@keyframes sUp{from{opacity:0;transform:translateY(26px) scale(.97)}to{opacity:1;transform:none}}
/* HEADER */
.ch{padding:30px 32px 22px;border-bottom:1px solid var(--bdr);border-radius:26px 26px 0 0;
  background:linear-gradient(155deg,rgba(79,70,229,.14),rgba(124,58,237,.07));text-align:center}
.ch-logo{width:60px;height:60px;margin:0 auto 12px;background:linear-gradient(135deg,var(--pri),var(--acc));
  border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#fff;
  box-shadow:0 8px 24px rgba(79,70,229,.48)}
.ch h1{font-size:18px;font-weight:800;margin-bottom:3px}
.ch p{font-size:11.5px;color:var(--muted)}
.cb{padding:26px 32px 30px}
/* WIZARD */
.wizard{display:flex;justify-content:center;gap:0;margin-bottom:26px;padding-bottom:18px;border-bottom:1px solid var(--bdr)}
.ws{display:flex;flex-direction:column;align-items:center;gap:4px;flex:1;position:relative}
.ws:not(:last-child)::after{content:'';position:absolute;left:calc(50% + 14px);right:calc(-50% + 14px);top:13px;height:2px;background:var(--bdr);z-index:0;transition:background .4s}
.ws.done:not(:last-child)::after{background:var(--ok)}
.wn{width:28px;height:28px;border-radius:50%;border:2px solid var(--bdr);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--muted);position:relative;z-index:1;transition:all .3s;background:var(--bg)}
.wn.active{background:var(--pri);border-color:var(--pri);color:#fff;box-shadow:0 0 0 4px rgba(79,70,229,.2)}
.wn.done{background:var(--ok);border-color:var(--ok);color:#fff}
.wt{font-size:9px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.wt.active{color:var(--pri)}.wt.done{color:var(--ok)}
/* PANELS */
.pnl{display:none}.pnl.active{display:block;animation:sIn .33s ease}
@keyframes sIn{from{opacity:0;transform:translateX(16px)}to{opacity:1;transform:none}}
/* FORM */
.fg{margin-bottom:14px}
.lbl{display:block;font-size:10.5px;font-weight:800;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.iw{position:relative}.iico{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none}
.fc{width:100%;padding:10px 11px 10px 36px;background:rgba(255,255,255,.05);border:1.5px solid var(--bdr);border-radius:9px;color:var(--txt);font-size:13.5px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s}
.fc::placeholder{color:rgba(148,163,184,.35)}.fc:focus{border-color:var(--pri);background:rgba(79,70,229,.06);box-shadow:0 0 0 3px rgba(79,70,229,.15)}
.btn{width:100%;padding:12px;background:linear-gradient(135deg,var(--pri),var(--acc));color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:transform .13s,box-shadow .13s;box-shadow:0 5px 18px rgba(79,70,229,.40)}
.btn:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 9px 24px rgba(79,70,229,.52)}.btn:disabled{opacity:.38;cursor:not-allowed}
.btn-sec{width:100%;padding:10px;background:rgba(255,255,255,.05);border:1.5px solid var(--bdr);border-radius:9px;color:var(--txt);font-size:13px;font-weight:600;cursor:pointer;margin-top:9px;transition:all .2s}
.btn-sec:hover{background:rgba(255,255,255,.09)}
/* CAM */
.cam-box{position:relative;width:100%;aspect-ratio:4/3;background:#000;border-radius:14px;overflow:hidden;margin-bottom:12px;border:2px solid var(--bdr)}
#video{width:100%;height:100%;object-fit:cover;transform:scaleX(-1)}
.cam-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center}
.face-ring{width:155px;height:190px;border:2.5px solid rgba(79,70,229,.65);border-radius:50% 50% 46% 46%;position:relative;box-shadow:0 0 0 2000px rgba(0,0,0,.38);animation:rP 2.5s ease-in-out infinite}
@keyframes rP{0%,100%{border-color:rgba(79,70,229,.65)}50%{border-color:rgba(79,70,229,1)}}
.face-ring.ok{border-color:rgba(16,185,129,.9)!important;animation:none}
.scan-line{position:absolute;left:0;right:0;top:0;height:2.5px;background:linear-gradient(90deg,transparent,rgba(79,70,229,.9),transparent);animation:scan 2s linear infinite}
@keyframes scan{from{top:0;opacity:0}10%{opacity:1}90%{opacity:1}to{top:100%;opacity:0}}
.scan-line.ok{background:linear-gradient(90deg,transparent,rgba(16,185,129,.9),transparent)}
.thumbs{display:flex;gap:7px;justify-content:center;margin-bottom:12px}
.thumb{width:54px;height:54px;border-radius:8px;background:rgba(255,255,255,.04);border:2px solid var(--bdr);display:flex;align-items:center;justify-content:center;font-size:20px;overflow:hidden;transition:border-color .3s}
.thumb.filled{border-color:var(--ok)}.thumb img{width:100%;height:100%;object-fit:cover}
.prog{height:5px;background:rgba(255,255,255,.07);border-radius:4px;overflow:hidden;margin-bottom:14px}
.prog-fill{height:100%;background:linear-gradient(90deg,var(--pri),var(--acc));transition:width .4s}
/* INSTR */
.instr{text-align:center;margin-bottom:10px}
.badge{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:18px;font-size:12.5px;font-weight:600;background:rgba(79,70,229,.12);border:1px solid rgba(79,70,229,.3);color:#a5b4fc;transition:all .3s}
.badge.ok{background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);color:#6ee7b7}
.badge.err{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.3);color:#fca5a5}
/* FP */
.fp-zone{display:flex;flex-direction:column;align-items:center;padding:14px 0 8px}
.fp-rings{position:relative;width:140px;height:140px;margin-bottom:12px}
.fpr{position:absolute;border:2.5px solid rgba(124,58,237,.28);border-radius:50%;transition:all .4s}
.fpr:nth-child(1){inset:50px}.fpr:nth-child(2){inset:33px}.fpr:nth-child(3){inset:16px}.fpr:nth-child(4){inset:1px}
.fp-ctr{position:absolute;inset:50px;background:rgba(124,58,237,.10);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px}
.fp-rings.scanning .fpr{animation:fpP 1.5s ease-in-out infinite}
.fp-rings.scanning .fpr:nth-child(2){animation-delay:.12s}.fp-rings.scanning .fpr:nth-child(3){animation-delay:.24s}.fp-rings.scanning .fpr:nth-child(4){animation-delay:.36s}
@keyframes fpP{0%,100%{opacity:.2;transform:scale(.97)}50%{opacity:1;transform:scale(1.04)}}
.fp-rings.ok .fpr{border-color:rgba(16,185,129,.65)!important;animation:none}
/* ALERTS */
.alert{display:flex;gap:9px;padding:12px 14px;border-radius:10px;margin-bottom:14px;font-size:12.5px;line-height:1.5}
.al-ok{background:rgba(16,185,129,.10);border:1px solid rgba(16,185,129,.25);color:#6ee7b7}
.al-err{background:rgba(239,68,68,.10);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
.link{color:#a5b4fc;text-decoration:none;font-size:12.5px}.link:hover{text-decoration:underline}
.sf{margin-top:20px;padding-top:16px;border-top:1px solid var(--bdr);text-align:center;font-size:10.5px;color:rgba(148,163,184,.5);display:flex;align-items:center;justify-content:center;gap:5px}
.sf .dot{width:5px;height:5px;background:var(--ok);border-radius:50%}
@media(max-width:480px){.cb{padding:20px 18px 26px}.ch{padding:24px 18px 18px}}
</style>
</head>
<body>
<div class="bg"><div class="bg-c bg-c1"></div><div class="bg-c bg-c2"></div></div>
<div class="card">
  <div class="ch">
    <div class="ch-logo">SGA</div>
    <h1>Inscription Biométrique</h1>
    <p>Université Protestante au Congo · Faculté des Sciences Informatiques</p>
  </div>
  <div class="cb">
    <!-- WIZARD -->
    <div class="wizard">
      <div class="ws" id="ws1"><div class="wn active" id="wn1">1</div><span class="wt active" id="wt1">Identité</span></div>
      <div class="ws" id="ws2"><div class="wn" id="wn2">2</div><span class="wt" id="wt2">Visage</span></div>
      <div class="ws" id="ws3"><div class="wn" id="wn3">3</div><span class="wt" id="wt3">Empreinte</span></div>
      <div class="ws"><div class="wn" id="wn4">✓</div><span class="wt" id="wt4">Terminé</span></div>
    </div>
    <div id="alertZone"></div>

    <!-- ÉTAPE 1 : Identité -->
    <div class="pnl active" id="p1">
      <div style="background:rgba(79,70,229,.08);border:1px dashed rgba(79,70,229,.3);border-radius:10px;padding:11px 14px;margin-bottom:18px;font-size:12px;color:#a5b4fc">
        <strong style="display:block;margin-bottom:2px">📋 Étape 1 — Vos informations</strong>
        Saisissez un identifiant unique et votre nom. Ces données seront liées à votre biométrie.
      </div>
      <div class="fg"><label class="lbl">Identifiant unique</label><div class="iw"><span class="iico">🔖</span><input class="fc" type="text" id="userId" placeholder="ex: dupont_jean" autocomplete="off"></div></div>
      <div class="fg"><label class="lbl">Nom complet</label><div class="iw"><span class="iico">👤</span><input class="fc" type="text" id="userName" placeholder="ex: Jean Dupont" autocomplete="off"></div></div>
      <button class="btn" onclick="goStep(2)">Continuer → Reconnaissance faciale</button>
      <div style="text-align:center;margin-top:14px"><a class="link" href="connexion.php">Déjà inscrit ? Se connecter</a></div>
    </div>

    <!-- ÉTAPE 2 : Visage -->
    <div class="pnl" id="p2">
      <div style="background:rgba(79,70,229,.08);border:1px dashed rgba(79,70,229,.3);border-radius:10px;padding:11px 14px;margin-bottom:14px;font-size:12px;color:#a5b4fc">
        <strong style="display:block;margin-bottom:2px">📸 Étape 2 — Reconnaissance faciale</strong>
        Positionnez votre visage et suivez les instructions. 5 captures depuis différents angles.
      </div>
      <div class="instr"><span class="badge" id="faceInstr">🔄 Chargement modèles IA…</span></div>
      <div class="cam-box">
        <video id="video" autoplay muted playsinline></video>
        <div class="cam-overlay"><div class="face-ring" id="faceRing"><div class="scan-line" id="scanLine"></div></div></div>
      </div>
      <div class="thumbs" id="thumbs">
        <div class="thumb" id="th0">📷</div><div class="thumb" id="th1">📷</div><div class="thumb" id="th2">📷</div>
        <div class="thumb" id="th3">📷</div><div class="thumb" id="th4">📷</div>
      </div>
      <div class="prog"><div class="prog-fill" id="fp" style="width:0%"></div></div>
      <button class="btn" id="capBtn" disabled onclick="captureNext()">⏳ Préparation…</button>
      <button class="btn-sec" onclick="goStep(1)">← Retour</button>
    </div>

    <!-- ÉTAPE 3 : Empreinte -->
    <div class="pnl" id="p3">
      <div style="background:rgba(79,70,229,.08);border:1px dashed rgba(79,70,229,.3);border-radius:10px;padding:11px 14px;margin-bottom:14px;font-size:12px;color:#a5b4fc">
        <strong style="display:block;margin-bottom:2px">👆 Étape 3 — Empreinte digitale</strong>
        Utilisez le capteur biométrique de votre appareil pour enregistrer votre empreinte.
      </div>
      <div class="fp-zone">
        <div class="fp-rings" id="fpRings">
          <div class="fpr"></div><div class="fpr"></div><div class="fpr"></div><div class="fpr"></div>
          <div class="fp-ctr" id="fpIcon">☁</div>
        </div>
        <div class="instr"><span class="badge" id="fpBadge">Prêt pour la lecture</span></div>
      </div>
      <div class="prog"><div class="prog-fill" id="fpProg" style="width:0%"></div></div>
      <button class="btn" id="fpBtn" onclick="startFP()">👆 Démarrer l'enregistrement</button>
      <button class="btn-sec" onclick="goStep(2)">← Retour</button>
    </div>

    <!-- ÉTAPE 4 : Succès -->
    <div class="pnl" id="p4" style="text-align:center">
      <div style="font-size:64px;margin:10px 0 16px">🎉</div>
      <h2 style="font-size:20px;color:var(--ok);margin-bottom:8px">Inscription réussie !</h2>
      <p style="color:var(--muted);font-size:13px;margin-bottom:24px">Votre visage et votre empreinte ont été enregistrés.<br>Vous pouvez maintenant vous connecter.</p>
      <a href="connexion.php"><button class="btn">🔐 Se connecter maintenant</button></a>
    </div>
    <div class="sf"><div class="dot"></div><span>Données stockées localement · Aucune transmission externe</span></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js"></script>
<script>
const MODEL='https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model';
const MOVES=['⬆ Regardez EN HAUT','⬇ Regardez EN BAS','⬅ Tournez à GAUCHE','➡ Tournez à DROITE','😐 Regardez DROIT'];
let userId,userName,modelsOk=false,stream=null,faceDescs=[],moveIdx=0,capturing=false;

function showAlert(type,msg){const c=type==='ok'?'al-ok':'al-err',i=type==='ok'?'✅':'⚠️';document.getElementById('alertZone').innerHTML=`<div class="alert ${c}"><span>${i}</span><div>${msg}</div></div>`;}
function clearAlert(){document.getElementById('alertZone').innerHTML='';}

function goStep(n){
  if(n===2){
    userId=document.getElementById('userId').value.trim().replace(/[^a-zA-Z0-9_-]/g,'');
    userName=document.getElementById('userName').value.trim();
    if(!userId){showAlert('err','Veuillez saisir un identifiant.');return;}
    if(!userName){showAlert('err','Veuillez saisir votre nom.');return;}
    clearAlert();startCamera();
  }
  if(n===3){stopCamera();initFPPanel();}
  if(n===1)stopCamera();
  [1,2,3,4].forEach(i=>{
    document.getElementById('p'+i).classList.toggle('active',i===n);
    const wn=document.getElementById('wn'+i),wt=document.getElementById('wt'+i);
    if(i<n){wn.className='wn done';wt.className='wt done';wn.textContent='✓';}
    else if(i===n){wn.className='wn active';wt.className='wt active';wn.textContent=i;}
    else{wn.className='wn';wt.className='wt';wn.textContent=i;}
  });
}

async function startCamera(){
  const instr=document.getElementById('faceInstr'),btn=document.getElementById('capBtn');
  instr.className='badge';instr.textContent='🔄 Chargement modèles IA…';btn.disabled=true;btn.textContent='⏳ Préparation…';
  try{
    if(!modelsOk){await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL);await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL);await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL);modelsOk=true;}
    stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:640,height:480},audio:false});
    const v=document.getElementById('video');v.srcObject=stream;await v.play();
    instr.textContent=MOVES[0];btn.disabled=false;btn.textContent=`📷 Capturer (${MOVES[0]})`;
    detectLoop();
  }catch(e){instr.className='badge err';instr.textContent='❌ Caméra : '+e.message;}
}

function stopCamera(){if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}}

async function detectLoop(){
  const v=document.getElementById('video'),r=document.getElementById('faceRing'),s=document.getElementById('scanLine');
  if(!stream)return;
  if(v.readyState>=2){const d=await faceapi.detectSingleFace(v,new faceapi.TinyFaceDetectorOptions({inputSize:160,scoreThreshold:.4})).catch(()=>null);const ok=!!d;r.classList.toggle('ok',ok);s.classList.toggle('ok',ok);}
  if(stream)requestAnimationFrame(detectLoop);
}

async function captureNext(){
  if(capturing)return;capturing=true;
  const btn=document.getElementById('capBtn'),instr=document.getElementById('faceInstr'),v=document.getElementById('video');
  btn.disabled=true;instr.className='badge';instr.textContent='🔍 Analyse…';
  try{
    const r=await faceapi.detectSingleFace(v,new faceapi.TinyFaceDetectorOptions({inputSize:224,scoreThreshold:.5})).withFaceLandmarks(true).withFaceDescriptor();
    if(!r){instr.className='badge err';instr.textContent='❌ Visage non détecté. Repositionnez-vous.';btn.disabled=false;capturing=false;return;}
    const c=document.createElement('canvas');c.width=v.videoWidth;c.height=v.videoHeight;
    const ctx=c.getContext('2d');ctx.save();ctx.scale(-1,1);ctx.drawImage(v,-c.width,0);ctx.restore();
    const b=result=r.detection.box,pad=20,t=document.createElement('canvas');t.width=60;t.height=60;
    t.getContext('2d').drawImage(c,Math.max(0,b.x-pad),Math.max(0,b.y-pad),b.width+pad*2,b.height+pad*2,0,0,60,60);
    const idx=faceDescs.length;faceDescs.push(Array.from(r.descriptor));
    const th=document.getElementById('th'+idx);th.innerHTML=`<img src="${t.toDataURL('image/jpeg',.7)}">`;th.classList.add('filled');
    document.getElementById('fp').style.width=((idx+1)/5*100)+'%';
    if(faceDescs.length>=5){instr.className='badge ok';instr.textContent='✅ Captures terminées ! Enregistrement…';await registerFace();}
    else{moveIdx++;instr.textContent=MOVES[moveIdx]||'Face caméra';btn.textContent=`📷 Capturer (${MOVES[moveIdx]||'Final'}) — ${faceDescs.length}/5`;btn.disabled=false;}
  }catch(e){instr.className='badge err';instr.textContent='❌ '+e.message;btn.disabled=false;}
  capturing=false;
}

async function registerFace(){
  try{
    const res=await fetch('api/face_register.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({userId,userName,descriptors:faceDescs})});
    const data=await res.json();
    if(data.ok){showAlert('ok',data.msg);goStep(3);}
    else if(data.duplicate){showAlert('err',data.msg);faceDescs=[];moveIdx=0;[0,1,2,3,4].forEach(i=>{const t=document.getElementById('th'+i);t.innerHTML='📷';t.classList.remove('filled');});document.getElementById('fp').style.width='0%';const btn=document.getElementById('capBtn');btn.disabled=false;btn.textContent=`📷 Capturer (${MOVES[0]})`;document.getElementById('faceInstr').textContent=MOVES[0];}
    else{showAlert('err',data.msg);document.getElementById('capBtn').disabled=false;}
  }catch(e){showAlert('err','Erreur réseau: '+e.message);document.getElementById('capBtn').disabled=false;}
}

async function initFPPanel(){
  clearAlert();
  const badge=document.getElementById('fpBadge'),btn=document.getElementById('fpBtn');
  if(!window.PublicKeyCredential){badge.className='badge err';badge.textContent='❌ WebAuthn non supporté';btn.disabled=true;return;}
  const ok=await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().catch(()=>false);
  badge.className='badge';badge.textContent=ok?'👆 Capteur détecté — Appuyez pour scanner':'⚠️ Capteur non détecté (PIN possible)';
}

async function startFP(){
  const badge=document.getElementById('fpBadge'),rings=document.getElementById('fpRings'),icon=document.getElementById('fpIcon'),prog=document.getElementById('fpProg'),btn=document.getElementById('fpBtn');
  btn.disabled=true;rings.className='fp-rings scanning';badge.className='badge';badge.textContent='🔄 Challenge…';prog.style.width='20%';
  try{
    const chRes=await fetch('api/fp_challenge.php');const ch=await chRes.json();
    const chArr=Uint8Array.from(atob(ch.challenge.replace(/-/g,'+').replace(/_/g,'/')),c=>c.charCodeAt(0));
    badge.textContent='👆 Posez votre doigt…';prog.style.width='40%';
    const cred=await navigator.credentials.create({publicKey:{challenge:chArr,rp:{id:window.location.hostname||'localhost',name:ch.rpName},user:{id:new TextEncoder().encode(userId),name:userId,displayName:userName},pubKeyCredParams:[{type:'public-key',alg:-7},{type:'public-key',alg:-257}],authenticatorSelection:{authenticatorAttachment:'platform',userVerification:'required',residentKey:'preferred'},timeout:ch.timeout||60000,attestation:'none'}});
    prog.style.width='70%';badge.textContent='📡 Enregistrement…';
    function b64(buf){return btoa(String.fromCharCode(...new Uint8Array(buf)))}
    const res=await fetch('api/fp_register.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({userId,userName,credentialId:btoa(String.fromCharCode(...new Uint8Array(cred.rawId))),publicKey:b64(cred.response.getPublicKey?cred.response.getPublicKey():new ArrayBuffer(0)),counter:0,clientDataJSON:b64(cred.response.clientDataJSON),attestationObject:b64(cred.response.attestationObject)})});
    const data=await res.json();prog.style.width='100%';
    if(data.ok){rings.className='fp-rings ok';icon.textContent='✅';badge.className='badge ok';badge.textContent='✅ Empreinte enregistrée !';showAlert('ok',data.msg);setTimeout(()=>goStep(4),1200);}
    else if(data.duplicate){badge.className='badge err';badge.textContent='⚠️ Empreinte déjà enregistrée';showAlert('err',data.msg);rings.className='fp-rings';icon.textContent='☁';prog.style.width='0%';btn.disabled=false;}
    else throw new Error(data.msg);
  }catch(e){rings.className='fp-rings';icon.textContent='❌';badge.className='badge err';badge.textContent='❌ '+(e.name==='NotAllowedError'?'Lecture refusée.':e.message);prog.style.width='0%';btn.disabled=false;}
}
</script>
</body></html>
