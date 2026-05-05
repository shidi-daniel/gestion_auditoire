<?php
session_start();
if(!isset($_SESSION['utilisateur_connecte'])||!$_SESSION['utilisateur_connecte']){header('Location: connexion.php');exit;}
$faces_dir=__DIR__.'/data/faces_data/';$fp_dir=__DIR__.'/data/fingerprints_data/';
$users=[];
foreach(glob($faces_dir.'*.json') as $f){
  $d=json_decode(file_get_contents($f),true);
  if($d)$users[$d['userId']]=['id'=>$d['userId'],'name'=>$d['userName'],'role'=>$d['role']??'utilisateur','created'=>$d['createdAt']??'–','face'=>true,'fp'=>false];
}
foreach(glob($fp_dir.'*.json') as $f){
  $d=json_decode(file_get_contents($f),true);
  if($d&&isset($d['userId'])){
    if(!isset($users[$d['userId']]))$users[$d['userId']]=['id'=>$d['userId'],'name'=>$d['userName']??$d['userId'],'role'=>'utilisateur','created'=>$d['createdAt']??'–','face'=>false,'fp'=>false];
    $users[$d['userId']]['fp']=true;
  }
}
$logLines=file_exists(__DIR__.'/data/connexions.log')?array_slice(array_reverse(file(__DIR__.'/data/connexions.log')),0,15):[];
$me=$_SESSION['identifiant']??'';$myRole=$_SESSION['role']??'utilisateur';
$un=$_SESSION['nom_utilisateur']??'Utilisateur';
?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Utilisateurs — SGA</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--bg:#0d0c1d;--sb:rgba(8,7,20,.98);--card:rgba(15,14,30,.93);--bdr:rgba(255,255,255,.08);--bdr2:rgba(255,255,255,.14);--txt:#e2e8f0;--muted:#94a3b8;--sw:220px}
html,body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);min-height:100vh}
.bg-g{position:fixed;inset:0;z-index:0;pointer-events:none;background:radial-gradient(ellipse 55% 45% at 3% 14%,rgba(79,70,229,.28) 0%,transparent 60%),radial-gradient(ellipse 35% 30% at 97% 82%,rgba(124,58,237,.17) 0%,transparent 55%)}
.sidebar{position:fixed;left:0;top:0;bottom:0;width:var(--sw);background:var(--sb);border-right:1px solid var(--bdr);z-index:100;display:flex;flex-direction:column}
.sb-top{padding:18px 14px 14px;border-bottom:1px solid var(--bdr)}.sb-brand{display:flex;align-items:center;gap:10px}
.sb-ic{width:40px;height:40px;background:linear-gradient(135deg,var(--pri),var(--acc));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:#fff;box-shadow:0 5px 18px rgba(79,70,229,.42)}
.sb-n h2{font-size:13px;font-weight:800}.sb-n p{font-size:9px;color:var(--muted)}
.sb-nav{flex:1;padding:9px;overflow-y:auto}
.nt{font-size:8px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;padding:7px 8px 3px;opacity:.65}
.ni{display:flex;align-items:center;gap:9px;padding:8px 9px;border-radius:8px;text-decoration:none;color:var(--muted);font-size:12px;font-weight:500;transition:all .18s;margin-bottom:1px}
.ni:hover{background:rgba(255,255,255,.055);color:var(--txt)}.ni.active{background:rgba(79,70,229,.17);color:#a5b4fc;font-weight:700;border-left:3px solid var(--pri)}
.ni-i{font-size:14px;width:18px;text-align:center}
.sb-foot{padding:10px 9px;border-top:1px solid var(--bdr)}
.sb-out{display:flex;align-items:center;justify-content:center;width:100%;padding:7px;background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.17);color:#fca5a5;border-radius:7px;font-size:11.5px;font-weight:700;text-decoration:none;transition:all .2s}
.sb-out:hover{background:rgba(239,68,68,.17)}
.main{margin-left:var(--sw);min-height:100vh;position:relative;z-index:1}
.topbar{padding:15px 24px;border-bottom:1px solid var(--bdr);background:rgba(8,7,20,.75);backdrop-filter:blur(22px);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
.topbar h1{font-size:15px;font-weight:800}.topbar p{font-size:10.5px;color:var(--muted)}
.chip{display:inline-flex;padding:3px 9px;border-radius:18px;font-size:10px;font-weight:700;background:rgba(79,70,229,.13);border:1px solid rgba(79,70,229,.26);color:#a5b4fc}
.content{padding:22px 24px}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px}
.gcard{background:var(--card);border:1px solid var(--bdr);border-radius:13px;overflow:hidden;margin-bottom:14px}
.gc-h{padding:12px 18px 10px;border-bottom:1px solid var(--bdr);background:linear-gradient(155deg,rgba(79,70,229,.09),rgba(124,58,237,.04));display:flex;align-items:center;justify-content:space-between}
.gc-h h2{font-size:13.5px;font-weight:800;display:flex;align-items:center;gap:7px}.gc-b{padding:16px}
.sc{display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--card);border:1px solid var(--bdr);border-radius:12px;transition:border-color .2s}
.sc-n{font-size:28px;font-weight:900;line-height:1}.sc-l{font-size:10px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-top:2px}
.sc-ico-w{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.tbl-wrap{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;font-size:12px}
.tbl th{background:rgba(79,70,229,.12);color:#a5b4fc;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;border:1px solid var(--bdr);text-align:left}
.tbl td{padding:9px 12px;border:1px solid var(--bdr);vertical-align:middle}
.tbl tr:nth-child(even) td{background:rgba(255,255,255,.016)}
.user-av{width:30px;height:30px;background:linear-gradient(135deg,var(--pri),var(--acc));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.user-cell{display:flex;align-items:center;gap:8px}
.bio-pill{display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:12px;font-size:9.5px;font-weight:700;margin:1px}
.bp-face{background:rgba(79,70,229,.13);color:#a5b4fc;border:1px solid rgba(79,70,229,.22)}
.bp-fp{background:rgba(16,185,129,.13);color:#34d399;border:1px solid rgba(16,185,129,.22)}
.bp-none{background:rgba(239,68,68,.09);color:#fca5a5;font-size:9px}
.role-b{display:inline-flex;padding:2px 7px;border-radius:10px;font-size:9.5px;font-weight:800}
.r-admin{background:rgba(245,158,11,.13);color:#fbbf24;border:1px solid rgba(245,158,11,.22)}
.r-user{background:rgba(79,70,229,.10);color:#a5b4fc;border:1px solid rgba(79,70,229,.18)}
.me-row td{background:rgba(79,70,229,.055)!important}
.del-btn{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5;border-radius:6px;font-size:11px;padding:4px 9px;cursor:pointer;font-weight:700;transition:all .18s}
.del-btn:hover{background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.4)}
.log-line{display:flex;align-items:center;gap:9px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:11px;font-family:monospace}
.log-line:last-child{border-bottom:none}
.log-ok{color:#34d399}.log-ko{color:#fca5a5}.log-ts{color:var(--muted);font-size:10.5px;white-space:nowrap}
.log-mth{padding:1px 5px;border-radius:5px;font-size:9px;font-weight:800}
.mth-face{background:rgba(79,70,229,.14);color:#a5b4fc}.mth-fp{background:rgba(16,185,129,.14);color:#6ee7b7}
.alert{display:flex;gap:9px;padding:11px 14px;border-radius:10px;margin-bottom:14px;font-size:12.5px}
.al-ok{background:rgba(16,185,129,.09);border:1px solid rgba(16,185,129,.22);color:#6ee7b7}
.al-err{background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.22);color:#fca5a5}
footer{padding:12px 24px;border-top:1px solid var(--bdr);background:rgba(8,7,20,.5);font-size:10.5px;color:var(--muted)}
@media(max-width:860px){.main{margin-left:0}.sidebar{display:none}.grid2{grid-template-columns:1fr}}
</style></head><body>
<div class="bg-g"></div>
<aside class="sidebar">
  <div class="sb-top"><div class="sb-brand"><div class="sb-ic">SGA</div><div class="sb-n"><h2>Gestion Auditoires</h2><p>UPC · FCI 2025–2026</p></div></div></div>
  <nav class="sb-nav">
    <div class="nt">Navigation</div>
    <a class="ni" href="index.php"><span class="ni-i">🏠</span>Tableau de bord</a>
    <a class="ni" href="index.php?action=generer"><span class="ni-i">🚀</span>Générer Planning</a>
    <a class="ni" href="index.php?action=afficher"><span class="ni-i">📅</span>Voir Planning</a>
    <a class="ni" href="index.php?action=conflits"><span class="ni-i">⚠️</span>Conflits</a>
    <div class="nt" style="margin-top:6px">Données</div>
    <a class="ni" href="formulaires.php"><span class="ni-i">📝</span>Saisie Données</a>
    <a class="ni" href="telecharger.php"><span class="ni-i">⬇️</span>Télécharger</a>
    <a class="ni active" href="gestion_utilisateurs.php"><span class="ni-i">👥</span>Utilisateurs</a>
  </nav>
  <div class="sb-foot"><a href="deconnexion.php" class="sb-out">🚪 Déconnexion</a></div>
</aside>
<main class="main">
  <div class="topbar"><div><h1>👥 Gestion des Utilisateurs</h1><p>Comptes biométriques enregistrés</p></div><span class="chip">👤 <?=htmlspecialchars($un)?></span></div>
  <div class="content">
    <div id="alertZone"></div>
    <div class="grid2">
      <div class="sc" style="border-color:rgba(79,70,229,.22)"><div class="sc-ico-w" style="background:rgba(79,70,229,.13)">👥</div><div><div class="sc-n"><?=count($users)?></div><div class="sc-l">Utilisateurs inscrits</div></div></div>
      <div class="sc" style="border-color:rgba(16,185,129,.22)"><div class="sc-ico-w" style="background:rgba(16,185,129,.13)">🔐</div><div><div class="sc-n"><?=count(array_filter($users,fn($u)=>$u['face']&&$u['fp']))?></div><div class="sc-l">Biométrie complète</div></div></div>
    </div>
    <div class="gcard">
      <div class="gc-h"><h2>👤 Utilisateurs Biométriques</h2><span style="font-size:11px;color:var(--muted)"><?=count($users)?> compte(s)</span></div>
      <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Utilisateur</th><th>Identifiant</th><th>Rôle</th><th>Biométrie</th><th>Inscrit le</th><th>Action</th></tr></thead><tbody>
        <?php foreach($users as $u):?>
        <tr class="<?=$u['id']===$me?'me-row':''?>">
          <td><div class="user-cell"><div class="user-av">👤</div><div><div style="font-weight:700;font-size:12.5px"><?=htmlspecialchars($u['name'])?></div><?php if($u['id']===$me):?><div style="font-size:9px;color:#a5b4fc">👉 Vous</div><?php endif?></div></div></td>
          <td style="font-family:monospace;font-size:11.5px;color:#94a3b8"><?=htmlspecialchars($u['id'])?></td>
          <td><span class="role-b <?=$u['role']==='admin'?'r-admin':'r-user'?>"><?=$u['role']==='admin'?'Admin':'Utilisateur'?></span></td>
          <td><?php if($u['face']):?><span class="bio-pill bp-face">👤 Visage</span><?php endif?><?php if($u['fp']):?><span class="bio-pill bp-fp">👆 Empreinte</span><?php endif?><?php if(!$u['face']&&!$u['fp']):?><span class="bio-pill bp-none">–</span><?php endif?></td>
          <td style="font-size:10.5px;color:var(--muted)"><?=htmlspecialchars($u['created'])?></td>
          <td><button class="del-btn" onclick="del('<?=htmlspecialchars($u['id'])?>','<?=htmlspecialchars($u['name'])?>','all')" <?=($u['role']==='admin'&&$myRole!=='admin')?'disabled':''?>>🗑 Suppr.</button></td>
        </tr>
        <?php endforeach; if(empty($users)):?><tr><td colspan="6" style="text-align:center;padding:28px;color:var(--muted)">Aucun utilisateur inscrit. <a href="inscription.php" style="color:#a5b4fc">S'inscrire</a></td></tr><?php endif?>
      </tbody></table></div>
    </div>
    <?php if(!empty($logLines)):?>
    <div class="gcard">
      <div class="gc-h"><h2>📋 Dernières Connexions</h2></div>
      <div class="gc-b">
        <?php foreach($logLines as $l): $l=trim($l); preg_match('/\[([^\]]+)\]\s+(\S+)\s+\|\s+(\S+)\s+\|\s+(\S+)/',$l,$m);if(!$m)continue;$ok=($m[4]??'')==='SUCCES';?>
        <div class="log-line">
          <span style="font-size:14px"><?=$ok?'✅':'❌'?></span>
          <span class="log-ts"><?=$m[1]??''?></span>
          <span style="font-weight:700;<?=$ok?'color:#e2e8f0':'color:#fca5a5'?>"><?=htmlspecialchars($m[2]??'')?></span>
          <span class="log-mth <?=($m[3]??'')==='face'?'mth-face':'mth-fp'?>"><?=$m[3]??''?></span>
          <span class="<?=$ok?'log-ok':'log-ko'?>"><?=$m[4]??''?></span>
        </div>
        <?php endforeach?>
      </div>
    </div>
    <?php endif?>
  </div>
  <footer>SGA v2.0 · UPC · FCI 2025–2026</footer>
</main>
<script>
async function del(id,name,type){
  if(!confirm(`Supprimer les données biométriques de "${name}" ?\nCette action est irréversible.`))return;
  const r=await fetch('api/user_delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({userId:id,type})});
  const d=await r.json();
  const z=document.getElementById('alertZone');
  z.innerHTML=`<div class="alert ${d.ok?'al-ok':'al-err'}"><span>${d.ok?'✅':'❌'}</span><div>${d.msg}</div></div>`;
  if(d.ok)setTimeout(()=>location.reload(),1200);
}
</script>
</body></html>
