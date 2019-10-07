<?php

require_once('data/PhoneManagerConfig.php');
class Phone {

    /**
     *
     * @param String $msg text of sms 
     * @param Bool $deliveryReport check for delivery values (default, yes, no)
     * @param String $destinationNumber who will recive sms
     * @param String $creatorID who create sms (In Epesi id logged in user)
     * 
     */

    public $dbType = SMS_DATABASETYPE;        //mysql pgsql sqlite3 
    public $dbUser = SMS_USER;        //username
    public $dbPassword = SMS_PASSWORD;    //password
    public $dbDatabase = SMS_DATABASE;    //database name
    public $dbHost = SMS_HOST;        //host
    
    public function getDbType(){
        return $this->dbType;
    }

    public function getDbUser(){
        return $this->dbUser;
    }
    
    public function getDbPassword(){
        return $this->dbPassword;
    }

    public function getDbDatabase(){
        return $this->dbDatabase;
    }

    public function getDbdbHost(){
        return $this->dbHost;
    }


    public function sendSMS($msg,$destinationNumber,$deliveryReport = 'yes',$creatorID = 0)
    {

        $db = new PDO("$this->dbType:dbname=$this->dbDatabase;host=$this->dbHost", $this->dbUser,$this->dbPassword);  
        if(strlen($msg) > 150){
            //multipart msg
            $msgParts = str_split($msg,150);
            $text = $msgParts[0];
            $lastID = $db->query("SELECT \"ID\" FROM outbox ORDER BY \"ID\" DESC LIMIT 1");
            $lastID = $lastID->fetch();
            if(!$lastID['ID']){
                $lastID = $db->query("SELECT \"ID\" FROM sentitems ORDER BY \"ID\" DESC LIMIT 1");
                $lastID = $lastID->fetch();
            }
            $udhBase = "050003";
            $id = "";
            if($lastID['ID']){
                $udhHex = $lastID['ID'] + 1;
                $id = $lastID['ID'] + 1;
            }else{
                $udhHex = 1;
                $id = 1;
            }
            if(($udhHex % 255) == 0){
                $stack = $udhHex / 255;
                $records = $db->query("SELECT * FROM sentitems WHERE \"UDH\" NOT LIKE '%_archive_%'", PDO::FETCH_ASSOC);
                foreach($records as $record){
                    $oldUDH = $record['UDH'];
                    $updatedUDH = $stack."_archive_".$oldUDH;
                    $recordUpdate = $db->prepare("UPDATE sentitems SET \"UDH\" = '$updatedUDH' WHERE \"UDH\" = '$oldUDH' ");
                    $recordUpdate->execute();
                }
            }
            $udhHex = dechex($udhHex);
            $udhHex = str_pad($udhHex, 2, '0', STR_PAD_LEFT);
            $udhMax = count($msgParts);
            $udhMax = str_pad($udhMax, 2, '0', STR_PAD_LEFT);
            $udhPart = $udhBase.$udhHex.$udhMax."01";
            $q = $db->prepare("INSERT INTO outbox (\"DestinationNumber\",\"TextDecoded\",\"CreatorID\", \"SenderID\", \"DeliveryReport\",\"MultiPart\",\"UDH\" ) 
            VALUES ('$destinationNumber','$text','$creatorID','', 'default', 'true', '$udhPart')");
            $q->execute();
            for($i = 1;$i<count($msgParts);$i++){
                $value = $msgParts[$i];
                $msgPart = "";
                $sequence = $i + 1;
                $udhIndex = str_pad($sequence, 2, '0', STR_PAD_LEFT);
                $UdhPart = $udhBase.$udhHex.$udhMax.$udhIndex;
                $query = $db->prepare("INSERT INTO outbox_multipart (\"TextDecoded\", \"UDH\" , \"ID\" , \"SequencePosition\") 
                VALUES ('$value' , '$UdhPart', '$id', '$sequence')");
                $query->execute();
            }
        }else{
            //single sms 
             $db->query("INSERT INTO outbox (\"DestinationNumber\",\"TextDecoded\",\"CreatorID\", \"SenderID\", \"DeliveryReport\") VALUES ('$destinationNumber','$msg','$creatorID','', '$deliveryReport')");
        }
    }

}