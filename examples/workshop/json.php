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
    echo "Unable to retrieve RSS feed";
    exit;
}

/*
 * Check if a callback function was provided with the request Url.
 * Default function is processResponse() 
 */
$callback = 'processResponse';
if ($_GET['callback']) {
    $callback = filter_input(INPUT_GET,'callback',FILTER_SANITIZE_STRING);
}

/*
 * load contents into Simple XML.
 * At this point the RSS feed is converted into a SimpleXML object
 */
$xml = simplexml_load_string($contents);

/*
 * Start the response data array
 */
$response = array();

/*
 * Get the just the channel object 
 */
$channel = $xml->channel;

/*
 * Get any namespaces added to the RSS
 */
$ns = $xml->getNamespaces(TRUE);

/*
 * Cycle though the XML objects.  Conversion with json_encode will not
 * work because CDATA does not convert, and the media attributes are
 * difficult to pull out of json data
 */
foreach ($channel->children() as $element) {

    /*
    * Start the data array for this child
    */
    $elementData = array();

    /* 
     * Get the element name and start and array
     */
    $elementName = (string) $element->getName();

    /*
     * Add entities without children to the response array
     */    
    if ($element->count() == 0) {
        $name = $element->getName();
        $elementData[$name] = (string) $element;
        continue;
    }
    
    /*
     * Add entities with children to the reponse array
     */    
    foreach ($element->children() as $item) {
    
        /*
         * Get the name of the element
         */
        $name = $item->getName();
    
        /*
         * Add items without attributes to the response array
         */
        if (sizeof($item->attributes()) == 0) {
            $elementData[$elementName][$name] = (string) $item;  
            continue;      
        }
        
        /*
         * Add the attributes
         */
        foreach ($item->attributes() as $attribute) {
            $attributeName = $attribute->getName();
            $elementData[$elementName][$name][$attributeName] = (string) $attribute;  
        }    
        
    }

    /*
     * Cycle through any defined names spaces and 
     * extract the elements
     */
    foreach($ns as $name=>$namespace) {
        $nsChildren = $element->children($namespace);
        
        /*
         * Cycle through the elements defined for this namespace
         */  
        $count = array();
        foreach($nsChildren as $item) {

            /*
             * Get the name for this element
             */
            $name = $item->getName();

            /*
             * Make sure there is a counter for each item and start counting at 0
             */
            if (!$count[$name]) {
                $count[$name] = 0;
            }
            $i = $count[$name];
        
            /*
             * Add the attributes
             */
            foreach ($item->attributes() as $attribute) {
                $attributeName = $attribute->getName();
                $elementData[$elementName][$name][$i][$attributeName] = (string) $attribute;  
            }

            /*
             * Add the value of the element if it exists
             */
            $content = (string) $item;
            if (!empty( $content) ) {
                $elementData[$elementName][$name][$i]['content'] = $content;  
            }

            /*
             * increment the counter for this item
             */
            $count[$name]++;
        }
    } 
    
    $response['responseData'][] = $elementData;
}

$response['responseStatus'] = 200;

/*
 * JSON encode the response
 */
$json = json_encode($response);

/*
 * Make sure json encoding passed
 */
if (!$json || $json == 'null') {
    $error['responseStatus'] = 500;
    $json = json_encode($error);
}

/*
 * Wrap the json in the callback
 */
$output = $callback ."(" . $json .");";

/*
 * Send to the user with headers
 */
header('Content-Type: text/plain; charset=UTF-8');
echo $output;
