(function ($) {
    'use strict';
    $(function () {
        console.log('init');
        //when the DOM is ready//
        //looks for aio cookies and clears them
        //
        /*      var cookieNames = document.cookie.split(/=[^;]*(?:;\s*|$)/);

        for (var i = 0; i < cookieNames.length; i++) {
            if (/^aio_analytics_/.test(cookieNames[i])) {
                var currentCookie = cookieNames[i];
                console.log(currentCookie);
                Cookies.set(currentCookie, '');
                Cookies.remove(currentCookie, '');
            }
        }*/
        $('form.checkout').on('click', 'input[name=\"payment_method\"]',
            function () {
                if (Cookies.get('aio_analytics_chose_payment_method') == null) {
                    Cookies.set('aio_analytics_chose_payment_method', 'true');
                    analytics.track('Checkout Step Completed', {
                        "step": 'chose_payment_method'
                    });
                }
            });

        $('form.checkout').on('change', 'input#billing_email',
            function () {
                if (Cookies.get('aio_analytics_entered_billing_email') == null) {
                    Cookies.set('aio_analytics_entered_billing_email', 'true');
                    analytics.track('Checkout Step Completed', {
                        "step": 'entered_billing_email'
                    });
                }
            });

    });


})(jQuery);