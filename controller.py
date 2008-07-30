from environment import *

class Main:
  
  def index(self, criteria=None, format='html'):
    pages = cherrypy.thread_data.graph.list(criteria)
    return render('index',format,pages=pages,criteria=criteria)
  index.exposed = True
  
  def list(self, criteria=None):
    """for ajax: returns a simple <ul>"""
    pages = cherrypy.thread_data.graph.list(criteria)
    return render('list','html',pages=pages)
  
  def show(self, page, format='html'):
    page = cherrypy.thread_data.graph[page]
    return render('show',format,page=page)
  show.exposed = True
  
