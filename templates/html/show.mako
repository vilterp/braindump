<%inherit file="layout.mako"/>  
<%def name="title()"> &raquo; ${page['name']}</%def>
<%def name="heading()"> &raquo; <span id="page_name">${page['name']}</span></%def>
<%def name="head()"><%include file="show.js.mako"/></%def>
<%def name="content()">

<a href="#" id="edit_metadata_link" class="control" accesskey="m">Edit</a>
<div id="metadata">
  <%include file="metadata-html.mako"/>
</div>

<div id="backlinks">
  % if page['backlinks']:
    <ul id="backlinks_list">
    % for attribute in page['backlinks'].keys():
      <li class="backlink">${pagelink(attribute)} of ${pagelink(page['backlinks'][attribute])}</li>
    % endfor
    </ul>
  % else:
    <p class="notice">No backlinks.</p>
  % endif
</div>

<a href="#" id="edit_description_link" class="control">Edit</a>
<div id="description">
  <%include file="description-html.mako"/>
</div>

<!-- body...? -->

</%def>
<%def name="sidebar_actions()">
<span class="sidebar_heading">This Page:</span>
<ul>
  <li><a href="#" id="rename_link">rename</li>
  <li><a href="#" id="delete_link">delete</li>
</ul>
</%def>