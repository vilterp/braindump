import sqlite3, re, os
from util import *

# TODO: more comparison operators (before, after, >, <, etc)
# TODO: change schema - text names in triples table
# FIXME: id cache must be flushed on rename()!
# FIXME: id cache making everything lowercase

class Graph:
  
  id_cache = {}
  
  def __init__(self, database_path):
    self.database_path = database_path
    self.connection = sqlite3.connect(database_path)
    self.connection.create_function('idfromname',1,self.id_from_name)
    self.connection.create_function('namefromid',1,self.name_from_id)
    self.cursor = self.connection.cursor()
    # need another connection & cursor for UDFs...
    self.connection2 = sqlite3.connect(database_path)
    self.cursor2 = self.connection2.cursor()
  
  def __repr__(self):
    return "<Graph source:%s>" % self.database_path
  
  def __iter__(self):
    return self.list().__iter__()
  
  def __len__(self):
    return len(self.list())
  
  def create_schema(self):
    self.execute("""CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        name text, 
                                        description text)""")
    self.execute("""CREATE TABLE triples (subject_id numeric,
                                          predicat_id numeric,
                                          object_id numeric)""")
  
  def query(self, query, replacements=()):
    # log = open('log.txt','a')
    # log.write((query,replacements).__str__() + '\n')
    # log.close()
    return self.cursor.execute(query,replacements)
  
  def execute(self, query, replacements=()):
    self.query(query,replacements)
    self.connection.commit()
  
  def id_from_name(self, name, create_if_nonexistent=False):
    if name in self.id_cache:
      return self.id_cache[name]
    else:
      result = self.cursor2.execute('SELECT id FROM pages WHERE name LIKE ?',(name,)).fetchone()
      # LIKE: case insensitive
      if not result:
        if create_if_nonexistent:
          return self.create_page(name)
        else:
          return None
      else:
        self.id_cache[name] = result[0]
        return result[0]
  
  def name_from_id(self, id):
    if id in self.id_cache.values():
      return find_key(self.id_cache,id)
    else:
      result = self.cursor2.execute('SELECT name FROM pages WHERE id = ?',(id,)).fetchone()
      if not result:
        raise NonexistentPageError # this wouldn't ever happen... where would the id # come from..
      else:
        self.id_cache[result[0].lower()] = id
        return result[0]
  
  def create_page(self, name):
    self.execute('INSERT INTO pages (name) VALUES (?)',(name,))
    return self.id_from_name(name) # wish it wasn't necessary to query again...
  
  def triple_exists(self, subject_id, predicate_id, object_id):
    result = self.query("""SELECT * FROM triples WHERE
                           subject_id = ? AND predicate_id = ? AND object_id = ?""",
                                      (subject_id,predicate_id,object_id)).fetchone()
    if result is None:
      return False
    else:
      return True
  
  def infer_types(self, page):
    return self.backlinks(page).keys()
  
  def list(self, criteria=None):
    if criteria is None:
      return [row[0] for row in self.query('SELECT name FROM pages').fetchall()]
    else: # the magic of braindump
      
      # this syntax is a little fragile -- what if page values contain 'is' or 'or' or 'and'?
      
      # parenthesized arguments: split but not in parens, recurse until
      #                          (condition) received
      
      # more sophisticated ordering? (by attributes, SQL style?)
      
      expressions = re.split(' or | OR ',criteria,1)
      if len(expressions) is 2: # match both conditions, return union
        results1 = self.list(expressions[0])
        results2 = self.list(expressions[1])
        results1.extend(results2)
        results1.sort()
        return results1
      
      expressions = re.split(' and | AND ',criteria,1)
      if len(expressions) is 2: # match both conditions, return intersection
        results1 = set(self.list(expressions[0]))
        results2 = set(self.list(expressions[1]))
        intersection = results1.intersection(results2)
        listified = list(intersection)
        listified.sort()
        return listified
      
      # match one condition - all queries eventually come down to this
      attr, value = re.split(' is | IS ',criteria)
      result = self.query("""SELECT pages.name FROM pages, triples WHERE
                             pages.id = triples.subject_id AND
                             triples.predicate_id = idfromname(?) AND
                             triples.object_id = idfromname(?)""",
                             (attr,value)).fetchall()
      pages = [row[0] for row in result]
      pages.sort()
      return pages
  
  def get(self, page, attribute=None):
    if attribute is None: # return dict with all attributes
      result = self.query("""SELECT namefromid(predicate_id), namefromid(object_id) FROM triples WHERE
                             subject_id = idfromname(?)""",(page,)).fetchall()
      if len(result) > 0:
        page = {}
        for row in result:
          page[row[0]] = set_or_append(page.get(row[0],None),row[1]) # group plurals here
        return pluralize_key_if_value_is_list(page)
      else:
        raise NonexistentPageError(page)
    else: # return single string attribute
      if is_plural(attribute): attribute = singularize(attribute)
      result = self.query("""SELECT namefromid(object_id) FROM triples WHERE
                             subject_id = idfromname(?) AND
                             predicate_id = idfromname(?)""",
                             (page,attribute)).fetchall()
      if len(result) is 1:
        return result[0][0]
      elif len(result) > 1:
        return [row[0] for row in result]
      else:
        raise NonexistentPageError(page)
  
  def set(self, subject, predicate, object=None):
    if object is None and isinstance(predicate,dict):
      for item in predicate.iteritems():
        self.set(subject,item[0],item[1])
    else:
      if is_plural(predicate) and isinstance(object,list):
        predicate = singularize(predicate)
      subject_id = self.id_from_name(subject,True)
      predicate_id = self.id_from_name(predicate,True)
      # delete any existing value(s)
      self.execute("""DELETE FROM triples WHERE 
                      subject_id = ? AND
                      predicate_id = ?""",
                   (subject_id,predicate_id))
      if is_plural(predicate) and isinstance(object,list): # set multiple values
        for value in object:
          object_id = self.id_from_name(value,True)
          # set new value
          self.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                        (subject_id,predicate_id,object_id))
      else: # set one value
        object_id = self.id_from_name(object,True)
        self.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                      (subject_id,predicate_id,object_id))
  
  def unset(self, page, attribute=None):
    if attribute is None: # unset entire page
      self.execute('DELETE FROM triples WHERE subject_id = idfromname(?)',(page,))
    else: # unset single attribute
      if is_plural(attribute):
        attribute = singularize(attribute)
      self.execute("""DELETE FROM triples WHERE
                      subject_id = idfromname(?) AND
                      predicate_id = idfromname(?)""",
                              (subject_id,predicate_id))
  
  def backlinks(self, page, attribute=None):
    object_id = self.id_from_name(page)
    if object_id is None:
      raise NonexistentPageError(page)
    if attribute is None: # return dict of all backlinks
      result = self.query("""SELECT namefromid(predicate_id), namefromid(subject_id)
                             FROM triples WHERE object_id = ?""",
                             (object_id,)).fetchall()
      if not result:
        return {}
      else:
        backlinks = {}
        for triple in result:
          backlinks[triple[0]] = set_or_append(backlinks.get(triple[0],None),triple[1])
        return backlinks
    else:
      predicate_id = self.id_from_name(attribute)
      if predicate_id is None:
        raise NonexistentPageError(attribute)
      result = self.query("""SELECT namefromid(subject_id) FROM triples WHERE
                             object_id = ? AND predicate_id = ?""",
                             (object_id,predicate_id)).fetchone()
      if result:
        return result[0]
      else:
        return None
  
  def between(self, page1, page2):
    id1 = self.id_from_name(page1)
    id2 = self.id_from_name(page2)
    result = self.query("""SELECT name FROM pages, triples WHERE
                           pages.id = triples.predicate_id AND
                           ((triples.subject_id = ? AND
                           triples.object_id = ?)
                           OR
                           (triples.subject_id = ? AND
                           triples.object_id = ?))""",
                           (id1,id2,id2,id1)).fetchall()
    if result:
      return [row[0] for row in result]
    else:
      return None # would be cool to find shortest distance between two pages not directly linked
  
  def describe(self, page, description=None): # should this be split up into 2 methods?
    if description is None: # get description
      result = self.query('SELECT description FROM pages WHERE name = ?',
                          (page,)).fetchone()
      if not result:
        raise NonexistentPageError(page)
      elif result[0] is None:
        return ''
      else:
        return result[0]
    else: # set description
      page_id = self.id_from_name(page,True) # just so page will be created if nonexistent
      self.execute('UPDATE pages SET description = ? WHERE id = ?',(description,page_id))
  
  def rename(self, old, new):
    self.execute('UPDATE pages SET name = ? WHERE name = ?',(new,old))
    # update id cache
    self.id_cache[new] = self.id_cache[old]
    del self.id_cache[old]
  

