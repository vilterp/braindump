import os, imp

plugins_path = os.getcwd() + '/plugins'
plugins = os.listdir(plugins_path)

for plugin in plugins:
  if '.' not in plugin:
    file_obj, path, description = imp.find_module(plugin,plugins_path)
    imp.load_module(plugin,file_obj,path,description)