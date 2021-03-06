import cherrypy, jinja2, graphstore, re, os, mimetypes, more_mime_types, helpers
import simplejson, yaml, textile

basedir = os.path.abspath(os.path.dirname(__file__))

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

additional_filters = {
  'smartstr': helpers.smart_str,
  'textilize': textile.textile,
  'toyaml': lambda obj: yaml.safe_dump(obj,default_flow_style=False),
  'tojson': lambda obj: simplejson.dumps(obj,indent=2)
}
environments = {}
templatesdir = os.path.join(basedir,'templates')
for format in [format for format in os.listdir(templatesdir) if '.' not in format]:
  formatpath = '%s/%s' % (templatesdir,format)
  environments[format] = jinja2.Environment(loader=jinja2.FileSystemLoader(formatpath))
  environments[format].filters.update(additional_filters)

def add_to_context(context, themodule):
  for function in dir(themodule):
    context[function] = getattr(themodule,function)
  return context

def render(template, format='html', **context):
  """render templates/[format]/[template].jinja and return result"""
  content_type(mimetypes.guess_type('.'+format)[0])
  template = environments[format].get_template('%s.jinja' % template)
  
  add_to_context(context,helpers)
  
  return template.render(**context)
