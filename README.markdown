# Description

Braindump is a semantic wiki. Each page has three parts:

1. a set of key/value pairs where the key is an attribute and the value is another page in the wiki. An example key/value pair on a page called "apple" could be "color: red"
2. a textual description, which is textilized.
3. a set of backlinks. If the "apple" page has the attribute "color: red", the "red" page automatically has the backlink "color of apple".

Pages can be filtered based on key/value pairs with a simple query language that's accessible through the web interface as well as the graphstore API. For example, to list all pages with a "color" attribute that has a value of "red", type "color is red" into the filter box. "is" is just one operator -- there's >, <, >=, <=, "is not", "matches" [regular expression], and even "is before", "is after" (uses [dateutil](http://labix.org/python-dateutil) to parse and compare dates), "is north|south|east|west of" and "is within x miles of" (uses [geopy](http://exogen.case.edu/projects/geopy/) and the [Google Maps API](http://code.google.com/apis/maps/)). Comparison operators are pluggable, so adding a new one is as easy as writing a Python function.

It's persisted in SQLite. If I have time I'll port it to run on top of [RDFlib](http://rdflib.net/) so a braindump wiki can be part of the semantic web.

# Installation

1. `easy_install` everything in dependencies.txt
2. cd into the directory
3. `python braindump.py`
4. open `http://localhost:8080/`, and you should be good to go.

To clear out everything in the development database, just go to `http://localhost:8080/delete_everything`. (Releases won't have that feature lol)