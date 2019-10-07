<?php


define('CID',false);
#define('READ_ONLY_SESSION',true);
require_once('../../include.php');
ModuleManager::load_modules();


$userID = Acl::get_user();
$readedMessage = $_GET['id'];
$resultsReturned = [];

require 'phone.php';
$phone = new Phone();
$type = $phone->getDbType();
$host = $phone->getDbdbHost();
$login = $phone->getDbUser();
$password = $phone->getDbPassword();
$dbName = $phone->getDbDatabase();


$db = new PDO("$type:dbname=$dbName;host=$host", "$login","$password");  

$query = $db->query("SELECT \"readedBy\" FROM inbox WHERE \"ID\" = '$readedMessage' ");
$record = $query->fetch(PDO::FETCH_ASSOC);
$readers = $record['readedBy'];
$readers .= "_".$userID;
$resultsReturned['record'] = $record;
$resultsReturned['query'] = $readers." WHERE $readedMessage";
$exec = $db->prepare("UPDATE inbox SET \"readedBy\" = '$readers' WHERE \"ID\" = '$readedMessage' ");
$exec->execute();
$resultsReturned['status'] = "success";


echo json_encode($resultsReturned);