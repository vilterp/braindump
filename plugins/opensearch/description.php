<?php mime_type("application/opensearchdescription+xml") ?>
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName>braindump</ShortName><?php // FIXME: specific site should go here... ?>
  <Url type="text/html" template="<?php echo pageurl("{searchTerms}") ?>"/>
</OpenSearchDescription>
