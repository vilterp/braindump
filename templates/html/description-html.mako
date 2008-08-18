% if page['description']:
  ${filters.do('description',page['description'])}
% else:
  <p class="notice">No description.</p>
% endif