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

### zu berücksichtigen:

- es gibt sehr unterschiedliche Formen von Übertragungswegen: Soap, Mails, FTP
- die Übertragungsprotokolle können auch sehr unterschiedlich sein: XML, HRXML in verschiedenen Versionen, CSV
- ein Übertragungsweg kann mehrstufig sein, zB ein neuer Job bei Career wird in eine Bearbeitungsqueue gesetzt, ist die Bearbeitung erfolgreich, wird zeitlich versetzt ein Success-Status zurückgegeben.
- Sofort zurückgegeben wird nur ein Bearbeitungsstatus 'inQueue' und eine Bearbeitungsnummer
- einzelne Schnittstellen bieten (werden vermutlich) sehr unterschiedliche Vorgehensweisen für eine ähnliche Aufgabe anbieten
- viele Protokolle halten sich grob an einen Standart, aber unterscheiden sich in Details und in der Notwendigkeit der Daten (pflicht, optional, nicht verwendet).
- manche Daten haben eine schnittstellenbezogene Kodierung (zB Career für Abitur=DR3211), diese Kodierung kann abhängig sein von den Ländern.

### Das Program soll können:

- Unterschiedliche Übertragungswege realisieren
- Mehrstufige Prozesse beherrschen
- Unterschiedliche Protokolle
- offenes Datensystem, d.h. es können beliebig spezielle Daten hinzugeschrieben werden, verwendet werden sollen jedoch nur die Daten, die für die jeweilige Schnittstelle notwendig sind
- schnittstellenspezifische Datendarstellung (zB für Abitur für Career oder Monster)

### Dependencies on Other Framework Components

- Zend_Soap_Client

### Theory of Operation

Die Operationen lassen sich auf drei Ebenen aufteilen

- Anwendung - hier werden die verschiedenen Protokolle der Schnittstelle umgesetzt
- Daten - hier werden die spezifischen Datenstrukturen umgesetzt
- Datengewinnung - eine Schnittstelle zu der Datengewinnung der Anwendung

Es soll eine Basis-Struktur für Daten und für Datenoperationen geben.
Grundsätzlich ist davon auszugehen, daß alle Informationen für alle Schnittstellen sich in dieser Struktur bis in eine gewisse Auflösung unterbringen lassen.
So wird es zB immer Kontaktinformationen zu einer Firma geben, und da wird es fast immer einen Ansprechpartner mit Telefonnummer geben.
Es werden aber auch einige Informationen spezifisch für eine Schnittstelle sein, zB Anfahrtsweg (nicht bei Monster aber bei Career) oder die Sichtbarkeit von Angaben (bei Monster aber nicht bei Career).
Diese Struktur entspricht einem Baum - ist wichtig weil nur so kann sie als XML oder serialize abgespeichert werden, was wiederum ermöglicht, beliebige spezifische Daten einzubinden

Ebenso werden einige Standart-Aufgaben immer wieder auftreten (zB CRUD und Anmeldung/Authentifizierung)
Die Standartaufgaben sollen mit Basisparametern immer laufen, darüber hinaus können schnittstellenspezifische Parameter übergeben werden.
schön wäre hier, daß die Parameter einer Schnittstelle für eine andere Schnittstelle keine Wirkung hätten - so daß die Verwendung von einer Schnittstelle direkt auf eine andere Schnittstelle sich übertragen lässt.
Milestones / Tasks



### Class Index

Schnittstelle (Export)

    Verbindungsinformationen setzen *

Daten

###Use Cases


#### Creating a Posterclass

The posterclass accomplish following tasks
- assembling a distributable coding from the job data (eg XML)
- etablish a protocol (eg SOAP) 

The assembling of joboffers are mostly following a common standard, for example [HRXML](http://www.hr-xml.org) in the version 2.4. 
So if we want to establish a posting-class for joboffers, we just to implement the divergences from the standart.
For HRXML24 you can rely on, that the the top-node of the XML will always be 'PositionOpening' with subsequent sub-nodes for 'PositionRecordInfo', 'PositionPosting', 'PositionProfile' and 'UserArea'.
And 'PositionPosting' will usually have a specified set of subnodes, some of them mandatory, some of them optional or even dispensable.
All these nodes have an own method, which will be implement in the parentclass according to the standards, and can be overwritten by the subclass to implement divergences.
In the end we can get back a ready-for-distribution code.

But we also place the distributing process into this class. Usually you expect to finally post a job-offer, and if you do this in e.g. in SOAP, you will have a WSDL with a method for this.
In the derived class you can compose such a posting-method and unburden the user to understand the different methods of all the job-portals.
Which does not implies you can't call this methods directly, maybe there are some methods in the API of the portal, which are relevant to your application.
So the derived class implements all the methods of the API plus some standards methods you expect all portals will have.

```php
<?php
class JobPoster_Careerbuilder extends JobPoster_HRXML24
{
	// the node 'HowToApply' is a subnode of 'positionprofile'
	// in the standard it includes several subnodes, but we just want to include
	// the recruiter and the method of application
 	protected function _xmlHowToApply() {
        	$node = $this->createElement('HowToApply');
        	$node->appendChild($this->_xmlApplyPersonName());
        	$node->appendChild($this->_xmlApplicationMethod());
        	return $node;
    	}

}
```

#### Verwendung im Controller:

* spezifische Schnittstellenklasse aufrufen
* Kontaktinfos sind die Informationen, die zum Verbindungsaufbau benötigt werden.
* die Daten-Klasse hat einige Standart-Methoden, ermöglicht aber auch eigene XML-Strukturen einzugeben und abzuspeichern (die Frage wäre hier, ob Strukturen dynamisch gebildet werden können, zB die Stelle, an der die JobInfo eingehängt werden kann)
* der Befehl für die Operation wird eingegeben und ausgeführt.

```php
<?php

     $op = new JobPoster_Careerbuilder();
     $op->setContactinfo(array(...));
 
     $data = new Model_Jobs();
     $data->setId(234);
     $data->setJobtitle('Zend-Programmierer');
     $data->setLocationTown('Frankfurt');
 
     $data->setRecruiterName('Jenny Recruiter');
     $data->setRecruiterPhone('0123-4567890');
     $data->setRecruiterEmail('JRecruiter@gotche.de');
 
     $data->setRecruiterAddress('<Location><Street>...</Street></Location>');
 
     $op->setData($data);
 
     $op->processHRXMLNew();
 
     $info = $op->getResponse();
```
 
#### Klasse zur Datenverwaltung:

```php
<?php
      class Model_Jobs extends Model_myParentClass implements HumanResourceData_Interface
      {
          public function setJobTitle($title) {
              $jobInfo = $this->getJobInfo();
              $jobInfo->addNode('title', $title);
          }

         public function getJobInfo() {
             $jobBasis = $this->getJobBasis();
             $jobBasis->addNode('JobInfo');
             return $jobBasis;
         }
     }
 ```

Class Skeletons

Übersicht aller Klassen