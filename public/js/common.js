function initRefreshTokenCalls() {
    setTimeout(function(){

        $.ajax({
            type: "POST",
            url: "/api_refresh_token",
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
            error: function() {
                alert('Your are disconected!');
                sessionStorage.setItem('refreshToken', response.refresh_token);
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