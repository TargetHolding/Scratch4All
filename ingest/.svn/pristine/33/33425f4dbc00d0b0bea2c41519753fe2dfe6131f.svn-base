/**
 * @file: of.event.js
 * @requires: of.common.js
 */

(function ($) {

	OF.namespace('OF.Event');

	// OF.Event constructor
	OF.Event = function (name) {
		this.name		= name;
		this.listeners	= [];
	};

	// OF.Event.attach
	OF.Event.method('attach', function (name, listener) {
		if (typeof listener === 'function') {
			this.listeners.push({
				name	: name,
				listener: listener
			});
		}
	});

	// OF.Event.notify
	OF.Event.method('notify', function () {
		var i, 
			il		= this.listeners.length,
			args	= arguments || {};

		OF.log('[%d] %d.triggered: %o, %o', 'e+', 'event', this.name, args);
		for (i=0; i < il; i++) {
			OF.log('[%d] %d.notifying: %o', 'e-', this.name, this.listeners[i].name);
			this.listeners[i].listener.apply(this, args);
		}
	});

}(jQuery));