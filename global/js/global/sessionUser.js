$.ajax({
  url: '/api/checkSessionUser',
  success: function (data, textStatus, xhr) {
    if (data.inactive) location.href = '/';
  },
});
