import cherrypy, graphstore, re, os
from mako.template import Template
from mako.lookup import TemplateLookup

def redirect(url):
  cherrypy.response.status = 302
  cherrypy.response.headers['Location'] = url

def init_graph(thread_index):
  cherrypy.thread_data.graph = graphstore.Graph('graph.db')

cherrypy.engine.subscribe('start_thread',init_graph)

lookups = {}
for format in os.listdir('templates'):
  if not re.search('^\.',format):
    lookups[format] = TemplateLookup(directories=['templates/%s' % format],
                                     module_directory='templates/%s' % format)

def render(template,format,**context):
  template = lookups[format].get_template('%s.mako' % template)
  return template.render(**context)
