$(document).ready(function () {
    var hash = window.location.hash.substr(1);

    if (hash.match(/login/)) {
        displayModal('modal_login');
    }

    if (hash.match(/password_reset/)) {
        displayModal('modal_resetting_request');
    }

    if (hash.match(/applicant_success/)) {
        displayModal('modal_simple', {'template': 'applicant-success'});
    }
})
