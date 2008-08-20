from framework import *

# todo: gzip static assets

class Main:
  
  def index(self, criteria=None, format='html'):
    pages = cherrypy.thread_data.graph.list(criteria)
    return render('index',format,pages=pages,criteria=criteria)
  index.exposed = True
  
  def list(self, criteria=None, **params):
    """for index's ajax interface: returns a simple <ul>"""
    pages = cherrypy.thread_data.graph.list(criteria)
    return render('list',pages=pages,criteria=criteria)
  list.exposed = True
  
  def show(self, pagename, section=None, format='html'):
    graph = cherrypy.thread_data.graph
    try:
      page = dict(name=pagename,
                  metadata=graph.get(pagename),
                  description=graph.describe(pagename),
                  backlinks=graph.backlinks(pagename))
    except:
      page = dict()
    # render metadata and description sections in alt. formats?
    # apply filters?!?
    if section == 'metadata':
      content_type('text/plain')
      return render('edit-metadata',page=page)
    elif section == 'description':
      content_type('text/plain')
      return render('edit-description',page=page)
    else:
      return render('show',format,page=page)
  show.exposed = True
  
  def save_metadata(self, page, metadata):
    # save inferred types either here or in Graph.save()
    pagedata = {}
    for line in metadata.split("\n"):
      item = line.split(':')
      if len(item) is 2: # check agains blank lines
        attribute = item[0].strip()
        value = item[1].strip()
        if attribute and item: # check against blank attrs/values
          cherrypy.thread_data.graph.set(page,attribute,value) # save to db
          pagedata[attribute] = value
    return render('metadata-html',page=dict(metadata=pagedata))
  save_metadata.exposed = True
  
  def save_description(self, page, description=None):
    if description.strip():
      cherrypy.thread_data.graph.describe(page,description)
    return render('description-html',page=dict(description=description))
  save_description.exposed = True
  
  def goto(self, page):
    redirect('show/%s' % page)
  goto.exposed = True
  
  def delete_everything(self):
    g = cherrypy.thread_data.graph
    g.execute('DELETE FROM pages')
    g.execute('DELETE FROM triples')
    redirect('/')
  delete_everything.exposed = True
  
