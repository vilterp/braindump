<!DOCTYPE html>
<!-- TODO: validate & correctly indent everything -->
<html>
  <head>
    <title>braindump${self.title()}</title>
    ${stylesheet_link_tag(url('/stylesheets/braindump'))}
    ${javascript_include_tag(url('/javascripts/jquery'))}
    ${javascript_include_tag(url('/javascripts/placeholders'))}
    ${self.head()}
  </head>
  <body>
    
    <div id="heading">
      <h1>${link_to('braindump',url('/'))}${self.heading()}</h1>
    </div>
    
    <!-- round this and just have it float there? -->
    <div id="sidebar">
      <form method="GET" action="redirect">
        <input type="text" value="Go To" id="goto_box" name="page" class="placeholder"/>
        <div id="sidebar_actions">
          ${self.sidebar_actions()}
        </div>
      </form>
    </div>
    
    <div id="content">
      ${self.content()}
    </div>
    
    <div id="footer">
      this is a <a href="http://code.google.com/p/brain-dump/">braindump</a>.
    </div>
    
  </body>
</html>