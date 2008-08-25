import cherrypy, jinja2, graphstore, re, os, mimetypes, more_mime_types, helpers

def redirect(url):
  """redirect the browser to url"""
  cherrypy.response.status = 302
  cherrypy.response.headers['Location'] = cherrypy.url(url)

def content_type(type):
  cherrypy.response.headers['Content-type'] = type

def init_graph(thread_index):
  """the graph object has to be in cherrypy's special thread_data 
     variable because the SQLite object it contains can't be passed
     between threads."""
  cherrypy.thread_data.graph = graphstore.Graph('graph.db')

cherrypy.engine.subscribe('start_thread',init_graph)

template_environment = jinja2.Environment(loader=jinja2.FileSystemLoader('templates'))
import simplejson, yaml, textile
template_environment.filters['smart_str'] = helpers.smart_str
template_environment.filters['textilize'] = textile.textile
template_environment.filters['yamlize'] = yaml.dump
template_environment.filters['jsonify'] = simplejson.dump

def add_to_context(context, themodule):
  for function in dir(themodule):
    context[function] = getattr(themodule,function)
  return context

def render(template,format='html',**context):
  """render templates/[format]/[template].jinja and return result"""
  content_type(mimetypes.guess_type('.'+format)[0])
  print '%s/%s.jinja' % (format, template)
  template = template_environment.get_template('%s/%s.jinja' % (format, template))
  
  # TODO: save all this in a permanent context object?
  # TODO: automate additions to context?
  add_to_context(context,helpers)
  
  return template.render(**context)
