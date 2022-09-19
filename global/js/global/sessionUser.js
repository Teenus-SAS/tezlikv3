$.ajax({
    url: '/api/checkSessionUser',
    success: function(data, textStatus, xhr) {
        console.log(data)
    },
})