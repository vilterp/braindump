import cherrypy, graphstore, re, os
from mako.template import Template
from mako.lookup import TemplateLookup
from mako.runtime import Context
import webhelpers

def redirect(url):
  """redirect the browser to url"""
  cherrypy.response.status = 302
  cherrypy.response.headers['Location'] = url

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
    lookups[format] = TemplateLookup(directories=['templates/%s' % format])

def register_to_context(context, themodule):
  for function in dir(themodule):
    context[function] = getattr(themodule,function)
  return context

def render(template,format,**context):
  """render templates/[format]/[template].mako and return result"""
  template = lookups[format].get_template('%s.mako' % template)
  # makes all functions from the webhelpers module usable
  register_to_context(context,webhelpers)
  context['url'] = cherrypy.url
  return template.render(**context)
