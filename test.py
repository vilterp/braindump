import graphstore

g = graphstore.Graph('graph.db')

print g.list('birthday is before 1995')