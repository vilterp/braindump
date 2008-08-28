from framework import *

# todo: gzip static assets

class Main:
  
  def index(self, criteria=None, format='html'):
    pages = cherrypy.thread_data.graph.list(criteria)
    return render('index',format,pages=pages,criteria=criteria)
  index.exposed = True
  
  def list(self, criteria=None, **params):
    """for index's ajax interface: returns a simple <ul>"""
    try:
      pages = cherrypy.thread_data.graph.list(criteria)
    except:
      pages = []
    return render('list',pages=pages,criteria=criteria)
  list.exposed = True
  
  def show(self, pagename, section=None, format='html', **junk):
    graph = cherrypy.thread_data.graph
    try:
      page = dict(name=pagename,
                  metadata=graph.get(pagename),
                  description=graph.describe(pagename),
                  backlinks=graph.backlinks(pagename))
    except:
      page = dict(name=pagename)
    # render metadata and description sections in alt. formats?
    # apply filters?!?
    if section == 'metadata':
      return render('edit-metadata',page=page)
    elif section == 'description':
      return page['description']
    else:
      return render('show',format,page=page)
  show.exposed = True
  
  def save_metadata(self, subject, object=None, predicate=None):
    print subject, predicate, object
    cherrypy.thread_data.graph.set(subject, predicate, object)
    return render('datum',datum=(predicate,object))
  save_metadata.exposed = True
  
  def unset_metadata(self, subject, predicate=None):
    cherrypy.thread_data.graph.unset(subject,predicate)
  unset_metadata.exposed = True
  
  def edit_description(self, page):
    description = cherrypy.thread_data.graph.describe(page)
    return render('edit-description',page=dict(description=description))
  edit_description.exposed = True
  
  def save_description(self, page, description=None):
    if description and description.strip():
      cherrypy.thread_data.graph.describe(page,description)
    else:
      description = cherrypy.thread_data.graph.describe(page)
    return render('description-html',page=dict(description=description))
  save_description.exposed = True
  
  def save(self, page, predicate=None, object=None, description=None):
    if description is not None: #save description
      cherrypy.thread_data.graph.describe(page,description)
      return render('description-html',page=dict(description=description))
    elif object is not None and predicate is not None:
      cherrypy.thread_data.graph.set(page, predicate, object)
      return render('datum',datum_tuple=(predicate,object))
  save.exposed = True
  
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
  
