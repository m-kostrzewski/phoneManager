<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');

class phoneManagerCommon extends ModuleCommon {

    public static function user_settings() {
       return array(__("Phone Manager Settings")=> 'settings');
    }

    public static function menu() {

      if (Base_AclCommon::check_permission('Phone Manager') || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" )
        return array(_M('Phone Manager') => array(
            '__icon__'=>'sms.png','__icon_small__'=>'sms.png'
          ));
		  else
        return array();
    }


    public static function sendSms($number, $message, $creatorID = -1){
      $ch = curl_init();
      if($creatorID === -1){
        $creatorID = CRM_ContactsCommon::get_contact_by_user_id(Base_AclCommon::get_user())['id'];
      }
      $number = str_replace(";",",",$number);
      $numbers=  explode(",",$number);
      foreach($numbers as $number){
        $number = str_replace(" ","",$number);
        curl_setopt($ch, CURLOPT_URL,"http://192.168.11.12:8000/api/send/sms");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "number=$number&message=$message&creator=$creatorID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close ($ch);
      }
    }
  

    public static function autoselect_company_or_contact($str, $crits, $format_callback){
      $str = explode(' ', trim($str));
      foreach ($str as $k=>$v)
          if ($v) {
              $v = "%$v%";
              $crits = Utils_RecordBrowserCommon::merge_crits($crits, array('(~first_name'=>$v,'|~last_name'=>$v, '|~mobile_phone'=>$v , '|~work_phone'=>$v ));
          }
      $recs = Utils_RecordBrowserCommon::get_records('contact', $crits, array(), array('last_name'=>'ASC'), 10);
      $ret = array();
      foreach($recs as $v) {
          $v['type'] = "contact";
          $number = "";
          if(strlen($v['work_phone']) > 0){
            $number = $v['work_phone'];
          }
          else if(strlen($v['mobile_phone'] > 0)){
            $number = $v['mobile_phone'];
          }
          if(strlen($number) > 0){
            $ret[$v['id']."_".$number."__".$number." ".$v['last_name']." ".$v['first_name']] = call_user_func($format_callback, $v, true);
          }
      }

      foreach ($str as $k=>$v)
          if ($v) {
              $v = "%$v%";
              $crits = Utils_RecordBrowserCommon::merge_crits($crits, array('(~company_name'=>$v,'|~phone'=>$v ));
          }
      $recs = Utils_RecordBrowserCommon::get_records('company', $crits, array(), array('company_name'=>'ASC'), 10);

      foreach($recs as $v) {
        $v['type'] = "company";
        if(strlen($v['phone']) > 0 ){
          $ret[$v['id']."_".$v['phone']."__"." ".$v['phone']." - ".$v['company_name']] = call_user_func($format_callback, $v, true);
        }
    }

      return $ret;
    }

  public static function contact_or_company_format($record, $nolink=false){
      if($record['type'] == "company"){
        $ret = "[FIRMA] ". $record['phone']." - ".$record['company_name'];
      }else{
        if(strlen($record['work_phone']) > 0){
            $ret =  "[KONTAKT] ".$record['work_phone'] ." - ". $record['last_name']." ".$record['first_name'];
        }else{
            $ret = "[KONTAKT] ". $record['mobile_phone']." - ". $record['last_name']." ".$record['first_name'];
        }
        
      }
      return $ret;
  }
}