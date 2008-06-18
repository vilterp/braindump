<?php mime_type('application/rdf+xml') ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>

<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/terms/"
         xmlns:braindump="<?php echo $config['base_url'] ?>">
         
    <?php include $runtime['view'] ?>
    
</rdf:RDF>