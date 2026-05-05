<?php
session_start();
if(!isset($_SESSION['utilisateur_connecte'])||!$_SESSION['utilisateur_connecte']){header('Location: connexion.php');exit;}
require_once 'fonctions.php';
$data_dir=__DIR__.'/data/';$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $type=$_POST['type']??'';
  try{
    if($type==='ajouter_salle'){$s=charger_salles($data_dir.'salles.json');$n=['id'=>trim($_POST['id_salle']),'designation'=>trim($_POST['designation']),'capacite'=>intval($_POST['capacite'])];if(isset($s[$n['id']])){$err="ID existe déjà.";}else{$s[$n['id']]=$n;file_put_contents($data_dir.'salles.json',json_encode(array_values($s),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));$msg="Salle {$n['id']} ajoutée.";}}
    if($type==='ajouter_promotion'){$p=charger_promotions($data_dir.'promotions.json');$n=['id'=>trim($_POST['id_promo']),'libelle'=>trim($_POST['libelle']),'effectif'=>intval($_POST['effectif'])];if(isset($p[$n['id']])){$err="ID existe déjà.";}else{$p[$n['id']]=$n;file_put_contents($data_dir.'promotions.json',json_encode(array_values($p),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));$msg="Promotion {$n['id']} ajoutée.";}}
    if($type==='ajouter_cours'){$c=charger_cours($data_dir.'cours.json');$n=['id'=>trim($_POST['id_cours']),'intitule'=>trim($_POST['intitule']),'volume_horaire'=>intval($_POST['volume_horaire']),'type'=>$_POST['type_cours'],'promotion'=>$_POST['promotion']];if(isset($c[$n['id']])){$err="ID existe déjà.";}else{$c[$n['id']]=$n;file_put_contents($data_dir.'cours.json',json_encode(array_values($c),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));$msg="Cours {$n['intitule']} ajouté.";}}
    if($type==='ajouter_option'){$o=charger_options($data_dir.'options.json');$n=['id'=>trim($_POST['id_option']),'libelle'=>trim($_POST['libelle_option']),'promotion_parent'=>$_POST['promotion_parent'],'effectif'=>intval($_POST['effectif_option'])];if(isset($o[$n['id']])){$err="ID existe déjà.";}else{$o[$n['id']]=$n;file_put_contents($data_dir.'options.json',json_encode(array_values($o),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));$msg="Option {$n['libelle']} ajoutée.";}}
  }catch(Exception $e){$err=$e->getMessage();}
}
$salles=[];$promotions=[];$cours=[];$options=[];
try{$salles=charger_salles($data_dir.'salles.json');$promotions=charger_promotions($data_dir.'promotions.json');$cours=charger_cours($data_dir.'cours.json');$options=charger_options($data_dir.'options.json');}catch(Exception $e){}
$un=$_SESSION['nom_utilisateur']??'Utilisateur';
?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Saisie Données — SGA</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--pri:#4f46e5;--acc:#7c3aed;--ok:#10b981;--err:#ef4444;--bg:#0d0c1d;--sb:rgba(8,7,20,.98);--card:rgba(15,14,30,.93);--bdr:rgba(255,255,255,.08);--bdr2:rgba(255,255,255,.14);--txt:#e2e8f0;--muted:#94a3b8;--sw:220px}
html,body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);min-height:100vh}
.bg-g{position:fixed;inset:0;z-index:0;pointer-events:none;background:radial-gradient(ellipse 55% 45% at 3% 14%,rgba(79,70,229,.28) 0%,transparent 60%),radial-gradient(ellipse 35% 30% at 97% 82%,rgba(124,58,237,.17) 0%,transparent 55%)}
.sidebar{position:fixed;left:0;top:0;bottom:0;width:var(--sw);background:var(--sb);border-right:1px solid var(--bdr);z-index:100;display:flex;flex-direction:column}
.sb-top{padding:18px 14px 14px;border-bottom:1px solid var(--bdr)}
.sb-brand{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.sb-ic{width:40px;height:40px;background:linear-gradient(135deg,var(--pri),var(--acc));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:#fff;box-shadow:0 5px 18px rgba(79,70,229,.42)}
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
.chip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:18px;font-size:10px;font-weight:700;background:rgba(79,70,229,.13);border:1px solid rgba(79,70,229,.26);color:#a5b4fc}
.content{padding:22px 24px}
.tabs-row{display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap}
.tb{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:1.5px solid var(--bdr2);background:rgba(255,255,255,.04);color:var(--muted);transition:all .2s;display:flex;align-items:center;gap:6px}
.tb:hover{background:rgba(255,255,255,.08);color:var(--txt)}.tb.active{background:rgba(79,70,229,.17);border-color:rgba(79,70,229,.48);color:#a5b4fc}
.tp{display:none}.tp.active{display:grid;grid-template-columns:340px 1fr;gap:18px;align-items:start}
.gcard{background:var(--card);border:1px solid var(--bdr);border-radius:14px;overflow:hidden}
.gc-h{padding:13px 18px 11px;border-bottom:1px solid var(--bdr);background:linear-gradient(155deg,rgba(79,70,229,.09),rgba(124,58,237,.04))}
.gc-h h2{font-size:13.5px;font-weight:800}.gc-b{padding:18px}
.fg{margin-bottom:12px}.lbl{display:block;font-size:10px;font-weight:800;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.iw{position:relative}.iico{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none}
.fc{width:100%;padding:9px 10px 9px 33px;background:rgba(255,255,255,.05);border:1.5px solid var(--bdr);border-radius:8px;color:var(--txt);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s}
.fc::placeholder{color:rgba(148,163,184,.32)}.fc:focus{border-color:var(--pri);background:rgba(79,70,229,.06);box-shadow:0 0 0 3px rgba(79,70,229,.14)}
select.fc{padding-left:10px}
.btn-s{width:100%;padding:11px;background:linear-gradient(135deg,var(--pri),var(--acc));color:#fff;border:none;border-radius:9px;font-size:13.5px;font-weight:700;cursor:pointer;transition:transform .12s,box-shadow .12s;box-shadow:0 4px 14px rgba(79,70,229,.38);margin-top:2px}
.btn-s:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(79,70,229,.50)}
.alert{display:flex;gap:9px;padding:11px 14px;border-radius:10px;margin-bottom:14px;font-size:12.5px;line-height:1.5}
.al-ok{background:rgba(16,185,129,.09);border:1px solid rgba(16,185,129,.22);color:#6ee7b7}
.al-err{background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.22);color:#fca5a5}
.tbl{width:100%;border-collapse:collapse;font-size:11.5px}
.tbl th{background:rgba(79,70,229,.12);color:#a5b4fc;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:8px 11px;border:1px solid var(--bdr);text-align:left}
.tbl td{padding:7px 11px;border:1px solid var(--bdr);vertical-align:middle}
.tbl tr:nth-child(even) td{background:rgba(255,255,255,.016)}
.bdg{display:inline-flex;padding:2px 7px;border-radius:10px;font-size:9.5px;font-weight:800}
.b-tc{background:rgba(79,70,229,.14);color:#a5b4fc;border:1px solid rgba(79,70,229,.22)}
.b-o{background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.22)}
footer{padding:12px 24px;border-top:1px solid var(--bdr);background:rgba(8,7,20,.5);font-size:10.5px;color:var(--muted)}
@media(max-width:860px){.main{margin-left:0}.sidebar{display:none}.tp.active{grid-template-columns:1fr}}
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
    <a class="ni active" href="formulaires.php"><span class="ni-i">📝</span>Saisie Données</a>
    <a class="ni" href="telecharger.php"><span class="ni-i">⬇️</span>Télécharger</a>
    <a class="ni" href="gestion_utilisateurs.php"><span class="ni-i">👥</span>Utilisateurs</a>
  </nav>
  <div class="sb-foot"><a href="deconnexion.php" class="sb-out">🚪 Déconnexion</a></div>
</aside>
<main class="main">
  <div class="topbar"><div><h1>📝 Saisie des Données</h1><p>Gérez salles, promotions, cours et options</p></div><span class="chip">👤 <?=htmlspecialchars($un)?></span></div>
  <div class="content">
    <?php if($msg):?><div class="alert al-ok"><span>✅</span><div><?=htmlspecialchars($msg)?></div></div><?php endif?>
    <?php if($err):?><div class="alert al-err"><span>❌</span><div><?=htmlspecialchars($err)?></div></div><?php endif?>
    <div class="tabs-row">
      <button class="tb active" onclick="showT('salles',this)">🏢 Salles (<?=count($salles)?>)</button>
      <button class="tb" onclick="showT('promos',this)">🎓 Promotions (<?=count($promotions)?>)</button>
      <button class="tb" onclick="showT('cours',this)">📚 Cours (<?=count($cours)?>)</button>
      <button class="tb" onclick="showT('opts',this)">🎯 Options (<?=count($options)?>)</button>
    </div>
    <!-- SALLES -->
    <div class="tp active" id="t-salles">
      <div class="gcard"><div class="gc-h"><h2>🏢 Ajouter une Salle</h2></div><div class="gc-b"><form method="POST"><input type="hidden" name="type" value="ajouter_salle">
        <div class="fg"><label class="lbl">Identifiant</label><div class="iw"><span class="iico">🔖</span><input class="fc" type="text" name="id_salle" placeholder="ex: AUD-L5" required></div></div>
        <div class="fg"><label class="lbl">Désignation</label><div class="iw"><span class="iico">🏢</span><input class="fc" type="text" name="designation" placeholder="ex: Auditoire Licence 5" required></div></div>
        <div class="fg"><label class="lbl">Capacité</label><div class="iw"><span class="iico">👥</span><input class="fc" type="number" name="capacite" placeholder="ex: 120" min="1" required></div></div>
        <button class="btn-s" type="submit">Ajouter la Salle</button>
      </form></div></div>
      <div class="gcard"><div class="gc-h"><h2>📋 Salles Actuelles</h2></div><div style="overflow-x:auto"><table class="tbl"><thead><tr><th>ID</th><th>Désignation</th><th>Capacité</th></tr></thead><tbody>
        <?php foreach($salles as $s):?><tr><td><?=htmlspecialchars($s['id'])?></td><td><?=htmlspecialchars($s['designation'])?></td><td><?=$s['capacite']?> places</td></tr><?php endforeach?>
      </tbody></table></div></div>
    </div>
    <!-- PROMOTIONS -->
    <div class="tp" id="t-promos">
      <div class="gcard"><div class="gc-h"><h2>🎓 Ajouter une Promotion</h2></div><div class="gc-b"><form method="POST"><input type="hidden" name="type" value="ajouter_promotion">
        <div class="fg"><label class="lbl">Identifiant</label><div class="iw"><span class="iico">🔖</span><input class="fc" type="text" name="id_promo" placeholder="ex: L5" required></div></div>
        <div class="fg"><label class="lbl">Libellé</label><div class="iw"><span class="iico">🎓</span><input class="fc" type="text" name="libelle" placeholder="ex: Licence 5" required></div></div>
        <div class="fg"><label class="lbl">Effectif</label><div class="iw"><span class="iico">👥</span><input class="fc" type="number" name="effectif" placeholder="ex: 80" min="1" required></div></div>
        <button class="btn-s" type="submit">Ajouter la Promotion</button>
      </form></div></div>
      <div class="gcard"><div class="gc-h"><h2>📋 Promotions Actuelles</h2></div><div style="overflow-x:auto"><table class="tbl"><thead><tr><th>ID</th><th>Libellé</th><th>Effectif</th></tr></thead><tbody>
        <?php foreach($promotions as $p):?><tr><td><?=htmlspecialchars($p['id'])?></td><td><?=htmlspecialchars($p['libelle'])?></td><td><?=$p['effectif']?> étudiants</td></tr><?php endforeach?>
      </tbody></table></div></div>
    </div>
    <!-- COURS -->
    <div class="tp" id="t-cours">
      <div class="gcard"><div class="gc-h"><h2>📚 Ajouter un Cours</h2></div><div class="gc-b"><form method="POST"><input type="hidden" name="type" value="ajouter_cours">
        <div class="fg"><label class="lbl">Identifiant</label><div class="iw"><span class="iico">🔖</span><input class="fc" type="text" name="id_cours" placeholder="ex: PHP-L2" required></div></div>
        <div class="fg"><label class="lbl">Intitulé</label><div class="iw"><span class="iico">📚</span><input class="fc" type="text" name="intitule" placeholder="ex: Programmation Web PHP" required></div></div>
        <div class="fg"><label class="lbl">Volume horaire (h)</label><div class="iw"><span class="iico">⏱</span><input class="fc" type="number" name="volume_horaire" placeholder="4" min="1" required></div></div>
        <div class="fg"><label class="lbl">Type</label><select class="fc" name="type_cours" required><option value="">— Sélectionnez —</option><option value="tronc_commun">Tronc Commun</option><option value="option">Option</option></select></div>
        <div class="fg"><label class="lbl">Promotion</label><select class="fc" name="promotion" required><option value="">— Sélectionnez —</option><option>L1</option><option>L2</option><option>L3</option><option>L4</option></select></div>
        <button class="btn-s" type="submit">Ajouter le Cours</button>
      </form></div></div>
      <div class="gcard"><div class="gc-h"><h2>📋 Cours Actuels</h2></div><div style="overflow-x:auto"><table class="tbl"><thead><tr><th>ID</th><th>Intitulé</th><th>Type</th><th>Promo</th><th>H</th></tr></thead><tbody>
        <?php foreach($cours as $c):?><tr><td><?=htmlspecialchars($c['id'])?></td><td><?=htmlspecialchars($c['intitule'])?></td><td><span class="bdg <?=$c['type']==='option'?'b-o':'b-tc'?>"><?=$c['type']==='option'?'Opt':'TC'?></span></td><td><?=$c['promotion']?></td><td><?=$c['volume_horaire']?>h</td></tr><?php endforeach?>
      </tbody></table></div></div>
    </div>
    <!-- OPTIONS -->
    <div class="tp" id="t-opts">
      <div class="gcard"><div class="gc-h"><h2>🎯 Ajouter une Option</h2></div><div class="gc-b"><form method="POST"><input type="hidden" name="type" value="ajouter_option">
        <div class="fg"><label class="lbl">Identifiant</label><div class="iw"><span class="iico">🔖</span><input class="fc" type="text" name="id_option" placeholder="ex: OPT-SECU" required></div></div>
        <div class="fg"><label class="lbl">Libellé</label><div class="iw"><span class="iico">🎯</span><input class="fc" type="text" name="libelle_option" placeholder="ex: Sécurité Informatique" required></div></div>
        <div class="fg"><label class="lbl">Promotion parente</label><select class="fc" name="promotion_parent" required><option value="">— Sélectionnez —</option><option>L3</option><option>L4</option></select></div>
        <div class="fg"><label class="lbl">Effectif du groupe</label><div class="iw"><span class="iico">👥</span><input class="fc" type="number" name="effectif_option" placeholder="ex: 25" min="1" required></div></div>
        <button class="btn-s" type="submit">Ajouter l'Option</button>
      </form></div></div>
      <div class="gcard"><div class="gc-h"><h2>📋 Options Actuelles</h2></div><div style="overflow-x:auto"><table class="tbl"><thead><tr><th>ID</th><th>Libellé</th><th>Promo</th><th>Effectif</th></tr></thead><tbody>
        <?php foreach($options as $o):?><tr><td><?=htmlspecialchars($o['id'])?></td><td><?=htmlspecialchars($o['libelle'])?></td><td><?=$o['promotion_parent']?></td><td><?=$o['effectif']?> étudiants</td></tr><?php endforeach?>
      </tbody></table></div></div>
    </div>
  </div>
  <footer>SGA v2.0 · UPC · FCI 2025–2026</footer>
</main>
<script>function showT(n,b){document.querySelectorAll('.tp').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.tb').forEach(x=>x.classList.remove('active'));document.getElementById('t-'+n).classList.add('active');b.classList.add('active');}</script>
</body></html>
