<%inherit file="layout.mako"/>  
<%def name="title()"></%def>
<%def name="heading()"></%def>
<%def name="head()">${javascript_include_tag(url('javascripts/filter_interface'))}</%def>
<%def name="content()">
<div id="filter_interface">
  <a href="#" id="visibility_toggle" accesskey="f"><% 
  if criteria: context.write('Hide')
  else: context.write('Filter')%></a>
  <form id="criteria_form"<%
  if not criteria: context.write(' style="display: none;"')
  %>>
    list pages where 
    <input type="text" value="<% 
    if criteria: context.write(criteria)
    else: context.write('') %>"
    id="criteria_input" name="criteria" size="30"/> 
    <input type="submit" value="Update"/>
    <a href="#" id="clear_link" style="display: none;">clear</a>
  </form>
</div>

<img src="${url('/images/spinner.gif')}" style="display: none;" id="spinner"/>
<div id="pages_list">
  <%include file="list.mako"/>
</div>
</%def>
<%def name="sidebar_actions()">
<span class="sidebar_heading">This List:</span>
<ul>
  <li><% 
  if criteria: theurl = '?criteria=%s' % escape(criteria) # url escape
  else: theurl = ''
  context.write(link_to('permalink',url(theurl),id='permalink')) %></li>
  <li><% 
  if criteria: theurl = '?format=dump&criteria=%s' % escape(criteria) # url escape
  else: theurl = '?format=dump'
  context.write(link_to('dump &raquo;',url(theurl),id="dump_link")) %></li>
</ul>
</%def>