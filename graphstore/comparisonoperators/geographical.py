import urllib

# uses google maps geocoding API 
# <http://code.google.com/apis/maps/documentation/services.html#Geocoding_Direct>
# to fetch coordinates and evaluate positions of addresses

def get_coordinates(q):
  data = urllib.urlopen('http://maps.google.com/maps/geo?q=%s&output=csv' % urllib.quote(q)).read().split(',')
  return (float(data[2]),float(data[3]))

def north_of(one, two):
  return get_coordinates(one)[0] > get_coordinates(two)[0]
north_of.pattern = 'is north of'

def south_of(one, two):
  return get_coordinates(one)[0] < get_coordinates(two)[0]
south_of.pattern = 'is south of'

def east_of(one, two):
  return get_coordinates(one)[1] > get_coordinates(two)[1]
east_of.pattern = 'is east of'

def west_of(one, two):
  return get_coordinates(one)[1] < get_coordinates(two)[1]
west_of.pattern = 'is west of'

def within_x_miles_of(one, two, miles=None):
  return True
within_x_miles_of.pattern = 'is within (\d) miles of'
