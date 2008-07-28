# for magic attribute access

class Page:

  def __init__(self, graph, name):
    self.graph = graph
    self.name = name

  def __repr__(self):
    return self.graph.get(self.name)
  
  def __getitem__(self, index):
    return self.graph.get(self.name,index)
  
  def __setitem__(self, index, value):
    return self.graph.set(self.name,index,value)
  

