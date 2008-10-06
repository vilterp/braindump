import unittest
from graph import Graph

# TODO: test comparison operators

class GraphstoreTest(unittest.TestCase):
  
  def setUp(self):
    self.g = Graph(':memory:')
    self.g.set('apple','color','red')
    self.g.set('grocery list','items',['milk','cookies'])
    self.g.set('braindump',{'type':'web app','language':'python'})
    self.g.set('trac','language','python')
    self.g.set('Jim','brother','Tim')
    self.g.set('Jim','arch enemy','Tim')
    self.g.create_page('empty page')
  
  def testCreatePage(self):
    g = Graph(':memory:')
    g.create_page('spam')
    self.assertEquals(g.query('SELECT * FROM pages').fetchall(),[(1,'spam','')])
  
  def testNormalizeName(self):
    self.assertEquals(self.g.normalize_name('GroCeRY LiST'),'grocery list')
  
  def testIdCache(self):
    # these only test id's being set in idfromname (called in set())
    g = Graph(':memory:')
    g.set('apple','color','red')
    del g.cursor # because it shouldn't need to query the db
    self.assertEquals(g.id_cache,{'apple':1,'color':2,'red':3})
    self.assertEquals(g.idfromname('Apple'),1)
  
  def testList(self):
    g = Graph(':memory:')
    g.set('apple','color','red')
    # basic comparison operators
    self.assertEquals(g.list(),['apple','color','red'])
    self.assertEquals(g.list('color is red'),['apple'])
    g.set('apple','type','fruit')
    g.set('banana','type','fruit')
    self.assertEquals(g.list('color is red and type is fruit'),['apple'])
    g.set('banana','color','yellow')
    self.assertEquals(g.list('color is red or color is yellow'),['apple','banana'])
    self.assertEquals(g.list('color is red or color is yellow and type is fruit'),['apple','banana'])
    g.set('gmail logo','colors',['red','white'])
    # this is how it behaves... is this how it should behave?
    self.assertEquals(g.list('color is red'),['apple','gmail logo'])
    # temporal
    g.set('Tim','birthday','6/2/95')
    g.set('Jim','birthday','10/7/81')
    self.assertEquals(g.list('birthday is after 1992'),['Tim'])
    self.assertEquals(g.list('birthday is before 1992'),['Jim'])
    self.assertEquals(g.list('birthday is after 1992 and birthday is before 2000'),['Tim'])
    self.assertEquals(g.list('birthday is after 1900'),['Jim','Tim'])
    # geographical
    g.set('Barrack Obama','home state','Illinois')
    g.set('John McCain','home state','Arizona')
    self.assertEquals(g.list('home state is west of Texas'),['John McCain'])
    self.assertEquals(g.list('home state is east of Texas'),['Barrack Obama'])
    self.assertEquals(g.list('home state is north of Mexico'),['Barrack Obama','John McCain'])
    self.assertEquals(g.list('home state is south of Canada'),['Barrack Obama','John McCain'])
    g.set('Barrack Obama','hometown','Chicago')
    self.assertEquals(g.list('hometown is within 50 miles of Gary, Indiana'),['Barrack Obama'])
  
  def testSelect(self):
    g = Graph(':memory:')
    g.set('Car A',{'type':'car','horsepower':'300'})
    g.set('Car B',{'type':'car','horsepower':'400'})
    g.set('Car C',{'type':'car','horsepower':'500'})
    self.assertEquals(g.select('type is car','horsepower'),[
      {'name': u'Car A', 'metadata': {u'horsepower': u'300', u'type': u'car'}},
      {'name': u'Car B', 'metadata': {u'horsepower': u'400', u'type': u'car'}},
      {'name': u'Car C', 'metadata': {u'horsepower': u'500', u'type': u'car'}}
    ])
  
  def testSet(self):
    # string
    g = Graph(':memory:')
    g.set('apple','color','red')
    self.assertEquals(g.query('SELECT * FROM pages').fetchall(),[
     (1,u'apple',''),
     (2,u'color',''),
     (3,u'red','')
    ])
    self.assertEquals(g.query('SELECT * FROM triples').fetchall(),[(1,1,2,3)])
    del g
    # list
    g = Graph(':memory:')
    g.set('grocery list','items',['milk','cookies'])
    self.assertEquals(g.query('SELECT * FROM pages').fetchall(),[
      (1,u'grocery list',''),
      (2,u'item',''),
      (3,u'milk',''),
      (4,u'cookies','')
    ])
    self.assertEquals(g.query('SELECT * FROM triples').fetchall(),[
      (1,1,2,3),
      (2,1,2,4)
    ])
    del g
    # dict
    g = Graph(':memory:')
    g.set('apple',{'color':'red','tastiness':'high'})
    self.assertEquals(g.query('SELECT * FROM pages').fetchall(),[
      (1,u'apple',''),
      (2,u'color',''),
      (3,u'red',''),
      (4,u'tastiness',''),
      (5,u'high','')
    ])
    self.assertEquals(g.query('SELECT * FROM triples').fetchall(),[
      (1,1,2,3),
      (2,1,4,5)
    ])
    del g
  
  def testDescribe(self):
    g = Graph(':memory:')
    g.describe('apple','eat to keep doctor away')
    self.assertEquals(g.describe('apple'),'eat to keep doctor away')
    g.describe('apple','better than spam')
    self.assertEquals(g.describe('apple'),'better than spam')
    g.set('a','b','c')
    self.assertEquals(g.describe('a'),'')
    # TODO: assertRaises g.describe('asdf') NonExistentPageError
  
  def testGet(self):
    self.assertEquals(self.g.get('apple','color'),'red')
    self.assertEquals(self.g.get('grocery list','items'),['milk','cookies'])
    self.assertEquals(self.g.get('braindump'),{'type':'web app','language':'python'})
    self.assertEquals(self.g.get('empty page'),{})
    # FIXME: raise error? return none?
    # self.assertEquals(self.g.get('empty page','myattr'),None)
  
  def testBacklinks(self):
    self.assertEquals(self.g.backlinks('red'),{'color':'apple'})
    self.assertEquals(self.g.backlinks('red','color'),'apple')
    self.assertEquals(self.g.backlinks('python'),{'language':['braindump','trac']})
    self.assertEquals(self.g.backlinks('python','language'),['braindump','trac'])
    self.assertEquals(self.g.backlinks('empty page'),{})
    # FIXME: raise error? return none?
    # self.assertEquals(self.g.backlinks('empty page','myattr'),None)
  
  def testInferTypes(self):
    self.assertEquals(self.g.infer_types('red'),['color'])
    self.assertEquals(self.g.infer_types('Tim'),['brother','arch enemy'])
  
  def testBetween(self):
    self.assertEquals(self.g.between('apple','red'),'color')
    self.assertEquals(self.g.between('red','apple'),'color')
    self.assertEquals(self.g.between('Jim','Tim'),['brother','arch enemy'])
    self.assertEquals(self.g.between('Tim','Jim'),['brother','arch enemy'])
  
  def testRename(self):
    g = Graph(':memory:')
    g.create_page('test page')
    g.rename('test page','Test Pageee!!!')
    self.assertEquals(g.list(),['Test Pageee!!!'])
    self.assertEquals(g.id_cache,{'Test Pageee!!!':1})
  

if __name__ == '__main__':
  unittest.main()
