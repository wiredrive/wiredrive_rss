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
 * Wiredrive RSS Loading Example
 * 
 * This is an example document illustrating how to load a remote RSS 
 * feed and render its contents into an HTML page
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
<title>Wiredrive RSS Loading Example</title>
<meta name="keywords" content="wiredrive, rss, mrss, example" >
<meta name="description" content="This is an example document illustrating how to load a remote RSS feed and render its contents into an HTML page" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="Content-Language" content="en-US" >
<meta http-equiv="expires" content="Wed, 19 Feb 2020 16:46:30 GMT" >
<meta http-equiv="pragma" content="cache" >
<meta http-equiv="Cache-Control" content="cache" >
 
<!-- Control the dimensions of the page in mobile safari -->
<meta content="user-scalable=no, width=device-width, minimum-scale=1.0, maximum-scale=1.0" name="viewport">         
        
<style type="text/css">

/*
 *  Styles to display the item list horizontally
 */
html,body {
    background: #000000;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}

.companyLogo {
    padding-top: 50px;
    padding-bottom: 50px;
    position: relative;
    width: 100%;
    text-align: center;
    font-size: 20px;
    color: #FFF;
    font-weight: bold;
    font-family: Georgia, "Times New Roman", Times, serif;
}

.wditems {
    position: relative;
    text-align: center;
}

.wditem {
    position: relative;
    display: inline-table;
    width: 180px;
    height: 260px;
    padding: 0 10px;
}

.wdinner {
    position: relative;
    color: #FFF;
    font-size: 9px;
    background: #000;
    text-align: center;
}

.wditem img {
    border: none;
}

/*
 *  Styles for the text size and color
 */
.wditem a {
    color: #AAAAAA;
    text-decoration: none;
}

.wditem a:hover {
    color: #FFFFFF;
}

.wditem .wdtitle {
    font-family: Georgia, "Times New Roman", Times, serif;
    font-style: italic;
    font-size: 16px;
    font-weight: bolder;
    margin-top: 7px;
}

.wditem .wdcredits {
    font-family: Helvetica, Verdana, Arial, sans-serif;
}

.wditem .wdattr {
    color: #0099ff;
}

.wditem .wdvalue {
    color: #AAAAAA;
}


</style>        
</head>
<body>
    <div class="companyLogo">Wiredrive RSS Example<!-- Put your company Logo Here --></div>
    <div class="wditems" id="itemcontainer">
    <?php 
    /* 
     * start the item loop
     */
    foreach ($data as $item) {
        
    ?>
    <div class="wditem">
        <div class="wdinner">
            <a href="<?php
            
                /*
                 * Get the content url
                 */
                echo $item['content'][0]['url'];
                
                ?>"><img src="<?php
            
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
</body>
</html>