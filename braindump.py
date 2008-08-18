from controller import *

import plugins

cherrypy.quickstart(Main(),config='development.config')