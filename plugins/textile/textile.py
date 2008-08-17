# textile plugin: textilize page descriptions

import textile

def textilize(text):
  return textile.textile(text)

filters.add('description',textilize)