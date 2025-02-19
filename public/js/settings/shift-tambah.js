document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init tabActive

        $(".timepick").timepicker({
            disableMousewheel: true,
        });
    });
});

function handleIsBreak() {
    if (document.getElementById("is_break").checked) {
        document.getElementById("jam_mulai_istirahat").disabled = false;
        document.getElementById("jam_selesai_istirahat").disabled = false;
    } else {
        document.getElementById("jam_mulai_istirahat").disabled = true;
        document.getElementById("jam_selesai_istirahat").disabled = true;
    }
}
