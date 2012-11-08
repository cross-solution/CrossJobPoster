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
 * @author Mathias Weitz (weitz@cross-solution.de)
 */

/**
 * Send the data-class with SOAP
 * 
 * @category  Cross
 * @package   Cross_JobPoster
 * @copyright  Copyright (c) 2012 Cross Solution. (http://www.cross-solution.de)
 * @license   New BSD License
 */

class Cross_JobPoster_Poster
{
    protected $_xlsPath;
    protected $_data;
    protected $_wsdl;
    protected $_user;
    protected $_password;
    protected $_soapFunction;
    protected $_contentArray;
    
    public function __construct($data = Null) {
        if (isset($data)) {
            $this->_data = $data;
        }
        $this->_contentArray = array();
        $this->init();
        return $this;
    }
    
    /**
     * a derived class should overwrite this method to provide
     * an own xls and wsdl-path
     */
    protected function init() {
    }
    
    private function _getByNameMatch($array, $searchKey = Null) {
        $result = $array;
        if (is_array($array)) {
            if (1 < count($array)) {
                if (!isset($key) && isset($this->_soapFunction)) {
                    $searchKey = $this->_soapFunction;
                }
                foreach ($array as $key => $value) {
                    if ($key == '*') {
                        $result = $value;
                    }
                    if ($key == $searchKey) {
                        $result = $value;
                        break;
                    }
                }
            }
            else {
                $result = current($array);
            }
        }
        return $result;
    }
    
    protected function _getXlsPath() {
        $xsl = $this->_getByNameMatch($this->_xlsPath);
        if (!is_array($xsl)) {
            $xsl = array($xsl);
        }
        return $xsl;
    }
    
    protected function _setXlsPath($path=Null) {
        $this->_xlsPath = $path;
        return $this;
    }
    
    protected function _getWsdl() {
        return $this->_getByNameMatch($this->_wsdl);
    }
    
    protected function _setWsdl($path) {
        $this->_wsdl = $path;
        return $this;
    }
    
    protected function _getData() {
        $this->_data->preprocessData();
        return $this->_data;
    }
    
    protected function _setData($data) {
        $this->_data = $data;
        return $this;
    }
    
    protected function _getUser() {
        return $this->_user;
    }
    
    protected function _setUser($user) {
        $this->_user = $user;
        return $this;
    }
    
    protected function _getPassword() {
        return $this->_password;
    }
    
    protected function _setPassword($password) {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * the result of a call ist likely to be encoded, you can use this method to process the result
     * 
     * @param string $name name of SOAP-call
     * @param string $erg most likely a XML, you can ie use DomDocument to disassemble this
     * @return array 
     */
    protected function _postProcess($name, $erg) {
        return array('raw' => $erg);
    }
    
    public function transformXLS() {
        $dataXml = '';
        $data = $this->_getData()->asArray();
        if (empty($data)) {
            
        } else {
            if (is_array($data)) {
                $dataXml = wddx_serialize_value($data);
            } else {
                $dataXml = $data->asXML();
            }
        }

        $erg = $dataXml;
        $xlsPathes = $this->_getXlsPath();
        foreach ($xlsPathes as $key => $xlsPath) {
            if (!empty($xlsPath)) {
                $realpath = realpath(dirname($xlsPath)) . '/' . basename($xlsPath);
                if ($realpath) {
                    if (is_readable($realpath)) {

                        $domdocument = new DomDocument();
                        $domdocument->load($realpath);
                        $t = $domdocument->saveXML();
                        if (!empty($dataXml)) {
                            $domdata = new DomDocument();
                            $domdata->loadXML($dataXml);

                            $xslt = new XSLTProcessor();
                            $xslt->importStylesheet($domdocument);
                            $erg[$key] = $xslt->transformToXml($domdata);
                            //echo htmlentities($erg[$key]);
                        }
                    }
                }
            }
        }
        return $erg;
    }

    /**
     * Aufruf einer SOAP-Funktion
     * 
     * @param string $name der SOAP-Funktion
     * @param type $arguments die Daten für die SOAP-Schnittstelle
     * @return type 
     */
    public function __call($name, $arguments) {
        $this->_soapFunction = $name;
        $soapOptions = array(
           'soap_version' => SOAP_1_2, 
            'encoding' => 'UTF-8',
            );
        
        $user = $this->_getUser();
        $password = $this->_getPassword(); 
        if (!empty($user)) {
            $soapOptions['login'] = $user;
        }
        
        if (!empty($password)) {
            $soapOptions['password'] = $password;
        }
       
        $client = new Zend_Soap_Client($this->_getWsdl(),
                $soapOptions
        );
        
        $content = '';
        if (0 < count($arguments)) {
            // data are given as argument
            $this->_data = $arguments[0];
        }
        if (isset($this->_data)) {
            $content = $this->transformXLS();
        }
        
        //Zend_Debug::dump($content);
        try {
            $erg = $client->$name($content);
        } catch (Exception $e) {
            $m = $e->getMessage();
            $c = $e->getCode();
            $f = $e->getFile();
            $l = $e->getLine();
            
        }
        
        //Zend_Debug::dump($erg);
        $erg = $this->_postProcess($name, $erg);
        $this->_soapFunction = Null;
        return $erg;

    }
}