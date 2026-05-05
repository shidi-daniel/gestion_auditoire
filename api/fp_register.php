<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
session_start();
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['ok'=>false,'msg'=>'Méthode non autorisée']);exit;}
$body=json_decode(file_get_contents('php://input'),true);
if(!$body||empty($body['userId'])||empty($body['credentialId'])){echo json_encode(['ok'=>false,'msg'=>'Données manquantes']);exit;}
if(!isset($_SESSION['webauthn_challenge'])||time()>($_SESSION['webauthn_expires']??0)){echo json_encode(['ok'=>false,'msg'=>'Challenge expiré. Réessayez.']);exit;}
$userId=preg_replace('/[^a-zA-Z0-9_\-]/','', $body['userId']);
$userName=htmlspecialchars($body['userName']??$userId,ENT_QUOTES);
$credentialId=$body['credentialId'];
$dir=__DIR__.'/../data/fingerprints_data/';
if(!is_dir($dir))mkdir($dir,0755,true);
foreach(glob($dir.'*.json') as $f){
  $stored=json_decode(file_get_contents($f),true);
  if($stored&&$stored['credentialId']===$credentialId){echo json_encode(['ok'=>false,'duplicate'=>true,'msg'=>"Cette empreinte est déjà enregistrée. Utilisez un autre doigt."]);exit;}
}
$record=['userId'=>$userId,'userName'=>$userName,'credentialId'=>$credentialId,'publicKey'=>$body['publicKey']??'','counter'=>(int)($body['counter']??0),'createdAt'=>date('Y-m-d H:i:s')];
file_put_contents($dir.$userId.'_'.substr(md5($credentialId),0,8).'.json',json_encode($record,JSON_PRETTY_PRINT));
unset($_SESSION['webauthn_challenge'],$_SESSION['webauthn_expires']);
echo json_encode(['ok'=>true,'msg'=>"Empreinte enregistrée avec succès pour $userName."]);
