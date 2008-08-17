filters = {}

def add(point, function):
  if not filters.has_key(point):
    filters[point] = []
  filters[point].append(function)

def do(point, text):
  for function in filters[point]:
    text = function(text)
  return text
