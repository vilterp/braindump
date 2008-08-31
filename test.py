from graphstore import *

g = Graph('graph.db')

print g.query('SELECT * FROM triples WHERE subject_id = idfromname(?)',('john mccain',))
print g.id_from_name('john mccain')