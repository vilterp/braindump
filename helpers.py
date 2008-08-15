from urllib import quote_plus as escape
from cherrypy import url

# let's see if enough helpers are necessary to make this page worth it

def htmloptions(**options):
  if options is None:
    return ''
  else:
    final = ''
    for option in options.keys():
      final += ' %s="%s"' % (option,options[option])
    return final

def link(text, href, **options):
  return '<a href="%s"%s>%s</a>' % (url(href),htmloptions(**options),text)

def pagelink(page, **options):
  return link(page,'/show/%s' % escape(page),**options)

def load_css(source):
  if '.css' not in source: source += '.css'
  return '<link rel="stylesheet" type="text/css" href="%s"/>' % url('/stylesheets/'+source)

def load_js(source):
  if '.js' not in source: source += '.js'
  return '<script type="text/javascript" src="%s"></script>' % url('/javascripts/'+source)

def autodiscover_link(source, title, type="atom"):
  mimetypes = {'atom': 'application/atom+xml', 'rss': 'application/rss+xml'}
  return '<link rel="alternate" title="%s" type="%s" href="%s"/>' % (title,mimetypes[type],url(source))
