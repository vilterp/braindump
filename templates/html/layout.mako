<!DOCTYPE html>
<html>
  <head>
    <title>${self.title()}</title>
    <style type="text/css" media="screen">
      /* fonts/global stuff */
      body {
        font-family: 'Lucida Grande' sans-serif;
      }
      a {
        text-decoration: none;
        color: blue;
      }
      a:hover {
        color: green;
      }
      /* layout */
      #heading {
        border-bottom: thin grey solid;
      }
      #heading h1 {
        margin: .3em 0px .3em 0px;
      }
      #sidebar {
        float: right;
        border-left: thin grey solid;
      }
      #footer {
        font-size: small;
        border-top: thin grey solid;
        clear: right;
      }
    </style>
  </head>
  <body>
    
    <div id="heading">
      <h1>braindump${self.heading()}</h1>
    </div>
    
    <div id="content">
      ${self.content()}
    </div>
    
    <div id="sidebar">
      <%include file="sidebar.mako"/>
    </div>
    
    <div id="footer">
      this is a <a href="http://code.google.com/p/brain-dump/">braindump</a>.
    </div>
    
  </body>
</html>