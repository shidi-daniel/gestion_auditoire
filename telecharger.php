<?php
session_start();
if(!isset($_SESSION['utilisateur_connecte'])||!$_SESSION['utilisateur_connecte']){header('Location: connexion.php');exit;}
require_once('TCPDF-main/tcpdf.php');
$out=__DIR__.'/output/';$data=__DIR__.'/data/';
function generate_pdf($content, $filename){
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('courier', '', 10);
    $pdf->Write(0, $content);
    $pdf->Output($filename, 'D');
}
function fsize($f){if(!file_exists($f))return'–';$b=filesize($f);return $b<1024?$b.' o':($b<1048576?round($b/1024,1).' Ko':round($b/1048576,1).' Mo');}
function fdate($f){return file_exists($f)?date('d/m/Y H:i',filemtime($f)):'–';}
$files=[
    ['action'=>'planning_txt','path'=>'planning.txt','dir'=>'out','name'=>'planning.pdf','icon'=>'📄','desc'=>'Planning hebdomadaire','bar'=>'linear-gradient(90deg,#4f46e5,#7c3aed)'],
    ['action'=>'planning_json','path'=>'planning.json','dir'=>'out','name'=>'planning.pdf','icon'=>'📦','desc'=>'Planning JSON','bar'=>'linear-gradient(90deg,#3b82f6,#6366f1)'],
    ['action'=>'rapport_txt','path'=>'rapport_occupation.txt','dir'=>'out','name'=>'rapport_occupation.pdf','icon'=>'📊','desc'=>'Rapport occupation','bar'=>'linear-gradient(90deg,#10b981,#059669)'],
    ['action'=>'salles_json','path'=>'salles.json','dir'=>'data','name'=>'salles.pdf','icon'=>'🏢','desc'=>'Salles','bar'=>'linear-gradient(90deg,#f59e0b,#ef4444)'],
    ['action'=>'promotions_json','path'=>'promotions.json','dir'=>'data','name'=>'promotions.pdf','icon'=>'🎓','desc'=>'Promotions','bar'=>'linear-gradient(90deg,#10b981,#3b82f6)'],
    ['action'=>'cours_json','path'=>'cours.json','dir'=>'data','name'=>'cours.pdf','icon'=>'📚','desc'=>'Cours','bar'=>'linear-gradient(90deg,#8b5cf6,#ec4899)'],
    ['action'=>'options_json','path'=>'options.json','dir'=>'data','name'=>'options.pdf','icon'=>'🎯','desc'=>'Options','bar'=>'linear-gradient(90deg,#06b6d4,#3b82f6)'],
];
$action=$_GET['action']??'';
foreach($files as $f){
    if($action===$f['action']){
        $dir=$f['dir']==='out'?$out:$data;
        $file=$dir.$f['path'];
        if(file_exists($file)){
            $content=strpos($f['path'],'.json')>0?json_encode(json_decode(file_get_contents($file)),JSON_PRETTY_PRINT):file_get_contents($file);
            generate_pdf($content, $f['name']);
        }
        break;
    }
}
$un=$_SESSION['nom_utilisateur']??'Utilisateur';
?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Téléchargements — SGA</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--bg:#0d0c1d;--sb:rgba(8,7,20,.98);--card:rgba(15,14,30,.93);--bdr:rgba(255,255,255,.08);--bdr2:rgba(255,255,255,.14);--txt:#e2e8f0;--muted:#94a3b8;--sw:220px}
html,body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);min-height:100vh}
.bg-g{position:fixed;inset:0;z-index:0;pointer-events:none;background:radial-gradient(ellipse 55% 45% at 3% 14%,rgba(79,70,229,.28) 0%,transparent 60%),radial-gradient(ellipse 35% 30% at 97% 82%,rgba(124,58,237,.17) 0%,transparent 55%)}
.sidebar{position:fixed;left:0;top:0;bottom:0;width:var(--sw);background:var(--sb);border-right:1px solid var(--bdr);z-index:100;display:flex;flex-direction:column}
.sb-top{padding:18px 14px 14px;border-bottom:1px solid var(--bdr)}.sb-brand{display:flex;align-items:center;gap:10px}
.sb-ic{width:40px;height:40px;background:linear-gradient(135deg,var(--pri),var(--acc));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:#fff}
.sb-n h2{font-size:13px;font-weight:800}.sb-n p{font-size:9px;color:var(--muted)}
.sb-nav{flex:1;padding:9px;overflow-y:auto}
.nt{font-size:8px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;padding:7px 8px 3px;opacity:.65}
.ni{display:flex;align-items:center;gap:9px;padding:8px 9px;border-radius:8px;text-decoration:none;color:var(--muted);font-size:12px;font-weight:500;transition:all .18s;margin-bottom:1px}
.ni:hover{background:rgba(255,255,255,.055);color:var(--txt)}.ni.active{background:rgba(79,70,229,.17);color:#a5b4fc;font-weight:700;border-left:3px solid var(--pri)}
.ni-i{font-size:14px;width:18px;text-align:center}
.sb-foot{padding:10px 9px;border-top:1px solid var(--bdr)}
.sb-out{display:flex;align-items:center;justify-content:center;width:100%;padding:7px;background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.17);color:#fca5a5;border-radius:7px;font-size:11.5px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s}
.sb-out:hover{background:rgba(239,68,68,.17)}
.main{margin-left:var(--sw);min-height:100vh;position:relative;z-index:1}
.topbar{padding:15px 24px;border-bottom:1px solid var(--bdr);background:rgba(8,7,20,.75);backdrop-filter:blur(22px);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
.topbar h1{font-size:15px;font-weight:800}.topbar p{font-size:10.5px;color:var(--muted)}
.chip{display:inline-flex;padding:3px 9px;border-radius:18px;font-size:10px;font-weight:700;background:rgba(79,70,229,.13);border:1px solid rgba(79,70,229,.26);color:#a5b4fc}
.content{padding:22px 24px}
.sec-title{font-size:12px;font-weight:800;color:#a5b4fc;text-transform:uppercase;letter-spacing:.5px;margin-bottom:13px;display:flex;align-items:center;gap:8px}
.sec-title::after{content:'';flex:1;height:1px;background:var(--bdr)}
.fgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;margin-bottom:26px}
.fc{background:var(--card);border:1px solid var(--bdr);border-radius:13px;padding:16px 18px;display:flex;flex-direction:column;gap:9px;transition:border-color .2s,transform .18s;position:relative;overflow:hidden}
.fc::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--bar,linear-gradient(90deg,var(--pri),var(--acc)))}
.fc:hover{border-color:var(--bdr2);transform:translateY(-1px)}
.fc-top{display:flex;align-items:flex-start;justify-content:space-between}
.fc-ico{font-size:26px;line-height:1}
.sp{padding:2px 8px;border-radius:12px;font-size:9.5px;font-weight:800}
.sp-ok{background:rgba(16,185,129,.11);border:1px solid rgba(16,185,129,.24);color:#34d399}
.sp-err{background:rgba(239,68,68,.11);border:1px solid rgba(239,68,68,.24);color:#fca5a5}
.fc-name{font-size:13.5px;font-weight:700;margin-bottom:1px}
.fc-desc{font-size:11px;color:var(--muted);line-height:1.5}
.fc-meta{display:flex;gap:10px;font-size:10px;color:var(--muted)}
.dl-btn{display:flex;align-items:center;justify-content:center;gap:5px;width:100%;padding:8px;background:linear-gradient(135deg,var(--pri),var(--acc));color:#fff;border:none;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;transition:transform .12s;box-shadow:0 3px 12px rgba(79,70,229,.34)}
.dl-btn:hover{transform:translateY(-1px)}
.dl-btn.off{background:rgba(255,255,255,.06);border:1.5px solid var(--bdr);color:var(--muted);box-shadow:none;cursor:not-allowed;pointer-events:none}
footer{padding:12px 24px;border-top:1px solid var(--bdr);background:rgba(8,7,20,.5);font-size:10.5px;color:var(--muted)}
@media(max-width:860px){.main{margin-left:0}.sidebar{display:none}}
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
    <a class="ni active" href="telecharger.php"><span class="ni-i">⬇️</span>Télécharger</a>
    <a class="ni" href="gestion_utilisateurs.php"><span class="ni-i">👥</span>Utilisateurs</a>
  </nav>
  <div class="sb-foot"><a href="deconnexion.php" class="sb-out">🚪 Déconnexion</a></div>
</aside>
<main class="main">
  <div class="topbar"><div><h1>⬇️ Téléchargements</h1><p>Fichiers générés et de configuration</p></div><span class="chip">👤 <?=htmlspecialchars($un)?></span></div>
  <div class="content">
    <div class="sec-title">📊 Résultats Générés (output/)</div>
    <div class="fgrid">
      <?php foreach(array_filter($files,fn($f)=>$f['dir']==='out') as $f):
        $dir=$out;$file=$dir.$f['path'];$e=file_exists($file);?>
      <div class="fc" style="--bar:<?=$f['bar']?>">
        <div class="fc-top"><div class="fc-ico"><?=$f['icon']?></div><?php if($e):?><span class="sp sp-ok">✓ Dispo</span><?php else:?><span class="sp sp-err">Non généré</span><?php endif?></div>
        <div><div class="fc-name"><?=$f['name']?></div><div class="fc-desc"><?=$f['desc']?></div></div>
        <?php if($e):?><div class="fc-meta"><span>📏 <?=fsize($file)?></span><span>🕐 <?=fdate($file)?></span></div><?php endif?>
        <?php if($e):?><a href="?action=<?=$f['action']?>" class="dl-btn">⬇️ Télécharger</a><?php else:?><a href="index.php?action=generer" class="dl-btn">🚀 Générer d'abord</a><?php endif?>
      </div>
      <?php endforeach?>
    </div>
    <div class="sec-title">⚙️ Configuration (data/)</div>
    <div class="fgrid">
      <?php foreach(array_filter($files,fn($f)=>$f['dir']==='data') as $f):
        $dir=$data;$file=$dir.$f['path'];$e=file_exists($file);?>
      <div class="fc" style="--bar:<?=$f['bar']?>">
        <div class="fc-top"><div class="fc-ico"><?=$f['icon']?></div><?php if($e):?><span class="sp sp-ok">✓ Présent</span><?php else:?><span class="sp sp-err">Manquant</span><?php endif?></div>
        <div><div class="fc-name"><?=$f['name']?></div><div class="fc-desc"><?=$f['desc']?></div></div>
        <?php if($e):?><div class="fc-meta"><span>📏 <?=fsize($file)?></span><span>🕐 <?=fdate($file)?></span></div><?php endif?>
        <?php if($e):?><a href="?action=<?=$f['action']?>" class="dl-btn">⬇️ Télécharger</a><?php else:?><a href="formulaires.php" class="dl-btn off">Fichier manquant</a><?php endif?>
      </div>
      <?php endforeach?>
    </div>
  </div>
  <footer>SGA v2.0 · UPC · FCI 2025–2026</footer>
</main>
</body></html>
