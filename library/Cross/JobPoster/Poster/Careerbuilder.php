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
 * Example for a derived job-poster class
 * 
 * @category  Cross
 * @package   Cross_JobPoster
 * @copyright  Copyright (c) 2012 Cross Solution. (http://www.cross-solution.de)
 * @license   New BSD License
 */


class Cross_JobPoster_Poster_Careerbuilder extends Cross_JobPoster_Poster
{
    protected function init() {
        $this->_setXlsPath(APPLICATION_PATH . '/../library/Cross/JobPoster/styleSheets/careerbuilder.xsl');
        $this->_setWsdl('http://dpi.careerbuilder.com/WebServices/RealTimeJobPost.asmx?WSDL');
    }
    
    protected function _preProcess($data) {
        return array('xmlJob' => $data);
    }
    
    protected function _postProcess($name, $data) {
        $erg = array();
        if ($name == 'ProcessHRXMLJob') {
            $domErg = new DomDocument();
            $domErg->loadXML($data->ProcessHRXMLJobResult);
            //$action      = $domErg->getElementsByTagName('ActionPerformed');
            $action      = $domErg->getElementsByTagName('ActionPerformed');
            $errorNumber = $domErg->getElementsByTagName('ErrorNumber');
            $message     = $domErg->getElementsByTagName('ErrorMessage');
            $did         = $domErg->getElementsByTagName('TransactionDID');
        
            if (0 < count($action)) {
                $erg['action'] = $action->item(0)->nodeValue;
            }
            if (0 < count($errorNumber)) {
                $erg['errorNumber'] = $errorNumber->item(0)->nodeValue;
            }
            if (0 < count($message)) {
                $erg['message'] = $message->item(0)->nodeValue;
            }
            if (0 < count($did)) {
                $erg['did'] = $did->item(0)->nodeValue;
            }
        
        }
        return $erg;
    }
    
}