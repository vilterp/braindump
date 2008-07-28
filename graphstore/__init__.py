import sqlite3, re
from util import *
from page import Page

# to test: correct behavior when pages are nonexistent?
## when a page doesn't exist, return some type of error?
## either way, distinguish between nonexistent page and empty page
## but errors are annoying

# will set_description() have to create the page if it doesn't exist?

# id caching

# case insensitivity

class Graph:
  
  def __init__(self, database_path):
    self.database_path = database_path
    self.connection = sqlite3.connect(database_path)
    self.cursor = self.connection.cursor()
    self.connection.create_function('id_from_name',1,self.id_from_name)
    self.connection.create_function('name_from_id',1,self.name_from_id)
  
  def __getitem__(self, index):
    return Page(self,index)
  
  def __setitem__(self, index, value):
    for attr in value.keys():
      self.set(index,attr,value[attr])
    return True
  
  def __repr__(self):
    return "<Graph source: %s>" % self.database_path
  
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
    result = self.cursor.execute('SELECT id FROM pages WHERE name = ?',(name,)).fetchone()
    if not result:
      if create_if_nonexistent:
        return self.create_page(name)
      else:
        raise NonexistentPageError(name)
        return None
    else:
      return result[0]
  
  def name_from_id(self, id):
    result = self.cursor.execute('SELECT name FROM pages WHERE id = ?',(id,)).fetchone()
    if not result:
      raise NonexistentPageError
    else:
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
  
  def list(self, criteria=None):
    if criteria is None:
      result = self.cursor.execute('SELECT name FROM pages').fetchall()
    else: # the magic of braindump
      
      # parenthesized arguments?
      # regular expressions?
      
      expressions = criteria.split(' or ',1)
      if len(expressions) is 2:
        results1 = self.list(expressions[0])
        results2 = self.list(expressions[1])
        results1.extend(results2)
        return results1
      
      expressions = criteria.split(' and ',1)
      if len(expressions) is 2: # match both conditions, return intersection
        results1 = set(self.list(expressions[0]))
        results2 = set(self.list(expressions[1]))
        intersection = results1.intersection(results2)
        return list(intersection)
      
      # match one condition - all queries eventually come down to this
      condition = criteria.split(' is ')
      attr_id = self.id_from_name(condition[0])
      value_id = self.id_from_name(condition[1])
      result = self.cursor.execute("""SELECT pages.name FROM pages, triples WHERE
                                      pages.id = triples.subject_id AND
                                      triples.predicate_id = ? AND
                                      triples.object_id = ?""",
                                      (attr_id,value_id)).fetchall()
    pages = []
    for page in result:
      pages.append(page[0])
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
        self.cursor.commit()
        # set new values
        self.cursor.execute('INSERT INTO triples VALUES (?, ?, ?)',
                                 (subject_id,predicate_id,object_id))
        self.connection.commit()
      else:
        if self.triple_exists(subject_id,predicate_id,object_id):
          self.cursor.execute("""UPDATE triples SET object_id = ? WHERE
                                 subject_id = ? AND predicate_id = ?""",
                                      (object_id,subject_id,predicate_id))
        else:
          self.cursor.execute('INSERT INTO triples VALUES (?, ?, ?)',
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
      return None
    else:
      backlinks = {}
      for triple in result:
        subj = self.name_from_id(triple[0])
        pred = self.name_from_id(triple[1])
        backlinks[pred] = set_or_append(backlinks.get(pred,None),subj)
      return backlinks
  
  def describe(self, page, description=None): # should this be split up into 2 methods?
    if description is None: # get description
      result = self.cursor.execute('SELECT description FROM pages WHERE name = ?',
                                                                            (page,)).fetchone()
      if result:
        return result[0]
      else:
        return '' # return None?
    else: # set description
      page_id = self.id_from_name(page,True) # just so page will be created if nonexistent
      self.cursor.execute("""UPDATE pages SET description = ? WHERE id = ?""",
                                                                (description,page_id))
      self.connection.commit()
      return True
  
