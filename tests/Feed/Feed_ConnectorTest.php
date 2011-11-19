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
 * Simple test suite for checking the connector's functionality
 */
class Feed_ConnectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Fetch Data Bad Request
     * Can't really test curl so check that all the steps are taken
     * that are expected.
     */
    public function testFetchDataBadRequest()
    {
        $connector = $this->getMock(
            'Feed_Connector',
            array('initialize', 'sendRequest', 'checkStatus', 'shutDown')
        );
        $connector->expects($this->once())
                  ->method('initialize')
                  ->will($this->returnValue($connector));
        $connector->expects($this->once())
                  ->method('sendRequest')
                  ->will($this->returnValue(false));
        $connector->expects($this->once())
                  ->method('shutDown');
        $connector->expects($this->never())
                  ->method('checkStatus');
        $result = $connector->fetchData('some url');
        $this->assertFalse($result);
    }

    /**
     * Test Fetch Data Bad Status
     * Test that a bad status check fails the request
     */
    public function testFetchDataBadStatus()
    {
        $connector = $this->getMock(
            'Feed_Connector',
            array('initialize', 'sendRequest', 'checkStatus', 'shutDown')
        );
        $connector->expects($this->once())
                  ->method('initialize')
                  ->will($this->returnValue($connector));
        $connector->expects($this->once())
                  ->method('sendRequest')
                  ->will($this->returnValue(''));
        $connector->expects($this->once())
                  ->method('shutDown');
        $connector->expects($this->once())
                  ->method('checkStatus')
                  ->will($this->returnValue(false));
        $result = $connector->fetchData('some url');
        $this->assertFalse($result);

    }

    /**
     * Test Fetch Data Success
     */
    public function testFetchDataSuccess()
    {
        $connector = $this->getMock(
            'Feed_Connector',
            array('initialize', 'sendRequest', 'checkStatus', 'shutDown')
        );
        $connector->expects($this->once())
                  ->method('initialize')
                  ->will($this->returnValue($connector));
        $connector->expects($this->once())
                  ->method('sendRequest')
                  ->will($this->returnValue('whee'));
        $connector->expects($this->never())
                  ->method('shutDown');
        $connector->expects($this->once())
                  ->method('checkStatus')
                  ->will($this->returnValue(true));
        $result = $connector->fetchData('some url');
        $this->assertEquals('whee', $result);
    }

    /**
     * Test Fetch Data Real
     * Using the provided example rss url, check that the connector
     * works.
     */
    public function testFetchDataReal()
    {
        $connector = new Feed_Connector();
        $url = 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d';

        $result = $connector->fetchData($url);
        $this->assertFalse(empty($result));
    }

}
