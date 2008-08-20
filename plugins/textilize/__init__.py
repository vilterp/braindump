import filters, textile

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

def textilize(text):
  return textile.textile(smart_str(text,'ascii'))

filters.add('description',textilize)