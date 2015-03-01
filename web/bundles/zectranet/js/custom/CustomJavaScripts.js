$(function () {
        $('.show-info').click(function () {
            $('.page-information').toggleClass('hidden');
        });

        $('.show-danger').click(function () {
            $('.page-danger-zone').toggleClass('hidden');
        });

        $('.show-lock').click(function () {
            $('.page-lock-zone').toggleClass('hidden');
        });
    }
);