!function(t, e, a) {
	"use strict";
	var o = {
		_imageFocal: "image-focal",
		imageFocal: {
			_wrapper: "image-focal__wrapper",
			_img: "image-focal__img",
			_point: "image-focal__point",
			_clickarea: "image-focal__clickarea",
			_button: "image-focal__button"
		},
		_button: "button",
		button: {
			_primary: "button-primary",
			_disabled: "button-disabled"
		}
	};
	t.imageFocal || (t.imageFocal = {}), t.imageFocal.focalPoint = function(a, n) {
		var c = this;
		c.$el = t(a), c.el = a, c.$el.data("imageFocal.focalPoint", c);
		var i, s;
		c.init = function() {
			c.options = t.extend({}, t.imageFocal.focalPoint.defaultOptions, n), c.addInterfaceElements(), c.attachment.init(), c.focusInterface.init(), c.saveButton.init(), t(e).on("resize", c.attachment.updateDimensionData)
		}, c.addInterfaceElements = function() {
			var e, a = t(".edit-attachment-frame .attachment-media-view .details-image, .edit-attachment-frame .image-editor .imgedit-crop-wrap img");
			a.addClass(o.imageFocal._img), a.wrap('<div class="' + o._imageFocal + '"><div class="' + o.imageFocal._wrapper + '"></div></div>'), (e = t("." + o.imageFocal._wrapper)).append('<div class="' + o.imageFocal._point + '"></div>'), e.append('<div class="' + o.imageFocal._clickarea + '"></div>'), i = t("." + o._imageFocal), s = t("." + o.imageFocal._clickarea)
		}, c.attachment = {
			$el: !1,
			_id: !1,
			_width: !1,
			_height: !1,
			_offset: {
				x: !1,
				y: !1
			},
			_focalPoint: {
				x: 50,
				y: 50
			},
			init: function() {
				c.attachment.$el = t("." + o.imageFocal._img), c.attachment.getData(), c.attachment.$el.load(function() {
					c.attachment.updateDimensionData()
				})
			},
			getData: function() {
				c.attachment._id = t(c.el).find('#attachment-id').data("id");
				var e = {
					id: c.attachment._id
				};
				t.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: "get_focal_point",
						attachment: e
					},
					dataType: "json"
				}).always(function(t) {
					if (!0 === t.success) try {
						if (!t.data.focal_point.hasOwnProperty("x") || !t.data.focal_point.hasOwnProperty("y")) throw "Wrong object properties";
						c.attachment._focalPoint = t.data.focal_point
					} catch (t) {
						console.log(t)
					}
					c.attachment.updateDimensionData(), c.focusInterface.updateStylePosition(), c.focusInterface.$el.css({
						display: "block"
					}), c.focusInterface.updateDimensionData(), c.focusInterface.updateStyleBackground()
				})
			},
			updateDimensionData: function() {
				var t = c.attachment.$el;
				c.attachment._width = t.width(), c.attachment._height = t.height(), c.attachment._offset.x = t.offset().left, c.attachment._offset.y = t.offset().top
			}
		}, c.focusInterface = {
			$el: !1,
			_width: 0,
			_height: 0,
			_radius: 0,
			_offset: {
				x: 0,
				y: 0
			},
			_position: {
				x: 0,
				y: 0
			},
			_clickPosition: {
				x: 0,
				y: 0
			},
			_state: {
				move: !1,
				active: !1,
				hover: !1
			},
			init: function() {
				c.focusInterface.$el = t("." + o.imageFocal._point), s.on("mousedown", function(t) {
					1 === t.which && c.focusInterface.startMove(t, !0).move(t)
				}), c.focusInterface.$el.on("mousedown", function(t) {
					1 === t.which && c.focusInterface.startMove(t)
				}).on("mouseenter", function() {
					c.focusInterface.state.hover(!0)
				}).on("mouseleave", function() {
					c.focusInterface.state.hover(!1)
				}), t(e).on("mouseup", function(t) {
					1 === t.which && (c.focusInterface._state.move = !1, c.focusInterface._state.active = !1, i.removeClass("is-active"))
				}).on("mousemove", function(t) {
					c.focusInterface.move(t)
				}).on("resize", function() {
					c.focusInterface.updateDimensionData().updateStyle()
				})
			},
			startMove: function(t, e) {
				return c.attachment.updateDimensionData(), c.focusInterface.updateDimensionData().updateClickPosition(t, e), c.saveButton.highlight(), i.addClass("is-active"), c.focusInterface._state.move = !0, c.focusInterface._state.active = !0, this
			},
			move: function(t) {
				if (!1 === c.focusInterface._state.move) return !1;
				var e = {
						x: t.pageX,
						y: t.pageY
					},
					a = {},
					o = c.attachment._offset,
					n = c.focusInterface._clickPosition;
				a.x = e.x - o.x - n.x, a.y = e.y - o.y - n.y, a.x = u.calc.maxRange(a.x, 0, c.attachment._width), a.y = u.calc.maxRange(a.y, 0, c.attachment._height);
				var i = {};
				return i.x = a.x / c.attachment._width * 100, i.y = a.y / c.attachment._height * 100, c.attachment._focalPoint = i, c.focusInterface._position = a, c.focusInterface.updateStyle(), this
			},
			updateStyle: function() {
				return c.focusInterface.updateStylePosition(), c.focusInterface.updateStyleBackground(), this
			},
			updateStylePosition: function() {
				return c.focusInterface.$el.css({
					left: c.attachment._focalPoint.x + "%",
					top: c.attachment._focalPoint.y + "%"
				}), this
			},
			updateStyleBackground: function() {
				var t = 0 - (c.focusInterface._position.x - c.focusInterface._radius),
					e = 0 - (c.focusInterface._position.y - c.focusInterface._radius);
				return c.focusInterface.$el.css({
					backgroundImage: 'url("' + c.attachment.$el.attr("src") + '")',
					backgroundSize: c.attachment._width + "px " + c.attachment._height + "px ",
					backgroundPosition: t + "px " + e + "px "
				}), this
			},
			updateClickPosition: function(t, e) {
				var a = {
					x: 0,
					y: 0
				};
				if (!0 !== e) {
					var o = {
							x: t.pageX,
							y: t.pageY
						},
						n = c.focusInterface._offset;
					(a = {}).x = o.x - n.x, a.y = o.y - n.y
				}
				return c.focusInterface._clickPosition = a, this
			},
			updateDimensionData: function() {
				c.focusInterface._width = c.focusInterface.$el.width(), c.focusInterface._height = c.focusInterface.$el.height();
				var t = c.focusInterface._width / 2;
				c.focusInterface._radius = t;
				var e = c.focusInterface.$el.offset();
				return c.focusInterface._offset = {
					x: e.left + t,
					y: e.top + t
				}, c.focusInterface._position = {
					x: c.attachment._focalPoint.x / 100 * c.attachment._width,
					y: c.attachment._focalPoint.y / 100 * c.attachment._height
				}, this
			},
			state: {
				hover: function(t) {
					c.focusInterface._state.hover = t, i.toggleClass("is-hover", t)
				}
			}
		}, c.saveButton = {
			$el: !1,
			_ajaxState: !1,
			init: function() {
				var e = '<button type="button" class="' + o._button + " " + o.button._disabled + " crop-attachment " + o.imageFocal._button + '">' + focalPointL10n.saveButton + "</button>";
				t(c.el).find(".attachment-actions, .imgedit-submit").append(e), c.saveButton.$el = t("." + o.imageFocal._button), c.saveButton.$el.on("click", c.sendImageCropDataByAjax)
			},
			highlight: function() {
				c.saveButton.$el.removeClass(o.button._disabled).addClass(o.button._primary).text(focalPointL10n.saveButton)
			},
			activate: function() {
				c.saveButton.$el.removeClass(o.button._disabled).removeClass(o.button._primary)
			},
			disable: function() {
				c.saveButton.$el.removeClass(o.button._primary).addClass(o.button._disabled)
			}
		}, c.sendImageCropDataByAjax = function() {
			var e = {
				id: c.attachment._id,
				focal_point: c.attachment._focalPoint
			};
			t.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: "set_focal_point",
					attachment: e
				},
				dataType: "json",
				beforeSend: function() {
					if (!0 === c.saveButton._ajaxState) return !1;
					c.saveButton.$el.text(focalPointL10n.saving), c.saveButton.disable(), c.saveButton._ajaxState = !0
				}
			}).always(function(t) {
				!0 !== t.success && (c.saveButton.activate(), e = focalPointL10n.tryAgain), c.saveButton.$el.text(focalPointL10n.saved), c.saveButton._ajaxState = !1
			})
		};
		var u = {};
		u.calc = {
			maxRange: function(t, e, a) {
				var o = t;
				return t < e ? o = e : t > a && (o = a), o
			}
		}
	}, t.imageFocal.focalPoint.defaultOptions = {
		myDefaultValue: ""
	}, t.fn.imageFocal_focalPoint = function(e) {
		return this.each(function() {
			new t.imageFocal.focalPoint(this, e).init()
		})
	}
}(jQuery, window, document), function(t, e, a) {
	t(a).on("ready", function() {
		setInterval(function() {
			var e = t(".attachment-details, .image-editor");
			if (e.find(".details-image, .imgedit-crop-wrap img").length && !t(".image-focal").length) try {
				e.imageFocal_focalPoint()
			} catch (t) {
				console.log(t);
			}
		}, 500)
	})
}(jQuery, window, document);