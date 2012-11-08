<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:php="http://php.net/xsl">

<xsl:output method="text" encoding="utf-8"/>

<xsl:template match="//data">
    <xsl:value-of select="struct/var[@name='did']/string" />
</xsl:template>

</xsl:stylesheet>