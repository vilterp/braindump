<textarea name="metadata" rows="8" cols="40">
  % for attribute in page['metadata'].keys():
${attribute}: ${list_to_human(page['metadata'][attribute])}
  % endfor
</textarea>