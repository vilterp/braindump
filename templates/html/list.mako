% if pages:
  <ul>
  % for page in pages:
    <li>${link(page,'show/%s' % page)}</li>
  % endfor
  </ul>
% elif criteria:
  <p class="notice">No pages match your criteria.</p>
% else:
  <p class="notice">There are no pages. Type a page's name in the box on the right to get started &raquo;</p>
% endif