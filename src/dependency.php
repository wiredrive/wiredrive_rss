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
 * Dependency file for the Feed module.  This is useful if you don't have an
 * autoloader configured.  If you do, please omit this file
 */
require_once('src/Feed/CacheAdapter.php');
require_once('src/Feed/Connector.php');
require_once('src/Feed/Parser.php');
require_once('src/Feed/Manager.php');
