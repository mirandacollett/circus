<?php
/* Works with updateCal.html as run from a Circus website. */
/* One database table: calendars */
/* For each new usage:
 *      Ensure that the Google Calendar Owner is set up on the Google APIs console
 *      at https://console.developers.google.com
 *      1. create a project
 *      2. Select the Calendar API
 *      3. Create an API key. Set the production URL, http://www.eventingsouthwest.co.uk and localhost:port as the URLs
 *      4. Create an oAuth consent with the same URLs
 *      5. Populate calendars table using SQL. All fields required.
 *      6. Ensure that all people like to maintain the calendar have been given Update rights (Share) by the Calendar's owner.
 
*/
    require_once ("connx.php");
    $return=array(
         'active_yn'=>'Y'
        ,'date_modified'=>''
        ,'equine_affairs_url'=>''
        ,'events_to_create'=>array()
        ,'google_api_key'=>''
        ,'google_calendar_id'=>''
        ,'google_client_id'=>''
        ,'google_scope' => "https://www.googleapis.com/auth/calendar"
        ,'maxAdditions'=>50
        ,'owner'=>'owned'
        ,'preamble'=>''
        ,'yql_url'=>''
    );
    $s = runQuery ("SELECT '0' id
                        ,MAX(CASE WHEN s.description = 'Equine Affairs List' THEN s.text_value ELSE NULL END) equine_affairs_url
                        ,MAX(CASE WHEN s.description = 'Google API Key' THEN s.text_value ELSE NULL END) google_api_key 
                        ,MAX(CASE WHEN s.description = 'Google Calendar ID' THEN s.text_value ELSE NULL END) google_calendar_id
                        ,MAX(CASE WHEN s.description = 'Google Client ID' THEN s.text_value ELSE NULL END) google_client_id
                        ,MAX(CASE WHEN s.description = 'Map Label' THEN s.text_value ELSE NULL END) preamble
                  FROM settings s WHERE 1= ?",array(1),"d","id");
    if(sizeof($s)>0) {
        $return['equine_affairs_url']=$s['0']['equine_affairs_url'];
        $return['google_api_key']=$s['0']['google_api_key'];
        $return['google_calendar_id']=$s['0']['google_calendar_id'];
        $return['google_client_id']=$s['0']['google_client_id'];
        $return['preamble']=$s['0']['preamble'];
        $return['EA_ID'] = substr($s['0']['equine_affairs_url'], strpos($s['0']['equine_affairs_url'],'?LocationID=') + 12);
        $return['EA_ID'] = substr($return['EA_ID'],0,strpos($return['EA_ID'],'&from'));
        $return['yql_url']="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D'https%3A%2F%2Fwww.equineaffairs.com%2FRemoteLocationEventList.aspx%3FLocationID%3D".$return['EA_ID']."%26from%3Drl'&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
        echo json_encode($return);
    }
    else echo json_encode(false);
