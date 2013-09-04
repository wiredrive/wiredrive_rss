<?php

/*****************************************************************************
 * Copyright (c) 2011 IOWA, llc dba Wiredrive
 * Author J.O.D. (Joint Operations/Development)
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
 
/*
 * Wiredrive JW Player Playlist Example
 * 
 * This is an example document illustrating how to load a remote RSS 
 * feed and render its contents into an HTML page
 *
 * Please make sure to use the latest version of JW Player.
 * http://www.longtailvideo.com/players/jw-flv-player/
 *
 * This file will get the RSS feed from the Wiredrive, save and 
 * serve from a local server. 
 *
 * Contents of the RSS feed are saved to a session so a request is not
 * going to the Wiredrive servers on every request.
 *
 */

/* set up base dir */
$basePath = realpath(dirname(__FILE__) . '/../../');
set_include_path(get_include_path() . PATH_SEPARATOR . $basePath);

/* load dependencies */
require_once('src/dependency.php');
date_default_timezone_set('America/Los_Angeles');

/* rss feed to bring into flowplayer */
$url    = 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d';

/* config options to send to the manager */
$config = array(
    'feedUrl'   => $url,
    'format'    => 'json',
    'itemsOnly' => TRUE,
);

/* get the feed */
$feedManager = new Feed_Manager($config);
$json_data = $feedManager->process();

$data = json_decode($json_data, TRUE);


?>
<html>
<head> 
<title>Wiredrive JW Player Playlist Example</title>
<meta name="keywords" content="wiredrive, rss, mrss, example, JW Player" >
<meta name="description" content="This is an example document illustrating how to load a remote RSS feed and render its contents into an HTML page and play the videos using JW Player" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="Content-Language" content="en-US" >
<meta http-equiv="expires" content="Wed, 19 Feb 2020 16:46:30 GMT" >
<meta http-equiv="pragma" content="cache" >
<meta http-equiv="Cache-Control" content="cache" >
 
<!-- Control the dimensions of the page in mobile safari -->
<meta content="user-scalable=no, width=device-width, minimum-scale=1.0, maximum-scale=1.0" name="viewport">         

<link rel="stylesheet" type="text/css" href="style.css"> 

<!-- Load the JW Embbeder Script -->
<!-- http://www.longtailvideo.com/support/jw-player/jw-player-for-flash-v5/15995/jw-embedder-reference-guide -->
<script type='text/javascript' src='jwplayer/jwplayer.js'></script>

</head>
<body>
    <div class="companyLogo">Wiredrive JW Player Playlist Example<!-- Put your company Logo Here --></div>
    
    <div id="player"></div>
    <script type="text/javascript">
      jwplayer('player').setup({
        'file': '<?php echo $data[0]['content'][0]['url']; ?>',
        'image': '<?php echo $data[0]['thumbnail'][0]['url']; ?>',
        'width': '640',
        'height': '480'
      });
    </script>     


  <div class="wditems" id="itemcontainer">
    <?php
    /* 
     * start the item loop
     */
    foreach ($data as $item) {
    
    ?>
    <div class="wditem">
        <div class="wdinner">
        <!-- http://www.longtailvideo.com/support/jw-player/jw-player-for-flash-v5/12540/javascript-api-reference -->
            <a onClick="jwplayer('player').load({'file': '<?php echo $item['content'][0]['url']; ?>', 'image': '<?php echo $item['thumbnail'][0]['url']; ?>', 'provider': 'http'}).play()" href="#">
                <img src="<?php
            
                /*
                 * Get the small thumbnail url
                 */
                echo $item['thumbnail'][1]['url'];
                
                ?>" height="<?php
            
                /*
                 * Get the small thumbnail height
                 */
                echo $item['thumbnail'][1]['height'];
                
                ?>" width="<?php
            
                /*
                 * Get the small thumbnail width
                 */
                echo $item['thumbnail'][1]['width'];
                
                ?>">
                
                <div class="wdtitle"><?php 
                
                    /*
                     * Title for this item
                     */
                     echo $item['title'];
                      
                     ?></div>
            </a>
            <div class="wdcredits">
                <?php
                /*
                 * Loop through all the credits and credit types
                 */
                foreach($item['credit'] as $credit) {
                
                ?>
                <div>
                    <span class="wdattr"><?php  
                        
                        /* 
                         * Credit Type is the role attribute for credit
                         * Upper case the words.  The Credit Types always
                         * come in lower case.
                         */
                        echo ucwords($credit['role']); 
                        
                        ?></span> : <span class="wdvalue"><?php 
                        
                        /*
                         * show the credit
                         */
                        echo $credit['content'];
                        
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
