<!DOCTYPE html>
<!-- TODO: validate & correctly indent everything -->
<html>
  <head>
    <title>braindump${self.title()}</title>
    ${load_css('braindump')}
    ${load_js('jquery')}
    ${load_js('placeholders')}
    ${self.head()}
  </head>
  <body>
    
    <div id="heading">
      <h1>${link('braindump','/')}${self.heading()}</h1>
    </div>
    
    <!-- round this and just have it float there? -->
    <div id="sidebar">
      <form method="GET" action="${url('/goto')}">
        <input type="text" value="Go To" id="goto_box" name="page" class="placeholder"/>
      </form>
      <div id="sidebar_actions">
        ${self.sidebar_actions()}
      </div>
    </div>
    
    <div id="content">
      ${self.content()}
    </div>
    
    <div id="footer">
      this is a <a href="http://code.google.com/p/brain-dump/">braindump</a>.
    </div>
    
  </body>
</html>