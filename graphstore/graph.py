import sqlite3, re, os, inspect, comparisonoperators
from util import *
from util import *

# TODO: don't pass 3rd to comparison operators if they don't take 3 params
# TODO: set('red','type','color') on set('apple','color','red')
# TODO: delete vs. unset etc...

class Graph:
  
  def __init__(self, database_path, language='en-us'):
    self.id_cache = {}
    self.comparison_operators = {}
    self.langauge = language
    self.database_path = database_path
    self.connection = sqlite3.connect(database_path)
    self.cursor = self.connection.cursor()
    # register comparison operators
    for functionname in dir(comparisonoperators):
      operator = comparisonoperators.__dict__[functionname]
      if hasattr(operator,'pattern'): # to check if it's supposed to be a comp. op.
        self.comparison_operators[operator.pattern] = (functionname,operator)
    if not os.path.exists(database_path) or database_path == ':memory:':
      self.create_schema()
  
  def set_language(self, language):
    self.language = language
  
  def name_attr_id():
    return self.idfromname('%s_name',self.language)
  
  def __repr__(self):
    return "<Graph source:%s>" % os.path.abspath(self.database_path)
  
  def __iter__(self):
    return self.list().__iter__()
  
  def __len__(self):
    return len(self.list())
  
  def create_schema(self):
    self.execute("""CREATE TABLE triples (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                          subject_id numeric,
                                          predicate_id numeric,
                                          object_id numeric,
                                          object_literal varchar)""")
    self.execute('CREATE TABLE entities (id INTEGER PRIMARY KEY AUTOINCREMENT)')
    self.execute("""INSERT INTO triples (subject_id, predicate_id, object_literal)
                    VALUES (?,?,?)""",(1,1,'%s_name' % self.language))
    self.execute('INSERT INTO entities (?)',(1))
  
  def query(self, query, replacements=(), printit=False):
    if printit: print query, replacements
    return self.cursor.execute(query,replacements)
  
  def execute(self, query, replacements=(), printit=False):
    self.query(query,replacements,printit)
    self.connection.commit()
  
  def idfromname(self, name, create_if_nonexistent=False, raise_nonexistent=True):
    if name.lower() in [key.lower() for key in self.id_cache.keys()]:
      return case_insensitive_lookup(self.id_cache,name)
    else:
      result = self.query("""SELECT subject_id FROM triples WHERE
                             predicate_id = ? AND
                             object_literal LIKE ?""",
                             (self.name_attr_id(),name)).fetchone()
      # LIKE: case insensitive
      if not result: # not there
        if create_if_nonexistent: # create it
          return self.create_page(name)
        elif raise_nonexistent:
          raise NonexistentPageError(name)
        else:
          return None
      else: # is there
        self.id_cache[result[1]] = result[0]
        return result[0]
  
  def namefromid(self, id):
    if id in self.id_cache.values():
      return find_key(self.id_cache,id)
    else:
      result = self.query("""SELECT object_literal FROM triples WHERE
                             predicate_id = ? AND
                             subject_id = ?""",
                             (self.name_attr_id(),id,)).fetchone()
      self.id_cache[result[0]] = id
      return result[0]
  
  def create_page(self, name):
    self.execute('INSERT INTO entities () VALUES ()')
    newid = self.query('SELECT id FROM entities ORDER BY id DESC').fetchone()
    self.execute("""INSERT INTO triples (subject_id,predicate_id,object_literal)
                    VALUES (?,?,?)""",(newid,self.name_attr_id(),name))
    return newid
  
  def rename(self, old, new):
    self.execute('UPDATE triples SET object_literal = ? WHERE object_literal = ?',(new,old))
    # update id cache
    if old in self.id_cache:
      self.id_cache[new] = self.id_cache[old]
      del self.id_cache[old]
  
  def normalize_name(self, name):
    """capitalized properly"""
    result = self.query("""SELECT object_literal FROM triples WHERE object_literal LIKE ?""",
                           (name,)).fetchone()
    if result is not None:
      return result[0]
    else:
      raise NonexistentPageError(name)
  
  def triple_exists(self, subject_id, predicate_id, object_id=None, object_literal=None):
    if object_literal is None:
      result = self.query("""SELECT * FROM triples WHERE
                             subject_id = ? AND predicate_id = ? AND object_id = ?""",
                             (subject_id,predicate_id,object_id)).fetchone()
    else:
      result = self.query("""SELECT * FROM triples WHERE
                             subject_id = ? AND predicate_id = ? AND object_literal = ?""",
                             (subject_id,predicate_id,object_literal)).fetchone()
    return result is None
  
  def list(self, criteria=None):
    if not criteria:
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
        union = list(set(results1).union(set(results2)))
        return sorted(union)
      
      expressions = re.split(' and | AND ',criteria,1)
      if len(expressions) is 2: # match both conditions, return intersection
        results1 = self.list(expressions[0])
        results2 = self.list(expressions[1])
        intersection = list(set(results1).intersection(set(results2)))
        return sorted(intersection)
      
      # match one condition - all queries eventually come down to this
      # bit of a mess
      for operator in reversed(sorted(self.comparison_operators.keys())):
        match = re.search(' %s ' % operator,criteria)
        if match is not None:
          matches = match.groups()
          params = re.search('(.*) %s (.*)' % operator, criteria).groups()
          attr, value = params[0], params[-1]
          current_operator, current_func = self.comparison_operators[operator]
          break
      try:
        op = lambda one,two: current_func(one,two,*matches)
        self.connection.create_function(current_operator,2,op)
      except:
        raise NoMatchingComparisonOperatorError(criteria)
      result = self.query("""SELECT pages.name FROM pages, triples WHERE
                             pages.id = triples.subject_id AND
                             triples.predicate_id = ? AND
                             %s((SELECT name FROM pages WHERE id = triples.object_id),?)""" % 
                             current_operator,
                             (self.idfromname(attr),value)).fetchall()
      return sorted([row[0] for row in result])
  
  def select(self, criteria=None, sections=['metadata'], orderby=None):
    # TODO: multiple attrs, asc/desc
    data = []
    for page in self.list(criteria):
      pagedata = {'name': page}
      if 'metadata' in sections: pagedata['metadata'] = self.get(page)
      if 'description' in sections: pagedata['description'] = self.describe(page)
      if 'backlinks' in sections: pagedata['backlinks'] = self.backlinks(page)
      data.append(pagedata)
    if orderby is not None:
      return sorted(data,key=lambda a: a['metadata'][orderby])
    else:
      return data
  
  def get(self, page, attribute=None):
    if attribute is None: # return dict with all attributes
      result = self.query("""SELECT predicate_id, object_id FROM triples WHERE
                             subject_id = ?""",(self.idfromname(page),)).fetchall()
      if len(result) is not None:
        page = {}
        for predicate_id, object_id in result:
          predicate = self.namefromid(predicate_id)
          page[predicate] = set_or_append(page.get(predicate,None),self.namefromid(object_id))
        return pluralize_key_if_value_is_list(page)
      else:
        raise NonexistentPageError(page)
    else: # return single string attribute
      if is_plural(attribute): attribute = singularize(attribute)
      result = self.query("""SELECT object_id FROM triples WHERE
                             subject_id = ? AND
                             predicate_id = ?""",
                             (self.idfromname(page),
                              self.idfromname(attribute))).fetchall()
      if len(result) > 1:
        return [self.namefromid(row[0]) for row in result]
      elif result is not None:
        return self.namefromid(result[0][0])
      else:
        raise NonexistentPageError(page)
  
  def set(self, subject, predicate, object=None):
    if object is None and isinstance(predicate,dict):
      for predicate, object in predicate.iteritems():
        self.set(subject,predicate,object)
    else:
      self.unset(subject,predicate)
      subject_id = self.idfromname(subject,True)
      if is_plural(predicate) and isinstance(object,list): # set multiple values
        predicate_id = self.idfromname(singularize(predicate),True)
        for value in object:
          object_id = self.idfromname(value,True)
          # set new value
          self.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                        (subject_id,predicate_id,object_id))
      else: # set one value
        predicate_id = self.idfromname(predicate,True)
        object_id = self.idfromname(object,True)
        self.execute('INSERT INTO triples VALUES (NULL, ?, ?, ?)',
                      (subject_id,predicate_id,object_id))
  
  def unset(self, page, attribute=None):
    if attribute is None: # unset entire page
      self.execute('DELETE FROM triples WHERE subject_id = ?',
                    (self.idfromname(page,raise_nonexistent=False),))
    else: # unset single attribute
      if is_plural(attribute): attribute = singularize(attribute)
      self.execute("""DELETE FROM triples WHERE
                      subject_id = ? AND
                      predicate_id = ?""",
                      (self.idfromname(page,raise_nonexistent=False),
                      self.idfromname(attribute,raise_nonexistent=False)))
  
  def backlinks(self, page, attribute=None):
    object_id = self.idfromname(page)
    if attribute is None: # return dict of all backlinks
      result = self.query("""SELECT predicate_id, subject_id
                             FROM triples WHERE object_id LIKE ?""",
                             (object_id,)).fetchall()
      if not result:
        return {}
      else:
        backlinks = {}
        for triple in [(self.namefromid(i[0]),self.namefromid(i[1])) for i in result]:
          backlinks[triple[0]] = set_or_append(backlinks.get(triple[0],None),triple[1])
        return backlinks
    else:
      predicate_id = self.idfromname(attribute)
      result = self.query("""SELECT subject_id FROM triples WHERE
                             object_id LIKE ? AND predicate_id LIKE ?""",
                             (object_id,predicate_id)).fetchall()
      if len(result) > 1:
        return [self.namefromid(row[0]) for row in result]
      elif result is not None:
        return self.namefromid(result[0][0])
      else:
        return None
  
  def infer_types(self, page):
    return self.backlinks(page).keys()
  
  def between(self, page1, page2):
    id1 = self.idfromname(page1)
    id2 = self.idfromname(page2)
    result = self.query("""SELECT name FROM pages, triples WHERE
                           pages.id = triples.predicate_id AND
                           ((triples.subject_id = ? AND
                           triples.object_id = ?)
                           OR
                           (triples.subject_id = ? AND
                           triples.object_id = ?))""",
                           (id1,id2,id2,id1)).fetchall()
    if len(result) is 1:
      return result[0][0]
    elif result is not None:
      return [row[0] for row in result]
    else:
      return None # would be cool to find shortest distance between two pages not directly linked
  
  def describe(self, page, description=None):
    if description is None:
      return self.get(page,'%s_description' % self.langauge)
    else:
      self.set(page,'%s_description' % self.language)
  
