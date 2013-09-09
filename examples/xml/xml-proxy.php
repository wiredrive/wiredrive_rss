<?php
/*****************************************************************************
 * Copyright (c) 2013 IOWA, llc dba Wiredrive
 * Author Wiredrive
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
 ****************************************************************************/

/**
    Example file to proxy an RSS feed and output it as XML.

    Defaults to serving a hard-coded RSS url as XML. For a little bit of added
    versitility, we add the ability to specify a specific feed url via a "key":

    http://example.com/xml-proxy.php
    http://example.com/xml-proxy.php?feed=wd

    Note that "feed" is a key string, **not** an RSS url. This provides a whitelist
    of known feeds that can be returned so that this proxy is not completely open for
    any user to request anything from anywhere.
 */
 
// Set up our whitelist
$URLS = array(
   //the default RSS feed that will be used if no specific feed is requested
    'default' => 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d',

    //a specific presentation, keyed by 'wd'
    //(obviously, the same presentation as the default, but you get the idea)
    'wd' => 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d'
);

/* set up base dir */
$basePath = realpath(dirname(__FILE__) . '/../../');
set_include_path(get_include_path() . PATH_SEPARATOR . $basePath);

/* load dependencies */
require_once('src/dependency.php');
date_default_timezone_set('America/Los_Angeles');

//try and GET the feed key being requested
$feed = filter_input(INPUT_GET, 'feed', FILTER_SANITIZE_STRING); //the feed key
$url = null;

if (array_key_exists($feed, $URLS)) {
    $url = $URLS[$feed];
} else if (!is_null($feed)) {
    //request for a specific feed that isn't in our whitelist. Error
    header('Feed not found', true, 400);
    exit;
} else {
    //No feed givin. Default to the (ahem) "default" feed.
    $url = $URLS['default'];
}

/* config options to send to the feed manager class */
$config = array(
    'feedUrl'   => $url,
    'format'    => 'xml', // <-- we want XML data
);

/* get the feed */
$feedManager = new Feed_Manager($config);
$output = $feedManager->process();

/* determine the expires time for this feed */   
$ttl = $feedManager->getParser()->getProperty('ttl');
$expires = gmdate("r", strtotime('+'. $ttl .' minutes'));    

/* send 304 headers if the client cache is not stale */
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    $modifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if ((time() - $ttl ) <= $modifiedSince) {
        header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified', true, 304);
        exit;
    }
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
    $output = gzencode($output, 9);
} 

header("Content-Length: ". strlen($output));

/* send cache headers based on the ttl */
header("Expires: $expires");
header('Cache-Control: max-age=86400, public');
header("Last-Modified: " . gmdate("r"));
header('Content-type: text/xml'); // <-- We are sending back XML data!

echo $output;
