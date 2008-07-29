# for magic attribute access

# how to return the whole dict?

class Page:
  
  def __init__(self, graph, name):
    self.graph = graph
    self.name = name
  
  # repr vs. str... hmm... 
  def __repr__(self): # should people just use ".name"?
    return self.name.__repr__()
  
  def __str__(self):
    return self.graph.get(self.name).__str__()
  
  def __eq__(self, other):
    return self.name is other.name
  
  def __getattr__(self, attr):
    if attr is 'description':
      return self.graph.describe(self.name)
    elif attr is 'backlinks':
      return self.graph.backlinks(self.name)
    else:
      raise AttributeError(attr)
  
  def __setattr__(self, attr, value):
    if attr is 'description':
      self.graph.describe(self.name, value)
    elif attr is 'graph' or attr is 'name':
      self.__dict__[attr] = value
    else:
      raise AttributeError(attr)
  
  def __getitem__(self, key):
    return self.graph.get(self.name,key)
  
  def __setitem__(self, key, value):
    return self.graph.set(self.name,key,value)
  
  def __delitem__(self, key):
    return self.graph.unset(self.name, key)
  
  def __contains__(self, key):
    if self.graph.get(self.name,key): return True
    else: return False
  
  def __iter__(self):
    return self.graph.get(self.name).itervalues()
  
  def keys(self):
    return self.graph.get(self.name).iterkeys()
  
  def __len__(self):
    return len(self.graph.get(self.name))
  
