<?php

/*
 * Wiredrive RSS Simple Example
 * 
 * Example file for RSS to get around same domain restrictions 
 * for Flash and Javascript.  
 *
 * This is about as simple as possible.  Get the RSS feed from Wiredrive
 * and send it on
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
 * get the the RSS feed and echo it out with XML headers
 * Change this to the RSS feed you would like to proxy on your server
 */
$rss = 'http://www.wdcdn.net/rss/presentation/library/client/merc/id/84b8b5e27e9f55c7417848abb3327240';
header('Content-Type: text/xml; charset=UTF-8');
echo file_get_contents($rss,'r');

