document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init tabActive

        $(function () {
            $("#date").daterangepicker({
                locale: {
                    format: "DD MMM YYYY",
                },
            });
        });
    });
});
