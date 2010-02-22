<?php

/*
 * Wiredrive RSS Caching
 * 
 * Example file for simple RSS caching to get around same domain 
 * restrictions for Flash and Javascript
 *
 * This file will get the RSS feed from the Wiredrive, save and 
 * serve from a local server.  Gzip will be used if it's available, to 
 * minimize the file size going out to the browser.
 *
 * Contents of the RSS feed are saved to a session so a request is not
 * going to the Wiredrive servers on every request.
 *
 */

/*********************************************************************************
 * Copyright 2010, Wiredrive
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

/*
 * start a session to save the contents of the rss feed
 */
session_start();

/*
 * URL for the RSS feed
 * Change this to the RSS feed you would like to proxy on your server
 */
$rss = 'http://www.wdcdn.net/rss/presentation/library/client/merc/id/84b8b5e27e9f55c7417848abb3327240';

/*
 * create a MD5 of the URL for caching in the sessions
 */
$rss_md5 = md5($rss);

/*
 * check if this feed exists already in the session and pull it out
 */
if (isset($_SESSION[$rss_md5])){
    $contents = $_SESSION[$rss_md5];
}

/*
 * read in the RSS feed from the Wiredrive server if it was 
 * not already in the session and save it to the session.
 */
if (!isset($contents)){
    $contents = file_get_contents($rss,'r');
    $_SESSION[$rss_md5] = $contents;
}


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
    $contents = gzencode($contents, 9);
}
                        
/*
 * send XML headers 
 */
header('Content-Type: text/xml; charset=UTF-8');

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
echo $contents; 

