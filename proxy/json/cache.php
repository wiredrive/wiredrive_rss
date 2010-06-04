<?php

/*
 * Wiredrive RSS Convert to JSON Example
 * 
 * Example file for converting RSS to JSON to get around same 
 * domain restrictions for Flash and Javascript.  
 *
 * This is about as simple as possible.  Get the RSS feed from Wiredrive
 * convert it to JSON and send it on from the local server.
 *
 */

/*********************************************************************************
 * Copyright (c) 2010 IOWA, llc dba Wiredrive
 * Author Daniel Bondurant
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ********************************************************************************/

include_once('rssToJson.php');

/*
 * start a session to save the contents of the rss feed
 */
session_start();

/*
 * URL for the RSS feed
 * Change this to the RSS feed you would like to proxy 
 * and transform to JSON on your server.  You can also
 * optinoally add the RSS feed to the request URL
 */
$rss = 'http://www.wdcdn.net/rss/presentation/library/client/merc/id/84b8b5e27e9f55c7417848abb3327240';
if ($_GET['feed']) {
    $rss = filter_input(INPUT_GET,'feed',FILTER_VALIDATE_URL);
}

/*
 * Make sure the RSS Url is set
 */
if (!$rss) {
    throw new Exception('RSS feed is not a valid URL');
}

/*
 * create a MD5 of the URL for caching in the sessions
 */
$json_md5 = md5($rss);

/*
 * check if this feed exists already in the session and pull it out
 */
if (isset($_SESSION[$json_md5])){

    /*
     * Get the json data from the session
     */
    $json = $_SESSION[$json_md5];
}

/*
 * read the remote RSS feed from the Wiredrive server 
 */
if (!isset($json)){
    $contents = file_get_contents($rss,'r');

    /*  
     * Make sure the RSS feed was opened.  Check the php manual
     * page on opening remote files if this fails
     *
     * @link: http://www.php.net/manual/en/features.remote-files.php
     */
    if (!$contents) {
        throw new Exception('Unable to retrieve RSS feed');
    }

    /*
     * load contents into Simple XML.
     * At this point the RSS feed is converted into a SimpleXML object
     */
    $xml = simplexml_load_string($contents);
    
    /*
     * Convert the XML to Json
     */
    $convert = new rssToJson($xml);
    $json = $convert->getJson();
}

/*
 * Save the json parsed data to a session for caching using MD5 of the 
 * URL as the session key
 */
$_SESSION[$json_md5] = $json;

/*
 * Check if a callback function was provided with the request Url.
 * Default function is processResponse() 
 */
$callback = 'processResponse';
if ($_GET['callback']) {
    $callback = filter_input(INPUT_GET,'callback',FILTER_SANITIZE_STRING);
}

/*
 * Wrap the json in the callback
 */
$output = $callback ."(" . $json .");";

/*
 * check if the client will accept it gzip using the server headers
 * and if the server has gzip installed
 */
$HTTP_ACCEPT_ENCODING = NULL;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])
        && function_exists('gzencode') ) {
    $HTTP_ACCEPT_ENCODING = $_SERVER['HTTP_ACCEPT_ENCODING'];
}
    
/*
 * Check if the client wants x-gzip or gzip headers
 */      
$encoding = FALSE;
if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== FALSE ) {
    $encoding = 'x-gzip';
} elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== FALSE ) {
    $encoding = 'gzip';
}
         

/*
 * gzip compress the output and send header if gzip encoding 
 * is possible
 */
if ($encoding != FALSE) {
    header('Content-Encoding: '. $encoding); 
    $output = gzencode($output, 9);
}

/*
 * Send to the user with headers
 */
header('Content-Type: text/plain; charset=UTF-8');

/*
 * send expires and cache headers
 */
$maxAge = 1800;
$expires = gmdate("D, d M Y H:i:s", time() + $maxAge) . " GMT";
header('Pragma: public');
header('Cache-Control: max-age='.$maxAge);
header("Expires: $expires");

/*
 * output the feed
 */
echo $output; 

