import cherrypy, jinja2, graphstore, re, os, mimetypes, more_mime_types, helpers
import simplejson, yaml, textile

def redirect(url):
  """redirect the browser to url"""
  cherrypy.response.status = 302
  cherrypy.response.headers['Location'] = cherrypy.url(url)

def content_type(type):
  print 'setting content type to', type
  cherrypy.response.headers['Content-type'] = type

def init_graph(thread_index):
  """the graph object has to be in cherrypy's special thread_data 
     variable because the SQLite object it contains can't be passed
     between threads."""
  cherrypy.thread_data.graph = graphstore.Graph('graph.db')

cherrypy.engine.subscribe('start_thread',init_graph)

environments = {}
additional_filters = {
  'smart_str': helpers.smart_str,
  'textilize': textile.textile,
  'yamlize': yaml.dump,
  'jsonify': simplejson.dump
}
for format in [format for format in os.listdir('templates') if '.' not in format]:
  environments[format] = jinja2.Environment(loader=jinja2.FileSystemLoader('templates/%s' % format))
  environments[format].filters.update(additional_filters)

def add_to_context(context, themodule):
  for function in dir(themodule):
    context[function] = getattr(themodule,function)
  return context

def render(template, format='html', contenttype=None, **context):
  """render templates/[format]/[template].jinja and return result"""
  if contenttype is not None:
    content_type(mimetypes.guess_type('.'+format)[0])
  else:
    print contenttype
    content_type(contenttype)
  template = environments[format].get_template('%s.jinja' % template)
  
  # TODO: save all this in a permanent context object?
  # TODO: automate additions to context?
  add_to_context(context,helpers)
  
  return template.render(**context)
