<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
session_start();
$challenge=base64_encode(random_bytes(32));
$_SESSION['webauthn_challenge']=$challenge;$_SESSION['webauthn_expires']=time()+120;
echo json_encode(['challenge'=>$challenge,'rpId'=>$_SERVER['HTTP_HOST']??'localhost','rpName'=>'SGA — UPC','timeout'=>60000]);
