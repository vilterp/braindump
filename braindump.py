from controller import *

cherrypy.quickstart(Main(),config='development.config')