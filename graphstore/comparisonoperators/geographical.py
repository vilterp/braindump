import geopy

g = geopy.geocoders.Google('ABQIAAAALF_wPa2EB69uDlWjblEszBR_kaQGh5mwnhfXDI65SHVheIGNsRRpbYi2FMuhVUYnAmYZZJKdy6EOVA')

def log(obj):
  f = open('/Users/pete/braindump/graphstore/comparisonoperators/log.txt','a')
  f.write(obj.__repr__()+"\n")
  f.close()
  return True


def get_coordinates(q):
  result = g.geocode(q)
  return result[1][0], result[1][1]

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
  loc_one = g.geocode(one)
  loc_two = g.geocode(two)
  distance = geopy.distance.distance(loc_one[1],loc_two[1]).miles
  log((one,two,miles,distance,distance <= float(miles)))
  return distance <= float(miles)
within_x_miles_of.pattern = 'is within (\d+) miles of'
