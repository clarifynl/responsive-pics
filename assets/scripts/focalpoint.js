/* global jQuery, ajaxurl, focalPointL10n */
(function($, win, doc) {
	function maxRange(val1, val2, val3) {
		let result = val1;

		if (val1 < val2) {
			result = val2;
		} else if (val1 > val3) {
			result = val3;
		}

		return result;
	}

	const CLASSES = {
		IMAGE_FOCAL          : 'image-focal',
		IMAGE_FOCAL_WRAPPER  : 'image-focal__wrapper',
		IMAGE_FOCAL_IMG      : 'image-focal__img',
		IMAGE_FOCAL_POINT    : 'image-focal__point',
		IMAGE_FOCAL_CLICKAREA: 'image-focal__clickarea',
		IMAGE_FOCAL_BUTTON   : 'image-focal__button',
		BUTTON               : 'button',
		BUTTON_PRIMARY       : 'button-primary',
		BUTTON_DISABLED      : 'button-disabled'
	};

	$.imageFocal = $.imageFocal || {};

	$.imageFocal.focalPoint = function(el, options) {
		const self = this;

		self.$el = $(el);
		self.el = el;
		self.$el.data('imageFocal.focalPoint', self);

		let $imageFocal;
		let $clickArea;

		self.init = function() {
			self.options = $.extend({}, $.imageFocal.focalPoint.defaultOptions, options);

			self.addInterfaceElements();
			self.attachment.init();
			self.focusInterface.init();
			self.saveButton.init();

			$(doc).on('resize', self.attachment.updateDimensionData);
		};

		self.addInterfaceElements = function() {
			const $el = $('.edit-attachment-frame .attachment-media-view .details-image, .edit-attachment-frame .image-editor .imgedit-crop-wrap img');

			$el.addClass(CLASSES.IMAGE_FOCAL_IMG);
			$el.wrap(`<div class="${CLASSES.IMAGE_FOCAL}"><div class="${CLASSES.IMAGE_FOCAL_WRAPPER}"></div></div>`);

			const $focalWrapper = $(`.${CLASSES.IMAGE_FOCAL_WRAPPER}`);

			$focalWrapper.append(`<div class="${CLASSES.IMAGE_FOCAL_POINT}"></div>`);
			$focalWrapper.append(`<div class="${CLASSES.IMAGE_FOCAL_CLICKAREA}"></div>`);

			$imageFocal = $(`.${CLASSES.IMAGE_FOCAL}`);
			$clickArea = $(`.${CLASSES.IMAGE_FOCAL_CLICKAREA}`);
		};

		self.attachment = {
			$el   : false,
			id    : false,
			width : false,
			height: false,
			offset: {
				x: false,
				y: false
			},
			focalPoint: {
				x: 50,
				y: 50
			},
			init() {
				self.attachment.$el = $(`.${CLASSES.IMAGE_FOCAL_IMG}`);
				self.attachment.getData();
				self.attachment.$el.load(() => {
					self.attachment.updateDimensionData();
				});
			},
			getData() {
				self.attachment.id = $(self.el).find('#attachment-id').data('id');

				const attachment = {
					'id': self.attachment.id
				};

				$.ajax({
					type: 'POST',
					url : ajaxurl,
					data: {
						action: 'get_focal_point',
						attachment
					},
					dataType: 'json'
				}).always(result => {
					if (result['success'] === true && result['data'] && result['data']['focal_point']) {
						try {
							if (result['data']['focal_point']['x'] === undefined || result['data']['focal_point']['y'] === undefined) {
								throw new Error('no coordinates');
							}

							self.attachment.focalPoint = result['data']['focal_point'];
						} catch (err) {
							console.log(err);
						}
					}

					self.attachment.updateDimensionData();
					self.focusInterface.updateStylePosition();
					self.focusInterface.$el.css({
						display: 'block'
					});
					self.focusInterface.updateDimensionData();
					self.focusInterface.updateStyleBackground();
				});
			},
			updateDimensionData() {
				const $el = self.attachment.$el;

				self.attachment.width = $el.width();
				self.attachment.height = $el.height();
				self.attachment.offset.x = $el.offset().left;
				self.attachment.offset.y = $el.offset().top;
			}
		};

		self.focusInterface = {
			$el   : false,
			width : 0,
			height: 0,
			radius: 0,
			offset: {
				x: 0,
				y: 0
			},
			position: {
				x: 0,
				y: 0
			},
			clickPosition: {
				x: 0,
				y: 0
			},
			state: {
				move  : false,
				active: false,
				hover : false
			},
			init() {
				self.focusInterface.$el = $(`.${CLASSES.IMAGE_FOCAL_POINT}`);

				$clickArea.on('mousedown', e => {
					if (e.which === 1) {
						self.focusInterface.startMove(e, true).move(e);
					}
				});

				self.focusInterface.$el
					.on('mousedown', e => {
						if (e.which === 1) {
							self.focusInterface.startMove(e);
						}
					})
					.on('mouseenter', () => {
						self.focusInterface.hover(true);
					})
					.on('mouseleave', () => {
						self.focusInterface.hover(false);
					});

				$(win)
					.on('mouseup', e => {
						if (e.which === 1) {
							self.focusInterface.state.move = false;
							self.focusInterface.state.active = false;
							$imageFocal.removeClass('is-active');
						}
					})
					.on('mousemove', e => {
						self.focusInterface.move(e);
					})
					.on('resize', () => {
						self.focusInterface.updateDimensionData().updateStyle();
					});
			},
			startMove(t, e) {
				self.attachment.updateDimensionData();
				self.focusInterface.updateDimensionData().updateClickPosition(t, e);
				self.saveButton.highlight();

				$imageFocal.addClass('is-active');

				self.focusInterface.state.move = true;
				self.focusInterface.state.active = true;

				return this;
			},
			move(e) {
				if (self.focusInterface.state.move === false) {
					return false;
				}

				const a = {};
				const offset = self.attachment.offset;
				const pos = self.focusInterface.clickPosition;

				a.x = e.pageX - offset.x - pos.x;
				a.y = e.pageY - offset.y - pos.y;
				a.x = maxRange(a.x, 0, self.attachment.width);
				a.y = maxRange(a.y, 0, self.attachment.height);

				self.attachment.focalPoint = {
					x: a.x / self.attachment.width * 100,
					y: a.y / self.attachment.height * 100
				};

				self.focusInterface.position = a;
				self.focusInterface.updateStyle();

				return this;
			},
			updateStyle() {
				self.focusInterface.updateStylePosition();
				self.focusInterface.updateStyleBackground();

				return this;
			},
			updateStylePosition() {
				self.focusInterface.$el.css({
					left: `${self.attachment.focalPoint.x}%`,
					top : `${self.attachment.focalPoint.y}%`
				});

				return this;
			},
			updateStyleBackground() {
				const x = 0 - (self.focusInterface.position.x - self.focusInterface.radius);
				const y = 0 - (self.focusInterface.position.y - self.focusInterface.radius);

				self.focusInterface.$el.css({
					backgroundImage   : `url("${self.attachment.$el.attr('src')}")`,
					backgroundSize    : `${self.attachment.width}px ${self.attachment.height}px`,
					backgroundPosition: `${x}px ${y}px `
				});

				return this;
			},
			updateClickPosition(t, e) {
				const pos = {
					x: 0,
					y: 0
				};

				if (e !== true) {
					const o = self.focusInterface.offset;

					pos.x = t.pageX - o.x;
					pos.y = t.pageY - o.y;
				}

				self.focusInterface.clickPosition = pos;

				return this;
			},
			updateDimensionData() {
				self.focusInterface.width = self.focusInterface.$el.width();
				self.focusInterface.height = self.focusInterface.$el.height();

				const radius = self.focusInterface.width / 2;

				self.focusInterface.radius = radius;

				const o = self.focusInterface.$el.offset();

				self.focusInterface.offset = {
					x: o.left + radius,
					y: o.top + radius
				};

				self.focusInterface.position = {
					x: self.attachment.focalPoint.x / 100 * self.attachment.width,
					y: self.attachment.focalPoint.y / 100 * self.attachment.height
				};

				return this;
			},
			hover(val) {
				self.focusInterface.state.hover = val;

				$imageFocal.toggleClass('is-hover', val);
			}
		};

		self.saveButton = {
			$el     : false,
			isSaving: false,
			init() {
				const button = `<button type="button" class="${CLASSES.BUTTON} ${CLASSES.BUTTON_DISABLED} crop-attachment ${CLASSES.IMAGE_FOCAL_BUTTON}">${focalPointL10n.saveButton}</button>`;

				$(self.el)
					.find('.attachment-actions, .imgedit-submit')
					.append(button);

				self.saveButton.$el = $(`.${CLASSES.IMAGE_FOCAL_BUTTON}`);
				self.saveButton.$el.on('click', self.sendImageCropDataByAjax);
			},
			highlight() {
				self.saveButton.$el
					.removeClass(CLASSES.BUTTON_DISABLED)
					.addClass(CLASSES.BUTTON_PRIMARY)
					.text(focalPointL10n.saveButton);
			},
			activate() {
				self.saveButton.$el
					.removeClass(CLASSES.BUTTON_DISABLED)
					.removeClass(CLASSES.BUTTON_PRIMARY);
			},
			disable() {
				self.saveButton.$el
					.removeClass(CLASSES.BUTTON_PRIMARY)
					.addClass(CLASSES.BUTTON_DISABLED);
			}
		};

		self.sendImageCropDataByAjax = function() {
			const attachment = {
				'id'         : self.attachment.id,
				'focal_point': self.attachment.focalPoint
			};

			$.ajax({
				type: 'POST',
				url : ajaxurl,
				data: {
					action: 'set_focal_point',
					attachment
				},
				dataType: 'json',
				beforeSend() {
					if (self.saveButton.isSaving === true) {
						return false;
					}

					self.saveButton.$el.text(focalPointL10n.saving);
					self.saveButton.disable();
					self.saveButton.isSaving = true;

					return true;
				}
			}).always(result => {
				let msg = focalPointL10n.saved;

				if (result.success !== true) {
					self.saveButton.activate();
					msg = focalPointL10n.tryAgain;
				}

				self.saveButton.$el.text(msg);
				self.saveButton.isSaving = false;
			});
		};
	};

	$.imageFocal.focalPoint.defaultOptions = {
		myDefaultValue: ''
	};

	$.fn.initFocalPoint = function(options) {
		return this.each(function() {
			console.log('initFocalPoint', this);
			new $.imageFocal.focalPoint(this, options).init();
		});
	};

	$(doc).on('ready', () => {
		win.setInterval(() => {
			const $el = $('.attachment-details, .image-editor');

			if ($el.find('.details-image, .imgedit-crop-wrap img').length && !$('.image-focal').length) {
				try {
					console.log('interval', $el);
					$el.initFocalPoint();
				} catch (err) {
					console.log(err);
				}
			}
		}, 500);
	});
})(jQuery, window, document);
