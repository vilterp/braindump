from controller import *

# TODO: get this working so it can be run w/out cd'ing into this dir.

cherrypy.quickstart(Main(),config=os.path.join(basedir,'development.config'))