<?php

/*
 * Wiredrive RSS SWF Example
 * 
 * This is an example document illustrating how to use a Wiredrive mRSS 
 * feed to display many interlinked swf files 
 *
 * It also proxies the swf files through the local server to avoid crossdomain
 * problems and so links embeded in the swf will properly link to each other.  
 *
 * Any files that are loaded through the mRSS feed must be contained in the same directory;
 * subfolders in the Wiredrive project will not be included in the mRSS feed.
 * This is a limitation of the RSS spec.
 *
 * Contents of the RSS feed are pulled from a session so a request is not
 * sent to the Wiredrive servers on every request. 
 *
 * The mRSS URL does not need to be appended to the URL because the contents 
 * have been stored as a session.
 *
 * mod_rewrite is required for this example to work properly.
 * The .htaccess file forces any path that does not exist on disk to the asset.php file.
 *
 * Usage:
 * http:/domain/shell.swf
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

/*
 * Send headers for this file
 */
header('Content-type: ' . $media->content->type);
header('Content-Disposition: inline; filename="'. $item->title .'"');

/*
 * Get the content url
 */
readfile($media->content->attributes()->url);