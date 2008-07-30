<!DOCTYPE html>
<html>
  <head>
    <title>${self.title()}</title>
    ${stylesheet_link_tag(url('stylesheets/braindump'))}
    ${javascript_include_tag(url('javascripts/jquery'))}
    ${self.head()}
  </head>
  <body>
    
    <div id="heading">
      <h1>${link_to('braindump',url('/')) + self.heading()}</h1>
    </div>
    
    <!-- round this and just have it float there? -->
    <div id="sidebar">
      <%include file="sidebar.mako"/>
    </div>
    
    <div id="content">
      ${self.content()}
    </div>
    
    <div id="footer">
      this is a <a href="http://code.google.com/p/brain-dump/">braindump</a>.
    </div>
    
  </body>
</html>