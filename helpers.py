from urllib import quote_plus as escape
from cherrypy import url
from graphstore.page import Page
import re

# let's see if enough helpers are necessary to make this page worth it

def list_to_human(thelist):
  # this can def be refactored
  # don't forget the oxford comma! (but not for 2-len lists)
  if isinstance(thelist,unicode): return thelist
  final = ''
  for item in thelist[:-2]:
    final += item + ', '
  return final + thelist[-2] + ' and ' + thelist[-1]

def human_to_list(thestr):
  return flatten(re.split(', |and ',thestr))

def flatten(thelist):
  # is this in the std lib somewhere..?
  final = []
  for item in thelist:
    if item: final.append(item)
  return final

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

# from django: http://code.djangoproject.com/browser/django/trunk/django/utils/encoding.py
def smart_str(s, encoding='utf-8', strings_only=False, errors='strict'):
    """
    Returns a bytestring version of 's', encoded as specified in 'encoding'.
    
    If strings_only is True, don't convert (some) non-string-like objects.
    """
    if strings_only and isinstance(s, (types.NoneType, int)):
        return s
    elif not isinstance(s, basestring):
        try:
            return str(s)
        except UnicodeEncodeError:
            return unicode(s).encode(encoding, errors)
    elif isinstance(s, unicode):
        return s.encode(encoding, errors)
    elif s and encoding != 'utf-8':
        return s.decode('utf-8', errors).encode(encoding, errors)
    else:
        return s
