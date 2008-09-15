from dateutil import parser

def compare_before(one, two):
  date_one = parser.parse(one)
  date_two = parser.parse(two)
  return date_one < date_two
compare_before.pattern = 'is before'

def compare_after(one, two):
  date_one = parser.parse(one)
  date_two = parser.parse(two)
  return date_one > date_two
compare_after.pattern = 'is after'
