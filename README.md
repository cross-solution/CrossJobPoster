### CrossJobPoster

## Status

*alpha*

## Overview

PHP implementation of various interfaces to post job offers to jobportals like careerbuilder, monster or FAZjob.NET

The services of this library are:
* providing a consistent way to store data for an API

The advantages of this library are:
* simplification for the application-code
* boiling down the migration to xsl-files, which can be shared easely

## References

* [hr-xml.org](http://hr-xml.org "HR-XML")
* [Dokumentation Career](http://dpi.careerbuilder.com/Site/Index.aspx "Careerbuilder Specs")
* [Dokumentation Monster](http://doc.monster.com/ "Monster Specs")
* FAZjob.NET - to be written

## Component Requirements, Constraints, and Acceptance Criteria

- This must support various kinds of protocols: Soap, Mails, FTP
- Thus must be able to assemble varying internal structure of the protocols (like HRXML for different portals)
- This may support multi-layer protocols

### Dependencies on Other Components

- Zend_Soap_Client
- libxml
- expat-library

### Theory of Operation

A data-object should contain all the information about a job.
A XSL-Stylesheet transforms the data-object into a xml or json-file.
The poster-object sends the file to a portal.

### Class Index

* Cross_JobPoster_Poster_CareerBuilder
* Cross_JobPoster_Poster_Monster
* Cross_JobPoster_Poster
* Cross_JobPoster_Data_Careerbuilder
* Cross_JobPoster_Data_Monster
* Cross_JobPoster_Data_Abstract

### Use Cases

#### Usage in the Controller:

* call a poster-class
* specify the connection-data (eq username, password) for the jobportal
* set the transformation-protocol
* call the data-class, which is specified to the Posterclass
* fill the data-class with information
* call the method in the poster-class, which will trigger the posting-process

```php
<?php
     $data = new Cross_JobPoster_Data_Careerbuilder();
     $data->setAction('ADD');
     $data->setStatus('Active');
     $data->setTitle('Zend-Programmierer');
     $data->setJoblocationtown('Frankfurt');
 
     $data->setRecruitername('Jenny Recruiter');
     $data->setRecruiterphone('0123-4567890');
     $data->setRecruiteremail('JRecruiter@gotche.de');
 
     // Sending the data-object to the API of the portal
     $op = new Cross_JobPoster_Poster_CareerBuilder();
     $op->ProcessHRXMLJob($data);
```
 
#### class for preserving data:

* basically this class is a container for all the data needed in the poster-protocol
* this class can be extended for completing the data before transforming them by the XSL
** some date may contain individual values (like industry types or qualification), a direct transfer by code-based value-mapping proved to be more convenient than databases
** a derived class may complete some data from a database or another source

For every stored data, there can be a method for transforming this data, if the data is stored as 'setFoo' in the application, the data is preprocessed with the method 'preFoo' in the derived class.

```php
<?php
     class Cross_JobPoster_Data_Careerbuilder extends Cross_JobPoster_Data
     {
         protected function preAction($action) {
              return strtoupper($action);
         }
     }
 ```
#### XSL

The input data for the XSL is a XML based on a WDDL-serialisation.

#### class for posting the data:

The requirements do make it most likely necessary to deal with several functions with more than on parameter and different WSDL for every function.
In the Poster-Class there is a method, which is calles immediatly after creating the object which will map the functions to xsl-file and a wsdl.
The asteriks '*' is taken as default if nothing else matches.
For the XLS-Pathes you can use keys, which is sometimes required in the SOAP-Functions.

```php
<?php
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
    
}
 ```

### Class Skeletons

The structure for the data-class is designed by the getter-methods, ie unless we use magics, every information for a job-offer has an own getter to provide these information.