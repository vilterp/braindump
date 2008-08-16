from urllib import quote_plus as escape
from cherrypy import url
from graphstore.page import Page
import re

# let's see if enough helpers are necessary to make this page worth it

# humanize helpers. FIXME: better place for these?
# def list_to_human(thelist):
#   final = ''
#   for item in thelist[:-1]:
#     final += item + ', '
#   return final + 'and ' + thelist[-1]
# 
# def human_to_list(thestr):
#   return flatten(re.split(', |and ',thestr))
# 
# def flatten(thelist):
#   # is this in the std lib somewhere..?
#   final = []
#   for item in thelist:
#     if item: final.append(item)
#   return final
# 
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
  print page
  if isinstance(page,list):
    return list_to_human([pagelink(ind_page,**options) for ind_page in page])
  elif isinstance(page,Page):
    return pagelink(page.name **options)
  else:
    return link(page,'/show/%s' % page, **options)

def load_css(source):
  if '.css' not in source: source += '.css'
  return '<link rel="stylesheet" type="text/css" href="%s"/>' % url('/stylesheets/'+source)

def load_js(source):
  if '.js' not in source: source += '.js'
  return '<script type="text/javascript" src="%s"></script>' % url('/javascripts/'+source)

def autodiscover_link(source, title, type="atom"):
  # TODO: opensearch
  mimetypes = {'atom': 'application/atom+xml', 'rss': 'application/rss+xml'}
  return '<link rel="alternate" title="%s" type="%s" href="%s"/>' % (title,mimetypes[type],url(source))
