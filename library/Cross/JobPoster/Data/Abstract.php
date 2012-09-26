<?php

/**
 * Cross Job Poster
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Cross
 * @package   Cross_JobPoster
 * @copyright  Copyright (c) 2012 Cross Solution. (http://www.cross-solution.de)
 * @license   New BSD License
 * @author Mathias Weitz (mweitz@cross-solution.de)
 */

/**
 * Storing of recursive data
 * 
 * the distinguished methods for setting data have the form setIdentifier($date1, $data2, ...)
 * single setter methods can be overwritten in a derived class, 
 * the purpose of deriving is to complete data with specialized setter (ie with access to a DB)
 *
 * the assigned data in a setter can be any scalar value, an array, 
 * or any object from a derived class of this class
 * 
 * @category  Cross
 * @package   Cross_JobPoster
 * @copyright  Copyright (c) 2012 Cross Solution. (http://www.cross-solution.de)
 * @license   New BSD License
 */
abstract class Cross_JobPoster_Data_Abstract
{
    protected $_data = array();
    
    public function __construct($data = Null) {
        if (isset($data)) {
            $this->_data = $data;
        }
        return $this;
    }
    
    public function asArray() {
        $erg = array();
        if (is_array($this->_data)) {
            foreach ($this->_data as $key => $data) {
                if (is_object($data) && is_subclass_of($data, 'Cross_JobPoster_Data_Abstract')) {
                    //require_once 'Zend/Controller/Exception.php';
                    //throw new Zend_Controller_Exception('Invalid response class');
                    $erg[$key] = $data->asArray();
                } else {
                    if (is_array($data)) {
                        foreach ($data as $subKey => $subData) {
                            if (is_object($subData) && is_subclass_of($subData, 'Cross_JobPoster_Data_Abstract')) {
                                $erg[$key][$subKey] = $subData->asArray();
                            } else {
                                $erg[$key][$subKey] = $subData;
                            }
                        }
                    }
                    else {
                        $erg[$key] = $data;
                    }
                }
            }
        } else {
            $erg = $this->_data;
        }
        return $erg;
    }
    
    public function preprocessData() {
        foreach ($this->_data as $key => &$value) {
            $methodName = 'pre' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $value = call_user_func(array(&$this, $methodName), $value);
            }
        }
    }
    
    public function asXML() {
        $asArray = $this->asArray();
        $this->complete(&$asArray);
        $erg = wddx_serialize_value($asArray);
        return $erg;
        
    }
    
    /**
     * before transforming the array to XML, here is the method to complete the data
     * @param array $dataArray 
     */
    protected function complete($dataArray) {
    }
    
    public function __call($method, $args) {
        $splitMethodName = preg_split('/([A-Z][a-z0-9]+)/', $method, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        
        if (strtolower($splitMethodName[0]) == 'set' && 1 == count($args)) {
            // assigning one Object
            if (!is_array($this->_data)) {
                require_once 'Zend/Controller/Exception.php';
                throw new Zend_Controller_Exception('assigning more than one Element to a scalar');
            }
            $this->_data[strtolower($splitMethodName[1])] = $args[0];
        } 
        
        if (strtolower($splitMethodName[0]) == 'add' || (strtolower($splitMethodName[0]) == 'set' && 1 < count($args))) {
            // assigning more than on Object
            if (!is_array($this->_data)) {
                require_once 'Zend/Controller/Exception.php';
                throw new Zend_Controller_Exception('assigning more than one Element to a scalar');
            }
            foreach($args as $arg) { 
                $this->_data[strtolower($splitMethodName[1])][] = $arg;
            }
        }
        return $this;
    }
}