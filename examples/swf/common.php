<?php

/*********************************************************************************
 * Copyright (c) 2010 IOWA, llc dba Wiredrive
 * Authors Daniel Bondurant
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
 * Get RSS feed from URL
 * Change this to the RSS feed you would like to proxy on your server
 */
$rss = filter_input(INPUT_GET, 'rss', FILTER_VALIDATE_URL);

/**
 * Get Filename to load into flash on initial page load from URL
 */
$fileName = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);

/*
 * create a MD5 of the URL for caching in the sessions
 */
$sessionKey = 'rss';

/*
 * check if this feed exists already in the session and pull it out
 */
if (isset($_SESSION[$sessionKey])){
    $contents = $_SESSION[$sessionKey];
}


/*
 * read the remote RSS feed from the Wiredrive server 
 */
if (!isset($contents)){
    $contents = file_get_contents($rss,'r');
}
 
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
 * Save the feed to a session for caching using MD5 of the 
 * URL as the session key
 */
$_SESSION[$sessionKey] = $contents;

/*
 * load contents into Simple XML.
 * At this point the RSS feed is converted into a SimpleXML object
 */
$xml = simplexml_load_string($contents);

/* 
 * start the item loop
 */
foreach ($xml->channel->item as $item) {

    /*
     * Find the filename in the RSS feed
     */
    if ($fileName !== (string) $item->title) {
        continue;
    }

    /*
     * Get nodes in the media: namespace
     * This is where the credit types, credits, thumbnails and
     * main content objects are stored
     */
    $media = $item->children('http://search.yahoo.com/mrss/');
    break;
    
} // end item loop    

