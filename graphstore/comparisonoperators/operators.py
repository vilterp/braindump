import re, geocoding

def compare_is(one, two):
  return one.lower() == two.lower()
compare_is.name = 'is'

def is_not(one, two):
  return one != two
is_not.name = 'is not'

def regexp(one, two):
  print one, two
  result = re.search(two,one,re.I)
  if result:
    return True
  else:
    return False
regexp.name = 'regexp'

from geocoding import north_of, east_of, south_of, west_of