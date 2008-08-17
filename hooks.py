hooks = {}

def add(point, function):
  if not hooks.has_key(point):
    hooks[point] = []
  hooks[point].append(function)

def do(point):
  for function in hooks[point]:
    function()
