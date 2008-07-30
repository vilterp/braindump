<ul>
% for page in pages:
  <li>${link_to(page,url('show/%s' % page))}</li>
% endfor
</ul>