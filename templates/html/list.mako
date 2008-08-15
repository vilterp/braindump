% if pages:
  <ul>
  % for page in pages:
    <li>${pagelink(page.name)}</li>
  % endfor
  </ul>
% else:
  <p class="notice">There are no pages. Type a page's name in the box on the right to get started &raquo;</p>
% endif