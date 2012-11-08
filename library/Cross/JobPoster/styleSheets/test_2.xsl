<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : test.xsl
    Created on : 27. August 2012, 10:47
    Author     : weitz
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="xml" encoding="utf-8"/>

    <!-- TODO customize transformation rules 
         syntax recommendation http://www.w3.org/TR/xslt 
    -->

<xsl:template match="//data">
  <JobPosting>
    <xsl:apply-templates/>
  </JobPosting>
</xsl:template>

<xsl:template match="struct/var[@name='job']">
  <PositionTitle>Das ist der Wert: <xsl:value-of select="struct/var[@name='PositionTitle']/number" /></PositionTitle>
  <Organization>Das ist der Wert: <xsl:value-of select="struct/var[@name='Organization']/string" /></Organization>
  <Locations>
   <xsl:for-each select="struct/var[@name='locations']/array/struct">
     <Location>
      <City><xsl:value-of select="var[@name='city']/string"/></City>
      <ZipCode><xsl:value-of select="var[@name='zip']/string"/></ZipCode>
     </Location>
   </xsl:for-each>
  </Locations>
</xsl:template>

</xsl:stylesheet>
