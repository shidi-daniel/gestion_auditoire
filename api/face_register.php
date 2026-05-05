<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
if ($_SERVER['REQUEST_METHOD']==='OPTIONS'){http_response_code(200);exit;}
if ($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['ok'=>false,'msg'=>'Méthode non autorisée']);exit;}
$body=json_decode(file_get_contents('php://input'),true);
if(!$body||empty($body['userId'])||empty($body['descriptors'])){echo json_encode(['ok'=>false,'msg'=>'Données manquantes']);exit;}
$userId=preg_replace('/[^a-zA-Z0-9_\-]/','', $body['userId']);
$userName=htmlspecialchars($body['userName']??$userId,ENT_QUOTES);
$descriptors=$body['descriptors'];
$dir=__DIR__.'/../data/faces_data/';
if(!is_dir($dir))mkdir($dir,0755,true);
function euclidean_dist($a,$b){$s=0;foreach($a as $i=>$v)$s+=($v-$b[$i])**2;return sqrt($s);}
$newAvg=array_map(fn($i)=>array_sum(array_column($descriptors,$i))/count($descriptors),range(0,127));
foreach(glob($dir.'*.json') as $f){
  $stored=json_decode(file_get_contents($f),true);
  if(!$stored)continue;
  if(euclidean_dist($newAvg,$stored['descriptor_avg'])<0.5){
    echo json_encode(['ok'=>false,'duplicate'=>true,'msg'=>"Ce visage est déjà enregistré (appartient à : {$stored['userName']}). Utilisez un autre visage."]);exit;
  }
}
$record=['userId'=>$userId,'userName'=>$userName,'descriptor_avg'=>$newAvg,'descriptors'=>$descriptors,'role'=>'utilisateur','createdAt'=>date('Y-m-d H:i:s')];
file_put_contents($dir.$userId.'.json',json_encode($record,JSON_PRETTY_PRINT));
echo json_encode(['ok'=>true,'msg'=>"Visage enregistré avec succès pour $userName."]);
