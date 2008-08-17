# for magic attribute access

# how to return the whole dict?

class Page:
  
  def __init__(self, graph, name):
    self.graph = graph
    self.name = name
    if not self.graph.lazy_lookup:
      self.metadata = self.graph.get(self.name)
      self.description = self.graph.describe(self.name)
      self.backlinks = self.graph.backlinks(self.name)
  
  def __repr__(self):
    return "<Page: %s>" % self.name
  
  def __cmp__(self, other):
    if self.name > other.name:
      return 1
    elif self.name < other.name:
      return -1
    else:
      return 0
  
  def __getattr__(self, attr):
    if attr is 'description':
      return self.graph.describe(self.name)
    elif attr is 'backlinks':
      return self.graph.backlinks(self.name)
    elif attr is 'metadata':
      return PageMeta(self.graph,self.name)
    else:
      raise AttributeError(attr)
  
  def __setattr__(self, attr, value):
    if attr is 'description':
      self.graph.describe(self.name, value)
    elif attr is 'metadata':
      self.graph.unset(self.name)
      for attribute in value.keys():
        self.graph.set(self.name,attribute,value[attribute])
    elif attr is 'graph' or attr is 'name':
      self.__dict__[attr] = value
    else:
      raise AttributeError(attr)
  

class PageMeta:
  
  def __init__(self, graph, name):
    self.graph = graph
    self.name = name
  
  def __repr__(self):
    return self.graph.get(self.name).__repr__()
  
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
    return self.graph.get(self.name).keys()
  
  def __len__(self):
    return len(self.graph.get(self.name))
  
