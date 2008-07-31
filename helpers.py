from webhelpers import *
from urllib import quote
from cherrypy import url

# let's see if enough helpers are necessary to make this page worth it

def pagelink(page):
  return link_to(page,url('/show/%s' % quote(page)))
