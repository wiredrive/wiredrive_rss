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

/**
 * Simple test suite for checking the cache adapter's functionality
 */
class Feed_Cache_AdapterTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $cacheDir = 'foobar';
        $adapter = new Feed_Cache_Adapter($cacheDir);
        $this->assertEquals($cacheDir, $adapter->getCacheDir());
    }

    /**
     * Test Get File Name
     */
    public function testGetFileName()
    {
        $cacheKey = 'readTest';
        $adapter = new Feed_Cache_Adapter();
        $fileName = md5($cacheKey);
        $result = $adapter->getFileName($cacheKey);
        $this->assertEquals($fileName, $result);
    }

    /**
     * Test Get File Path
     */
    public function testGetFilePath()
    {
        $cacheKey = 'readTest';
        $adapter = new Feed_Cache_Adapter();
        $result = $adapter->getFilePath($cacheKey);
        $filePath = $adapter->getCacheDir() . '/' . md5($cacheKey);
        $this->assertEquals($filePath, $result);
    }

    /**
     * Test Get Data Failure
     * Simple test to retrieve data from a file that doesn't exist
     */
    public function testGetDataFailure()
    {
        $adapter = new Feed_Cache_Adapter();
        $cacheKey = 'some key';
        $result = $adapter->getData($cacheKey);
        $this->assertFalse($result);
    }

    /**
     * Test Get Data Success
     */
    public function testGetDataSuccess()
    {
        $fileContents   = 'test file contents';
        $cacheKey       = 'readTest';
        $cacheDir       = 'testdata/cache';
        $adapter        = new Feed_Cache_Adapter($cacheDir);
        $result         = $adapter->getData($cacheKey);
        $this->assertEquals($fileContents, trim($result));
    }

    /**
     * Test Update Cache
     */
    public function testUpdateCache()
    {
        $fileContents = 'test write contents';
        $cacheKey       = 'writeTest';
        $cacheDir       = 'testdata/cache';
        $adapter        = new Feed_Cache_Adapter($cacheDir);
        $result         = $adapter->updateCache($cacheKey, $fileContents);
        $this->assertTrue($result !== false);
        
        $result = $adapter->getData($cacheKey);
        $this->assertEquals($fileContents, trim($result));

        $filePath = $adapter->getFilePath($cacheKey);
        unlink($filePath);
    }

    /**
     * Test Is Stale
     */
    public function testIsStale()
    {
        $cacheKey   = 'readTest';
        $cacheDir   = 'testdata/cache';
        $adapter    = new Feed_Cache_Adapter($cacheDir);
        $result     = $adapter->isStale($cacheKey, 10);
        $this->assertTrue($result);

        $contents = 'blah';
        $cacheKey = 'writeTest';
        $adapter->updateCache($cacheKey, $contents);
        $result = $adapter->isStale($cacheKey, 60);
        $this->assertFalse($result);
    
        $filePath = $adapter->getFilePath($cacheKey);
        unlink($filePath);
    }
}
