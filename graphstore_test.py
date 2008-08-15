from graphstore import *

g = Graph('graph.db')

for page in g:
  print page.metadata

print ''