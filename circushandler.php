<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once ("connx.php"); // Works in dorsetshowground 
$userroles = userroles();
$PostedData=array();
function GetPostedData() {
    global $PostedData;
    if(sizeof($_POST)>0){foreach ($_POST as $key=>$value) {$PostedData[$key]=$value;}}
    return $PostedData;
}
function fGet($Label){
    global $PostedData;
    if(array_key_exists($Label, $PostedData)) return $PostedData[$Label];
    else return null;
}
$PostedData=GetPostedData();
$Action=fGet("Action");
if($Action=="Test"){
    echo 'circushandler.php has returned this message';
    echo ReportArray($userroles);
}
if($Action=="getEventCards"){
    $html="";
    $news="";
    $menu=array();
    $b=getSetting('Background');
    if($b){$b=$b['text_value'];}
    $query="SELECT e.*, p.image_url FROM events e LEFT JOIN photos p ON p.id = e.photoid WHERE e.hide_yn = ?";
    $events=RunQuery ($query,array('N'),"s","id");
    $info='There are no visible events';
    if(sizeof($events)>0){
        usort($events,'sortEvents');
        $info=array();
        $info[]='There are '.sizeof($events).' visible events';
        $setting = getSetting('Event Card Photographs');
        $eventPhoto=false;
        foreach($events as $key=>$event){
            $query="SELECT * FROM eventlinks WHERE eventid = ? AND hide_yn = ?";
            $eventlinks=RunQuery($query,array($event['id'],'N'),"ds","id");
            $buttons=false;
            $info[]='Event '.$event['id'].' has '.sizeof($eventlinks).' events';
            $title=$event['title'];
            if($event['startdate']>""){
                $startdate=strtotime($event['startdate']);
                $menu[]=array('hash'=>'#Event'.$key,'description'=>$title." on ".date('d/m/Y',$startdate));
                $title=$title."<br>".date('d/m/Y',$startdate);
            }
            if(sizeof($eventlinks)==1){
                $info[]='One Event Link';
                $link=ReduceArray($eventlinks);
                $buttons=writeAnchor($link['url'],$link['label'],'chevron_right');
            }
            if(sizeof($eventlinks)>1){
                $info[]='Multiple Event Links';
                $temp=array();
                foreach ($eventlinks as $key => $row){$temp[$key] = $row['seq'];}
                array_multisort($temp, SORT_DESC, $eventlinks);
                $buttons="";
//                $buttons='<a id="btMore_'.$event['id'].'" '
//                        . 'class="mdl-button mdl-js-button">'
//                        . 'Information<i class="material-icons">arrow_drop_up</i></a>'
//                        . '<ul class="mdl-menu mdl-menu--top-left mdl-js-menu mdl-js-ripple-effect" '
//                        . 'for="btMore_'.$event['id'].'">';
                foreach($eventlinks as $i=>$link){
                    $buttons.=writeAnchor($link['url'],$link['label'],'chevron_right')."<br>";
//                    $buttons.='<li class="mdl-menu__item">';
//                        $buttons.='<a class="l-link mdl-typography--text-uppercase" href="'.$link['url'].'">';
//                        $buttons.=$link['label'];
//                        $buttons.='<i class="material-icons">chevron_right</i>';
//                        $buttons.='</a>';
//                    $buttons.='</li>';
                }
                $buttons.='</ul>';
            }
            $eventPhoto=false;
            if($setting['text_value']=='Y') $eventPhoto=$event['image_url'];
            $html.=createCard($eventPhoto,$title,$event['headline'],$buttons,4,0,'Event'.$key);
        }
    }
    $query="SELECT n.*,p.image_url FROM news n LEFT JOIN photos p ON p.id = n.photoid WHERE n.hide_yn = ?";
    $stories=RunQuery ($query,array('N'),"s","id");
    if(sizeof($stories)>0){
        usort($stories,'sortNews');
        foreach($stories as $id=>$story){
            if($story['wide_yn']=="Y"){
                if($story['image_url']==null){
                    $news.=createCard(false,$story['title'],$story['content'],false,6,$story['seq'],'News'.$id);
                }
                else{
                    $news.=createCard($story['image_url'],$story['title'],false,false,6,$story['seq'],'News'.$id);
                }
            } else {
                $news.=createCard(false,$story['title'],$story['content'],false,3,$story['seq'],'News'.$id);
            }
        }
    }
    $query="SELECT s.id,s.url,REPLACE(p.image_url,'w_auto','w_180') image_url, p.title FROM sidebars s LEFT JOIN photos p ON p.id = s.photoid WHERE s.location = ?";
    $sidebarL=RunQuery ($query,array('L'),"s","id");
    $sidebarR=RunQuery ($query,array('R'),"s","id");
    $info=createFirstUser();
    echo json_encode(array("cards"=>$html
                          ,"info"=>$info
                          ,"news"=>$news
                          ,"menu"=>$menu
                          ,"info"=>$info
                          ,"background"=>$b
                          ,"sidebarL"=>$sidebarL
                          ,"sidebarLsettings"=>getSidebarSettings('L')
                          ,"sidebarR"=>$sidebarR
                          ,"sidebarRsettings"=>getSidebarSettings('R')
                          ));
}
// ============================================================ Admin Hub ======
function getfmLogon(){
    $form= '<form id="fmLogon">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="text" id="iUsername">
                    <label class="mdl-textfield__label" for="iUsername">Username</label>
                    <span class="mdl-textfield__error">Please enter your username</span>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="password" minlength = 6 id="iPassword">
                    <label class="mdl-textfield__label" for="iPassword">Password</label>
                    <span class="mdl-textfield__error">Please enter your password</span>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label hide">
                    <input class="mdl-textfield__input" type="password" minlength = 6 id="iPassword2">
                    <label class="mdl-textfield__label" for="iPassword2">Enter password again</label>
                    <span class="mdl-textfield__error">Please enter your password again</span>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label hide">
                    <input class="mdl-textfield__input" type="email" id="iEmail">
                    <label class="mdl-textfield__label" for="iEmail">Email Address</label>
                </div>
                <p id="pMessage" class="hide">Please complete all the fields then click the Register button. You will not be able to do anything on the website until you are given user permissions.</p>
                <div>'.writeButton('id="btLogon"','Log On',false,false)
                      .writeButton('id="btReset"','Reset Password',false,false)
              .'</div>
            </form>';
    $logon=createCard(false,'Admin Log In',$form,false,6,0,false);
    return $logon;
}
if($Action=="getfmLogon"){
    emergencyStart();
    $userroles = userroles();
    if($userroles['loggedon']){
        echo "";
    } else {
        echo getfmLogon();
    }
}
if($Action=="fmLogonSubmit"){
    $rtn="Y";
    if(!logged_on()) $rtn=log_on ($_POST["username"],$_POST["password"]);
    echo json_encode(array("loggedon"=>$rtn));
}
if($Action=="getAdminCards"){
    $cards1=false;
    $cards2=false;
    $cards3=false;
    $cards4=false;
    $logon=false;
    emergencyStart();
    $userroles = userroles();
    if($userroles['loggedon']){
        $query="SELECT e.id,e.title, e.headline, e.startdate,e.hide_yn, e.duration,e.seq, p.image_url FROM events e LEFT JOIN photos p ON p.id = e.photoid WHERE 1 = ?";
        $events=RunQuery ($query,array(1),"d","id");
        if(sizeof($events)>0){
            usort($events,'sortEvents');
            foreach($events as $key=>$event){
                $buttons='';
                $title=$event['title'];
                if($event['startdate']>""){
                    $startdate = strtotime($event['startdate']);
                    $title = $title."<br>".date('d/m/Y',$startdate);
                }
                if($userroles['events']){
                    $buttons.='<button class="btEditEvent mdl-button mdl-js-button" data-id="'.$event['id'].'">Edit Event</button>';
                }
                else {
                    $buttons.='<button class="btEditEvent mdl-button mdl-js-button" data-id="'.$event['id'].'" disabled>Edit Event</button>';
                }
                if($userroles['links']){
                    $buttons.='<button class="btGetAttachments mdl-button mdl-js-button" data-id="'.$event['id'].'">Attachments</button>';
                }
                else {
                    $buttons.='<button class="btGetAttachments mdl-button mdl-js-button" data-id="'.$event['id'].'" disabled>Attachments</button>';
                }
                if($event['hide_yn']=="N"){$cards1.=createCard($event['image_url'],$title,$event['headline'],$buttons,3,0,false);}
                elseif($event['startdate']>""){$cards2.=createCard($event['image_url'],$title,$event['headline'],$buttons,3,0,false);}
                else {$cards3.=createCard($event['image_url'],$title,$event['headline'],$buttons,3,0,false);}
            }
        }
        if($userroles['events']){
            $cards4.=createCard(false,"Add an Event","Click to create a blank event for you to edit"
                               ,writeButton('id="btCreateEvent"',"Create New Event",false,false),3,0,false);
        }
        if($userroles['news']){
            $cards4.=createCard(false,"News","Click to create and edit news stories"
                               ,writeAnchor("admin_news.php",'News',false),3,0,false);
        }
        if($userroles['users']){
            $cards4.=createCard(false,"Users","Click to create new users or alter their permissions"
                               ,writeAnchor("admin_users.php",'Edit Users',false),3,0,false);
            $cards4.=createCard(false,"Photos","Click to upload photographs"
                               ,writeAnchor("admin_photos.php",'Photographs',false),3,0,false);
            $cards4.=createCard(false,"Left Bar","Click to change the images on the left of the homepage"
                               ,writeAnchor("admin_sidebar_left.php",'Sidebar Left',false),3,0,false);
            $cards4.=createCard(false,"Right Bar","Click to change the images on the right of the homepage"
                               ,writeAnchor("admin_sidebar_right.php",'Sidebar Right',false),3,0,false);
            $cards4.=createCard(false,"Settings","Click to change the display settings for the whole site"
                               ,writeAnchor("admin_settings.php",'Site Settings',false),3,0,false);
            $cards4.=createCard(false,"Calendar","Click to go to the page to synchronise calendars"
                               ,writeAnchor("updateCal.html",'Calendar Sync',false),3,0,false);
        }
    } else {
        $logon=getfmLogon();
    }
    echo json_encode(array("logon"=>$logon
                          ,"cards"=>array("current"=>$cards1,"future"=>$cards2,"past"=>$cards3,"users"=>$cards4)
                          ,"php"=>phpversion()
                          )
                    );
}
// ============================================================ Events =========
if($Action=="updateEvent"){
    if($_POST['fieldname']=='photoid' && $_POST["value"] == '0') {
        $query="UPDATE events SET ".$_POST['fieldname']." = NULL,user_modified=? WHERE id = ?";
        ExecuteQuery ($query,array($_SESSION ['username'],$_POST["id"]));        
    } else{
        $query="UPDATE events SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
        ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
    }
}
if($Action=="deleteEvent"){
    $query="DELETE FROM eventlinks WHERE eventid = ?";
    echo ExecuteQuery ($query,array(fGet("id")));
    $query="DELETE FROM events WHERE id = ?";
    echo ExecuteQuery ($query,array(fGet("id")));
}
function sortEvents($event1,$event2){
    // sort events by seq and startdate
    if ($event1['seq'] == $event2['seq']) {
        if ($event1['startdate'] == $event2['startdate']) {
            return 0;
        }
        return ($event1['startdate'] > $event2['startdate']) ? 1 : -1;
    }
    return (intval($event1['seq']) > intval($event2['seq'])) ? 1 : -1;
}
if($Action=="getfmEditEvent"){
    $event=array();
    $startdate="";
    $query="SELECT * FROM events WHERE id = ?";
    $event=RunQuery ($query,array($_POST["id"]),"d","id");
    $event=ReduceArray($event);
    $startdate = strtotime($event['startdate']);
    $startdate = date('d/m/Y',$startdate);
    $form ='<form id="fmEditEvent" data-id="'.$_POST["id"].'">';
    $form.=writeTextField(array ('id'=>'title'
                                ,'label'=>'Title'
                                ,'datafieldname'=>'title'
                                ,'value'=>$event['title']
                                ,'maxlength'=>60
                                ,'required'=>'required'
                                ,'error'=>'This is a mandatory field'
                                ,'class'=>'mdl-textfield--floating-label'));
    $form.=writeTextField(array ('id'=>'startdate'
                                ,'label'=>'Start Date'
                                ,'datafieldname'=>'startdate'
                                ,'value'=>$event['startdate']
                                ,'type'=>'date'
                                ,'maxlength'=>60
                                ,'required'=>'required'
                                ,'error'=>'This is a mandatory field'
                                ,'class'=>'mdl-textfield--floating-label'));
    $form.='<br>';
    $form.=writeCheckBox (array ('id'=>'hide_yn'
                                ,'label'=>'Hide this event?'
                                ,'datafieldname'=>'hide_yn'
                                ,'value'=>$event['hide_yn']));
    $form.=writeTextField(array ('id'=>'duration'
                                ,'label'=>'Duration'
                                ,'datafieldname'=>'duration'
                                ,'value'=>$event['duration']
                                ,'type'=>'number'
                                ,'required'=>'required'
                                ,'error'=>'This is a mandatory field'
                                ,'class'=>'mdl-textfield--floating-label'));
    $form.=writeTextField(array ('id'=>'seq'
                                ,'label'=>'Order on Page'
                                ,'datafieldname'=>'seq'
                                ,'value'=>$event['seq']
                                ,'type'=>'number'
                                ,'required'=>'required'
                                ,'error'=>'This is a mandatory field'
                                ,'class'=>'mdl-textfield--floating-label'));
    $form.='<br><p>Image: </p>';
    $form.=writePhotosSelect($event['photoid'],$event['id'],false);
    $form.='<br>';
    $form.='    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label l_fullwidth">
                    <textarea class="fText mdl-textfield__input l-font" type="text" data-fieldname="headline" maxlength=500 rows=1 id="headline">'.$event['headline'].'</textarea>
                    <label class="mdl-textfield__label" for="headline">Headline</label>
                </div><br>
            </form>';
    echo createCard(false,$event['title'].'<br>'.$startdate,$form,writeButton('onclick="deleteEvent('.$event['id'].');"','Delete',false,false),12,0,false);
}
if($Action=="createEvent"){
    $query="INSERT INTO events (title,startdate,duration,headline,user_modified) values (?,?,?,?,?)";
    $params=array('Event Name'
                 ,'2016-01-01'
                 ,1
                 ,'Please edit this record'
                 ,$_SESSION ['username']
                 );
    echo ExecuteQuery ($query,$params);
}
// ============================================================ Attachments ====
if($Action=="getUploader"){
    $form ='<form id="fmQueue" name="fmQueue" data-id="'.$_POST["id"].'")>
                <div id="queue" class="uploadifive-queue">Click "Select File" to choose the file to upload, then click Upload</div>
                <input class="mdl-button mdl-js-button" id="file_upload" name="file_upload" id="file_upload" data-id="'.$_POST["id"].'" type="file" multiple="false">
                <button class="mdl-button mdl-js-button" onclick="uploadFile()">Upload</button>
                <button class="mdl-button mdl-js-button" onclick="getStarted()">Cancel</button>
            </form>';
    echo createCard(false,'Upload a pdf file',$form,false,12,0,false);
}
if($Action=="getfmAttachments"){
    // admin_links
    $table="";
    $query="SELECT el.*, a.description FROM eventlinks el JOIN att_types a ON el.atid = a.id WHERE el.eventid = ?";
    $links=RunQuery ($query,array($_POST["id"]),"d","id");
    if(sizeof($links)>0){
        $table='<table class="mdl-data-table mdl-js-data-table">
                <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">Type</th>
                        <th class="mdl-data-table__cell--non-numeric">URL</th>
                        <th class="mdl-data-table__cell--non-numeric">Label</th>
                        <th class="mdl-data-table__cell--non-numeric">Position</th>
                        <th>Hide</th>
                        <th class="mdl-data-table__cell--non-numeric">Delete</th>
                    </tr>
                </thead>
                <tbody>';
        foreach($links as $key=>$link){
            $checked="";
            if($link['hide_yn']=="Y"){$checked="checked";}
            $table.='<tr data-id="'.$link['id'].'">
                        <td class="mdl-data-table__cell--non-numeric">';
            $table.=        writeLinkTypesSelect($link['atid']);
            $table.=   '</td>
                        <td class="mdl-data-table__cell--non-numeric"><a class="mdl-button mdl-button--colored mdl-js-button" href="'.$link['url'].'" target="_blank">Test Link</a></td>
                        <td class="mdl-data-table__cell--non-numeric">';
            $table.=        '<input class="fText mdl-textfield__input" type="text" data-fieldname="label" maxlength=30 value="'.$link['label'].'">';
            $table.=   '</td>
                        <td><input class="fText mdl-textfield__input" type="number" data-fieldname="seq" value="'.$link['seq'].'"></td>
                        <td><label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect">
                            <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-fieldname="hide_yn" '.$checked.'></label></td>
                        <td class="mdl-data-table__cell--non-numeric"><a class="mdl-button mdl-button--colored mdl-js-button" onclick="deleteLink('.$link['id'].');">Delete</a></td>
                    </tr>';
        }
        $table.='</tbody>
                 </table>';
    }
    $query="SELECT id,title FROM events WHERE id = ?";
    $event=ReduceArray(RunQuery ($query,array($_POST['id']),"d","id"));
    $buttons ='<button class="mdl-button mdl-js-button" onclick="goBack()">Back to Admin</a>';
    $buttons.='<button class="mdl-button mdl-js-button" onclick="getNewLink('.$_POST["id"].')">New Link</button>';
    $buttons.='<button class="mdl-button mdl-js-button" onclick="getUploader('.$_POST["id"].')">Upload File</button>';
    echo createCard(false,$event['title'],$table,$buttons,12,0,false);
}
if($Action=="updateAttachment"){
    $query="UPDATE eventlinks SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
    ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
}
if($Action=="getNewLink"){
    $form ='<form onsubmit=createNewLink('.$_POST["id"].')>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="fText mdl-textfield__input" type="url" maxlength=500 required id="new_url">
                    <label class="mdl-textfield__label" for="new_url">URL</label>
                    <span class="mdl-textfield__error">This is a mandatory field</span>
                </div><br><br>
                    <p>Type or paste in the URL beginning http://www...</p>
            </form';
    $buttons='<button class="mdl-button mdl-js-button" onclick="createNewLink('.$_POST["id"].')">Save</button>';
    $buttons.='<button class="mdl-button mdl-js-button" onclick="getAttachments('.$_POST["id"].')">Cancel</button>';
    echo createCard(false,'Create your new link here',$form,$buttons,12,0,false);
}
if($Action=="createNewLink"){
    $query="INSERT INTO eventlinks(eventid,url,label,atid,user_modified) values (?,?,?,?,?)";
    echo ExecuteQuery ($query,array($_POST['eventid'],$_POST['url'],'Edit this title',1,$_SESSION ['username']));
}
if($Action=="deleteLink"){
    $query="DELETE FROM eventlinks WHERE id = ?";
    echo ExecuteQuery ($query,array(fGet("id")));
}
// ============================================================ Photos =========
if($Action=="getPhotos"){
    emergencyStart();
    $userroles = userroles();
    $cards=false;
    $background=false;
    $url=false;
    if($userroles['users']){
        $query="SELECT * FROM photos WHERE 1 = ?";
        $photos=RunQuery ($query,array(1),"d","id");
        $cards="";
        if(sizeof($photos)>0){
            foreach($photos as $key=>$photo){
                $id=$photo['id'];
                $form= '<form class="l-disregard-form" data-id="'.$id.'">';
                $form.=writeTextField(array ('id'=>'fmPhoto_title_'.$id
                                            ,'label'=>'Title'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'title'
                                            ,'value'=>$photo['title']
                                            ,'maxlength'=>200
                                            ,'required'=>'required'
                                            ,'error'=>'A title is required'
                                            ,'class'=>'mdl-textfield--floating-label'));
                $btn=writeButton('data-id="'.$id.'"','Delete',false,'deletePhoto');
                $form.='</form>';
                $cards.=createCard($photo['image_url'],false,$form,$btn,3,0,false);
            }
        }
        $query="SELECT s.id,p.image_url,s.numeric_value FROM settings s LEFT JOIN photos p ON s.numeric_value = p.id WHERE s.description = ? ";
        $b=RunQuery ($query,array('background'),"s","id");
        if(!$b){
            setSetting('background',null,null,0);
            $query="SELECT s.id,p.image_url,s.numeric_value FROM settings s LEFT JOIN photos p ON s.numeric_value = p.id WHERE s.description = ? ";
            $b=RunQuery ($query,array('background'),"s","id");
            $url=$b['image_url'];
        } else {
            $b=ReduceArray($b);
            $url=$b['image_url'];
        }
        $background = '<form id="fmBackground">';
        $background.= writePhotosSelect($b['numeric_value'],0);
        $background.= '</form>';
    } else {
        $cards= createCard (false,'Sorry',"You do not have permission to Manage Users",false,3,0,false);
    }
    echo json_encode(array('cards'=>$cards,'background'=>$background,'b'=>$b,'url'=>$url));
}
if($Action=="createNewPhoto"){
    $query="INSERT INTO photos(image_url,title,user_modified) values (?,?,?)";
    echo ExecuteQuery ($query,array($_POST['url'],$_POST['title'],$_SESSION ['username']));
}
if($Action=="updatePhoto"){
    $query="UPDATE photos SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
    ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
}
if($Action=="deletePhoto"){
    $id=fGet('id');
    $query="SELECT COUNT(*) numUsages FROM sidebars WHERE photoid = ?";
    $x=ReduceArray(RunQuery($query,array($id),"d","numUsages"));
    if($x['numUsages']>0){
        echo json_encode(array('message'=>'This photo is in use on a sidebar. Please change the sidebar to use another photo before deleting this one','deleted'=>false));
    }
    else{
        $query="SELECT COUNT(*) numUsages FROM news WHERE photoid = ?";
        $x=ReduceArray(RunQuery($query,array($id),"d","numUsages"));
        if($x['numUsages']>0){
            echo json_encode(array('message'=>'This photo is in use on a news story. Please change the news story to use another photo before deleting this one','deleted'=>false));
        }
        else{
            $query="SELECT COUNT(*) numUsages FROM events WHERE photoid = ?";
            $x=ReduceArray(RunQuery($query,array($id),"d","numUsages"));
            if($x['numUsages']>0){
                echo json_encode(array('message'=>'This photo is in use on an event. Please change the event to use another photo before deleting this one','deleted'=>false));
            }
            else{
                $m=ExecuteQuery("DELETE FROM photos WHERE id = ?",array(fGet('id')));
                if($m) echo json_encode(array('message'=>$m,'deleted'=>true));
                else echo json_encode(array('message'=>'Error when deleting photo: '.ReportArray($m),'deleted'=>false));
            }
        }
    }
}
// ============================================================ News ===========
function sortNews($item1,$item2){
    // sort $stories by hide_yn, seq, id desc
    if ($item1['hide_yn'] == $item2['hide_yn']) {
        if ($item1['seq'] == $item2['seq']) {
            return (intval($item1['id']) < intval($item2['id'])) ? 1 : -1;
        }
        return (intval($item1['seq']) > intval($item2['seq'])) ? 1 : -1;
    }
    return ($item1['hide_yn'] > $item2['hide_yn']) ? 1 : -1;
}
if($Action=="getNews"){
    emergencyStart();
    $userroles = userroles();
    if($userroles['news']){
        $rtn=array('live'=>false
                  ,'hidden'=>false
                  ,'new'=>createCard(false
                                    , 'New Story'
                                    , 'Click the button below to create a new story, then edit the story and un-hide it.'
                                    , '<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="newNews()">New Story</button>'
                                    , 6,0,false));
        $query="SELECT n.*, p.image_url FROM news n LEFT JOIN photos p ON p.id = n.photoid WHERE 1 = ?";
        $stories=RunQuery ($query,array(1),"d","id");
        $photos=RunQuery ("SELECT * FROM photos WHERE 1 = ?",array(1),"d","id");
        if(sizeof($stories)>0){
            usort($stories,'sortNews');
            foreach($stories as $key=>$story){
                $id=$story['id'];
                $target='live';
                if($story['hide_yn']=='Y') $target='hidden';
                $form= '<form class="l-disregard-form">';
                $form.=writeTextField(array ('id'=>'fmNews_title_'.$id
                                            ,'label'=>'Title'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'title'
                                            ,'value'=>$story['title']
                                            ,'maxlength'=>200
                                            ,'required'=>'required'
                                            ,'error'=>'A title is required'
                                            ,'class'=>'mdl-textfield--floating-label'));
                $form.='<select id="fPhoto" class="fSelect" data-fieldname="photoid" data-id="'.$id.'">
                            <option value="0">Use Text not Photo</option>';
                $hideTextArea='';
                if(sizeof($photos)>0){
                    foreach($photos as $p=>$photo){
                        if($p==$story['photoid']){
                            $hideTextArea='hide';
                            $form.='<option selected value="'.$p.'">'.$photo['title'].'</option>';
                        }
                        else{
                            $form.='<option value="'.$p.'">'.$photo['title'].'</option>';
                        }
                    }
                }
                $form.='</select>';
                $form.='<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label '.$hideTextArea.' contentParent" style="width:100%;">
                            <textarea class="fText mdl-textfield__input l-font" rows= "1" maxlength=8000
                                      data-fieldname="content"
                                      data-id="'.$id.'"
                                      id="fmNews_content_'.$id.'">'.$story['content'].'</textarea>
                            <label class="mdl-textfield__label" for="fmNews_content_'.$id.'">Content</label>
                        </div>';
                $form.=writeTextField(array ('id'=>'fmNews_seq_'.$id
                                            ,'label'=>'Order'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'seq'
                                            ,'value'=>$story['seq']
                                            ,'type'=>'number'
                                            ,'required'=>'required'
                                            ,'error'=>'A sequence number is required'
                                            ,'class'=>'mdl-textfield--floating-label'));
                $form.=writeCheckBox(array ('id'=>'fmNews_hide_'.$id
                                            ,'label'=>'Hide this story'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'hide_yn'
                                            ,'value'=>$story['hide_yn']));
                $form.=writeCheckBox(array ('id'=>'fmNews_wide_'.$id
                                            ,'label'=>'Wide card'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'wide_yn'
                                            ,'value'=>$story['wide_yn']));
                $form.='</form>';
                if($story['wide_yn']=="Y"){
                    $rtn[$target].=createCard(false,false,$form,writeButton('onclick="deleteNews('.$id.');"','Delete',false,false),6,$story['seq'],false);
                } else {
                    $rtn[$target].=createCard(false,false,$form,writeButton('onclick="deleteNews('.$id.');"','Delete',false,false),3,$story['seq'],false);
                }
            }
        }
    } else {
        $cards= createCard (false,'Sorry',"You do not have permission to Manage Users",false,3,0,false);
    }
    echo json_encode($rtn);
}
if($Action=="createNewNews"){
    $query="UPDATE news SET seq = seq + 1, user_modified = ? WHERE 1 = ?";
    echo ExecuteQuery ($query,array(1,$_SESSION ['username']));
    $query="INSERT INTO news(title,content,user_modified) values (?,?,?)";
    echo ExecuteQuery ($query,array('Edit this title','Also edit the content and change the checkbox to publish the story',$_SESSION ['username']));
}
if($Action=="updateNews"){
    $query="UPDATE news SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
    if($_POST['fieldname']=='photoid'&&$_POST["value"]=='0'){
        $query="UPDATE news SET photoid = NULL,user_modified=? WHERE id = ?";
        ExecuteQuery ($query,array($_SESSION ['username'],$_POST["id"]));
    }
    else{
        ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
    }
    $r=ReduceArray(RunQuery ('SELECT * FROM news WHERE id = ?',array($_POST["id"]),"d","id"));
    echo ' '.$r['id'].' being '.$r['content'].' from '.$_SESSION ['username'];
}
if($Action=="deleteNews"){
    $query="DELETE FROM news WHERE id = ?";
    echo ExecuteQuery ($query,array(fGet("id")));
}
// ============================================================ Sidebars =======
if($Action=="getSidebar"){
    $userroles = userroles();
    $cards = '';
    if($userroles['users']){
        $query="SELECT s.id,s.url,s.photoid,p.image_url FROM sidebars s LEFT JOIN photos p ON s.photoid = p.id WHERE s.location = ? ";
        $b=RunQuery ($query,array(fGet('location')),"s","id");
        $photos=RunQuery ("SELECT * FROM photos WHERE 1 = ?",array(1),"d","id");
        if(sizeof($b)>0){
            foreach($b as $id=>$img){
                $form= '<form class="l-disregard-form">';
                $form.=writePhotosSelect($img['photoid'],$id);
                $form.=writeTextField(array ('id'=>'fSidebar_'.$id
                                            ,'label'=>'URL'
                                            ,'dataid'=>$id
                                            ,'datafieldname'=>'url'
                                            ,'value'=>$img['url']
                                            ,'maxlength'=>200
                                            ,'required'=>false
                                            ,'error'=>''
                                            ,'class'=>'mdl-textfield--floating-label'));
                $form.='</form>';
                $cards.=createCard($img['image_url']
                                , $form
                                , false
                                , '<button class="mdl-button mdl-js-button mdl-button--colored" onclick="deleteSidebarImage('.$id.')">Delete</button>'
                                , 12,0,false);
            }
        }
        if(fGet('location')=='L'){
            $cards.=createCard(false
                            , 'New Image'
                            , 'Click the button below to add an image, which you can alter.'
                            , '<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="newSidebarLeft()">New Image</button>'
                            , 12,0,false);
            $cards.=createCard(false
                            , 'Clone sidebar'
                            , 'Click this button to make the Left Sidebar identical to the Right Sidebar. NB This can result in the deletion of sidebar items.'
                            , '<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="cloneSidebarRL()">Clone</button>'
                            , 12,0,false);
        }
        else{
            $cards.=createCard(false
                            , 'New Image'
                            , 'Click the button below to add an image, which you can alter.'
                            , '<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="newSidebarRight()">New Image</button>'
                            , 12,0,false);
            $cards.=createCard(false
                            , 'Clone sidebar'
                            , 'Click this button to make the Right Sidebar identical to the Left Sidebar. NB This can result in the deletion of sidebar items.'
                            , '<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="cloneSidebarLR()">Clone</button>'
                            , 12,0,false);
        }
    }
    else{
        $cards= createCard (false,'Sorry',"You do not have permission to play with Sidebars",false,3,0,false);
    }
    echo json_encode(array('cards'=>$cards,'settings'=>getSidebarSettings(fGet('location'))));
}
function getSidebarSettings($location){
    $settings=[];
    if($location=='L') $settings['sidebar']=getSetting('LeftSidebar');
    if($location=='R') $settings['sidebar']=getSetting('RightSidebar');
    $settings['sidebar'] = $settings['sidebar']['text_value'];
    if($location=='L') $settings['facebook']=getSetting('LeftFacebook');
    if($location=='R') $settings['facebook']=getSetting('RightFacebook');
    $settings['facebook'] = $settings['facebook']['text_value'];
    return $settings;
}
if($Action=="createSidebarImage"){
    $query="INSERT INTO sidebars(location,photoid,user_modified) (SELECT ?,MIN(id),? FROM photos)";
    echo ExecuteQuery ($query,array(fGet('location'),$_SESSION ['username']));
}
if($Action=="updateSidebar"){
    $query="UPDATE sidebars SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
    ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
}
if($Action=="deleteSidebarImage"){
    $query="DELETE FROM sidebars WHERE id = ?";
    echo ExecuteQuery ($query,array(fGet('id')));
}
if($Action=="cloneSidebar"){
    $query="DELETE FROM sidebars WHERE location = ?";
    echo ExecuteQuery ($query,array(fGet('to')),"s");
    $query="INSERT INTO sidebars (location,photoid,url,user_modified) (SELECT ?,photoid,url,? FROM sidebars WHERE location = ?)";
    echo ExecuteQuery ($query,array(fGet('to'),$_SESSION ['username'],fGet('from')));
}
// ============================================================ Settings =======
if($Action=="setBackground"){
    $query="SELECT s.id,p.image_url FROM settings s LEFT JOIN photos p ON s.numeric_value = p.id WHERE s.description = ? ";
    $background=RunQuery ($query,array('background'),"s","id");
    if(sizeof($background)>0) {
        $background=ReduceArray($background);
        $background=$background['image_url'];
    }
    else {
        $background=false;
    }
    setSetting('background',null,null,$_POST["value"]);
    echo $background;
}
if($Action=="setSetting"){
    echo setSetting($_POST["description"]
                   ,$_POST["text_value"]
                   ,$_POST["date_value"]
                   ,$_POST["numeric_value"]);
}
if($Action=="pageSettings"){
    $settings=RunQuery ("SELECT s.*, p.image_url
                         FROM settings s
                         LEFT JOIN photos p ON s.text_value = p.id AND s.description = 'Background'
                         WHERE 1 = ?",array(1),"d","id");
    $php=phpversion();
    $settings[]=array('description'=>'phpversion','text_value'=>phpversion());
    echo json_encode($settings);
}
if($Action=="startSettingsPage"){
    $settings=RunQuery ("SELECT * FROM settings WHERE 1 = ?",array(1),"d","id");
    $photos=RunQuery ("SELECT * FROM photos WHERE 1 = ?",array(1),"d","id");
    echo json_encode(array("settings"=>$settings,"photos"=>$photos));
}
function getSetting($setting){
    $settings=RunQuery ("SELECT * FROM settings WHERE description = ?",array($setting),"s","id");
    if(sizeof($settings)==0) {return false;}
    else{return ReduceArray($settings);}
}
function setSetting($setting,$text,$date,$numeric){
    $settings=getSetting($setting);
    if(!$settings) {
        $query="INSERT INTO settings(description,text_value,date_value,numeric_value,user_modified) values (?,?,?,?,?)";
        return ExecuteQuery ($query,array($setting,$text,$date,$numeric,$_SESSION ['username']));
    }
    else {
        if($text>''&&$text!==$settings['text_value']){
            $query="UPDATE settings SET text_value = ?,user_modified=? WHERE id = ?";
            return ExecuteQuery ($query,array($text,$_SESSION ['username'],$settings['id']));
        }
        elseif($text==''&&$settings['text_value']>""){
            $query="UPDATE settings SET text_value = NULL,user_modified=? WHERE id = ?";
            return ExecuteQuery ($query,array($_SESSION ['username'],$settings['id']));
        }
        elseif ($date>''&&$date!==$settings['date_value']){
            $query="UPDATE settings SET date_value = ?,user_modified=? WHERE id = ?";
            return ExecuteQuery ($query,array($date,$_SESSION ['username'],$settings['id']));
        }
        elseif($numeric>''&&$numeric!==$settings['numeric_value']){
            $query="UPDATE settings SET numeric_value = ?,user_modified=? WHERE id = ?";
            return ExecuteQuery ($query,array($numeric,$_SESSION ['username'],$settings['id']));
        }
        else {
            return 'No update made';
        }
    }
    return true;
}
// ============================================================ Users ==========
if($Action=="getUsers"){
    emergencyStart();
    $userroles = userroles();
    if($userroles['users']){
        $query="SELECT * FROM users WHERE 1 = ?";
        $users=RunQuery ($query,array(1),"d","id");
        $cards="";
        if(sizeof($users)>0){
            $form='<form>
                    <legend>Enter their username and email address.</legend>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input class="mdl-textfield__input" type="text" id="fmNewUserName" required>
                        <label class="mdl-textfield__label" for="fmNewUserName">UserName e.g. Kizzie</label>
                        <span class="mdl-textfield__error">A username is required</span>
                    </div>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input class="mdl-textfield__input" type="email" id="fmNewUserEmail" required>
                        <label class="mdl-textfield__label" for="fmNewUserEmail">Email Address</label>
                        <span class="mdl-textfield__error">An email address is required</span>
                    </div>
                    <div id="dResponse" class="error"></div>
                    <input type="text" style="margin-left: -1000px;" onfocus="createNewUser();"/>
                </form>';
            $cards.=createCard(false
                              ,'Create a new user'
                              ,$form
                              ,'<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="createNewUser();">Create</button>',4,0,false);
            foreach($users as $key=>$user){
                $c1='';
                $c2='';
                $cNews='';
                $c3='';
                $c4='';
                if($user['admin_users']=='Y') $c1="checked";
                if($user['admin_news']=='Y') $cNews="checked";
                if($user['admin_events']=='Y') $c2="checked";
                if($user['admin_links']=='Y') $c3="checked";
                if($user['inactive']=='Y') $c4="checked";
                $id=$user['id'];
                $form= '<form>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="fText mdl-textfield__input" type="email" data-id="'.$id.'" data-fieldname="emailaddress" id="fmUserEmail_'.$user['id'].'" value="'.$user['emailaddress'].'" required="true">
                                <label class="mdl-textfield__label" for="fmUserEmail_'.$id.'">Email Address</label>
                                <span class="mdl-textfield__error">An email address is required</span>
                            </div>
                            <label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fmUserusers_'.$id.'">
                                <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-id="'.$id.'" data-fieldname="admin_users" id="fmUserusers_'.$id.'" '.$c1.'>
                                <span class="mdl-checkbox__label">Users</span>
                             </label>
                            <label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fmUsernews_'.$id.'">
                                <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-id="'.$id.'" data-fieldname="admin_news" id="fmUsernews_'.$id.'" '.$cNews.'>
                                <span class="mdl-checkbox__label">News</span>
                             </label>
                            <label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fmUserevents_'.$id.'">
                                <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-id="'.$id.'" data-fieldname="admin_events" id="fmUserevents_'.$id.'" '.$c2.'>
                                <span class="mdl-checkbox__label">Events</span>
                             </label>
                            <label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fmUserlinks_'.$id.'">
                                <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-id="'.$id.'" data-fieldname="admin_links" id="fmUserlinks_'.$id.'" '.$c3.'>
                                <span class="mdl-checkbox__label">Attachments</span>
                             </label>
                            <label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fmUserinactive_'.$id.'">
                                <input type="checkbox" class="fCheckbox mdl-checkbox__input" data-id="'.$id.'" data-fieldname="inactive" id="fmUserinactive_'.$id.'" '.$c4.'>
                                <span class="mdl-checkbox__label">Lock out</span>
                             </label>
                        </form>';
                $cards.=createCard(false
                                  ,$user['username']
                                  ,$form
                                  ,'<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored send" data-id="'.$user['id'].'">Reset Password</button>'
                                  .'<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="resendNewUser('.$user['id'].');">Re-send</button>'
                                  ,4
                                  ,0
                                  ,false);
            }
        }
    } else {
        $cards= createCard (false,'Sorry',"You do not have permission to Manage Users",false,3,0,false);
    }
    echo $cards;
}
if($Action=="updateUser"){
    $query="UPDATE users SET ".$_POST['fieldname']." = ?,user_modified=? WHERE id = ?";
    ExecuteQuery ($query,array($_POST["value"],$_SESSION ['username'],$_POST["id"]));
}
if($Action=="writeResetEmail"){
    $users=RunQuery ("SELECT * FROM users WHERE 1 = ?",array(1),"d","id");
    $user=false;
    foreach($users as $key=>$value){
        if($value['id']==$_POST["userid"])$user=$value;
    }
    echo json_encode( array ("message"=>resetPasswordLink(getRandomKey($users),$_POST["url"],$user)
                            ,"username"=>$user['username']
                            ,"emailaddress"=>$user['emailaddress']
                            ));
}
if($Action=="getfmReset"){
    $query="SELECT * FROM users WHERE resetcode = ?";
    $buttons=false;
    $user=RunQuery ($query,array($_POST["id"]),"d","id");
    if(sizeof($user)==0){$form="This is not a valid reset link";}
    if(sizeof($user)>1){$form="This is not a valid reset link";}
    if(sizeof($user)==1){
        $user=ReduceArray($user);
        if($user['resetexpires']<date("Y-m-d H:i:s")){
            $form="This reset link has expired. Please ask to be sent a new link. They are valid for 24 hours.";
        } else {
            $form="<p><b>This is the reset form for ".$user["username"]."</b> Please enter a new password. Please do not use your online banking password - this site is not particularly secure.</p>";
            $form.= '<form id="fmLogon">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" maxlength=20 type="password" id="iPassword">
                            <label class="mdl-textfield__label" for="iPassword">Password</label>
                            <span class="mdl-textfield__error" id="iError">Please enter your new password</span>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" maxlength=20 type="password" id="iPassword2">
                            <label class="mdl-textfield__label" for="iPassword2">Re-enter Password</label>
                            <span class="mdl-textfield__error" id="iError2">Please re-enter your new password</span>
                        </div>
                    </form>';
            $buttons ='<button  class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="validate('.$user['id'].')">Save</button>';
        }
    }
    echo createCard(false,'Reset Password',$form,$buttons,6,0,false);
}
if($Action=="resetPassword"){
    $query="UPDATE users SET resetpassworddate=NOW(),password=?,resetcode=null,resetexpires=null WHERE id = ?";
    if(phpversion().substr (0,3)>'5.2'){
        $pw = password_hash($_POST["pw"], PASSWORD_DEFAULT);
    }
    else{
        $pw = md5($_POST["pw"]);
    }
    ExecuteQuery($query,array($pw,$_POST["id"]));
    log_off();
}
if($Action=="createExpiredUser"){
    echo createExpiredUser($_POST["username"],$_POST["email"]);
}
if($Action=="createUser"){
    $query="SELECT * FROM users where 1 = ?";
    $users=RunQuery ($query,array(1),"s","id");
    $ok=true;
    $message='Sorry, we cannot register your details at this time.';
    if(sizeof($users)>0){
        foreach($users as $key=>$user){
            if($user['username']==fGet('username')) {$ok=false;}
            if($user['emailaddress']==fGet('emailaddress')) {$ok=false;}
        }
    }
    if($ok){
        echo 'We may be able to save. Ref ';
        if(phpversion().substr (0,3)>'5.2'){
            $pw = password_hash(fGet('password'), PASSWORD_DEFAULT);
        } else{
            $pw = md5(fGet('password'));
        }
        ExecuteQuery ("INSERT INTO users (username,password,emailaddress,inactive,user_modified) VALUES (?,?,?,?,?)"
                     ,array(fGet('username'),$pw,fGet('emailaddress'),'Y','Website'));
        $user = ReduceArray(RunQuery("SELECT * FROM users WHERE username = ?",array(fGet('username')),'s','id'));
        echo $user['id'];
    }
    else{
        echo $message;
    }
}
function createFirstUser(){
    $query="SELECT * FROM users where 1 = ?";
    $users=RunQuery ($query,array(1),"s","id");
    if(phpversion().substr (0,3)>'5.2'){
        $pw = password_hash('rafferty', PASSWORD_DEFAULT);
    } else {
        $pw = md5('rafferty');
    }
    if(sizeof($users)==0){
        $query='INSERT INTO users (username,password,emailaddress,admin_users,admin_events,admin_links,admin_news,resetpassworddate) values (?,?,?,?,?,?,?,NOW())';
        return ExecuteQuery($query
                    ,array('miranda',$pw,'miranda@merrymeet.org.uk','Y','Y','Y','Y')
                    );
//        $query='INSERT INTO users (username,password,emailaddress,admin_users,admin_events,admin_links,admin_news,resetpassworddate) values (?,?,?,?,?,?,?,NOW())';
//        ExecuteQuery($query
//                    ,array('hannah',md5('charles18' ),'hannah@rectorydesigns.co.uk','Y','Y','Y','Y')
//                    );
    }
    else {
        $query="UPDATE users SET password = ? WHERE username = 'miranda' AND password <> ?";
        return ExecuteQuery($query, array($pw,$pw));
    }
}
function createExpiredUser($username,$email){
    $query="SELECT * FROM users where username = ? or emailaddress=?";
    $users=RunQuery ($query,array($username,$email),"ss","id");
    if(sizeof($users)==0){
        $query='INSERT INTO users (username,password,emailaddress,resetcode,resetpassworddate) '
                . 'values (?,?,?,?,DATE_ADD(NOW(),INTERVAL 5 DAY))';
        $key=getRandomKey(false);
        if(phpversion().substr (0,3)>'5.2'){
            $pw = password_hash('ajr7)+y4UU', PASSWORD_DEFAULT);
        } else{
            $pw = md5('ajr7)+y4UU');
        }
        ExecuteQuery($query
                    ,array($username,$pw,$email,$key));
        echo json_encode( array ("message"=>resetPasswordLink(getRandomKey($users),$_POST["url"],$user)
                                ,"username"=>$user['username']
                                ,"emailaddress"=>$user['emailaddress']
                                ,'faulty'=>false
                                ));
    }
    else{
        echo json_encode( array ("message"=>'We already have a user with that username or emailaddress'
                                ,"username"=>$username
                                ,"emailaddress"=>$email
                                ,'faulty'=>true
                                ));
    }
}
function getRandomKey($users){
    if(!$users) $users=RunQuery ("SELECT * FROM users WHERE 1 = ?",array(1),"d","id");
    $keys=array();
    if(sizeof($users)>0){
        foreach($users as $key=>$value){
            // collect all the reset codes already in use
            if($value['resetcode']>0){$keys[]=$value['resetcode'];}
        }
    }
    $key=rand(100000000,999999999);
    // Ensure that it is unique
    while(in_array($key,$keys)) {$key=rand(100000000,999999999);}
    return $key;
}
function resetPasswordLink($key,$url,$user){
    if($user['resetcode']>""&&$user['resetexpires']<getdate()){
        // Already expired and overdue - give them another 24 hours
        $key=$user['resetcode'];
        $query="UPDATE users SET resetexpires=DATE_ADD(NOW(),INTERVAL 1 DAY) WHERE id = ?";
        ExecuteQuery($query,array($user['id']));
    } else {
        $query="UPDATE users SET resetcode = ?, resetexpires=DATE_ADD(NOW(),INTERVAL 1 DAY) WHERE id = ?";
        ExecuteQuery($query,array($key,$user['id']));
    }
    return 'Please click <a href="http://'.$url.'/passwords.php?id='.$key.'">this link</a> to reset your password.';
}
if($Action=="getWelcomeEmail"){
    $user=ReduceArray(RunQuery ("SELECT * FROM users WHERE id = ?",array($_POST["userid"]),"d","id"));
    $key="";
    if($user['resetcode']>""){
        $key=$user['resetcode'];
    }
    else{
        $key=getRandomKey(false);
    }
    echo json_encode( array ("message"=>resetPasswordLink($key,$_POST["url"],$user)
                            ,"username"=>$user['username']
                            ,"emailaddress"=>$user['emailaddress']
    ));
}
// ============================================================ Utilities ======
function createCard($media,$title,$text,$actions,$width,$order,$name){
    if(!$width) $width=3;
    $rtn='<div class="mdl-cell mdl-cell--'.$width.'-col mdl-card mdl-shadow--3dp"';
        if($name) $rtn.='name="'.$name.'"';
        $rtn.=' style="order:'.$order.'">';
    if($media){$rtn.='<div class="mdl-card__media"><img class="cld-responsive" data-src="'.$media.'" alt="Event"></div>';}
    if($title){$rtn.='<div class="mdl-card__title"><h4 class="mdl-card__title-text">'.$title.'</h4></div>';}
    if($text) {$rtn.='<div class="mdl-card__supporting-text">
                         <span class="mdl-typography--font-light mdl-typography--subhead">'.$text.'</span>
                      </div>';
    }
    if($actions){
        $rtn.='<div class="mdl-layout-spacer"></div>';
        $rtn.='<div class="mdl-card__actions">'.$actions.'</div>';
    }
    $rtn.= '</div>';
    return $rtn;
}
function writeTextField($parameters){
    // must have id and label in the parameters array
    $rtn = '<div class="mdl-textfield mdl-js-textfield';
    if(array_key_exists('class', $parameters)){$rtn.=' '.$parameters['class'];}
    $rtn.='">';
    if(array_key_exists('type', $parameters)&&$parameters['type']=="date"){
        $rtn.= '<input  class="fText mdl-textfield__input l-font"';
    } else {
        $rtn.= '<input  class="fText mdl-textfield__input"';
    }
        if(array_key_exists('type', $parameters)){$rtn.=' type="'.$parameters['type'].'"';}
        else{$rtn.=' type="text"';}
    $rtn.= '        id="'.$parameters['id'].'"';
        if(array_key_exists('dataid', $parameters)){$rtn.=' data-id="'.$parameters['dataid'].'"';}
        if(array_key_exists('datafieldname', $parameters)){$rtn.=' data-fieldname="'.$parameters['datafieldname'].'"';}
        if(array_key_exists('value', $parameters)){$rtn.=' value="'.$parameters['value'].'"';}
        if(array_key_exists('maxlength', $parameters)){$rtn.=' maxlength='.$parameters['maxlength'];}
        if(array_key_exists('required', $parameters)){$rtn.=' '.$parameters['required'];}
    $rtn.= '>';
    $rtn.= '    <label class="mdl-textfield__label" for="'.$parameters['id'].'">'.$parameters['label'].'</label>';
    if(array_key_exists('error', $parameters)){$rtn.='<span class="mdl-textfield__error">'.$parameters['error'].'</span>';}
    $rtn.= '</div>';
    return $rtn;
}
function writeCheckBox($parameters){
    // must have id and label in the parameters array
    $rtn = '<label class="l-wrap mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="'.$parameters['id'].'">';
    $rtn.= '    <input type="checkbox" class="fCheckbox mdl-checkbox__input"';
    if(array_key_exists('dataid', $parameters)){$rtn.=' data-id="'.$parameters['dataid'].'"';}
    $rtn.= ' id="'.$parameters['id'].'"';
    if(array_key_exists('datafieldname', $parameters)){$rtn.=' data-fieldname="'.$parameters['datafieldname'].'"';}
    if(array_key_exists('value', $parameters)){
        if($parameters['value']=="Y"||$parameters['value']=="checked"){$rtn.=' checked';}
    }
    $rtn.= '>';
    $rtn.= '<span class="mdl-checkbox__label">'.$parameters['label'].'</span>';
    $rtn.= '</label>';
    return $rtn;
}
function writePhotosSelect($value,$dataid){
    $query="SELECT * FROM photos WHERE 1 = ?";
    $photos=RunQuery ($query,array(1),"d","id");
    $rtn ='<select class="fSelect" data-fieldname="photoid" data-id='.$dataid.'>';
        if($value==""){
            $rtn.='<option value = "0" selected>No Photo</option>';
        } else {
            $rtn.='<option value = "0">No Photo</option>';
        }
        foreach($photos as $k=>$v){
            $optionvalue = 'value="'.$v['id'].'"';
            if($value==$k){$optionvalue.=" selected";}
            $rtn.='<option '.$optionvalue.'>'.$v['title'].'</option>';
        }
    $rtn.='</select>';
    return $rtn;
}
function writeLinkTypesSelect($value){
    $query="SELECT * FROM att_types WHERE 1 = ?";
    $atypes=RunQuery ($query,array(1),"d","id");
    $rtn ='<select class="fSelect" data-fieldname="atid">';
        foreach($atypes as $k=>$v){
            $optionvalue = 'value="'.$v['id'].'"';
            if($value==$k){$optionvalue.=" selected";}
            $rtn.='<option '.$optionvalue.'>'.$v['description'].'</option>';
        }
    $rtn .='</select>';
    return $rtn;
}
function writeAnchor($href,$text,$iconName){
    if($iconName){
        $icon='<i class="material-icons">'.$iconName.'</i>';
        return '<a href="'.$href.'" class="mdl-button mdl-js-button mdl-button--primary">'.$text.$icon.'</a>';
    } else {
        return '<a href="'.$href.'" class="mdl-button mdl-js-button mdl-button--primary">'.$text.'</a>';
    }
}
function writeButton($attrString,$text,$iconName,$class){
    $icon='';
    if($iconName) $icon='<i class="material-icons">'.$iconName.'</i>';
    $classes='mdl-button mdl-js-button mdl-button--primary';
    if($class) $classes.=' '.$class;
    return '<button type="button" class="'.$classes.'" '.$attrString.'>'.$text.$icon.'</button>';
}
/* TODO
 *
 * Contact us form & send email
 * User admin - create a new user
 * User admin - send email to new user
 * Settings - number of cells across (3 or 4)
 * Settings - extra page elements such as logos or images
 * Logo loader
 * Logo manager
 *
 */
?>
