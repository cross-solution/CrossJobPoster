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
        $this->_setXlsPath(array(
            '*'                => array('sTGDID' => APPLICATION_PATH . '/../library/Cross/JobPoster/styleSheets/careerbuilderJobStatus.xsl'),
            'ProcessHRXMLJob'  => array('xmlJob' => APPLICATION_PATH . '/../library/Cross/JobPoster/styleSheets/careerbuilder.xsl'),
            'GetJobPostStatus' => array('sTGDID' => APPLICATION_PATH . '/../library/Cross/JobPoster/styleSheets/careerbuilderJobStatus.xsl')
            ));
        
        $this->_setWsdl(array(
            '*'                => 'http://dpi.careerbuilder.com/WebServices/RealTimeJobStatus.asmx?WSDL',
            'ProcessHRXMLJob'  => 'http://dpi.careerbuilder.com/WebServices/RealTimeJobPost.asmx?WSDL',
            'GetJobPostStatus' => 'http://dpi.careerbuilder.com/WebServices/RealTimeJobStatus.asmx?WSDL'
            ));;
    }
    
    protected function _postProcess($name, $data) {
        $erg = array();
        if ($name == 'ProcessHRXMLJob' && !empty($data)) {
            $domErg = new DomDocument();
            $domErg->loadXML($data->ProcessHRXMLJobResult);
            //$action      = $domErg->getElementsByTagName('ActionPerformed');
            $action        = $domErg->getElementsByTagName('ActionPerformed');
            $errorNumber   = $domErg->getElementsByTagName('ErrorNumber');
            $message       = $domErg->getElementsByTagName('ErrorMessage');
            $did           = $domErg->getElementsByTagName('TransactionDID');
            $InternalJobID = $domErg->getElementsByTagName('InternalJobID');
            $UserJobID     = $domErg->getElementsByTagName('UserJobID');
        
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
            if (0 < count($InternalJobID)) {
                $erg['InternalJobID'] = $InternalJobID->item(0)->nodeValue;
            }
            if (0 < count($UserJobID)) {
                $erg['UserJobID'] = $UserJobID->item(0)->nodeValue;
            }
        
        }
        elseif ($name == 'GetJobPostStatus') {
            $erg = (array) $data;
        }
        return $erg;
    }
    
}