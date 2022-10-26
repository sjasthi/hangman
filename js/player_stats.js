// every jquery file needs this document.ready function to be able to run the code, it makes sure the dom is loaded first
$(document).ready(function () {

    const modal = $(".modal-container"); // the player stats popup modal

    showModal(modal);
    hideModal(modal);
});


function showModal(modal) {
    $('#stats-btn').click(() => {
        modal.show();
    

    });
}

function hideModal(modal) {
    $('#close-btn').click(() => {
        modal.hide();

    });
}