// Create a queue to push events and stub all methods
window.analytics || (window.analytics = {});
window.analytics_queue || (window.analytics_queue = []);
(function () {
	var methods = ['identify', 'track', 'trackLink', 'trackForm', 'trackClick', 'trackSubmit', 'page', 'pageview', 'ab', 'alias', 'ready', 'group', 'on', 'once', 'off'];
	var factory = function (method) {
		return function () {
			var args = Array.prototype.slice.call(arguments);
			args.unshift(method);
			analytics_queue.push(args);
			return window.analytics;
		};
	};

	for (var i = 0; i < methods.length; i++) {
		var method = methods[i];
		window.analytics[method] = factory(method);
	}
})();

// Load analytics.js after everything else
analytics.load = function (callback) {
	var script = document.createElement('script');
	script.async = true;
	script.type = 'text/javascript';
	script.src = settingsAIO.analyticsUrl;
	if (script.addEventListener) {
		script.addEventListener('load', function (e) {
			if (typeof callback === 'function') {
				callback(e);
			}
		}, false);
	} else {  // IE8
		script.onreadystatechange = function () {
			if (this.readyState === 'complete' || this.readyState === 'loaded') {
				callback(window.event);
			}
		};
	}
	var firstScript = document.getElementsByTagName('script')[0];
	firstScript.parentNode.insertBefore(script, firstScript);
};
analytics.load(function () {
	var trackingSettings = jQuery.parseJSON(settingsAIO.settings);
	analytics.initialize(trackingSettings);

	// Loop through the interim analytics queue and reapply the calls to their
	// proper analytics.js method.
	while (window.analytics_queue.length > 0) {
		var item = window.analytics_queue.shift();
		var method = item.shift();
		if (analytics[method]) analytics[method].apply(analytics, item);
	}
});
//page call needs to be added elsewhere
