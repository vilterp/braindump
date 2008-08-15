import sqlite3, re, os
from util import *
from page import Page

# TODO: firm up NonexistentPageError vs. returning None...

# should describe() check if page exists or just go ahead and UPDATE?

class Graph:
  
  id_cache = {}
  
  def __init__(self, database_path):
    self.database_path = database_path
    self.connection = sqlite3.connect(database_path)
    self.cursor = self.connection.cursor()
  
  def __repr__(self):
    return "<Graph source: %s/%s>" % (os.getcwd(), self.database_path)
  
  def __getitem__(self, key):
    return Page(self,key)
  
  def __delitem__(self, key):
    self.unset(key)
    return True
  
  def __iter__(self):
    return self.list().__iter__()
  
  def __len__(self):
    return len(self.list())
  
  def create_schema(self):
    # pages
    self.cursor.execute("""CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                             name text, 
                                             description text)""")
    self.connection.commit()
    
    # triples
    self.cursor.execute("""CREATE TABLE triples (subject_id numeric,
                                                 predicat_id numeric,
                                                 object_id numeric)""")
    self.connection.commit()
    
    return True
  
  def id_from_name(self, name, create_if_nonexistent=False):
    if name.lower() in self.id_cache:
      return self.id_cache[name.lower()]
    else:
      result = self.cursor.execute('SELECT id FROM pages WHERE name LIKE ?',(name,)).fetchone()
      # LIKE: case insensitive
      if not result:
        if create_if_nonexistent:
          return self.create_page(name)
        else:
          raise NonexistentPageError(name)
          return None
      else:
        self.id_cache[name.lower()] = result[0]
        return result[0]
  
  def name_from_id(self, id):
    if id in self.id_cache.values():
      return find_key(self.id_cache,id)
    else:
      result = self.cursor.execute('SELECT name FROM pages WHERE id = ?',(id,)).fetchone()
      if not result:
        raise NonexistentPageError # this wouldn't ever happen... where would the id # come from..
      else:
        self.id_cache[result[0].lower()] = id
        return result[0]
  
  def create_page(self, name):
    self.cursor.execute('INSERT INTO pages (name) VALUES (?)',(name,))
    self.connection.commit()
    return self.id_from_name(name) # wish it wasn't necessary to query again...
  
  def triple_exists(self, subject_id, predicate_id, object_id):
    result = self.cursor.execute("""SELECT * FROM triples WHERE
                                    subject_id = ? AND predicate_id = ? AND object_id = ?""",
                                              (subject_id,predicate_id,object_id)).fetchone()
    if result is None:
      return False
    else:
      return True
  
  def infer_types(self, page):
    return self.backlinks(page).keys()
  
  def list(self, criteria=None):
    if criteria is None or criteria.strip() is '':
      result = self.cursor.execute('SELECT name FROM pages').fetchall()
      # upacked & returned at the bottom of this method
    
    else: # the magic of braindump
      
      # parenthesized arguments: split but not in parens, recurse until
      #                          (condition) received
      
      # more sophisticated ordering? (by attributes, SQL style?)
      
      expressions = re.split(' or | OR ',criteria,1)
      if len(expressions) is 2: # match both connections, return union
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
        listify = list(intersection)
        listify.sort()
        return listify
      
      # match one condition - all queries eventually come down to this
      attr, value = re.split(' is | IS ',criteria)
      attr_id = self.id_from_name(attr)
      value_id = self.id_from_name(value)
      result = self.cursor.execute("""SELECT pages.name FROM pages, triples WHERE
                                      pages.id = triples.subject_id AND
                                      triples.predicate_id = ? AND
                                      triples.object_id = ?""",
                                      (attr_id,value_id)).fetchall()
    pages = []
    for page in result:
      pages.append(Page(self,page[0]))
    pages.sort()
    return pages
  
  def get(self, page, attribute=None):
    if attribute is None: # return dict with all attributes
      page_id = self.id_from_name(page)
      result = self.cursor.execute("""SELECT predicate_id, object_id FROM triples WHERE
                                      subject_id = ?""",(page_id,)).fetchall()
      page = {}
      for row in result:
        pred = self.name_from_id(row[0])
        obj = self.name_from_id(row[1])
        page[pred] = set_or_append(page.get(pred,None),obj) # group plurals here
      return pluralize_key_if_value_is_list(page)
      
    else: # return single string attribute
      if is_plural(attribute): attribute = singularize(attribute)
      page_id = self.id_from_name(page)
      attribute_id = self.id_from_name(attribute)
      result = self.cursor.execute("""SELECT object_id FROM triples WHERE
                                      subject_id = ? AND
                                      predicate_id = ?""",
                                      (page_id,attribute_id)).fetchall()
      if len(result) is 1:
        return self.name_from_id(result[0][0])
      elif len(result) > 1:
        answers = []
        for row in result: answers.append(self.name_from_id(row[0]))
        return answers
      else:
        return None
  
  def set(self, subject, predicate, objekt, allow_multiple_values=False):
    if is_plural(predicate) and isinstance(objekt,list):
      for value in objekt:
        self.set(subject,singularize(predicate),value,True)
    else:
      subject_id = self.id_from_name(subject,True)
      predicate_id = self.id_from_name(predicate,True)
      object_id = self.id_from_name(objekt,True)
      
      if allow_multiple_values: # e.g. languages: [php, python]
        # delete any existing value(s)
        self.cursor.execute("""DELETE FROM triples WHERE 
                               subject_id = ? AND predicate_id = ?""",
                                              (subject_id,predicate_id))
        self.connection.commit()
        # set new values
        self.cursor.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                                 (subject_id,predicate_id,object_id))
        self.connection.commit()
      else:
        if self.triple_exists(subject_id,predicate_id,object_id):
          self.cursor.execute("""UPDATE triples SET object_id = ? WHERE
                                 subject_id = ? AND predicate_id = ?""",
                                      (object_id,subject_id,predicate_id))
        else:
          self.cursor.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                                   (subject_id,predicate_id,object_id))
        self.connection.commit()
    return True
  
  def unset(self, page, attribute=None):
    subject_id = self.id_from_name(page)
    if attribute is None: # unset entire page
      self.cursor.execute('DELETE FROM triples WHERE subject_id = ?',(subject_id,))
      self.connection.commit()
    else: # unset single attribute
      if is_plural(attribute): attribute = singularize(attribute)
      predicate_id = self.id_from_name(attribute)
      self.cursor.execute('DELETE FROM triples WHERE subject_id = ? AND predicate_id = ?',
                                                                  (subject_id,predicate_id))
    return True
  
  def backlinks(self, page):
    object_id = self.id_from_name(page)
    result = self.cursor.execute("""SELECT subject_id, predicate_id FROM triples WHERE
                                    object_id = ?""",(object_id,)).fetchall()
    if not result:
      return {}
    else:
      backlinks = {}
      for triple in result:
        subj = self.name_from_id(triple[0])
        pred = self.name_from_id(triple[1])
        backlinks[pred] = set_or_append(backlinks.get(pred,None),subj)
      return backlinks
  
  def between(self, page1, page2):
    id1 = self.id_from_name(page1)
    id2 = self.id_from_name(page2)
    result = self.cursor.execute("""SELECT name FROM pages, triples WHERE
                                    pages.id = triples.predicate_id AND
                                    ((triples.subject_id = ? AND
                                    triples.object_id = ?)
                                    OR
                                    (triples.subject_id = ? AND
                                    triples.object_id = ?))""",
                                    (id1,id2,id2,id1)).fetchone()
    if result:
      return result[0]
    else:
      return None # would be cool to find shortest distance between two pages not directly linked
  
  def describe(self, page, description=None): # should this be split up into 2 methods?
    if description is None: # get description
      result = self.cursor.execute('SELECT description FROM pages WHERE name = ?',
                                                                            (page,)).fetchone()
      if result:
        return result[0]
      else:
        return ''
    else: # set description
      page_id = self.id_from_name(page,True) # just so page will be created if nonexistent
      self.cursor.execute("""UPDATE pages SET description = ? WHERE id = ?""",
                                                                (description,page_id))
      self.connection.commit()
      return True
  
  def rename(self, old, new):
    self.cursor.execute('UPDATE pages SET name = ? WHERE name = ?',(new,old))
    self.connection.commit()
    return True
  
  # is this necessary with unset?
  def delete(self, page):
    page_id = self.id_from_name(page)
    # delete triples containing page
    self.cursor.execute("""DELETE FROM triples WHERE
                           subject_id = ? OR
                           predicate_id = ? OR
                           object_id = ?""",(page_id,page_id,page_id))
    self.connection.commit()
    # delete page
    self.cursor.execute("DELETE FROM pages WHERE id = ?",(page_id,))
    self.connection.commit()
  
