function base_url() {
    var pathArray = window.location.pathname.split("/");
    return window.location.origin + pathArray.slice(0, 3).join("/") + "/";
}
function none() {
    return null;
}
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");

// function getNotification(user_id = null) {
//     $.ajax({
//         url: base_url() + `ajax/get-notification`,
//         type: "GET",
//         cache: false,
//         success: function (res) {
//             console.log(res);
//         },
//     });
// }
// getNotification();
