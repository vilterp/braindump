import re

<<<<<<< .mine
def compare_is(one, two, extraparam):
=======
def compare_is(one, two, slag):
  log((one, two, one.lower() == two.lower()))
>>>>>>> .r229
  return one.lower() == two.lower()
compare_is.pattern = 'is'

def is_not(one, two):
  return one != two
is_not.pattern = 'is not|!='

def compare_equals(one, two):
  return one == two
compare_equals.pattern = '='

def greater_than(one, two):
  return one > two
greater_than.pattern = '>'

def less_than(one, two):
  return one < two
less_than.pattern = '<'

def greater_than_or_equal_to(one, two):
  return one >= two
greater_than_or_equal_to.pattern = '>='

def less_than_or_equal_to(one, two):
  return one < two
less_than_or_equal_to.pattern = '<='

def regexp(one, two):
  result = re.search(two,one,re.I)
  if result:
    return True
  else:
    return False
regexp.pattern = 'regexp'
