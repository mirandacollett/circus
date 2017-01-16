<?php
// MC Created 26/02/2016 to replace Mandrill
// Source reference https://github.com/sendgrid/sendgrid-php
require($_SERVER["DOCUMENT_ROOT"]."/sendgrid-php/sendgrid-php.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/client/usefulness.php");
    $PostedData=array();
    function GetPostedData() {
        global $PostedData;
        if(sizeof($_POST)>0){
            foreach ($_POST as $key=>$value) {
                $PostedData[$key]=$value;
            }
        }
        return $PostedData;
    }
    function fGetElse($Label,$Else){
        global $PostedData;
        if(array_key_exists($Label, $PostedData)) {
            if($PostedData[$Label]==null) return $Else;
            else return $PostedData[$Label];
        }
        else return $Else;
    }
    $PostedData=GetPostedData();
$options = array(
    'endpoint' => '/api/mail.send.json'
);
if(fGetElse('vContent','No content')!=='No content'){
    $sendgrid = new SendGrid(SENDGRID_APIKEY,$options);
    $email = new SendGrid\Email();
    $email
        ->addTo(fGetElse('vEmail',BOOKINGS_TO))
        ->addBcc(EMAILS_BCC)
        ->setFrom(EMAILS_FROM)
        ->setFromName(WEBSITE_TITLE)
        ->setSubject(fGetElse('vHeader','An Email from the '.WEBSITE_TITLE))
        ->setHtml(fGetElse('vContent','No content'))
        ->addCategory(WEBSITE_CATEGORY);
    try {
        $a = $sendgrid -> send($email);
        echo json_encode($a);
        echo 'The email has been sent';
    } catch (Exception $e) {
        echo $e->getCode();
        foreach($e->getErrors() as $er) {
            echo $er;
        }
    }
} else{
    echo "Unable to send a message without content.";
}
