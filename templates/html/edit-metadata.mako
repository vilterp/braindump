<textarea name="metadata" rows="8" cols="40">
  % for attribute in page['metadata'].keys():
${attribute}: ${page['metadata'][attribute]}
  % endfor
</textarea>