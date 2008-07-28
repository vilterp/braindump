from inflector.Rules.English import English

def singularize(word):
  return English().singularize(word)

def pluralize(word):
  return English().pluralize(word)

def is_singular(word):
  return word == singularize(word)

def is_plural(word):
  return word == pluralize(word)

def merge(*sequences):
  merged = {}
  for sequence in sequences:
    for item in sequence:
      merged[item] = 1
  return merged.keys()

def set_or_append(target, item):
  if target is None:
    return item
  elif isinstance(target,list):
    target.append(item)
    return target
  else:
    target = [target]
    target.append(item)
    return target

def pluralize_key_if_value_is_list(the_dict):
  for attribute in the_dict.keys():
    if isinstance(the_dict[attribute],list):
      the_dict[pluralize(attribute)] = the_dict[attribute]
      del the_dict[attribute]
  return the_dict

class NonexistentPageError(NameError):
  pass
