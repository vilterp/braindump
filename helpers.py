import re, mimetypes, more_mime_types
from urllib import quote_plus as escape
from cherrypy import url as cherrypy_url

# let's see if enough helpers are necessary to make this page worth it

def list_to_human(thelist):
  # this can def be refactored
  # don't forget the oxford comma! (but not for 2-len lists)
  if isinstance(thelist,unicode): return thelist
  if len(thelist) is 2: return '%s and %s' % (thelist[0],thelist[1])
  final = ''
  for item in thelist[:-2]: final += item + ', '
  return final + thelist[-2] + ' and ' + thelist[-1]

def human_to_list(thestr):
  return flatten(re.split(', |and ',thestr))

def flatten(list):
  # is this in the std lib somewhere..?
  return [item for item in list if item]

def htmloptions(**options):
  if options is None:
    return ''
  else:
    final = ''
    for option in options.keys():
      final += ' %s="%s"' % (option,options[option])
    return final

def link(text, href='#', **options):
  return '<a href="%s"%s>%s</a>' % (url(href),htmloptions(**options),text)

def pagelink(page, **options):
  if isinstance(page,list):
    return list_to_human([pagelink(ind_page,**options) for ind_page in page])
  else:
    return link(page,'/show/%s' % page, **options)

def pageurl(page, **params):
  return url('/show/%s' % page, **params)

def load_css(source):
  if '.css' not in source: source += '.css'
  return '<link rel="stylesheet" type="text/css" href="%s"/>' % url('/stylesheets/'+source)

def load_js(source):
  if '.js' not in source: source += '.js'
  return '<script type="text/javascript" src="%s"></script>' % url('/javascripts/'+source)

def load_dynamic_js(source, **args):
  if '.js' not in source: source += '.js'
  return '<script type="text/javascript" src="%s"></script>' % url('/dynamic_javascripts/'+source, **args)

def autodiscovery_link(source, title, type="atom"):
  # TODO: opensearch
  mimetype = mimetypes.guess_type('.' + type)[0]
  return '<link rel="alternate" title="%s" type="%s" href="%s"/>' % \
                                                          (title,mimetype,url(source))

# this is probably in the stdlib somewhere...
def GET_params(**params):
  final = ''
  first = True
  for param in params.iteritems():
    if first: 
      final += '?'
      first = False
    else: final += '&'
    final += '%s=%s' % (escape(param[0]),escape(param[1]))
  return final

def url(path, **params):
  if path is '#': return path
  return cherrypy_url(path) + GET_params(**params)

def image(path, **options):
  return '<img src="%s"%s/>' % (url('/images/%s' % path),htmloptions(**options))

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
