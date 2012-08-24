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

### to regard:

- there are various kinds of protocols: Soap, Mails, FTP
- the internal structure of the protocols can vary: XML, HRXML in different versions, CSV
- one task can have several measures of transfering information, all with their own protocol, eg. a new job for "CareerBuilder" is stored in a queue and marked as "in progress", and just after a certain time it changes status to "success".
- different portals do vary on the implementation of very similar accomplishments
- some protocols diverge just slightly from some standards, or have an own coding for classification (industry-type, kind of employment).

### capabilities:

- implement different datastructures
- support protocols with multiple transfers
- open system, ie the dataclass can take care of additional data, the posterclass can apply addional method, which are specific to a portal
- portalspecific encoding for data (ie industry-type)

### Dependencies on Other Components

- Zend_Soap_Client
- libxml
- expat-library

### Theory of Operation

the operation can by classified as

- storing information
- retrieving/completing information
- posting

The posterclass accomplish following tasks
- assembling a distributable coding from the job data (eg XML)
- transform the data to the structure, which is needed for the client
- etablish a protocol (eg SOAP) 

The intention is, to use one data-scheme for all portals and a common transformation standard for assembling the specific portal.
For data-storage we use a class with as less conditions as possible. Since we want to be able to connect this class with a database,
this class may contain a lot of getters, which are able to fetch the approbate values from the database and return them for the initial establishment of a common data object.
In implementation this would deduce, that we write a derived class, and overwrite those getter, to establish an access to our database.

For transformation we have chosen XSL for the reason:
* it's powerful
* we don't have to write code for every distinct portal (except XSL)
* we can handle large data

### Class Index

* Poster-Class (Export)
* Data-Class

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

     $op = new JobPoster();
     $op->setContactinfo(array('username' => 'carl jobposter', 'password' => 'go123'));
     $op->setProtocol('Careerbuilder');
 
     $data = new Model_Jobs();
     $data->setId(234);
     $data->setJobtitle('Zend-Programmierer');
     $data->setLocationTown('Frankfurt');
 
     $data->setRecruiterName('Jenny Recruiter');
     $data->setRecruiterPhone('0123-4567890');
     $data->setRecruiterEmail('JRecruiter@gotche.de');
 
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
     class MyModelBase_Jobs extends HumanResourceData24_Abstract implements HumanResourceData24_Interface
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