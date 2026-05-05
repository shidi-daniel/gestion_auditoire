<?php
session_start();
$n=$_SESSION['nom_utilisateur']??'';
session_destroy();
?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<meta http-equiv="refresh" content="3;url=connexion.php">
<title>Déconnexion — SGA</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#0d0c1d;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;background-image:radial-gradient(ellipse 70% 55% at 20% 30%,rgba(79,70,229,.28) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 80% 70%,rgba(124,58,237,.18) 0%,transparent 55%)}
.card{background:rgba(15,14,30,.92);border:1px solid rgba(255,255,255,.10);border-radius:22px;padding:44px 36px;text-align:center;max-width:360px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.6);backdrop-filter:blur(24px);animation:up .5s cubic-bezier(.16,1,.3,1)}
@keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.icon{font-size:54px;margin-bottom:16px}
h1{font-size:20px;font-weight:700;margin-bottom:6px}
p{font-size:13.5px;color:#94a3b8;margin-bottom:22px}
.prog{height:3px;background:rgba(255,255,255,.08);border-radius:2px;overflow:hidden;margin-bottom:20px}
.prog-fill{height:100%;background:linear-gradient(90deg,#4f46e5,#7c3aed);animation:prog 3s linear forwards}
@keyframes prog{from{width:0}to{width:100%}}
a{display:inline-flex;align-items:center;gap:7px;padding:11px 22px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-size:13.5px;font-weight:700;box-shadow:0 4px 16px rgba(79,70,229,.4)}
</style></head><body>
<div class="card">
  <div class="icon">👋</div>
  <h1>Au revoir<?php if($n): ?>, <?=htmlspecialchars($n)?><?php endif ?> !</h1>
  <p>Vous avez été déconnecté du Système de Gestion des Auditoires.<br>Redirection automatique…</p>
  <div class="prog"><div class="prog-fill"></div></div>
  <a href="connexion.php">🔐 Se reconnecter</a>
</div></body></html>
