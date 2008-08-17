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
      page = object()
      page.name = pagename
      page.metadata = graph.get(pagename)
      page.description = graph.describe(pagename)
      page.backlinks = graph.backlinks(page)
    except graphstore.util.NonexistentPageError:
      page = object()
    # render metadata and description sections in alt. formats?
    # apply filters?!?
    if section == 'metadata':
      content_type('text/plain')
      return render('metadata-plain',page=page)
    elif section == 'description':
      content_type('text/plain')
      return render('description-plain',page=page)
    else:
      return render('show',format,page=page)
  show.exposed = True
  
  def savemetadata(self, page, metadata=None, **kwargs):
    # save inferred types either here or in Graph.save()
    pagedata = {}
    for line in metadata.split("\n"):
      item = line.split(':')
      attribute = item[0].strip()
      value = item[1].strip()
      pagedata[attribute] = value
    cherrypy.thread_data.graph[page].metadata = pagedata
    return render('metadata-html',page=pagedata)
  savemetadata.exposed = True
  
  def goto(self, page):
    redirect('show/%s' % page)
  goto.exposed = True
  
  def delete_everything(self):
    g = cherrypy.thread_data.graph
    g.execute('DELETE FROM pages')
    g.execute('DELETE FROM triples')
    redirect('/')
  delete_everything.exposed = True

  