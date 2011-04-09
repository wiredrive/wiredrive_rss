<?php

/*
 * Wiredrive Flowplayer Playlist Example
 * 
 * This is an example document illustrating how to load a remote RSS 
 * feed and render its contents into an HTML page
 *
 * Please make sure to use the latest version of Flowplayer.
 * http://flowplayer.org/download/index.html
 *
 * This file will get the RSS feed from the Wiredrive, save and 
 * serve from a local server. 
 *
 * Contents of the RSS feed are saved to a session so a request is not
 * going to the Wiredrive servers on every request.
 *
 */

/*********************************************************************************
 * Copyright (c) 2010 IOWA, llc dba Wiredrive
 * Authors Adam Portilla, Drew Baker and Daniel Bondurant
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
$rss = 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d';

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
$_SESSION[$rss_md5] = $contents;

/*
 * load contents into Simple XML.
 * At this point the RSS feed is converted into a SimpleXML object
 */
$xml = simplexml_load_string($contents);

    /* 
     * Setup varibles to get the first item in the feed
     */
        $item = $xml->channel->item;
        
        /*
         * Get nodes in the media: namespace
         * This is where the credit types, credits, thumbnails and
         * main content objects are stored
         */
                 
        $media = $item->children('http://search.yahoo.com/mrss/');
        
        /*
         * Content URL for the first file and thumbnail from the media object 
         */

        $firstThumbURL = $media->thumbnail[0]->attributes()->url;
        $firstFile = $media->content->attributes()->url;

?>
<html>
<head> 
<title>Wiredrive Flowplayer Playlist Example</title>
<meta name="keywords" content="wiredrive, rss, mrss, example, Flowplayer" >
<meta name="description" content="This is an example document illustrating how to load a remote RSS feed and render its contents into an HTML page and play the videos using Flowplayer" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="Content-Language" content="en-US" >
<meta http-equiv="expires" content="Wed, 19 Feb 2020 16:46:30 GMT" >
<meta http-equiv="pragma" content="cache" >
<meta http-equiv="Cache-Control" content="cache" >
 
<!-- Control the dimensions of the page in mobile safari -->
<meta content="user-scalable=no, width=device-width, minimum-scale=1.0, maximum-scale=1.0" name="viewport">         

<link rel="stylesheet" type="text/css" href="style.css"> 

<!-- Include flowplayer JavaScript file. Provides Flash embedding and provides the Flowplayer API. -->
<script type="text/javascript" src="flowplayer/flowplayer-3.2.6.min.js"></script>

</head>
<body>
    <div class="companyLogo">Wiredrive Flowplayer Playlist Example<!-- Put your company Logo Here --></div>


		<!-- this div tag is where your Flowplayer will be placed. it can be anywhere -->
		<div id="player">
		      <!-- Anything in here will be used as the "splash" image -->      
            <div class="big-thumb" style="background-image: url(<?php echo $firstThumbURL; ?>)"></div>
		</div> 
	
		<!-- this will install flowplayer inside the "player" DIV tag. -->
		<script>
			$f("player", "flowplayer/flowplayer-3.2.7.swf", {
                clip: {
                    url: '<?php echo $firstFile; ?>',
                    autoPlay: true,
                    autoBuffering: true,
                    scaling: 'fit'
                }
            });
		</script>


  <div class="wditems" id="itemcontainer">
    <?php
    /* 
     * start the item loop
     */
    foreach ($xml->channel->item as $item) {
    
        /*
         * Get nodes in the media: namespace
         * This is where the credit types, credits, thumbnails and
         * main content objects are stored
         */
        $media = $item->children('http://search.yahoo.com/mrss/');
        
        
        /*
         * Content URL from media object
         */
        $contentUrl = $media->content->attributes()->url;
        
    ?>
    <div class="wditem">
        <div class="wdinner">
            
            <a onClick="$f().play('<?php echo $contentUrl; ?>');">
                <img src="<?php
            
                /*
                 * Get the small thumbnail url
                 */
                echo $media->thumbnail[1]->attributes()->url;
                
                ?>" height="<?php
            
                /*
                 * Get the small thumbnail height
                 */
                echo $media->thumbnail[1]->attributes()->height;
                
                ?>" width="<?php
            
                /*
                 * Get the small thumbnail width
                 */
                echo $media->thumbnail[1]->attributes()->width;
                
                ?>">
                
                <div class="wdtitle"><?php 
                
                    /*
                     * Title for this item
                     */
                     echo $item->title;
                      
                     ?></div>
            </a>
            <div class="wdcredits">
                <?php
                /*
                 * Loop through all the credits and credit types
                 */
                foreach($media->credit as $credit) {
                
                ?>
                <div>
                    <span class="wdattr"><?php  
                        
                        /* 
                         * Credit Type is the role attribute for credit
                         * Upper case the words.  The Credit Types always
                         * come in lower case.
                         */
                        echo ucwords($credit->attributes()->role); 
                        
                        ?></span> : <span class="wdvalue"><?php 
                        
                        /*
                         * show the credit
                         */
                        echo $credit; 
                        
                        ?></span>
                </div>
                <?php
                
                } // end credit loop
                
                ?>
            </div>
        </div>
    </div>
    <?php
    
     } // end item loop
    
    ?>
    </div>
    
    </body>
</html>