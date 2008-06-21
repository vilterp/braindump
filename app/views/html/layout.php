<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>&laquo; braindump &raquo;</title>
    <?php load_css('braindump') ?>
    <?php load_js('jquery') ?>
    <?php load_js('goto-box') ?>
    <?php if($runtime['action'] == 'show'): ?>
      <?php load_js('jquery.jeditable') ?>
      <?php include 'assets/js/edit-in-place.php' ?>
    <?php endif ?>
    <?php if($runtime['action'] == 'index') load_js('criteria-box') ?>
    <?php do_hooks('head') ?>
  </head>
  <body>
    <?php // TODO: layout with <div>s, css ?>
    <table id="content" width="100%">
      <tr>
        <td colspan="2" id="header">
          <h1><?php echo get_link('Braindump').'/'.implode('/',get_app_path()) ?></h1>
        </td>
      </tr>
      <tr id="content">
        <td id="main">
          <?php // FIXME: flashes don't work
          /* 
          <?php if (get_flashes()): ?>
            <?php foreach(get_flashes() as $flash): ?>
              <div class="flash"><?php echo $flash ?></div>
            <?php endforeach ?>
          <?php endif ?>
          */ ?>
          <?php if(file_exists($runtime['view'])) include $runtime['view'] ?>
          <?php do_hooks('after_main_content') ?>
        </td>
        <td id="sidebar" >
          <?php load_partial('sidebar') ?>
        </td>
      </tr>
      <tr>
        <td id="footer" colspan="2">
          this is a <a href="http://code.google.com/p/brain-dump/" title="v<?php echo BD_VERSION ?>">braindump</a>.
        </td>
      </tr>
    </table>
  </body>
</html>