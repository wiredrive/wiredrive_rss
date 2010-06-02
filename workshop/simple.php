<?php

/*
 * Wiredrive RSS Conver to JSON Example
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

/*
 * URL for the RSS feed
 * Change this to the RSS feed you would like to proxy 
 * and transform to JSON on your server
 */
$rss = 'http://www.wdcdn.net/rss/presentation/library/client/merc/id/84b8b5e27e9f55c7417848abb3327240';
if ($_GET['feed']) {
    $rss = filter_input(INPUT_GET,'feed',FILTER_VALIDATE_URL);
}

/*
 * Make sure the RSS Url is set
 */
if (!$rss) {
    echo "RSS feed is not a valid URL";
    exit;
}

/*
 * read the remote RSS feed from the Wiredrive server 
 */
$contents = file_get_contents($rss,'r');

/*
 * Make sure the RSS feed was opened.  Check the php manual
 * page on opening remote files if this fails
 *
 * @link: http://www.php.net/manual/en/features.remote-files.php
 */
if (!$contents) {
    echo "Unable to RSS feed";
    exit;
}

/*
 * Get the just the channel object 
 */
$channel = $xml->channel;

/*
 * Check if a callback function was provided
 * Default function is processResponse but should be
 * overriden in the GET string
 */
$callback = 'processResponse';
if ($_GET['callback']) {
    $callback = filter_input(INPUT_GET,'callback',FILTER_CALLBACK);
}

/*
 * json encode the simpleXML object
 */
$json = json_encode($channel);

/*
 * Wrap the json in the callback
 */
$output = $callback ."(" . $json .");";

/*
 * Send to the user with headers
 */
header('Content-Type: text/plain; charset=UTF-8');
echo $output;