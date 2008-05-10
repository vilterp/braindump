require 'XMLRPC/Client'

server = XMLRPC::Client.new2('http://mac-mini.local/~petevilter/svn/braindump/trunk/xmlrpc.php')

while 1
  $stdout.print 'braindump> '
  query = $stdin.gets
  puts server.call('braindump.query',query)
end

# TODO:
# humanize result arrays
# say something if result is 'false'
# 'set' and 'list' don't work