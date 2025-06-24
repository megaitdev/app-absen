function base_url() {
    var pathArray = window.location.pathname.split("/");
    // artisan serve
    // console.log(window.location.origin + pathArray.slice(0, 1).join("/") + "/");

    // standalone
    // console.log(window.location.origin + pathArray.slice(0, 3).join("/") + "/");

    return window.location.origin + pathArray.slice(0, 1).join("/") + "/";
}
function none() {
    return null;
}
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");

function firstUp(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

moment.tz.add([
    "Asia/Jakarta|LMT BMT +0720 +0730 WIB|-77.c -77.c -7k -7u -70|01232425|-1Q0Tk luM0 mPzO 8vWu 6kpu 4PXu xhcu|31e6",
]);

moment.tz.setDefault("Asia/Jakarta");
