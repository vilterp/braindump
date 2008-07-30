<%inherit file="layout.mako"/>  
<%def name="title()"></%def>
<%def name="heading()"></%def>
<%def name="head()">
${javascript_include_tag(url('javascripts/filter_interface.js'))}
</%def>
<%def name="content()">
<div id="filter_interface">
% if criteria:
  <a href="#" id="visibility_toggle" accesskey="f">Hide</a>
  <form id="criteria_form">
% else:
  <a href="#" id="visibility_toggle" accesskey="f">Filter</a>
  <form id="criteria_form" style="display: none;">
% endif
    list pages where 
    <input type="text" value="${criteria}" id="criteria_input"/> 
    <input type="submit" value="Update"/> 
    <a href="#" id="clear_link">clear</a>
  </form>
</div>

<div id="pages_list">
  <%include file="list.mako"/>
</div>
</%def>