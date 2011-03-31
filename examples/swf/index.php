<?php

/*
 * Wiredrive RSS SWF Example
 * 
 * This is an example document illustrating how to use a Wiredrive mRSS 
 * feed to display many interlinked swf files 
 *
 * It also proxies the swf files through the local server to avoid crossdomain
 * problems so links embeded in the swf will properly link to each other.  
 *
 * Any files that are loaded through the mRSS feed must be contained in the same directory;
 * subfolders in the Wiredrive project will not be included in the mRSS feed.
 * This is a limitation of the RSS spec.
 *
 * Contents of the RSS feed are saved to a session so a request is not
 * sent to the Wiredrive servers on every request.
 *
 * mod_rewrite is required for this example to work properly.
 * The .htaccess file forces any path that does not exist on disk to the asset.php file.
 *
 * Usage:
 * You will need to set two variables in the URL. The mRSS feed created by Wiredrive, 
 * and the swf file to load initially.
 * http:/domain/index.php?rss=http://www.wdcdn.net/rss/presentation/projects/client/iowa/id/f638ab3d62b501723e9298a78cbb04e7/project/1061701/folder/1061702/&file=shell.swf
 */

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

require_once('common.php');

?>
<html>
<head> 
<title>Wiredrive RSS SWF Example</title>
<meta name="keywords" content="wiredrive, rss, mrss, example" >
<meta name="description" content="This is an example document illustrating how to load a remote RSS feed and render its contents into an HTML page" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="Content-Language" content="en-US" >
<meta http-equiv="expires" content="Wed, 19 Feb 2020 16:46:30 GMT" >
<meta http-equiv="pragma" content="cache" >
<meta http-equiv="Cache-Control" content="cache" >
 
<!-- Control the dimensions of the page in mobile safari -->
<meta content="user-scalable=no, width=device-width, minimum-scale=1.0, maximum-scale=1.0" name="viewport">         
       
<!-- Load the SWFObject Library -->
<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2/swfobject.js"></script>
                
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

#swfcontainer {
    margin: 0 auto;
    display: block;
}

</style>        
</head>
<body>
    <div class="companyLogo">Wiredrive RSS Example<!-- Put your company Logo Here --></div>
    <div class="swfitems" id="swfcontainer">
       <script type="text/javascript">
       swfobject.embedSWF("<?php echo $item->title ?>", "swfcontainer", "<?php echo $media->content->attributes()->width ?>", "<?php echo $media->content->attributes()->height ?>", "9.0.0");
       </script>
    </div>
</body>
</html>
