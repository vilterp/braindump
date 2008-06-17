<?php mime_type('application/xml') ?>
<?xml version="1.0" encoding="UTF-8"?>

<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?php echo (empty($_GET['criteria']) ? 'braindump' : $_GET['criteria']) ?></title>
    <link href="<?php echo $runtime['entire_url'] ?>" rel="self"/>
    <!-- <link href="<?php echo $config['base_url'] ?>"/> -->
    
    <?php include $runtime['view'] ?>
    
</feed>