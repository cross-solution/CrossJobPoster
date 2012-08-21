CrossJobPoster
==============

    <p></p>
    <h1>Overview</h1>
    <p><br />
      Protokoll zur Übertragung von Arbeitsplatzangeboten<br />
    </p>
    <h1>References</h1>
    <p><br />
      hr-xml.org<br />
      Dokumentation Career<br />
      Dokumentation Monster<br />
    </p>
    <h1>Component Requirements, Constraints, and Acceptance Criteria</h1>
    <p>zu berücksichtigen:<br />
      <br />
    </p>
    <ul>
      <li>    es gibt sehr unterschiedliche Formen von Übertragungswegen: Soap,
        Mails, FTP</li>
      <li>    die Übertragungsprotokolle können auch sehr unterschiedlich sein:
        XML, HRXML in verschiedenen Versionen, CSV</li>
      <li>    ein Übertragungsweg kann mehrstufig sein, zB ein neuer Job bei
        Career wird in eine Bearbeitungsqueue gesetzt, ist die Bearbeitung
        erfolgreich, wird zeitlich versetzt ein Success-Status zurückgegeben.</li>
      <li>    Sofort zurückgegeben wird nur ein Bearbeitungsstatus 'inQueue' und
        eine Bearbeitungsnummer</li>
      <li>    einzelne Schnittstellen bieten (werden vermutlich) sehr
        unterschiedliche Vorgehensweisen für eine ähnliche Aufgabe anbieten</li>
      <li>    viele Protokolle halten sich grob an einen Standart, aber
        unterscheiden sich in Details und in der Notwendigkeit der Daten
        (pflicht, optional, nicht verwendet).</li>
      <li>    manche Daten haben eine schnittstellenbezogene Kodierung (zB
        Career für Abitur=DR3211), diese Kodierung kann abhängig sein von den
        Ländern.</li>
    </ul>
    <p><br />
      Das Program soll können:<br />
      <br />
    </p>
    <ul>
      <li>    Unterschiedliche Übertragungswege realisieren</li>
      <li>    Mehrstufige Prozesse beherrschen</li>
      <li>    Unterschiedliche Protokolle</li>
      <li>    offenes Datensystem, d.h. es können beliebig spezielle Daten
        hinzugeschrieben werden, verwendet werden sollen jedoch nur die Daten,
        die für die jeweilige Schnittstelle notwendig sind</li>
      <li>    schnittstellenspezifische Datendarstellung (zB für Abitur für
        Career oder Monster)</li>
    </ul>
    <p><br />
    </p>
    <h1>Dependencies on Other Framework Components</h1>
    <p><br />
    </p>
    <ul>
      <li>    Zend_Soap_Client</li>
    </ul>
    <p><br />
    </p>
    <ul>
      <li>Theory of Operation</li>
    </ul>
    <p>Die Operationen lassen sich auf drei Ebenen aufteilen<br />
      <br />
    </p>
    <ol>
      <li>    Anwendung - hier werden die verschiedenen Protokolle der
        Schnittstelle umgesetzt</li>
      <li>    Daten - hier werden die spezifischen Datenstrukturen umgesetzt</li>
      <li>    Datengewinnung - eine Schnittstelle zu der Datengewinnung der
        Anwendung</li>
    </ol>
    <p><br />
      Es soll eine Basis-Struktur für Daten und für Datenoperationen geben.<br />
      Grundsätzlich ist davon auszugehen, daß alle Informationen für alle
      Schnittstellen sich in dieser Struktur bis in eine gewisse Auflösung
      unterbringen lassen.<br />
      So wird es zB immer Kontaktinformationen zu einer Firma geben, und da wird
      es fast immer einen Ansprechpartner mit Telefonnummer geben.<br />
      Es werden aber auch einige Informationen spezifisch für eine Schnittstelle
      sein, zB Anfahrtsweg (nicht bei Monster aber bei Career) oder die
      Sichtbarkeit von Angaben (bei Monster aber nicht bei Career).<br />
      Diese Struktur entspricht einem Baum - ist wichtig weil nur so kann sie
      als XML oder serialize abgespeichert werden, was wiederum ermöglicht,
      beliebige spezifische Daten einzubinden<br />
      <br />
      Ebenso werden einige Standart-Aufgaben immer wieder auftreten (zB CRUD und
      Anmeldung/Authentifizierung)<br />
      Die Standartaufgaben sollen mit Basisparametern immer laufen, darüber
      hinaus können schnittstellenspezifische Parameter übergeben werden.<br />
      schön wäre hier, daß die Parameter einer Schnittstelle für eine andere
      Schnittstelle keine Wirkung hätten - so daß die Verwendung von einer
      Schnittstelle direkt auf eine andere Schnittstelle sich übertragen lässt.<br />
    </p>
    <h1>Milestones / Tasks</h1>
    <p><br />
      ...<br />
    </p>
    <h1>Class Index</h1>
    <p>Schnittstelle (Export)<br />
      <br />
          Verbindungsinformationen setzen *<br />
      <br />
      Daten<br />
    </p>
    <h1>Use Cases</h1>
    <p><br />
      Verwendung im Controller:<br />
      <br />
    </p>
    <ol>
      <li>    spezifische Schnittstellenklasse aufrufen</li>
      <li>    Kontaktinfos sind die Informationen, die zum Verbindungsaufbau
        benötigt werden.</li>
      <li>    die Daten-Klasse hat einige Standart-Methoden, ermöglicht aber
        auch eigene XML-Strukturen einzugeben und abzuspeichern (die Frage wäre
        hier, ob Strukturen dynamisch gebildet werden können, zB die Stelle, an
        der die JobInfo eingehängt werden kann)</li>
      <li>    der Befehl für die Operation wird eingegeben und ausgeführt.</li>
    </ol>
    <p><br />
      <code>      $op = new ExportJob_Careerbuilder();<br />
              $op-&gt;setContactinfo(array(...));<br />
          <br />
              $data = new Model_Jobs();<br />
              $data-&gt;setId(234);<br />
              $data-&gt;setJobtitle('Zend-Programmierer');<br />
              $data-&gt;setLocationTown('Frankfurt');<br />
          <br />
              $data-&gt;setRecruiterName('Jenny Recruiter');<br />
             $data-&gt;setRecruiterPhone('0123-4567890');<br />
             $data-&gt;setRecruiterEmail('JRecruiter@gotche.de');<br />
         <br />
            
$data-&gt;setRecruiterAddress('&lt;Location&gt;&lt;Street&gt;...&lt;/Street&gt;&lt;/Location&gt;');<br /> <br />
             $op-&gt;setData($data);<br />
         <br />
             $op-&gt;processHRXMLNew();<br />
         <br />
             $info = $op-&gt;getResponse();</code><br />
       <br />
       <br />
      <br />
      Klasse zur Datenverwaltung:<br />
      <code><br />
              class Model_Jobs extends Model_myParentClass implements
        HumanResourceData_Interface<br />
              {<br />
                  public function setJobTitle($title) {<br />
                      $jobInfo = $this-&gt;getJobInfo();<br />
                      $jobInfo-&gt;addNode('title', $title);<br />
                  }<br />
        <br />
                 public function getJobInfo() {<br />
                     $jobBasis = $this-&gt;getJobBasis();<br />
                     $jobBasis-&gt;addNode('JobInfo');<br />
                     return $jobBasis;<br />
                 }<br />
             }</code><br />
       <br />
      <br />
    </p>
    <h1>Class Skeletons<br />
    </h1>
    <p>Übersicht aller Klassen<br />
      <br />
      <br />
    </p>