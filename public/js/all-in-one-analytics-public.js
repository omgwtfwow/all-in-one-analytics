(function ($) {
	'use strict';
	$(function () {

		var cookieNames = document.cookie.split(/=[^;]*(?:;\s*|$)/);
		for (var i = 0; i < cookieNames.length; i++) {
			if (/^aio_analytics_product_clicked/.test(cookieNames[i])) {
				var currentCookie = cookieNames[i];
				var currentValue = Cookies.get(currentCookie);
				if (currentValue != null) {
					console.log(currentCookie);
					Cookies.set(currentCookie, '');
					Cookies.remove(currentCookie, '');
				}

			}
		}

		//PRODUCT CLICKED
		$('a.woocommerce-LoopProduct-link.woocommerce-loop-product__link').click(function () {
			if (true === $(this).hasClass('add_to_cart_button')) {
				return;
			}
			Cookies.set('aio_analytics_product_clicked', 'true');

		});

		//PAYMENT METHOD SELECTED
		$('form.checkout').on('click', 'input[name=\"payment_method\"]',
			function () {
				if (Cookies.get('aio_analytics_chose_payment_method') == null) {
					Cookies.set('aio_analytics_chose_payment_method', 'true');
					analytics.track('Checkout Step Completed', {
						"step": 'chose_payment_method'
					});
				}
			});

		//ENTERED BILLING EMAIL
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