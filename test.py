from graphstore import *

g = Graph('graph.db')

g.set('United States of America','capital city','Washington, D.C.')

print g.list('capital city is south of canada')