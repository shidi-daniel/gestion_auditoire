<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
session_start();
if(!isset($_SESSION['utilisateur_connecte'])||!$_SESSION['utilisateur_connecte']){echo json_encode(['ok'=>false,'msg'=>'Non autorisé']);exit;}
$body=json_decode(file_get_contents('php://input'),true);
$userId=preg_replace('/[^a-zA-Z0-9_\-]/','', $body['userId']??'');
$type=$body['type']??'all';
if($_SESSION['role']!=='admin'&&$_SESSION['identifiant']!==$userId){echo json_encode(['ok'=>false,'msg'=>'Permission refusée']);exit;}
$deleted=0;
if($type==='face'||$type==='all'){$f=__DIR__.'/../data/faces_data/'.$userId.'.json';if(file_exists($f)){unlink($f);$deleted++;}}
if($type==='fingerprint'||$type==='all'){foreach(glob(__DIR__.'/../data/fingerprints_data/'.$userId.'_*.json') as $f){unlink($f);$deleted++;}}
echo json_encode(['ok'=>true,'deleted'=>$deleted,'msg'=>"$deleted fichier(s) supprimé(s)."]);
