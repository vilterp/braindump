% if page['description']:
  ${page['description'] | smart_str, textile}
% else:
  <p class="notice">No description.</p>
% endif