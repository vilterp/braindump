import sqlite3
from util import *

class Graph:
  
  def __init__(self, database_path):
    self.connection = sqlite3.connect(database_path)
    self.cursor = self.connection.cursor()
    self.connection.create_function('id_from_name',1,self.id_from_name)
    self.connection.create_function('name_from_id',1,self.name_from_id)
  
  def __get__():
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
  
  def id_from_name(self, name, create_if_nonexistent=False):
    result = self.cursor.execute('SELECT id FROM pages WHERE name = ?',(name,)).fetchone()
    if not result:
      if create_if_nonexistent: return self.create_page(name)
      else: return None
    else: return result[0]
  
  def name_from_id(self, id):
    result = self.cursor.execute('SELECT name FROM pages WHERE id = ?',(id,)).fetchone()
    if not result: return None
    else: return result[0]
  
  def create_page(self, name):
    self.cursor.execute('INSERT INTO pages (name) VALUES (?)',(name,))
    self.connection.commit()
    return self.id_from_name(name) # wish it wasn't necessary to query again...
  
  def list(self, criteria=None):
    result = self.cursor.execute('SELECT name FROM pages').fetchall()
    pages = []
    for page in result:
      pages.append(page[0])
    return pages
  
  def get(self, page, attribute=None):
    if attribute is None: # return dict with all attributes
      page_id = self.id_from_name(page)
      result = self.cursor.execute("""SELECT predicate_id, object_id FROM triples WHERE
                                      subject_id = ?""",(page_id,)).fetchall()                    
      if not result: return None
      else:
        page = {}
        for row in result:
          pred = self.name_from_id(row[0])
          obj = self.name_from_id(row[1])
          page[pred] = set_or_append(page.get(pred,None),obj) # group plurals here
      
        for attribute in page.keys():
          if isinstance(page[attribute],list):
            page[pluralize(attribute)] = page[attribute]
            del page[attribute]
        return page
      
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
      
      if allow_multiple_values:
        # FIXME: this misses existing values
        self.cursor.execute('INSERT INTO triples VALUES (?, ?, ?)',
                                                    (subject_id,predicate_id,object_id))
        self.connection.commit()
      else:
        self.cursor.execute("""UPDATE triples SET object_id = ? WHERE
                               subject_id = ? AND predicate_id = ?""",
                                    (object_id,subject_id,predicate_id))
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
  
