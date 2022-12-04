function initRefreshTokenCalls() {
    setTimeout(function(){

        $.ajax({
            type: "POST",
            url: "/api/refresh-token",
            contentType: "application/json",
            dataType: "json",
            data: '{"refresh_token":"'+sessionStorage.getItem('refreshToken')+'"}',

            beforeSend: function() {
            },
            success: function(response) {
                if (response && response.token && response.refresh_token) {
                    sessionStorage.setItem('token', response.token);
                    sessionStorage.setItem('refreshToken', response.refresh_token);
                    initRefreshTokenCalls();
                }
            },
            error: function(request, status, error) {
                alert('Your are disconnected!');
                sessionStorage.clear();
                document.location.href="/";
            }
        });

    }, 15000);
}

function sessionExists(){
    var token = sessionStorage.getItem('token');
    var refreshToken = sessionStorage.getItem('refreshToken');

    return (token && refreshToken && token !== '' && refreshToken !== '');
}

$('#btn-log-out').click(function() {
    sessionStorage.clear();
    document.location.href="/";
});

function getToken() {
    $.ajax({
        type: "GET",
        url: "/api/security/get-public-token",
        contentType: "application/json",
        dataType: "json",

        beforeSend: function() {
            $('#loading').html('<img src="/images/loading.gif" width="25" height="25"/>');
        },
        success: function(response) {
            if (response.token) {
                $('#token').val(response.token);
                $('#loading').html('');
                return;
            }
            $('#loading').html('Error');
        },
        error: function(request, status, error) {
            $('#loading').html('Error');
        }
    });
}