from framework import *
from graphstore.util import *

class Main:
  
  def index(self, criteria=None, sections='metadata|description', format='html'):
    """main api."""
    graph = cherrypy.thread_data.graph
    try:
      sections = sections.split('|')
      pages = graph.select(criteria,sections=sections)
      for page in pages: page['url'] = helpers.pageurl(page['name'])
    except NonexistentPageError:
      pages = []
    except NoMatchingComparisonOperatorError:
      pages = []
    return render('index',format,pages=pages,criteria=criteria)
  index.exposed = True
  
  def list(self, criteria=None, **params):
    """for index's ajax interface: returns a simple <ul>"""
    try:
      pages = cherrypy.thread_data.graph.list(criteria)
    except NonexistentPageError:
      pages = []
    except NoMatchingComparisonOperatorError:
      pages = []
    return render('list',pages=pages,criteria=criteria)
  list.exposed = True
  
  def visualize(self, visualization, criteria=None):
    return render(visualization,criteria=criteria)
  visualize.exposed = True
  
  def show(self, pagename, format='html'):
    graph = cherrypy.thread_data.graph
    try:
      page = dict(name=graph.resolve_name(pagename),
                  metadata=graph.get(pagename),
                  description=graph.describe(pagename),
                  backlinks=graph.backlinks(pagename))
    except: # page doesn't exist
      page = dict(name=pagename)
    return render('show',format,page=page)
  show.exposed = True
  
  def edit_description(self, page):
    description = cherrypy.thread_data.graph.describe(page)
    return render('edit-description',page=dict(description=description))
  edit_description.exposed = True
  
  def save(self, page, predicate=None, object=None, description=None):
    if description is not None: #save description
      cherrypy.thread_data.graph.describe(page,description)
      return render('description-html',page=dict(description=description))
    elif object is not None and predicate is not None: # save datum
      cherrypy.thread_data.graph.set(page, predicate, object)
      return render('datum',datum_tuple=(predicate,object))
  save.exposed = True
  
  def unset(self, subject, predicate=None):
    cherrypy.thread_data.graph.unset(subject,predicate)
  unset.exposed = True
  
  def delete(self, page):
    cherrypy.thread_data.graph.unset(page)
    cherrypy.thread_data.graph.describe(page,'')
    redirect('/')
  delete.exposed = True
  
  def rename(self, page, newname):
    cherrypy.thread_data.graph.rename(page,newname)
    redirect('/show/%s' % newname)
    # flash something? "page renamed"..?
  rename.exposed = True
    
  def goto(self, page):
    redirect('show/%s' % page)
  goto.exposed = True
  
  def delete_everything(self):
    g = cherrypy.thread_data.graph
    g.execute('DELETE FROM pages')
    g.execute('DELETE FROM triples')
    redirect('/')
  delete_everything.exposed = True
  
  def dynamic_javascripts(self, path, **args):
    content_type('text/javascript')
    template = jinja2.Template(open('templates/html/javascripts/%s.jinja' % path).read())
    add_to_context(args,helpers)
    return template.render(**args)
  dynamic_javascripts.exposed = True
  
