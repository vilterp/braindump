<%inherit file="layout.mako"/>  
<%def name="title()"></%def>
<%def name="heading()"></%def>
<%def name="content()">
<ul>
% for page in pages:
  <li>${page}</li>
% endfor
</ul>
</%def>