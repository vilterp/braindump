import graphstore

g = graphstore.Graph('graph.db')

print g.list('hometown is within 30 miles of Gary, Indiana')