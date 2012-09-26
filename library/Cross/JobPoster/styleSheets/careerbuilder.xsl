<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:php="http://php.net/xsl">

<xsl:output method="xml" encoding="utf-8"/>

<xsl:template match="//data">
  <PositionOpening>
    <xsl:apply-templates/>
  </PositionOpening>
</xsl:template>

<!-- <xsl:template match="struct/var[@name='job']"> -->
<xsl:template match="struct">
    
  <PositionRecordInfo>
    <Status><xsl:value-of select="var[@name='status']/string" /></Status>
    <xsl:if test="0 &lt; string-length(var[@name='id']/string)">
        <Id>
            <IdValue><xsl:value-of select="var[@name='id']/string" /></IdValue>
        </Id>
    </xsl:if>
  </PositionRecordInfo>
  <PositionProfile>
    <PositionDetail>
      <PositionTitle><xsl:value-of select="var[@name='title']/string" /></PositionTitle>
            <PhysicalLocation>
        <Area type="municipality">
          <Value><xsl:value-of select="var[@name='joblocationtown']/string" /></Value>
        </Area>
        <Area type="x:state">
          <Value><xsl:value-of select="var[@name='joblocationstate']/string" /></Value>
        </Area>
        <Area type="CountryCode">
          <Value><xsl:value-of select="var[@name='joblocationcountry']/string" /></Value>
        </Area>
      </PhysicalLocation>
    </PositionDetail>
    <HowToApply>
      <ApplicationMethod>
        <InternetEmailAddress><xsl:value-of select="var[@name='recruiteremail']/string" /></InternetEmailAddress>
        <Telephone>
         <SubscriberNumber><xsl:value-of select="var[@name='recruiterphone']/string" /></SubscriberNumber>
        </Telephone>
      </ApplicationMethod>
    </HowToApply>
      <FormattedPositionDescription>
        <Name>Description</Name>
        <Value>
            <!-- <xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text> -->
            <xsl:value-of select="var[@name='description']/string" />
            <!-- <xsl:text disable-output-escaping="yes">]]&gt;</xsl:text> -->
        </Value>
      </FormattedPositionDescription>
      <FormattedPositionDescription>
            <Name>Requirements</Name>
            <Value>
            <!-- <xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text> -->
            <xsl:value-of select="var[@name='requirements']/string" />
            <!-- <xsl:text disable-output-escaping="yes">]]&gt;</xsl:text> -->
            </Value>
        </FormattedPositionDescription>
  </PositionProfile>
  <UserArea>
     <CareerBuilder>
         <xsl:if test="0 &lt; string-length(var[@name='posteremail']/string) and 0 &lt; string-length(var[@name='posterpassword']/string)">
            <Field name="CBPosterEmail">
                <xsl:attribute name="value"><xsl:value-of select="var[@name='posteremail']/string" /></xsl:attribute>
            </Field>
            <Field name="CBPosterPassword">
                <xsl:attribute name="value"><xsl:value-of select="var[@name='posterpassword']/string" /></xsl:attribute>
            </Field>
        </xsl:if>
        <xsl:if test="0 &lt; string-length(var[@name='vendorid']/string)">
            <Field name="CBVendorID">
                <xsl:attribute name="value"><xsl:value-of select="var[@name='vendorid']/string" /></xsl:attribute>
            </Field>
        </xsl:if>
        <Field name="CBOrigin">
          <xsl:attribute name="value"><xsl:value-of select="var[@name='origin']/string" /></xsl:attribute>
        </Field>
        <Field name="CBJobTypeCode">
          <xsl:attribute name="value"><xsl:value-of select="var[@name='posterjobtype']/string" /></xsl:attribute>
        </Field>
        <Field name="CBAction">
          <xsl:attribute name="value"><xsl:value-of select="var[@name='action']/string" /></xsl:attribute>
        </Field>
        <xsl:if test="0 &lt; string-length(var[@name='userjobid']/string)">
            <Field name="CBUserJobID">
                <xsl:attribute name="value"><xsl:value-of select="var[@name='userjobid']/string" /></xsl:attribute>
            </Field>
        </xsl:if>
     </CareerBuilder>
  </UserArea>
  
</xsl:template>
</xsl:stylesheet>