<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>&laquo; braindump &raquo;</title>
    <?php load_css('braindump') ?>
    <?php load_js('jquery') ?>
    <?php include('app/helpers/in-place-edit.php') ?>
  </head>
  <body>
    <table id="content" width="100%">
      <tr>
        <td colspan="2" id="header">
          <h1><?php echo getLink('Braindump').'/'.implode('/',get_path()) ?></h1>
        </td>
      </tr>
      <tr id="content">
        <td id="main">
          <?php if(file_exists($view)) include $view ?>
        </td>
        <td id="sidebar" >
          <?php load_partial('sidebar') ?>
        </td>
      </tr>
      <tr>
        <td id="footer" colspan="2">
          <small>
            this is a <a href="http://code.google.com/p/brain-dump/" title="v<?php echo BD_VERSION ?>">braindump</a>.
          </small>
        </td>
      </tr>
    </table>
  </body>
</html>