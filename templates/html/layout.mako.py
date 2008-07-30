from mako import runtime, filters, cache
UNDEFINED = runtime.UNDEFINED
_magic_number = 2
_modified_time = 1217383067.709403
_template_filename=u'templates/html/layout.mako'
_template_uri=u'layout.mako'
_template_cache=cache.Cache(__name__, _modified_time)
_source_encoding=None
_exports = []


def render_body(context,**pageargs):
    context.caller_stack.push_frame()
    try:
        __M_locals = dict(pageargs=pageargs)
        self = context.get('self', UNDEFINED)
        # SOURCE LINE 1
        context.write(u'<!DOCTYPE html>\n<html>\n  <head>\n    <title>')
        # SOURCE LINE 4
        context.write(unicode(self.title()))
        context.write(u'</title>\n    <style type="text/css" media="screen">\n      /* fonts/global stuff */\n      body {\n        font-family: \'Lucida Grande\' sans-serif;\n      }\n      a {\n        text-decoration: none;\n        color: blue;\n      }\n      a:hover {\n        color: green;\n      }\n      /* layout */\n      #heading {\n        border-bottom: thin grey solid;\n      }\n      #heading h1 {\n        margin: .3em 0px .3em 0px;\n      }\n      #sidebar {\n        float: right;\n        border-left: thin grey solid;\n      }\n      #footer {\n        font-size: small;\n        border-top: thin grey solid;\n        clear: right;\n      }\n    </style>\n  </head>\n  <body>\n    \n    <div id="heading">\n      <h1>braindump')
        # SOURCE LINE 38
        context.write(unicode(self.heading()))
        context.write(u'</h1>\n    </div>\n    \n    <div id="content">\n      ')
        # SOURCE LINE 42
        context.write(unicode(self.content()))
        context.write(u'\n    </div>\n    \n    <div id="sidebar">\n      ')
        # SOURCE LINE 46
        runtime._include_file(context, u'sidebar.mako', _template_uri)
        context.write(u'\n    </div>\n    \n    <div id="footer">\n      this is a <a href="http://code.google.com/p/brain-dump/">braindump</a>.\n    </div>\n    \n  </body>\n</html>')
        return ''
    finally:
        context.caller_stack.pop_frame()


