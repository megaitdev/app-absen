document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        console.log(base_url());

        $(`#profile-tab`).click(function (e) {
            setProfileTabActive();
        });
        $(`#change-password-tab`).click(function (e) {
            setResetPasswordTabActive();
        });
    });
});

function setProfileTabActive() {
    $.ajax({
        type: "get",
        url: base_url() + `profile/tab/profile`,
        success: function (res) {
            // console.log(res);
            iziToast.show({
                title: "Profile!",
                message: "This tab show your profile information",
                position: "bottomRight",
                timeout: 1680,
            });
        },
    });
}
function setResetPasswordTabActive() {
    $.ajax({
        type: "get",
        url: base_url() + `profile/tab/change-password`,
        success: function (res) {
            // console.log(res);
            iziToast.show({
                title: "Reset Password!",
                message: "This tab show your form to change password",
                position: "bottomRight",
                timeout: 1680,
            });
        },
    });
}
