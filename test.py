from graphstore import *
import sqlite3

g = Graph('graph.db')

print g.list('running mate regexp .ah.')