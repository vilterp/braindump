import re, mimetypes, more_mime_types
from urllib import quote as escape
from cherrypy import url as cherrypy_url

def list_to_human(thelist):
  # list_to_human(['eggs','bacon','spam']) => 'eggs, bacon, and spam'
  if len(thelist) is 2: return '%s and %s' % (thelist[0],thelist[1])
  else: return ', '.join(thelist[:2]) + ', and ' + thelist[-1]

def human_to_list(thestr):
  # human_to_list('eggs, bacon, and spam') => ['eggs','bacon','spam']
  return re.split(', and |, | and ',thestr)

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
  elif isinstance(page,dict) and page.has_key('name'):
    return pagelink(page['name'])
  else:
    return link(page,'/show/%s' % escape(page), **options)

def pageurl(page, **params):
  return url('/show/%s' % escape(page), **params)

def load_css(source):
  if '.css' not in source: source += '.css'
  return '<link rel="stylesheet" type="text/css" href="%s"/>' % url('/stylesheets/'+source)

def load_js(source):
  if '.js' not in source: source += '.js'
  if not 'http://' in source: source = url('/javascripts/' + source)
  return '<script type="text/javascript" src="%s"></script>' % source

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
