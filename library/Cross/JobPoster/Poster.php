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
    
    public function __construct($data = Null) {
        if (isset($data)) {
            $this->_data = $data;
        }
        $this->init();
        return $this;
    }
    
    /**
     * a derived class should overwrite this method to provide
     * an own xls and wsdl-path
     */
    protected function init() {
    }
    
    protected function _getXlsPath() {
        return $this->_xlsPath;
    }
    
    protected function _setXlsPath($path) {
        $this->_xlsPath = $path;
        return $this;
    }
    
    protected function _getWsdl() {
        return $this->_wsdl;
    }
    
    protected function _setWsdl($path) {
        $this->_wsdl = $path;
        return $this;
    }
    
    protected function _getData() {
        return $this->_data;
    }
    
    protected function _setData($data) {
        $this->_data = $data;
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
    
    /**
     * the content send to the portal is just coming out of the transforming
     * i.e. a XML-String for a SOAP
     * sometimes this parameter has to have an associative key
     * here you can put this XML-String into a parameter-array for the SOAP-Call
     */
    protected function _preProcess($content) {
        return array($content);
    }
    
    
    public function transformXLS() {
        $erg = Null;
        $realpath = realpath(dirname($this->_getXlsPath())) . '/' . basename($this->_getXlsPath());
        if ($realpath) {
            if (is_readable($realpath)) {

                $domdocument = new DomDocument();
                $domdocument->load($realpath);
                $t = $domdocument->saveXML();
                $data = $this->_getData();
                if (empty($data)) {
                }
                else {
                    $dataXml = $data->asXML();
                    
                    $domdata = new DomDocument();
                    $domdata->loadXML($dataXml);

                    $xslt = new XSLTProcessor();
                    $xslt->importStylesheet($domdocument);
                    $erg = $xslt->transformToXml($domdata);
                }
            }
        }        
        return $erg;
    }
    
    public function __call($name, $arguments) {
        $client = new Zend_Soap_Client($this->_getWsdl(),
        array(
           'soap_version' => SOAP_1_2, 
            'encoding' => 'UTF-8',
            )
        );
        
        $content = '';
        if (isset($this->_data)) {
            $content = $this->transformXLS();
        }
        $contentArray = $this->_preProcess($content);
        $erg = $client->$name($contentArray);
        $erg = $this->_postProcess($name, $erg);
        return $erg;

    }
}