<?php

/**
 * Simple visitor counter
 * --
 * @author 		Jichiuo <zeng78 at gmail dot com>
 * @version 	1.0
 * @date 		2022-01-01
 * in your homepage you can use the following method to use it
 * <script type="text/javascript" src="vcounter.php?p=js"></script>     
 */

$vst_id = "";               // vistor id - session_id()
$filename = 'vstCnt.txt';   // the file store the online visitors count,the first line of this file is ip,timestamp,visitor_id
$timeon = 180;              // number of secconds to keep a user online , 3 mins here
$session_timeout = 1440;    // number of secconds to keep a session alive, 24 mins here
$delimiter = ',';           // characters used to separate the IP, time stamp and vistor id
$newVst = 0;                // if the visitor is new, this variable will be set to 1
$online = 0;                // number of online users
$today  = 0;                // total number of visitors today
$total  = 0;                // total number of visitors

/* if you find your today count is not correct, 
 * it may cause by the server timezone is different with your local timezone
 * you can set the timezone to your local timezone by the following code
 * and then you can get the correct count
 */
//set timezone
//date_default_timezone_set('Asia/Singapore');

//get visitor id from cookie
if (!isset($_COOKIE['vst_unique_id'])) {
    $newVst = 1;
    $vst_id = uniqid();
    //set cookie and will expire after $session_timeout,if user disable cookie, the counter will ignore it  
    setcookie('vst_unique_id', $vst_id, time() + $session_timeout, '/');
} else {
    $vst_id = $_COOKIE['vst_unique_id'];
}
// check if the file from $filename exists and is readable
if (is_readable($filename)) {
    //read the file and map to array
    $csv = array_map('str_getcsv', file($filename));
    array_walk($csv, function (&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
    });
    array_shift($csv);
    $lstVstId = array_column($csv, 'visitor_id');
    $lstTimeStamp = array_reverse(array_column($csv, 'timestamp'));
} else {
    if (is_writable($filename)) {
        // if the file does not exist, create it
        $fp = fopen($filename, 'w');
        if (fwrite($fp, "ip,timestamp,visitor_id") === FALSE) {
            echo "Cannot write to file ($filename)";
        }
        fclose($fp);
    } else {
        // the file exists but is not writable
        echo "The file $filename is not writable";
    }
}
//check if the vistor id already in the lstVstId array
if (in_array($vst_id, $lstVstId)) {
    $newVst = 0;
}
//set online and total
//loop the lstTimestamp to check if the current time minus the time stamp is less than $timeon
foreach ($lstTimeStamp as $value) {
    if ((time() - intval($value)) < $timeon) {
        $online++;
        $today++;
    } else {
        //if read the value is greater than timeon value then all others in array will be greater than timeon value
        if (date('m/d/Y', time()) == date('m/d/Y', intval($value))) {
            $today++;
        } else {
            break;
        }
    }
}
$online = $online + $newVst; // now how many users online , approximate not accurate
if ($online == 0) {
    $online = 1;
}
$today = $today + $newVst; //today visitors
if ($today == 0) {
    $today = 1;
}
$total = count($lstVstId) + $newVst;  // total visitors
if ($total == 0) {
    $total = 1;
}
if ($newVst > 0) {
    // log the IP , timestamp and visitor id to the file
    $strNewLine = getUserIP() . $delimiter . time() . $delimiter . $vst_id;
    // write data in $filename
    if (!file_put_contents($filename, $strNewLine . PHP_EOL, FILE_APPEND | LOCK_EX)) {
        $strOut = 'Error: Recording file not exists, or is not writable';
    }
}

// the HTML code with data to be displayed
$strOut = '<span id="vstCnt"> Online: ' . $online . ' Today: ' . $today . ' Total: ' . $total . '</span>';

// if access from <script>, with GET 'p=js', adds the string to return into a JS statement
// in this way the script can also be included in .html files
if (isset($_GET['p']) && $_GET['p'] == 'js') {
    $strOut = "document.write('$strOut');";
}
// output /display the result
echo $strOut;

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}
