% if page['metadata'].keys():
  <ul id="metadata_list">
  % for attribute in page['metadata'].keys():
    <li class="metadata">${pagelink(attribute)}: ${pagelink(page['metadata'][attribute])}</li>
  % endfor
  </ul>
% else:
  <p class="notice">No metadata.</p>
% endif