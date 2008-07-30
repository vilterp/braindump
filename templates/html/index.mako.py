from mako import runtime, filters, cache
UNDEFINED = runtime.UNDEFINED
_magic_number = 2
_modified_time = 1217383066.8205659
_template_filename='templates/html/index.mako'
_template_uri='index.mako'
_template_cache=cache.Cache(__name__, _modified_time)
_source_encoding=None
_exports = ['content', 'heading', 'title']


def _mako_get_namespace(context, name):
    try:
        return context.namespaces[(__name__, name)]
    except KeyError:
        _mako_generate_namespaces(context)
        return context.namespaces[(__name__, name)]
def _mako_generate_namespaces(context):
    pass
def _mako_inherit(template, context):
    _mako_generate_namespaces(context)
    return runtime._inherit_from(context, u'layout.mako', _template_uri)
def render_body(context,**pageargs):
    context.caller_stack.push_frame()
    try:
        __M_locals = dict(pageargs=pageargs)
        # SOURCE LINE 1
        context.write(u'  \n')
        # SOURCE LINE 2
        context.write(u'\n')
        # SOURCE LINE 3
        context.write(u'\n')
        return ''
    finally:
        context.caller_stack.pop_frame()


def render_content(context):
    context.caller_stack.push_frame()
    try:
        pages = context.get('pages', UNDEFINED)
        # SOURCE LINE 4
        context.write(u'\n<ul>\n')
        # SOURCE LINE 6
        for page in pages:
            # SOURCE LINE 7
            context.write(u'  <li>')
            context.write(unicode(page))
            context.write(u'</li>\n')
        # SOURCE LINE 9
        context.write(u'</ul>\n')
        return ''
    finally:
        context.caller_stack.pop_frame()


def render_heading(context):
    context.caller_stack.push_frame()
    try:
        return ''
    finally:
        context.caller_stack.pop_frame()


def render_title(context):
    context.caller_stack.push_frame()
    try:
        return ''
    finally:
        context.caller_stack.pop_frame()


