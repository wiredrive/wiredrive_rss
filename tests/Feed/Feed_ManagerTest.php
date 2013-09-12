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

class Feed_ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Constructor
     * Test the various cases where the optional configurations are set.
     */
    public function testConstructor()
    {
        $manager = new Feed_Manager();
        $this->assertEquals('', $manager->getFeedUrl());
        $this->assertTrue($manager->isCache());
        $this->assertEquals('json', $manager->getFormat());


        $string = 'testString';
        $config = array('feedUrl' => $string);
        $manager = new Feed_Manager($config);
        $this->assertEquals($string, $manager->getFeedUrl());
        
        $string = 'xml';
        $config = array('format' => $string);
        $manager = new Feed_Manager($config);
        $this->assertEquals($string, $manager->getFormat());

        $value = false;
        $config = array('isCache' => $value);
        $manager = new Feed_Manager($config);
        $this->assertEquals($value, $manager->isCache());

        $value = 'some dir';
        $config = array('cacheDir' => $value);
        $manager = new Feed_Manager($config);
        $this->assertEquals($value, $manager->getCacheDir());
    }

    /**
     * Test Set Blank Url
     * @expectedException Exception
     */
    public function testSetBlankUrl()
    {
        $manager = new Feed_Manager();
        $manager->setFeedUrl('');
    }

    /**
     * Test Get Contents Blank Url
     * @expectedException Exception
     */
    public function testGetContentsBlankUrl()
    {
        $manager = new Feed_Manager();
        $manager->getContents('');
    }

    /**
     * Test Get Contents Invalid Feed
     * @expectedException Exception
     */
    public function testGetContentsInvalidFeed()
    {
        $manager = new Feed_Manager();
        $connector = $this->getMock(
            'Feed_Connector',
            array('fetchData')
        );
        $connector->expects($this->once())
                  ->method('fetchData')
                  ->will($this->returnValue(false));
        $manager->setConnector($connector);
        
        $result = $manager->getContents('somestring');
    }

    /**
     * Test Process Data
     * Make sure the expected objects are used, doesn't test real functionality
     */
    public function processData()
    {
        $manager = new Feed_Manager();
        $connector = $this->getMock(
            'Feed_Connector',
            array('fetchData')
        );
        $connector->expects($this->once())
                  ->method('fetchData')
                  ->will($this->returnValue('<rss></rss>'));
        
        $parser = $this->getMock(
            'Feed_Parser',
            array('setContents', 'process')
        );
        $parser->expects($this->once())
               ->method('setContents')
               ->will($this->returnValue($parser));
        $parser->expects($this->once())
               ->method('process')
               ->will($this->returnValue('{}'));
        $manager->setParser($parser)
                ->setConnector($connector);

        $result = $manager->processData();
        $this->assertEquals('{}', $result);
    }

    /**
     * Test Process Cache Not Found
     * Make sure the cache object is used and then get contents is 
     * used
     */
    public function testProcessCacheNotFound()
    {
        $manager = $this->getMock(
            'Feed_Manager',
            array('getContents')
        );
        $manager->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue('<rss></rss>'));
        
        $cacheAdapter = $this->getMock(
            'Feed_Cache_Adapter',
            array('getData', 'updateCache')
        );
        $cacheAdapter->expects($this->once())
                     ->method('getData')
                     ->will($this->returnValue(false));
        $cacheAdapter->expects($this->once())
                     ->method('updateCache')
                     ->will($this->returnValue($cacheAdapter));

        $parser = $this->getMock(
            'Feed_Parser',
            array('setContents', 'process')
        );
        $parser->expects($this->once())
               ->method('setContents')
               ->will($this->returnValue($parser));
        $parser->expects($this->once())
               ->method('process')
               ->will($this->returnValue('{}'));
        $manager->setParser($parser)
                ->setCacheAdapter($cacheAdapter);
    
        $result = $manager->processCache();
        $this->assertEquals('{}', $result);
    }

    /**
     * Test Process Cache Found Not Stale
     */
    public function testProcessCacheFoundNotStale()
    {
        $manager = $this->getMock(
            'Feed_Manager',
            array('getContents')
        );
        $manager->expects($this->never())
                ->method('getContents');
        
        $cacheAdapter = $this->getMock(
            'Feed_Cache_Adapter',
            array('getData', 'updateCache', 'isStale')
        );
        $cacheAdapter->expects($this->once())
                     ->method('getData')
                     ->will($this->returnValue('<rss></rss>'));
        $cacheAdapter->expects($this->never())
                     ->method('updateCache');
        $cacheAdapter->expects($this->once())
                     ->method('isStale')
                     ->will($this->returnValue(false));

        $parser = $this->getMock(
            'Feed_Parser',
            array('setContents', 'process', 'getProperty')
        );
        $parser->expects($this->once())
               ->method('setContents')
               ->will($this->returnValue($parser));
        $parser->expects($this->once())
               ->method('process')
               ->will($this->returnValue('{}'));
        $parser->expects($this->once())
               ->method('getProperty')
               ->will($this->returnValue(1));
        $manager->setParser($parser)
                ->setCacheAdapter($cacheAdapter);
    
        $result = $manager->processCache();
        $this->assertEquals('{}', $result);

    }

    /**
     * Test Process Cache Found Is Stale
     */
    public function testProcessCacheFoundIsStale()
    {
        $manager = $this->getMock(
            'Feed_Manager',
            array('getContents')
        );
        $manager->expects($this->once())
                ->method('getContents');
        
        $cacheAdapter = $this->getMock(
            'Feed_Cache_Adapter',
            array('getData', 'updateCache', 'isStale')
        );
        $cacheAdapter->expects($this->once())
                     ->method('getData')
                     ->will($this->returnValue('<rss></rss>'));
        $cacheAdapter->expects($this->once())
                     ->method('updateCache');
        $cacheAdapter->expects($this->once())
                     ->method('isStale')
                     ->will($this->returnValue(true));

        $parser = $this->getMock(
            'Feed_Parser',
            array('setContents', 'process', 'getProperty')
        );
        $parser->expects($this->exactly(2))
               ->method('setContents')
               ->will($this->returnValue($parser));
        $parser->expects($this->exactly(2))
               ->method('process')
               ->will($this->returnValue('{}'));
        $parser->expects($this->once())
               ->method('getProperty')
               ->will($this->returnValue(1));
        $manager->setParser($parser)
                ->setCacheAdapter($cacheAdapter);
    
        $result = $manager->processCache();
        $this->assertEquals('{}', $result);

    }

    /**
     * Test Process Cache Used
     */
    public function testProcessCacheUsed()
    {
        $manager = $this->getMock(
            'Feed_Manager',
            array('processCache', 'processData')
        );
        $manager->expects($this->once())
                ->method('processCache')
                ->will($this->returnValue('{}'));
        $manager->expects($this->never())
                ->method('processData');
        
        $result = $manager->process();
        $this->assertEquals('{}', $result);
                
    }
    
    /**
     * Test Process Cache Not Used
     */
    public function testProcessCacheNotUsed()
    {
        $manager = $this->getMock(
            'Feed_Manager',
            array('processCache', 'processData'),
            array(array('isCache' => false))
        );
        $manager->expects($this->once())
                ->method('processData')
                ->will($this->returnValue('{}'));
        $manager->expects($this->never())
                ->method('processCache');
       
        $result = $manager->process();
        $this->assertEquals('{}', $result);

    }   

}
