$(function() {
    if (!sessionStorage.getItem('token') || sessionStorage.getItem('token')==='') {
        $('#btn-settings').hide();
        $('#btn-log-out').hide();
    }
});
