<?php
    session_start();
    require_once ($_SERVER["DOCUMENT_ROOT"]."/client/usefulness.php");
    // Create database connections
    $UseUTF8=true;
    function UsesPDO () {true;}
    function WriteMessage(&$message,$text) {
        $TimeNow=microtime();
        $comps = explode(' ', $TimeNow);
        $mytime=sprintf('%d%03d', $comps[1], $comps[0] * 1000);
        $message.=date('H:i:s')." ".$text."<br>";
    }
    function getconnection() {
        $_connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (!$_connection->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $_connection->error);
        }
        if ($_connection -> connect_error) {
                die('Connect Error: ' . $_connection->connect_error);
            }
        return $_connection;
    }
    function getPDOconnection() {
        try {
            $connectors = getconnectors();
            $host=$connectors["DB_HOST"];
            $dbname=$connectors["DB_NAME"];
            $user=$connectors["DB_USER"];
            $pass=$connectors["DB_PASSWORD"];
            $dbc=new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass); // Original
            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            return $dbc;
        }
        catch (PDOException $err) {
            echo $err->getMessage();
        }
    }
    function getconnectors() {
        $myarray=array('DB_HOST' =>DB_HOST
                      ,'DB_USER' =>DB_USER
                      ,'DB_PASSWORD' =>DB_PASSWORD
                      ,'DB_NAME' =>DB_NAME
                      );
        return $myarray;
    }
    class connectDB  {
        private $Config;
        private $MyConn;
        private $UseUTF8;
    function __construct() {
        $this->Config = new controllerconfig();
        $this->MyConn = mysqli_connect(
                   $this->Config->getDB_HOST()
                  ,$this->Config->getDB_USER()
                  ,$this->Config->getDB_PASSWORD()
                  ,$this->Config->getDB_NAME())
            or die('Error connecting to database.');
      }
    function __destruct() {$dbc=null;}
    function Connection() {
        if ($this->MyConn == null) {
        $this->Config = new controllerconfig();
        $this->MyConn = mysqli_connect(
                   $this->Config->getDB_HOST()
                  ,$this->Config->getDB_USER()
                  ,$this->Config->getDB_PASSWORD()
                  ,$this->Config->getDB_NAME())
            or die('Error connecting to database.');}
        if ($this->UseUTF8) $this->MyConn ->set_charset("utf8");
        return $this->MyConn;} 
    } // End of class
function QueryToArray($query,$pk,$arrayindex) {
    global $returnarray;
    $message="";
    $returnarray=null;
    $mysqlnd = function_exists('mysqli_fetch_all');
    try{
        $message.="Using PDO<br>";
        $message.="Query is $query<br>";
        $dbc=getPDOconnection();
        //$params = array(':id' => $pk);
        $params = array( $pk);
        //echo print_r($params);
        $statement = $dbc->prepare($query);
        $statement->execute($params);
        $counter=0;
        while($row=$statement->fetch(PDO::FETCH_ASSOC)) 
            {$returnarray[$row[$arrayindex]]=$row;$counter++;}
        $message.="Counter = $counter<br>";
        }
        catch(PDOException $e) {echo $e->getMessage();} 
    if (sizeof($returnarray>0)) {return $returnarray;}
}
function RunQuery ($query,$params,$types,$arrayindex) {
    global $returnarray;
    $args =array();
    foreach($params as $k => &$arg) {$args[$k] = &$arg;} 
    $returnarray=null;
    try{$dbc=getPDOconnection();
        $statement = $dbc->prepare($query);
        $statement->execute($args);
        while($row=$statement->fetch(PDO::FETCH_ASSOC)) 
            {$returnarray[$row[$arrayindex]]=$row;}
        }
        catch(PDOException $e) {echo $e->getMessage();}    
    if (sizeof($returnarray>0)) {return $returnarray;}
}
function ExecuteQuery ($query,$params) {
    $args =array();
    foreach($params as $k => &$arg) {
        $x=&$arg;
        if ($x=="") $args[$k] = null;
        else $args[$k] = $x;
    }
    $dbc=getPDOconnection();
    $statement = $dbc->prepare($query);
    $statement->execute($args);
    return ReportArray($dbc->errorInfo());
}
function GetCharSet() {
    $dbc=getPDOconnection();
    return "PDO method: " . $dbc->character_set_name();
}
function ExecuteStatement ($query) {
    $message="";
    $dbc=getPDOconnection();
    $statement = $dbc->query($query) or die($message."Database execution failed: " . $statement->error);
    return $message;
}
function ReportArray($array) {
    $returnstring="";
    if (is_array($array)) {
        foreach ($array as $key=>$value) {
            if (is_array($value)) {
                 $returnstring.=$key." => array - - - - - - - - - - - - - <br>";
                 $returnstring.=ReportArray($value);
            }
            elseif(is_bool($value)) { if ($value){$returnstring.=$key." = boolean TRUE<br>";} else {$returnstring.=$key." = boolean FALSE<br>";}}
            else {$returnstring.=$key." = ".$value."<br>";}
        }
    }
    elseif (is_null($array)) {$returnstring="Not an array. Null";}
    elseif ($array="") {$returnstring="Not an array. Empty";}
    else {$returnstring=$array;}
    return $returnstring;
}
function ReduceArray($array) {
    $ReturnArray=array();
    if (sizeof($array)>0) {foreach($array as $key=>$value) {$ReturnArray=$value;}}
    return $ReturnArray;
}
function GetFromArray($array,$searchkey) {
    $ReturnValue="";
    if (is_array($array)) {
        foreach ($array as $key=>$value) {
            if ($key==$searchkey) {$ReturnValue=$value;}
            if (is_array($value)) {
                foreach ($value as $k=>$v) {
                    if ($k==$searchkey) {$ReturnValue=$v;}
                }
            }
        }
    }
    return $ReturnValue;
}
function getSchema($schema,$table) {
    $query ="SELECT lower(column_name) column_name ";
    $query.="     , data_type ";
    $query.="     , character_maximum_length ";
    $query.="     , CASE WHEN data_type = 'int' THEN 'd' WHEN data_type = 'double' THEN 'd' ELSE 's' END dtype ";
    $query.="     , is_nullable ";
    $query.="FROM INFORMATION_SCHEMA.COLUMNS  ";
    $query.="WHERE table_schema = ? ";
    $query.="AND   table_name = ? ";
    $query.="ORDER BY ordinal_position ";
    $params = array($schema,$table);
    return RunQuery ($query,$params,"ss","column_name");
}
    function logged_on()
        {return isset($_SESSION['userid']);}
    function log_on ($username,$password) {
        $rtn = array("loggedon"=>'N',"userid"=>false,'p1'=>false,'p2'=>false,'m'=>[]);
        $query = "SELECT id,password,username,emailaddress,resetpassworddate FROM users WHERE username = ? LIMIT 1";
        $user=RunQuery ($query,array($username),"s","id");
        if (sizeof($user)>0){
            $user = ReduceArray($user);
            $rtn['m'][]=$user;
            if(password_verify($password,$user['password'])){
                $_SESSION ['userid'] = $user['id'];
                $rtn['userid'] = $user['id'];
                $rtn['loggedon'] = 'Y';
                $_SESSION ['username'] = $user['username'];
            } else{
                $rtn['p1'] = $password;
                $rtn['p2'] = $user['password'];
                //$rtn['m'][] = password_hash();
            }
        }
        return $rtn;
    }
    function log_off(){
        unset($_SESSION['userid']);
        unset($_SESSION['username']);
    }
    function userroles(){
        $rtn=array("loggedon"=>logged_on(),"users"=>false,"events"=>false,"links"=>false,"news"=>false);
        if(logged_on()){
            $query = "SELECT * FROM users WHERE id = ?";
            $user=RunQuery ($query,array($_SESSION ['userid']),"d","id");
            $user=ReduceArray($user);
            if($user['admin_users']=="Y") $rtn['users']=true;
            if($user['admin_events']=="Y") $rtn['events']=true;
            if($user['admin_links']=="Y") $rtn['links']=true;
            if($user['admin_news']=="Y") $rtn['news']=true;
        }
        return $rtn;
    }
function emergencyStart(){
    $query = "SELECT * FROM users WHERE 1 = ?";
    $users=RunQuery ($query,array(1),"d","id");
    $pw = password_hash('rafferty', PASSWORD_DEFAULT);
    if(sizeof($users)==0){
        return ExecuteQuery('INSERT INTO users (username,password,emailaddress,admin_users,admin_events,admin_links,admin_news) values (?,?,?,?,?,?,?)'
                           ,array('Miranda',$pw
                           ,'miranda@merrymeet.org.uk','Y','Y','Y','Y'),'sssssss');
    }
    else{
        return ExecuteQuery("UPDATE users SET password = ? WHERE username = 'Miranda' AND password <> ?", array($pw,$pw));
    }
}
?>
