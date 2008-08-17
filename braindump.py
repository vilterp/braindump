from controller import *

import plugins.textile.textile

cherrypy.quickstart(Main(),config='development.config')