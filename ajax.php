<?php


define('CID',false);
define('READ_ONLY_SESSION',true);
require_once('../../include.php');
ModuleManager::load_modules();


$userID = CRM_ContactsCommon::get_contact_by_user_id(Base_AclCommon::get_user ())['id'];
$readedMessage = $_GET['id'];
$resultsReturned = [];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://192.168.11.12:8000/api/read/sms/$readedMessage/$userID");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, "/$readedMessage/$userID");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$server_output = curl_exec($ch);
curl_close ($ch);

$resultsReturned['status'] = "success";


echo json_encode($resultsReturned);