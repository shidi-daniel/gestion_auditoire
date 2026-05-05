<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
if ($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['ok'=>false,'msg'=>'Méthode non autorisée']);exit;}
$body=json_decode(file_get_contents('php://input'),true);
if(!$body||empty($body['descriptor'])){echo json_encode(['ok'=>false,'msg'=>'Données manquantes']);exit;}
$desc=$body['descriptor'];$dir=__DIR__.'/../data/faces_data/';$threshold=0.5;
function euclidean_dist($a,$b){$s=0;foreach($a as $i=>$v)$s+=($v-$b[$i])**2;return sqrt($s);}
$bestDist=PHP_FLOAT_MAX;$bestRecord=null;
foreach(glob($dir.'*.json') as $f){
  $stored=json_decode(file_get_contents($f),true);
  if(!$stored||empty($stored['descriptor_avg']))continue;
  $dist=euclidean_dist($desc,$stored['descriptor_avg']);
  if($dist<$bestDist){$bestDist=$dist;$bestRecord=$stored;}
}
if($bestRecord&&$bestDist<$threshold){
  $_SESSION['utilisateur_connecte']=true;$_SESSION['identifiant']=$bestRecord['userId'];
  $_SESSION['nom_utilisateur']=$bestRecord['userName'];$_SESSION['role']=$bestRecord['role']??'utilisateur';
  $_SESSION['methode_auth']='face';$_SESSION['derniere_connexion']=time();
  file_put_contents(__DIR__.'/../data/connexions.log',"[".date('Y-m-d H:i:s')."] {$bestRecord['userId']} | face | SUCCES\n",FILE_APPEND);
  echo json_encode(['ok'=>true,'userId'=>$bestRecord['userId'],'userName'=>$bestRecord['userName'],'msg'=>"Connexion réussie. Bienvenue, {$bestRecord['userName']} !"]);
}else{
  file_put_contents(__DIR__.'/../data/connexions.log',"[".date('Y-m-d H:i:s')."] inconnu | face | ECHEC\n",FILE_APPEND);
  echo json_encode(['ok'=>false,'msg'=>"Visage non reconnu. Veuillez vous inscrire.",'dist'=>round($bestDist,4)]);
}
