import xmlrpclib, sys

server = xmlrpclib.Server('http://mac-mini.home/~petevilter/svn/braindump/trunk/xmlrpc.php')

try:  
  while 1:
    query = raw_input('braindump> ')
    print server.braindump.query(query)
    
except KeyboardInterrupt: # ctrl-c
  print "\nbye!"
  sys.exit(0)
  
# TODO:
# humanize result arrays
# give an english message instead of 'false' or 'true'

# FIXME:
# changing attributes doesn't work cuz it doesn't unset the old value