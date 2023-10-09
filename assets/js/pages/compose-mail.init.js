let editor;

function composeMail() {
  $('#compose-editor').length &&
    ClassicEditor.create(document.querySelector('#compose-editor'))
      .then(function (o) {
        editor = o;
      })
      .catch(function (o) {
        console.error('error', o);
      });
}
$(function () {
  composeMail();
});

/* Obtener contenido */
getContent = (op) => {
  content = editor.getData();

  if (op == 1)
    if (content.includes('<p>'))
      content = content.replace('<p>', '').replace('</p>', '');

  return content;
};

/* Estabecer contenido */
setContent = (data) => {
  editor.setData(data);
};
