<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
session_start();
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['ok'=>false,'msg'=>'Méthode non autorisée']);exit;}
$body=json_decode(file_get_contents('php://input'),true);
if(!$body||empty($body['credentialId'])){echo json_encode(['ok'=>false,'msg'=>'Données manquantes']);exit;}
if(!isset($_SESSION['webauthn_challenge'])||time()>($_SESSION['webauthn_expires']??0)){echo json_encode(['ok'=>false,'msg'=>'Challenge expiré. Réessayez.']);exit;}
$credentialId=$body['credentialId'];$dir=__DIR__.'/../data/fingerprints_data/';$found=null;$foundFile='';
foreach(glob($dir.'*.json') as $f){
  $stored=json_decode(file_get_contents($f),true);
  if($stored&&$stored['credentialId']===$credentialId){$found=$stored;$foundFile=$f;break;}
}
if(!$found){
  file_put_contents(__DIR__.'/../data/connexions.log',"[".date('Y-m-d H:i:s')."] inconnu | fingerprint | ECHEC\n",FILE_APPEND);
  echo json_encode(['ok'=>false,'msg'=>"Empreinte non reconnue. Veuillez vous inscrire."]);exit;
}
$found['counter']=(int)($body['counter']??$found['counter'])+1;
file_put_contents($foundFile,json_encode($found,JSON_PRETTY_PRINT));
$_SESSION['utilisateur_connecte']=true;$_SESSION['identifiant']=$found['userId'];
$_SESSION['nom_utilisateur']=$found['userName'];$_SESSION['role']=$found['role']??'utilisateur';
$_SESSION['methode_auth']='fingerprint';$_SESSION['derniere_connexion']=time();
unset($_SESSION['webauthn_challenge'],$_SESSION['webauthn_expires']);
file_put_contents(__DIR__.'/../data/connexions.log',"[".date('Y-m-d H:i:s')."] {$found['userId']} | fingerprint | SUCCES\n",FILE_APPEND);
echo json_encode(['ok'=>true,'userId'=>$found['userId'],'userName'=>$found['userName'],'msg'=>"Connexion réussie. Bienvenue, {$found['userName']} !"]);
