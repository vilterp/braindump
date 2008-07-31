% if page.keys():
  <ul id="metadata_list">
  % for attribute in page.keys():
    <li class="metadata">${pagelink(attribute)}: ${pagelink(page[attribute])}</li>
  % endfor
  </ul>
% else:
  <p class="notice">No metadata.</p>
% endif
<!-- TODO: plurals! -->