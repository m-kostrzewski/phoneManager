<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');


class phoneManager extends Module {

    public function settings(){

    }

    public function getDataCurl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function body(){

        $change = "";
        if($_REQUEST['change']){
            $this->set_module_variable("view",$_REQUEST['change']);
        }
        if(!$this->get_module_variable("view")){
            $this->set_module_variable("view","recive");
            $change = $this->create_href(array("change" => "send"));
        }else{
            $view = $this->get_module_variable("view");
            if($view == "recive"){
                $change = $this->create_href(array("change"=>"send"));
            }else{
                $change = $this->create_href(array("change" =>"recive"));
            }
        }

        $correctIMG = "<img height='25' width='25' src='modules/phoneManager/theme/correct.png' />";
        $incorrectIMG = "<img height='25' width='25' src='modules/phoneManager/theme/incorrect.png' />";
        Base_ThemeCommon::install_default_theme($this->get_type());
        Base_LangCommon::install_translations($this->get_type());
        load_css($this->get_module_dir().'theme/default.css');
        $theme = $this->init_module('Base/Theme');
        $theme->assign("correctIMG", $correctIMG);
        $theme->assign("incorrectIMG", $incorrectIMG);
        $theme->assign("change",$change);
        $recordsPerPage = 25;

        if(isset($_REQUEST['changeSendPage'])){
            $this->set_module_variable("sendingPage",$_REQUEST['changeSendPage']);
        }
        if(isset($_REQUEST['changeRecivePage'])){
            $this->set_module_variable("recivePage",$_REQUEST['changeRecivePage']);
        }
        if(isset($_REQUEST['changeRecivePageNext'])){
            $val = $this->get_module_variable("recivePage");
            $val += 1;
            $this->set_module_variable("recivePage",$val);
        }
        if(isset($_REQUEST['changeRecivePagePrev'])){
            $val = $this->get_module_variable("recivePage");
            $val -= 1;
            $this->set_module_variable("recivePage",$val);
        }
        if(isset($_REQUEST['changeSendPageNext'])){
            $val = $this->get_module_variable("sendingPage");
            $val += 1;
            $this->set_module_variable("sendingPage",$val);
        }
        if(isset($_REQUEST['changeSendPagePrev'])){
            $val = $this->get_module_variable("sendingPage");
            $val -= 1;
            $this->set_module_variable("sendingPage",$val);
        }

        if(!$this->get_module_variable("recivePage")){
            $this->set_module_variable("recivePage",0);
            $count = $this->getDataCurl("http://192.168.11.12:8000/api/recived/count");
            $count = json_decode($count);  
            $count = $count[0];
            if($count->count){
                $max = floor($count->count / $recordsPerPage);
                $this->set_module_variable("reciveMaxPages",$max);
            }else{
                $this->set_module_variable("reciveMaxPages",'0');
            }

        }
        if(!$this->get_module_variable("sendingPage")){
            $this->set_module_variable("sendingPage",0);
            $count = $this->getDataCurl("http://192.168.11.12:8000/api/sended/count");
            $count = json_decode($count);  
            $count = $count[0];
            if($count->count){
                $max = floor($count->count / $recordsPerPage);
                $this->set_module_variable("sendingMaxPages",$max);
            }else{
                $this->set_module_variable("sendingMaxPages",'0');
            }
        }
        $companyRbo = new RBO_RecordsetAccessor("company");
        $contactRbo = new RBO_RecordsetAccessor("contact");
        if($this->get_module_variable("view") == "send"){
            $page = $this->get_module_variable("sendingPage");
            $max = $this->get_module_variable("sendingMaxPages");
            if($page != 0 ){
                $link = $this->create_href(array("changeSendPagePrev" => 1));
                $pageList[] = "<a $link >&laquo;</a>";
            }
            $pagerStart = $page - 3;
            $pageEnd = $page + 3;
            if($pagerStart < 0){
                $pagerStart = 0;
            }
            if($pageEnd >= $max){
                $pageEnd = $max;
            }

            for($start = $pagerStart; $start <= $pageEnd;$start++){
                $value = $start;
                $display = $start + 1;
                $link = $this->create_href(array("changeSendPage" => $value));
                if($value == $page){
                    $pageList[] =  "<a style='color:#000000;' $link>$display</a>";
                }else{  
                    $pageList[] =  "<a $link>$display</a>";
                }
            }
            if($page != $max){
                $link = $this->create_href(array("changeSendPageNext" => 1));
                $pageList[] = "<a $link >&raquo;</a>";
            }
            $theme->assign("pages",$pageList);
            
            $records = $this->getDataCurl("http://192.168.11.12:8000/api/sended/page/".$page);
            $records = json_decode($records);  
            foreach($records as $record){
                $record->TextDecoded = nl2br($record->TextDecoded);
                    if($record->CreatorID){
                        $contact = $contactRbo->get_record((int) $record->CreatorID);
                        $record->CreatorID = $contact->record_link($contact['first_name']." ".$contact['last_name']);
                    }
                    $str = $record->DestinationNumber;
                    $str = str_replace("+48","",$str);
                    $findInCompany = $companyRbo->get_records( array("(~phone" => "%$str", "|~phonenext" => "%$str") , array(),array());
                    $contactLink = Null;
                    if(count($findInCompany) == 0 ){
                        $findInContacts = $contactRbo->get_records( array("(~work_phone" => "%$str", "|~mobile_phone" => "%$str") , array(),array());
                        if(count($findInContacts) > 0 ){
                            foreach($findInContacts as $_contact){
                                $contactLink = $_contact->record_link($_contact['first_name']." ".$_contact['last_name']);
                            }
                        }
                    }else{
                        foreach($findInCompany as $_company){
                            $contactLink = $_company->record_link($_company['company_name']);
                        }
                    }
                    $country = substr($str, 0, 3);
                    $first = substr($str, 3, 3);
                    $second = substr($str, 6, 3);
                    $last = substr($str, 9, 3);
                    $record->DestinationNumber = "$contactLink ($country $first $second $last)";
                }
            $theme->assign("records",$records);
            //}
            $form = & $this->init_module('Libs/QuickForm');
            $crits = array();
            $fcallback = array('phoneManagerCommon','contact_or_company_format');
            $form->addElement('autoselect', 'contact', __('contact'), array(),
            array(array('phoneManagerCommon','autoselect_company_or_contact'), array($crits, $fcallback)), $fcallback);
            $form->addElement("textarea","message", __("message"), array("cols" => "20", "rows" => "20" )) ;
            $form->addElement('submit','submit',__("submit"), array("class" => "phoneManagerButton"));

            if ($form->validate()) {
                $values = $form->exportValues();
                $id = explode("_",$values['contact']);
                $number = $id[1];
                $id = $id[0];
              //  $phone->sendSMS($values['message'], $number , 'yes', Acl::get_user());
                $creator =   CRM_ContactsCommon::get_contact_by_user_id(Base_AclCommon::get_user ())['id'];
                $message = $values['message'];
                $ch = curl_init();

                // set url
                curl_setopt($ch, CURLOPT_URL,"http://192.168.11.12:8000/api/send/sms");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "number=$number&message=$message&creator=$creator");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $server_output = curl_exec($ch);
                curl_close ($ch);
            }
            $form->toHtml();
            $form->assign_theme('my_form', $theme);
        }else{
            $page = $this->get_module_variable("recivePage");
            $max = $this->get_module_variable("reciveMaxPages");
            if($page != 0){
                $link = $this->create_href(array("changeRecivePagePrev" => 1));
                $pageList[] = "<a $link >&laquo;</a>";  
            }
            $pagerStart = $page - 3;
            $pageEnd = $page + 3;
            if($pagerStart < 0){
                $pagerStart = 0;
            }
            if($pageEnd >= $max){
                $pageEnd = $max;
            }

            for($start = $pagerStart; $start <= $pageEnd;$start++){
                $value = $start;
                $display = $start + 1;
                $link = $this->create_href(array("changeRecivePage" => $value));
                if($value == $page){
                    $pageList[] =  "<a style='color:#000000;' $link>$display</a>";
                }else{  
                    $pageList[] =  "<a $link>$display</a>";
                }
            }
            if($page != $max){
                $link = $this->create_href(array("changeRecivePageNext" => 1));
                $pageList[] = "<a $link >&raquo;</a>";
            }
            $theme->assign("pages",$pageList);
            $records = $this->getDataCurl("http://192.168.11.12:8000/api/recived/page/".$page);
            $records =  json_decode($records);  
            foreach($records as $record){
                $readers = $record->readed;
                $readers = explode(',',$readers);
                $readed = false;
                if(array_search( CRM_ContactsCommon::get_contact_by_user_id(Base_AclCommon::get_user())['id'],$readers) != NULL){
                    $readed  = true;
                }
                $record->readed = $readed;
                $str = $record->SenderNumber;
                $str = str_replace("+48","",$str);
                $findInCompany = $companyRbo->get_records( array("(~phone" => "%$str", "|~phonenext" => "%$str") , array(),array());
                $contactLink = Null;
                if(count($findInCompany) == 0 ){
                    $findInContacts = $contactRbo->get_records( array("(~work_phone" => "%$str", "|~mobile_phone" => "%$str") , array(),array());
                    if(count($findInContacts) > 0 ){
                        foreach($findInContacts as $_contact){
                            $contactLink = $_contact->record_link($_contact['first_name']." ".$_contact['last_name']);
                        }
                    }
                }else{
                    foreach($findInCompany as $_company){
                        $contactLink = $_company->record_link($_company['company_name']);
                    }
                }
                $country = substr($str, 0, 3);
                $first = substr($str, 3, 3);
                $second = substr($str, 6, 3);
                $last = substr($str, 9, 3);
                $record->SenderNumber = "$contactLink ($country $first $second $last)";
            }

            $theme->assign("records",$records);
        }


        $theme->display($this->get_module_variable("view"));
        if($this->get_module_variable("view") == "send"){
            load_js($this->get_module_dir().'js/send.js');
        }else{
            load_js($this->get_module_dir().'js/recive.js');
        }
       
    }



}