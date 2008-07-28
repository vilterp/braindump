# for magic attribute access

class Page:
  
  def __init__(self, graph, name):
    self.graph = graph
    self.name = name
  
  def __repr__(self):
    return self.graph.get(self.name)
  
  def __getitem__(self, key):
    return self.graph.get(self.name,key)
  
  def __setitem__(self, key, value):
    return self.graph.set(self.name,key,value)
  
  def __delitem__(self, key):
    return self.graph.unset(self.name, key)
  
  def __contains__(self, key):
    if self.graph.get(self.name,key): return True
    else: return False
  
  def __iter__(self): # http://docs.python.org/lib/typeiter.html
    return self.graph.get(self.name)
  
  def __len__(self):
    return len(self.graph.get(self.name))
  
