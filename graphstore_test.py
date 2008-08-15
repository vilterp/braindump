from graphstore import *

g = Graph('graph.db')

g['banana'].metadata = {'color': 'yellow', 'type': 'fruit', 'shape': 'crescent'}

for page in g:
  print page.metadata