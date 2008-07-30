from mako import runtime, filters, cache
UNDEFINED = runtime.UNDEFINED
_magic_number = 2
_modified_time = 1217383068.102056
_template_filename=u'templates/html/sidebar.mako'
_template_uri=u'sidebar.mako'
_template_cache=cache.Cache(__name__, _modified_time)
_source_encoding=None
_exports = []


def render_body(context,**pageargs):
    context.caller_stack.push_frame()
    try:
        __M_locals = dict(pageargs=pageargs)
        # SOURCE LINE 1
        context.write(u'<form>\n  <input type="text" name="some_name" value="Go To" id="some_name">\n</form>')
        return ''
    finally:
        context.caller_stack.pop_frame()


