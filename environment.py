import cherrypy, graphstore, re, os, urllib
from mako.template import Template
from mako.lookup import TemplateLookup
from mako.runtime import Context
import helpers

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

# lookup objects needed by mako to find inherited templates, etc
# one per format folder
lookups = {}
for format in os.listdir('templates'):
  if not re.search('^\.',format):
    lookups[format] = TemplateLookup(directories=['templates/%s' % format],module_directory='templates/%s' % format)

def register_to_context(context, themodule):
  for function in dir(themodule):
    context[function] = getattr(themodule,function)
  return context

def render(template,format='html',**context):
  """render templates/[format]/[template].mako and return result"""
  template = lookups[format].get_template('%s.mako' % template)
  
  # TODO: save all this in a permanent context object?
  # TODO: automate additions to context?
  register_to_context(context,helpers)
  
  return template.render(**context)
