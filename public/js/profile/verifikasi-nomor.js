document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        moment.locale("id");
        user = JSON.parse($(`#user`).val());
        checkIsVerified();
    });
});

let user;
let isVerified;
let verification;

function checkIsVerified() {
    $.ajax({
        type: "get",
        url:
            base_url() + `profile/ajax/verifikasi-nomor/is-verified/${user.id}`,
        success: function (res) {
            isVerified = res;
            if (res) {
                swal({
                    title: "WhatsApp Number is Verified",
                    text: "Your phone number has been successfully verified. Thank you for completing the verification process. You can now enjoy full access to all features!",
                    icon: "success",
                    closeOnClickOutside: false,
                }).then((GoBack) => {
                    if (GoBack) {
                        window.location.href = base_url() + "profile";
                    } else {
                        swal("Your imaginary file is safe!");
                    }
                });
            } else {
                sendVerificationCode();
            }
        },
    });
}

function sendVerificationCode() {
    $.ajax({
        type: "get",
        url: base_url() + `profile/ajax/verifikasi-nomor/send-code/${user.id}`,
        data: "data",
        success: function (res) {
            verification = res.data;
            if (res.code != 200) {
                iziToast.info({
                    title: "Info",
                    message: res.message + " to " + user.nomor_wa,
                    position: "bottomRight",
                    timeout: 1680,
                });
            } else {
                iziToast.success({
                    title: "Success",
                    message: "We've sent verification code to " + user.nomor_wa,
                    position: "bottomRight",
                    timeout: 1680,
                });
            }
        },
    });
}

function verifikasi() {
    var expired = moment(verification.expired_at);

    // console.log("this time : " + moment().format("D MMMM Y, H:mm:ss"));
    // console.log("expired time : " + expired.format("D MMMM Y, H:mm:ss"));
    // console.log(moment().isBefore(expired));

    var code = $(`#code`).val();
    $(`#error-code`).html("Invalid Code");
    if (code == verification.code) {
        if (moment().isAfter(expired)) {
            $(`#error-code`).html("Code is expired");
            $(`#error-code`).attr("hidden", false);
        } else {
            $(`#error-code`).attr("hidden", true);
            $.ajax({
                type: "get",
                url:
                    base_url() +
                    `profile/ajax/verifikasi-nomor/verified/${user.id}`,
                success: function (res) {
                    checkIsVerified();
                },
            });
        }
    } else {
        $(`#error-code`).attr("hidden", false);
    }
}

function resendVerificationCode() {
    $.ajax({
        type: "get",
        url:
            base_url() + `profile/ajax/verifikasi-nomor/resend-code/${user.id}`,
        data: "data",
        success: function (res) {
            verification = res.data;
            iziToast.success({
                title: "Success",
                message: "We've sent verification code to " + user.nomor_wa,
                position: "bottomRight",
                timeout: 1680,
            });
        },
    });
    $(`#error-code`).attr("hidden", true);
}
