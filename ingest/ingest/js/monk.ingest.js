/**
 * @file: monk.ingest.js
 * @requires: of.common.js, of.event.js, jquery.jcrop.js
 */

(function ($, namespace) {

	var defaults, methods;

	$.fn[namespace] = function (method) {
		if (methods[method]) {
			methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			methods.init.apply(this, arguments);
		} else {
			$.error('Method '+ method +' does not exist in jQuery.' + namespace);
		}
	};

	// defaults
	defaults = {
		trackDocument	: true,
		baseClass		: 'jcrop',
		addClass		: null,

		bgColor			: 'black',
		bgOpacity		: .6,
		borderOpacity	: .4,
		handleOpacity	: .5,

		handlePad		: 5,
		handleSize		: 9,
		handleOffset	: 5,
		edgeMargin		: 14,
		aspectRatio		: 0,

		keySupport		: false,
		cornerHandles	: true,
		sideHandles		: true,
		drawBorders		: true,
		dragEdges		: true,

		boxWidth		: 0,
		boxHeight		: 0,

		boundary		: 8,
		animationDelay	: 20,
		swingSpeed		: 3,

		allowSelect		: true,
		allowMove		: true,
		allowResize		: true,

		minSelect		: [10, 10],
		maxSize			: [0, 0],
		minSize			: [10, 10]
	};

	// methods
	methods = {
		init: function (options) {
			var opts = $.extend({}, defaults, options);

			return this.each(function () {
				var img, ratioW, ratioH, api, showDim,
					$this = $(this);

				img = new Image();
				img.src = $this.attr('src');

				ratioW = img.width / $this.width();
				ratioH = img.height / $this.height();

				showDim = function (dim) {
					var parent = $this.closest('.image-holder');

					parent.find('.text-holder input[name=dimX]').val(Math.round(dim.x * ratioW));
					parent.find('.text-holder input[name=dimY]').val(Math.round(dim.y * ratioH));
					parent.find('.text-holder input[name=dimW]').val(Math.round(dim.w * ratioW));
					parent.find('.text-holder input[name=dimH]').val(Math.round(dim.h * ratioH));
				};

				api = $.Jcrop(this, $.extend({
					onChange: showDim,
					onSelect: showDim
				}, opts));

				var x, y, w, h;

				x = (opts.x / ratioW);
				y = (opts.y / ratioH);
				w = (opts.w / ratioW);
				h = (opts.h / ratioH);

				api.setSelect([x, y, (x + w), (y + h)]);
			});
		}
	};

}(jQuery, 'ingest'));