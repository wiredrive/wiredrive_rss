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
 * Simple test suite for checking the parser's functionality
 */
class Feed_ParserTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $format = 'xml';
        $parser = new Feed_Parser($format);
        $this->assertEquals($format, $parser->getFormat());
    }

    /**
     * Test Items Only
     */
    public function testItemsOnly()
    { 
        $parser = new Feed_Parser();
        $this->assertTrue($parser->itemsOnly());
        
        $parser->setItemsOnly(false);
        $this->assertFalse($parser->itemsOnly());
    }

    /**
     * Test Set/Get contents
     */
    public function testSetGetContents()
    {
        $contents = 'wheee';
        $parser = new Feed_Parser();
        $parser->setContents($contents);
        $this->assertEquals($contents, $parser->getContents());
    }

    /**
     * Test Bad Contents
     * @expectedException Exception
     */
    public function testBadContents()
    {
        $contents = 'wheeeeeee';
        $parser = new Feed_Parser();
        $parser->setContents($contents);
        $parser->process();
    }

    /**
     * Test Bad Process Output Format
     * @expectedException Exception
     */
    public function testBadProcessOuputFormat()
    {
        $parser = new Feed_Parser();
        $parser->processOutput('feed', false);
    }
   
    /**
     * Test Bad Process ProcessOuput
     * @expectedException Exception
     */
    public function testBadProcessNoContents()
    {
        $parser = new Feed_Parser();
        $parser->processOutput('json', true); 
    }

    /**
     * Test Full Parse
     */
    public function testFullParse()
    {
        $contents = file_get_contents('testdata/realfeed.xml');
        $parser = new Feed_Parser('array');
        $parser->setContents($contents);
        $result = $parser->process();
        $this->assertInternalType('array', $result);
    }
}
