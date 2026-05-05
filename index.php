<?php
session_start();
if (!isset($_SESSION['utilisateur_connecte']) || !$_SESSION['utilisateur_connecte']) { header('Location: connexion.php'); exit; }
require_once 'fonctions.php';
$data_dir = __DIR__.'/data/'; $output_dir = __DIR__.'/output/';
if (!is_dir($output_dir)) mkdir($output_dir, 0755, true);
$messages=[]; $erreurs=[]; $planning=null; $salles=null; $promotions=null; $cours=null; $options=null; $conflits=[];
$action = $_GET['action'] ?? 'dashboard';
switch($action){
  case 'generer':
    try{
      $salles=charger_salles($data_dir.'salles.json'); $messages[]=count($salles)." salle(s) chargée(s)";
      $promotions=charger_promotions($data_dir.'promotions.json'); $messages[]=count($promotions)." promotion(s) chargée(s)";
      $cours=charger_cours($data_dir.'cours.json'); $messages[]=count($cours)." cours chargé(s)";
      $options=charger_options($data_dir.'options.json'); $messages[]=count($options)." option(s) chargée(s)";
      $planning=generer_planning($salles,$promotions,$cours,$options,creer_creneaux_disponibles());
      $messages[]="Planning généré avec succès ✓";
      sauvegarder_planning($planning,$output_dir.'planning.txt'); $messages[]="planning.txt sauvegardé";
      sauvegarder_planning_json($planning,$output_dir.'planning.json'); $messages[]="planning.json sauvegardé";
      generer_rapport_occupation($planning,$salles,$output_dir.'rapport_occupation.txt'); $messages[]="Rapport d'occupation généré";
      $conflits=detecter_conflits($planning);
      if(empty($conflits))$messages[]="Aucun conflit détecté ✓"; else $erreurs[]=count($conflits)." conflit(s) détecté(s)";
    }catch(Exception $e){$erreurs[]=$e->getMessage();}
    break;
  case 'afficher':
    try{
      if(file_exists($output_dir.'planning.json')){
        $planning=charger_planning($output_dir.'planning.json');
        if(file_exists($data_dir.'salles.json'))$salles=charger_salles($data_dir.'salles.json');
        if(file_exists($data_dir.'cours.json'))$cours=charger_cours($data_dir.'cours.json');
      }else{$erreurs[]="Aucun planning. Générez d'abord le planning.";}
    }catch(Exception $e){$erreurs[]=$e->getMessage();}
    break;
  case 'conflits':
    try{
      if(file_exists($output_dir.'planning.json')){$planning=charger_planning($output_dir.'planning.json');$conflits=detecter_conflits($planning);}
      else $erreurs[]="Aucun planning disponible.";
    }catch(Exception $e){$erreurs[]=$e->getMessage();}
    break;
  default:
    try{
      if(file_exists($data_dir.'salles.json'))$salles=charger_salles($data_dir.'salles.json');
      if(file_exists($data_dir.'promotions.json'))$promotions=charger_promotions($data_dir.'promotions.json');
      if(file_exists($data_dir.'cours.json'))$cours=charger_cours($data_dir.'cours.json');
      if(file_exists($data_dir.'options.json'))$options=charger_options($data_dir.'options.json');
      if(file_exists($output_dir.'planning.json'))$planning=charger_planning($output_dir.'planning.json');
    }catch(Exception $e){}
}
$hasPlanning=file_exists($output_dir.'planning.json');
$userName=$_SESSION['nom_utilisateur']??$_SESSION['identifiant']??'Utilisateur';
$methode=$_SESSION['methode_auth']??'';
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SGA — Tableau de Bord</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--warn:#f59e0b;--info:#3b82f6;
  --bg:#0d0c1d;--sb:rgba(8,7,20,.98);--card:rgba(15,14,30,.93);--bdr:rgba(255,255,255,.08);
  --bdr2:rgba(255,255,255,.14);--txt:#e2e8f0;--muted:#94a3b8;--sw:256px;
}
html,body{height:100%;font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);overflow-x:hidden}
.bg-glow{position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(ellipse 60% 50% at 2% 12%,rgba(79,70,229,.32) 0%,transparent 60%),
             radial-gradient(ellipse 40% 35% at 98% 82%,rgba(124,58,237,.20) 0%,transparent 55%)}
/* ── SIDEBAR ── */
.sidebar{position:fixed;left:0;top:0;bottom:0;width:var(--sw);
  background:var(--sb);border-right:1px solid var(--bdr);z-index:200;
  display:flex;flex-direction:column;transition:transform .3s}
.sb-top{padding:22px 18px 16px;border-bottom:1px solid var(--bdr);flex-shrink:0}
.sb-brand{display:flex;align-items:center;gap:11px;margin-bottom:12px}
.sb-badge{width:44px;height:44px;background:linear-gradient(135deg,var(--pri),var(--acc));
  border-radius:13px;display:flex;align-items:center;justify-content:center;
  font-size:14px;font-weight:900;color:#fff;box-shadow:0 6px 20px rgba(79,70,229,.45);flex-shrink:0}
.sb-name h2{font-size:14px;font-weight:800;line-height:1.2}
.sb-name p{font-size:9.5px;color:var(--muted)}
.sb-uni{background:rgba(79,70,229,.09);border:1px solid rgba(79,70,229,.18);border-radius:8px;padding:8px 10px}
.sb-uni strong{display:block;font-size:9.5px;color:#c7d2fe;margin-bottom:1px}
.sb-uni p{font-size:9px;color:var(--muted);line-height:1.55}
/* NAV */
.sb-nav{flex:1;padding:10px;overflow-y:auto}
.nt{font-size:8.5px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;padding:9px 9px 4px;opacity:.65}
.ni{display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:9px;
  text-decoration:none;color:var(--muted);font-size:12.5px;font-weight:500;
  transition:all .18s;margin-bottom:1px;border:none;background:none;cursor:pointer;width:100%;text-align:left}
.ni:hover{background:rgba(255,255,255,.055);color:var(--txt)}
.ni.active{background:rgba(79,70,229,.17);color:#a5b4fc;font-weight:700;border-left:3px solid var(--pri)}
.ni-ico{font-size:15px;width:20px;text-align:center;flex-shrink:0}
.ni-badge{margin-left:auto;background:var(--pri);color:#fff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:20px}
.ni-badge.ok{background:var(--ok)}
/* USER + LOGOUT */
.sb-foot{padding:12px 10px;border-top:1px solid var(--bdr);flex-shrink:0}
.sb-user{display:flex;align-items:center;gap:9px;padding:9px 10px;background:rgba(255,255,255,.04);border:1px solid var(--bdr);border-radius:9px;margin-bottom:8px}
.sb-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--pri),var(--acc));display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.sb-ui p{font-size:12.5px;font-weight:600;line-height:1.2}
.sb-ui span{font-size:9.5px;color:var(--muted)}
.sb-logout{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px;background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.18);color:#fca5a5;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s}
.sb-logout:hover{background:rgba(239,68,68,.17)}
/* ── MAIN ── */
.main{margin-left:var(--sw);min-height:100vh;position:relative;z-index:1;display:flex;flex-direction:column}
/* TOPBAR */
.topbar{padding:16px 26px;border-bottom:1px solid var(--bdr);background:rgba(8,7,20,.75);
  backdrop-filter:blur(22px);display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:100;flex-shrink:0}
.tb-title h1{font-size:16px;font-weight:800}
.tb-title p{font-size:11px;color:var(--muted);margin-top:1px}
.tb-chips{display:flex;gap:7px;align-items:center;flex-wrap:wrap}
.chip{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:10.5px;font-weight:700}
.chip-ok{background:rgba(16,185,129,.11);border:1px solid rgba(16,185,129,.25);color:#34d399}
.chip-info{background:rgba(79,70,229,.13);border:1px solid rgba(79,70,229,.28);color:#a5b4fc}
.chip-warn{background:rgba(245,158,11,.11);border:1px solid rgba(245,158,11,.25);color:#fbbf24}
/* CONTENT */
.content{padding:24px 26px;flex:1}
/* STATS */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px}
.sc{background:var(--card);border:1px solid var(--bdr);border-radius:14px;padding:18px 18px;
  position:relative;overflow:hidden;transition:border-color .2s,transform .2s}
.sc::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--c1),var(--c2))}
.sc:hover{border-color:var(--bdr2);transform:translateY(-2px)}
.sc-ico{font-size:22px;margin-bottom:9px}
.sc-num{font-size:28px;font-weight:900;line-height:1}
.sc-lbl{font-size:10.5px;color:var(--muted);margin-top:3px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
/* CARD */
.gcard{background:var(--card);border:1px solid var(--bdr);border-radius:16px;overflow:hidden;margin-bottom:18px}
.gc-h{padding:14px 20px 12px;border-bottom:1px solid var(--bdr);background:linear-gradient(155deg,rgba(79,70,229,.09),rgba(124,58,237,.04));display:flex;align-items:center;justify-content:space-between}
.gc-h h2{font-size:14px;font-weight:800;display:flex;align-items:center;gap:8px}
.gc-b{padding:20px}
/* BTN */
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 17px;border-radius:9px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;border:none;transition:transform .12s,box-shadow .12s;white-space:nowrap}
.btn:hover:not(:disabled){transform:translateY(-1px)}
.btn-pri{background:linear-gradient(135deg,var(--pri),var(--acc));color:#fff;box-shadow:0 4px 15px rgba(79,70,229,.38)}
.btn-ghost{background:rgba(255,255,255,.055);color:var(--txt);border:1.5px solid var(--bdr2)}
.btn-ghost:hover{background:rgba(255,255,255,.09)}
.btn-ok{background:rgba(16,185,129,.11);color:#34d399;border:1.5px solid rgba(16,185,129,.25)}
.btn-warn{background:rgba(245,158,11,.11);color:#fbbf24;border:1.5px solid rgba(245,158,11,.25)}
.actions{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:20px}
/* ALERT */
.alert{display:flex;gap:9px;padding:12px 14px;border-radius:11px;margin-bottom:14px;font-size:12.5px;line-height:1.5}
.al-ok{background:rgba(16,185,129,.09);border:1px solid rgba(16,185,129,.22);color:#6ee7b7}
.al-err{background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.22);color:#fca5a5}
.al-info{background:rgba(79,70,229,.09);border:1px solid rgba(79,70,229,.22);color:#a5b4fc}
/* DATA LIST */
.dlist{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:9px}
.di{background:rgba(79,70,229,.07);border:1px solid rgba(79,70,229,.16);border-radius:10px;padding:11px 13px}
.di-id{font-size:10px;font-weight:800;color:#a5b4fc;text-transform:uppercase;letter-spacing:.3px;margin-bottom:2px}
.di-lbl{font-size:12.5px;font-weight:600}
.di-sub{font-size:10.5px;color:var(--muted);margin-top:2px}
.prog-bar{height:4px;background:rgba(255,255,255,.07);border-radius:3px;margin-top:6px;overflow:hidden}
.prog-fill{height:100%;background:linear-gradient(90deg,var(--pri),var(--acc));border-radius:3px}
/* TABLE */
.tbl-wrap{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;font-size:12px;min-width:600px}
.tbl th{background:rgba(79,70,229,.13);color:#a5b4fc;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:10px 12px;border:1px solid var(--bdr);text-align:center}
.tbl td{padding:9px 11px;border:1px solid var(--bdr);vertical-align:top;text-align:center}
.tbl tr:hover td{background:rgba(79,70,229,.04)}
.tbl td:first-child{font-weight:700;color:#a5b4fc;background:rgba(79,70,229,.06);text-align:left;white-space:nowrap;font-size:11px}
.slot{background:rgba(79,70,229,.10);border:1px solid rgba(79,70,229,.22);border-radius:7px;padding:5px 7px;margin:2px;text-align:left;display:inline-block;width:calc(100% - 4px)}
.slot-c{font-weight:700;color:#c7d2fe;font-size:11px}
.slot-s{color:var(--muted);font-size:9.5px}
.slot-g{display:inline-flex;padding:1px 5px;background:rgba(16,185,129,.14);border-radius:4px;color:#6ee7b7;font-size:9px;font-weight:800;margin-top:2px}
.cell-e{color:rgba(148,163,184,.25);font-size:9.5px}
/* HERO WELCOME */
.hero{background:linear-gradient(135deg,rgba(79,70,229,.16) 0%,rgba(124,58,237,.10) 60%,transparent 100%);border:1px solid rgba(79,70,229,.20);border-radius:16px;padding:26px 28px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;gap:20px}
.hero-art{font-size:58px;flex-shrink:0}
.hero h2{font-size:19px;font-weight:800;margin-bottom:6px}
.hero p{font-size:12.5px;color:var(--muted);line-height:1.65;max-width:480px}
/* BADGE */
.badge{display:inline-flex;padding:2px 8px;border-radius:12px;font-size:9.5px;font-weight:800}
.b-tc{background:rgba(79,70,229,.14);color:#a5b4fc;border:1px solid rgba(79,70,229,.24)}
.b-opt{background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.24)}
/* CONF TABLE */
.ctbl{width:100%;border-collapse:collapse;font-size:12px}
.ctbl th{background:rgba(239,68,68,.11);color:#fca5a5;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;border:1px solid rgba(239,68,68,.14);text-align:left}
.ctbl td{padding:9px 12px;border:1px solid var(--bdr)}
.ctbl tr:nth-child(even) td{background:rgba(255,255,255,.015)}
/* MOB */
.mob-btn{display:none;position:fixed;top:12px;left:12px;z-index:300;width:36px;height:36px;border-radius:9px;background:var(--pri);border:none;color:#fff;font-size:17px;cursor:pointer;align-items:center;justify-content:center}
footer{padding:14px 26px;border-top:1px solid var(--bdr);background:rgba(8,7,20,.5);font-size:10.5px;color:var(--muted);display:flex;justify-content:space-between}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:none}.main{margin-left:0}.topbar{padding:14px 16px 14px 56px}.content{padding:18px 14px}.stats{grid-template-columns:repeat(2,1fr)}.mob-btn{display:flex}.hero{flex-direction:column;gap:10px}.hero-art{display:none}}
@media(max-width:450px){.stats{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="bg-glow"></div>
<button class="mob-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>

<aside class="sidebar">
  <div class="sb-top">
    <div class="sb-brand">
      <div class="sb-badge">SGA</div>
      <div class="sb-name"><h2>Gestion des Auditoires</h2><p>UPC · FCI · 2025–2026</p></div>
    </div>
    <div class="sb-uni"><strong>Université Protestante au Congo</strong><p>Faculté des Sciences Informatiques<br>Année académique 2025–2026</p></div>
  </div>
  <nav class="sb-nav">
    <div class="nt">Principal</div>
    <a class="ni <?=$action==='dashboard'?'active':''?>" href="?action=dashboard"><span class="ni-ico">🏠</span>Tableau de bord</a>
    <a class="ni <?=$action==='generer'?'active':''?>" href="?action=generer"><span class="ni-ico">🚀</span>Générer Planning</a>
    <a class="ni <?=$action==='afficher'?'active':''?>" href="?action=afficher"><span class="ni-ico">📅</span>Voir Planning<?php if($hasPlanning):?><span class="ni-badge ok">✓</span><?php endif?></a>
    <a class="ni <?=$action==='conflits'?'active':''?>" href="?action=conflits"><span class="ni-ico">⚠️</span>Conflits</a>
    <div class="nt" style="margin-top:6px">Données</div>
    <a class="ni" href="formulaires.php"><span class="ni-ico">📝</span>Saisie Données</a>
    <a class="ni" href="telecharger.php"><span class="ni-ico">⬇️</span>Télécharger<?php if($hasPlanning):?><span class="ni-badge">↓</span><?php endif?></a>
    <a class="ni" href="gestion_utilisateurs.php"><span class="ni-ico">👥</span>Utilisateurs</a>
    <div class="nt" style="margin-top:6px">Infos</div>
    <?php if($salles):?><a class="ni" href="#salles"><span class="ni-ico">🏢</span>Salles<span class="ni-badge"><?=count($salles)?></span></a><?php endif?>
    <?php if($promotions):?><a class="ni" href="#promos"><span class="ni-ico">🎓</span>Promotions<span class="ni-badge"><?=count($promotions)?></span></a><?php endif?>
    <?php if($cours):?><a class="ni" href="#cours-s"><span class="ni-ico">📚</span>Cours<span class="ni-badge"><?=count($cours)?></span></a><?php endif?>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">
      <div class="sb-av">👤</div>
      <div class="sb-ui"><p><?=htmlspecialchars($userName)?></p><span><?=$methode==='face'?'👤 Visage':($methode==='fingerprint'?'👆 Empreinte':'🔐 Auth')?></span></div>
    </div>
    <a href="deconnexion.php" class="sb-logout">🚪 Déconnexion</a>
  </div>
</aside>

<main class="main">
  <?php
  $titles=['dashboard'=>'Tableau de Bord','generer'=>'Générer le Planning','afficher'=>'Planning Hebdomadaire','conflits'=>'Analyse des Conflits'];
  $subs=['dashboard'=>'Vue d\'ensemble du SGA','generer'=>'Génération automatique sans conflit','afficher'=>'Visualisation du planning actuel','conflits'=>'Détection et analyse des conflits'];
  ?>
  <div class="topbar">
    <div class="tb-title"><h1><?=$titles[$action]??'SGA'?></h1><p><?=$subs[$action]??''?></p></div>
    <div class="tb-chips">
      <?php if($hasPlanning):?><span class="chip chip-ok">📅 Planning actif</span><?php endif?>
      <span class="chip chip-info">📆 <?=date('d/m/Y')?></span>
    </div>
  </div>

  <div class="content">
    <?php if(!empty($messages)):?><div class="alert al-ok"><span>✅</span><div><strong>Succès :</strong> <?=implode(' · ',$messages)?></div></div><?php endif?>
    <?php if(!empty($erreurs)):?><div class="alert al-err"><span>⚠️</span><div><?=implode('<br>',$erreurs)?></div></div><?php endif?>

    <?php if($action==='dashboard'): ?>
    <div class="hero">
      <div>
        <h2>Bienvenue, <?=htmlspecialchars($userName)?> 👋</h2>
        <p>Le Système de Gestion des Auditoires planifie automatiquement les créneaux horaires hebdomadaires pour les 4 promotions de la FCI, sans collision ni dépassement de capacité.</p>
        <div class="actions" style="margin-top:16px;margin-bottom:0">
          <a href="?action=generer" class="btn btn-pri">🚀 Générer le Planning</a>
          <?php if($hasPlanning):?><a href="?action=afficher" class="btn btn-ghost">📅 Voir le Planning</a><?php endif?>
          <a href="formulaires.php" class="btn btn-ghost">📝 Saisir les Données</a>
        </div>
      </div>
      <div class="hero-art">🏫</div>
    </div>

    <div class="stats">
      <div class="sc" style="--c1:#4f46e5;--c2:#7c3aed"><div class="sc-ico">🏢</div><div class="sc-num"><?=$salles?count($salles):'–'?></div><div class="sc-lbl">Salles</div></div>
      <div class="sc" style="--c1:#10b981;--c2:#059669"><div class="sc-ico">🎓</div><div class="sc-num"><?=$promotions?count($promotions):'–'?></div><div class="sc-lbl">Promotions</div></div>
      <div class="sc" style="--c1:#3b82f6;--c2:#6366f1"><div class="sc-ico">📚</div><div class="sc-num"><?=$cours?count($cours):'–'?></div><div class="sc-lbl">Cours</div></div>
      <div class="sc" style="--c1:#f59e0b;--c2:#ef4444"><div class="sc-ico">📅</div><div class="sc-num"><?=$hasPlanning?'✓':'–'?></div><div class="sc-lbl">Planning</div></div>
    </div>

    <?php if($salles):?><div class="gcard" id="salles"><div class="gc-h"><h2>🏢 Salles Disponibles</h2><span class="chip chip-info"><?=count($salles)?> salles</span></div><div class="gc-b"><div class="dlist"><?php foreach($salles as $s):?><div class="di"><div class="di-id"><?=htmlspecialchars($s['id'])?></div><div class="di-lbl"><?=htmlspecialchars($s['designation'])?></div><div class="di-sub"><?=$s['capacite']?> places</div><div class="prog-bar"><div class="prog-fill" style="width:<?=min(100,($s['capacite']/300)*100)?>%"></div></div></div><?php endforeach?></div></div></div><?php endif?>

    <?php if($promotions):?><div class="gcard" id="promos"><div class="gc-h"><h2>🎓 Promotions</h2><span class="chip chip-ok"><?=count($promotions)?></span></div><div class="gc-b"><div class="dlist"><?php foreach($promotions as $p):?><div class="di"><div class="di-id"><?=htmlspecialchars($p['id'])?></div><div class="di-lbl"><?=htmlspecialchars($p['libelle'])?></div><div class="di-sub"><?=$p['effectif']?> étudiants</div></div><?php endforeach?></div></div></div><?php endif?>

    <?php if($cours):?><div class="gcard" id="cours-s"><div class="gc-h"><h2>📚 Cours</h2><span class="chip chip-info"><?=count($cours)?></span></div><div class="gc-b"><div class="tbl-wrap"><table class="tbl"><thead><tr><th>ID</th><th>Intitulé</th><th>Type</th><th>Promo</th><th>Heures</th></tr></thead><tbody><?php foreach($cours as $c):?><tr><td><?=htmlspecialchars($c['id'])?></td><td style="text-align:left"><?=htmlspecialchars($c['intitule'])?></td><td><span class="badge <?=$c['type']==='option'?'b-opt':'b-tc'?>"><?=$c['type']==='option'?'Option':'TC'?></span></td><td><?=$c['promotion']?></td><td><?=$c['volume_horaire']?>h</td></tr><?php endforeach?></tbody></table></div></div></div><?php endif?>

    <?php elseif($action==='generer'):?>
    <div class="alert al-info"><span>ℹ️</span><div>La génération effectue 6 étapes : chargement des données, création des créneaux, algorithme best-fit, sauvegarde TXT/JSON, rapport d'occupation, détection des conflits.</div></div>
    <div class="gcard"><div class="gc-h"><h2>🚀 Générer le Planning</h2></div><div class="gc-b">
      <p style="color:var(--muted);font-size:13px;margin-bottom:16px">Génération automatique sans conflit pour toutes les promotions (L1–L4), cours tronc commun et options.</p>
      <div class="actions">
        <a href="?action=generer" class="btn btn-pri">🚀 Lancer la Génération</a>
        <?php if($hasPlanning):?><a href="?action=afficher" class="btn btn-ok">📅 Voir le Planning</a><a href="?action=conflits" class="btn btn-warn">⚠️ Vérifier Conflits</a><?php endif?>
        <a href="telecharger.php" class="btn btn-ghost">⬇️ Télécharger</a>
      </div>
    </div></div>

    <?php elseif($action==='afficher'&&$planning):?>
    <div class="gcard"><div class="gc-h"><h2>📅 Planning Hebdomadaire</h2><div style="display:flex;gap:7px"><a href="?action=conflits" class="btn btn-warn" style="padding:6px 12px;font-size:11.5px">⚠️ Conflits</a><a href="telecharger.php" class="btn btn-ghost" style="padding:6px 12px;font-size:11.5px">⬇️ Export</a></div></div>
    <div style="padding:0"><div class="tbl-wrap" style="padding:0"><?php echo ($salles&&$cours)?afficher_planning_html($planning,$salles,$cours):afficher_planning_html($planning)?></div></div></div>
    <div class="actions"><a href="output/planning.txt" download class="btn btn-ghost">📄 planning.txt</a><a href="output/planning.json" download class="btn btn-ghost">📦 planning.json</a><a href="output/rapport_occupation.txt" download class="btn btn-ghost">📊 rapport.txt</a></div>

    <?php elseif($action==='conflits'):?>
    <?php if(!empty($conflits)):?>
    <div class="alert al-err"><span>⚠️</span><div><?=count($conflits)?> conflit(s) détecté(s) dans le planning.</div></div>
    <div class="gcard"><div class="gc-h"><h2>⚠️ Conflits Détectés</h2></div><div class="gc-b" style="padding:0">
      <table class="ctbl"><thead><tr><th>Type</th><th>Jour</th><th>Horaire</th><th>Détails</th></tr></thead><tbody>
      <?php foreach($conflits as $c):?><tr><td><?=$c['type']==='conflit_salle'?'🏢 Salle':'👥 Groupe'?></td><td><?=$c['jour']?></td><td><?=$c['heure_debut']?>h–<?=$c['heure_fin']?>h</td>
      <td><?php if($c['type']==='conflit_salle'):?>Salle <strong><?=$c['salle']?></strong> : <?=$c['cours1']?> / <?=$c['cours2']?><?php else:?>Groupe <strong><?=$c['groupe']?></strong> : <?=$c['cours1']?> / <?=$c['cours2']?><?php endif?></td></tr>
      <?php endforeach?></tbody></table>
    </div></div>
    <?php elseif(isset($planning)):?><div class="alert al-ok"><span>✅</span><div><strong>Aucun conflit.</strong> Le planning est valide et cohérent.</div></div>
    <?php elseif(!$hasPlanning):?><div class="alert al-info"><span>ℹ️</span><div>Aucun planning disponible. <a href="?action=generer" style="color:#c7d2fe">Générez le planning</a> d'abord.</div></div><?php endif?>
    <?php endif?>
  </div>

  <footer><span>SGA v2.0 · Université Protestante au Congo · FCI 2025–2026</span><span><?=$methode==='face'?'👤 Reconnaissance faciale':($methode==='fingerprint'?'👆 Empreinte digitale':'🔐 Biométrie')?> · <?=htmlspecialchars($userName)?></span></footer>
</main>
<script>document.addEventListener('click',e=>{const s=document.querySelector('.sidebar');if(window.innerWidth<=900&&s.classList.contains('open')&&!s.contains(e.target)&&!e.target.closest('.mob-btn'))s.classList.remove('open');});</script>
</body></html>
