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
 * Simple feed parsing class.  Does some validation and data conversion for
 * convinience but it is not a full parsing library.
 */
class Feed_Parser 
{
    /**
     * Contents
     * The current raw contents of the feed.
     * @var string
     */
    protected $contents = null;

    /**
     * Format
     * The current format to parse the feed
     * @var string
     */
    protected $format = null;

    /**
     * Valid formats
     * A list of valid formats that this parser can handle
     * @var array
     */
    protected $validFormats = array('xml', 'json');

    /**
     * Xml
     * The xml object that represents the current contents
     * @var SimpleXMLElement
     */
    protected $xml = null;

    /**
     * Constructor
     *
     * @param   string      $format
     */
    public function __construct($format = 'json')
    {
        $this->setFormat($format);
    }

    /**
     * @param   string      $format
     * @return  Feed_Parser
     */
    public function setFormat($format)
    {
        $validFormats = $this->getValidFormats();
        if (! in_array($format, $validFormats)) {
            throw new Exception('Invalid parsing format');
        }
        $this->format = $format;
        return $this;
    }

    /**
     * @return  string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getValidFormats()
    {
        return $this->validFormats;
    }

    /**
     * When setting content, the current parsed xml feed is destroyed
     *
     * @param   string      $contents
     * @return  Feed_Parser
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        $this->xml = null;
        return $this;
    }

    /**
     * @return  SimpleXMLElement
     */
    protected function getXml()
    {
        return $this->xml;
    }

    /**
     * Return the current raw contents
     * @return  string
     */
    public function getContents()
    {
        return $this->contents;        
    }

    /**
     * Process
     * Convert the current contents into a simple xml element and return
     * the data based on the desired format.
     *
     */
    public function process()
    {
        $contents = $this->getContents();
        if (empty($contents)) {
            throw new Exception('Invalid contents for feed processing');
        }

        try {
            $xml = new SimpleXMLElement($contents);
        } catch (Exception $exception) {
            throw new Exception('Failed parsing contents', 1, $exception);
        }

        $this->xml = $xml;
        
        return $this->processOutput($this->getFormat()); 
    }

    /**
     * Process the current xml object based on the desired format
     * 
     * @param   string      $format
     * @return  mixed
     */
    public function processOutput($format)
    {
        $validFormats = $this->getValidFormats();
        if (! in_array($format, $validFormats)) {
            throw new Exception('Invalid format requested');
        }
        
        $xml = $this->getXml();
        if (! $xml instanceof SimpleXMLElement) {
            throw new Exception('Cannot process an non-existant feed');
        }
        switch ($format) {
            case 'xml';
                return $this->getContents();
            case 'json':
                return $this->processJson();
        }
    }

    /**
     * Process Json
     * Process the currently set xml object as json output.
     *
     * @return  string
     */
    protected function processJson()
    {
        $xml = $this->getXml();

        /*
         * Start the response data array
         */
        $response = array();
        
        /*
         * Get the just the channel object 
         */
        $channel = $this->xml->channel;
        
        /*
         * Get any namespaces added to the RSS
         */
        $ns = $this->xml->getNamespaces(TRUE);
        
        /*
         * Cycle though the XML objects.  Conversion with json_encode will not
         * work because CDATA does not convert, and the media attributes are
         * difficult to pull out of json data
         */
        foreach ($channel->children() as $element) {
        
            /*
            * Start the data array for this child
            */
            $elementData = array();
        
            /* 
             * Get the element name and start and array
             */
            $elementName = (string) $element->getName();
        
            /*
             * Add entities without children to the response array
             */    
            if (0 === count($element->children())) {
                $name = $element->getName();
                $elementData[$name] = (string) $element;
                $response[] = $elementData;
                continue;
            }
            
            /*
             * Add entities with children to the reponse array
             */    
            foreach ($element->children() as $item) {
            
                /*
                 * Get the name of the element
                 */
                $name = $item->getName();
            
                /*
                 * Add items without attributes to the response array
                 */
                if (sizeof($item->attributes()) == 0) {
                    $elementData[$elementName][$name] = (string) $item;  
                    $response[] = $elementData;
                    continue;      
                }
                
                /*
                 * Add the attributes
                 */
                foreach ($item->attributes() as $attribute) {
                    $attributeName = $attribute->getName();
                    $elementData[$elementName][$name][$attributeName] = (string) $attribute;  
                }    
                
            }
        
            /*
             * Cycle through any defined names spaces and 
             * extract the elements
             */
            foreach($ns as $name=>$namespace) {
                $nsChildren = $element->children($namespace);
                
                /*
                 * Cycle through the elements defined for this namespace
                 */  
                $count = array();
                foreach($nsChildren as $item) {
        
                    /*
                     * Get the name for this element
                     */
                    $name = $item->getName();
        
                    /*
                     * Make sure there is a counter for each item and start counting at 0
                     */
                    if (!$count[$name]) {
                        $count[$name] = 0;
                    }
                    $i = $count[$name];
                
                    /*
                     * Add the attributes
                     */
                    foreach ($item->attributes() as $attribute) {
                        $attributeName = $attribute->getName();
                        $elementData[$elementName][$name][$i][$attributeName] = (string) $attribute;  
                    }
        
                    /*
                     * Add the value of the element if it exists
                     */
                    $content = (string) $item;
                    if (!empty( $content) ) {
                        $elementData[$elementName][$name][$i]['content'] = $content;  
                    }
        
                    /*
                     * increment the counter for this item
                     */
                    $count[$name]++;
                }
            } 
            
            $response['responseData'][] = $elementData;
        }
        
        $response['responseStatus'] = 200;
        
        /*
         * JSON encode the response
         */
        $json = json_encode($response);
        
        /*
         * Make sure json encoding passed
         */
        if (!$json || $json == 'null') {
            $error['responseStatus'] = 500;
            $json = json_encode($error);
        }
        
        $this->setJson($json);
    }
}
