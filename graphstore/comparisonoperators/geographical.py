import geopy

g = geopy.geocoders.Google('ABQIAAAALF_wPa2EB69uDlWjblEszBR_kaQGh5mwnhfXDI65SHVheIGNsRRpbYi2FMuhVUYnAmYZZJKdy6EOVA')

cache = {}

def get_coordinates(q):
  if q in cache:
    return cache[q]
  else:
    lat, long = g.geocode(q)[1]
    cache[q] = (lat, long)
    return lat, long

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

def within_x_miles_of(one, two, miles):
  loc_one = get_coordinates(one)
  loc_two = get_coordinates(two)
  distance = geopy.distance.distance(loc_one,loc_two).miles
  return distance <= float(miles)
within_x_miles_of.pattern = 'is within (\d+) miles of'
