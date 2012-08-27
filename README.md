### CrossJobPoster

## Status

*pre-alpha*

## Overview

PHP implementation of various interfaces to post job offers to jobportals like careerbuilder, monster or FAZjob.NET

##References

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
The data-object get assigned to a class for transforming the data for Transfer to a portal.

### Class Index

* Cross_JobPoster_CareerBuilder
* Cross_JobPoster_Monster
* Cross_JobPoster_Abstract
* Cross_JobPoster_Data_Hrxml
* Cross_JobPoster_Data_Abstract
* Cross_JobPoster_Data_Interface

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

     $op = new Cross_JobPoster_CareerBuilder();
     $op->setCredentialName('username');
     $op->setCredentialPassword('password');
 
     $data = new Model_Jobs();
     $data->id = 234;
     $data->jobtitle = 'Zend-Programmierer';
     $data->locationTown = 'Frankfurt';
 
     $data->recruiterName = 'Jenny Recruiter';
     $data->recruiterPhone = '0123-4567890';
     $data->recruiterEmail = 'JRecruiter@gotche.de';
 
     $op->setData($data);
 
     // the following command is determined by the API of the portal
     $op->processHRXMLNew();
 
     // the following command is determined by the API of the portal
     $info = $op->getResponse();
```
 
#### class for preserving data:

* basically this class is a container for all the data needed in the poster-protocol
* what is really needed are just the getter-method, because they will provide an interface for all the data, which is used in the poster-class
* the intention is to write own classes, which implements the getter-methods directly from the database

```php
<?php
     class Cross_JobPoster_Data_Hrsml extends Cross_JobPoster_Data_Abstract implements Cross_JobPoster_Data_Interface
     {
         public function setJobTitle($title) {
              $this->setData('jobtitle', $title);
         }

         public function getJobInfo() {
             return $this->getData('jobinfo');
         }

	// the following method is inherited and provides a standarized datascheme
	public function getXML() {
		$data = new StdClass;
		// this getter either provides stored data, 
		// or fetch data from a database.
		// in the second case you can expect this method coming from a derived class
		$data->PositionTitle = $this->getPositionTitle();
		xmlString = wddx_serialize_vars("job");
	}
     }
 ```

### Class Skeletons

The structure for the data-class is designed by the getter-methods, ie unless we use magics, every information for a job-offer has an own getter to provide these information.