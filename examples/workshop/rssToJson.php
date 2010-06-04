<?php

/*
 * Class to convert mRSS to JSON
 * 
 * Accepts a SimpelXMlElement object and returns a JSON formated string
 */

/*********************************************************************************
 * Copyright (c) 2010 IOWA, llc dba Wiredrive
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
 
class rssToJson 
{

    /*
     * XML Object
     *
     * @var SimpleXMLElement
     */
    private $xml = NULL;
    
    /*
     * JSON String
     *
     * @var string
     */
    public $json = NULL;

    function __construct(SimpleXMLElement $xml) 
    {
    
        $this->xml = $xml;

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
            if ($element->count() == 0) {
                $name = $element->getName();
                $elementData[$name] = (string) $element;
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
    
    function setJson($json) {
        $this->json = $json;
    }
    
    function getJson() {
        return $this->json;
    }

}
