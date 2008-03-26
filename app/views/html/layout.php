<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Hello!</title>
    
    <?php load_scriptaculous(); ?>
    
    <style type="text/css" media="screen">
      body {
        font-family: 'Lucida Grande', Verdana, Sans-serif;
        padding: 20px;
      }
      .path {
        border: thin solid blue;
        padding: 7px;
        background-color: #F3F3F3;
      }
      a {
        color: blue;
        text-decoration: none;
      }
      a:hover {
        color: green;
      }
    </style>
    
  </head>
  <body>
    <?php include($view); ?>
  </body>
</html>