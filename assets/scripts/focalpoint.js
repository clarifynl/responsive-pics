(function($) {
	/**
	 Global variables
	**/
	let _view;
	let $image;
	let $imageFocal;
	let $imageFocalWrapper;
	let $imageFocalPoint;
	let $imageFocalClickarea;
	let $imageFocalSave;

	/**
	 Focal
	**/
	const Focal = {
		/**
		 Set variables
		**/
		init: focalPoint => {
			Focal.wrapper = $imageFocalWrapper;
			Focal.picker = $imageFocalClickarea;
			Focal.point  = $imageFocalPoint;
			Focal.positionFocalPoint(focalPoint);
			Focal.setEventListeners();
		},

		/**
		 Event Listeners
		**/
		setEventListeners: () => {
			Focal.picker.on('click', Focal.setFocalPoint);

			// Check if jQuery UI Draggable widget is active
			if (typeof($.ui.draggable) === 'function') {
				Focal.point.draggable({
					cursor: 'move',
					start: Focal.startDrag,
					drag: Focal.dragging,
					stop: Focal.stopDrag,
					containment: Focal.wrapper
				});
			}
		},

		positionFocalPoint: position => {
			Focal.x = position.x;
			Focal.y = position.y;

			Focal.point.css({
				left: `${position.x}%`,
				top: `${position.y}%`
			});
		},

		setFocalPoint: e => {
			$imageFocalSave.removeClass('button-disabled');

			var pointYOffset = e.offsetY - Focal.point.height() / 2,
				pointXOffset = e.offsetX - Focal.point.width() / 2;

			// Convert absolute coordinates to percentages
			Focal.x = Number(pointXOffset / Focal.picker.width() * 100).toFixed(2);
			Focal.y = Number(pointYOffset / Focal.picker.height() * 100).toFixed(2);
			Focal.positionFocalPoint(Focal);
		},

		startDrag: e => {
			$('body').addClass('focal-point-dragging');
			$imageFocalSave.removeClass('button-disabled');
		},

		dragging: e => {
			Focal.x = Number(e.target.offsetLeft / Focal.picker.width() * 100).toFixed(2);
			Focal.y = Number(e.target.offsetTop / Focal.picker.height() * 100).toFixed(2);
		},

		stopDrag: e => {
			$('body').removeClass('focal-point-dragging');
			Focal.positionFocalPoint(Focal);
		}
	};

	$(document).ready(() => {
		/**
		 * Init templates
		 */
		const initTemplates = element => {
			// Append focal point selector
			const selectView   = wp.media.template('attachment-select-focal-point');
			const selectParent = element.find('.thumbnail');
			const selectImage  = element.find('.details-image');

			if (selectView) {
				console.log(selectParent, selectImage);
				selectParent.prepend(selectView);
				// Set image focal elements
				$imageFocal          = element.find('.image-focal');
				$imageFocalWrapper   = element.find('.image-focal__wrapper');
				$imageFocalPoint     = element.find('.image-focal__point');
				$imageFocalClickarea = element.find('.image-focal__clickarea');
				selectImage.prependTo($imageFocalWrapper);
				$image               = $imageFocalWrapper.find('.details-image');
			}

			// Append focal point save button
			const saveView   = wp.media.template('attachment-save-focal-point');
			const saveParent = element.find('.attachment-actions');
			if (saveView) {
				saveParent.append(saveView);
				$imageFocalSave = element.find('button.save-attachment-focal-point');
			}
		};

		/**
		 * Save Focal Point
		 */
		const saveFocalPoint = attachment => {
			const focalPoint = {
				x: Focal.x,
				y: Focal.y
			};

			attachment.set({focalPoint});
			$.ajax({
				url: wp?.ajax?.settings?.url,
				method: 'POST',
				data: {
					action: 'save_focal_point',
					attachment: attachment?.attributes
				}
			})
			// Update view on succesfull save
			.done(data => {
				_view.update();
			})
			.fail((jqXHR, textStatus) => {
				console.log('save focal point error', jqXHR);
			})
			.always(() => {
				$imageFocalSave.addClass('button-disabled');
			});
		};

		/**
		 * Update Focus Interface
		 */
		const updateFocusInterface = () => {
			$imageFocalWrapper.css({
				width: `${$image.width()}px`
			});
		};

		/**
		 * Init Focus Interface
		 */
		const initFocusInterface = attachment => {
			const focalPoint = attachment.get('focalPoint');

			// Interface
			$(window).on('resize', updateFocusInterface);
			$image.on('load', e => {
				updateFocusInterface();
				Focal.init(focalPoint);
			});

			// Save button
			$imageFocalSave.on('click', e => {
				e.preventDefault();
				saveFocalPoint(attachment);
			});
		};

		/**
		 * Extended view render
		 */
		const renderView = view => {
			console.log('renderView', view);
			const type = view.model.get('type');

			if (type === 'image') {
				initTemplates(view.$el);
				initFocusInterface(view.model);
			}
		}

		/**
		 * Extended view changed
		 */
		const changeView = view => {
			console.log('changeView', view);
			const type       = view.model.get('type');
			const focalPoint = view.model.get('focalPoint');

			if (type === 'image') {
				Focal.positionFocalPoint(focalPoint);
			}
		};

		/**
		 * Extend TwoColumn view
		 */
		const TwoColumnView = wp.media.view.Attachment.Details.TwoColumn;
		if (TwoColumnView) {
			wp.media.view.Attachment.Details.TwoColumn = TwoColumnView.extend({
				// Add focalPoint change listener
				initialize: function() {
					_view = this;
					this.model.on('change:focalPoint', this.change, this);

					return this;
				},
				// Init extended template
				render: function() {
					wp.media.view.Attachment.prototype.render.apply(this, arguments);
					renderView(this);

					return this;
				},
				// Re-init focal point on input change
				change: function() {
					changeView(this);

					return this;
				},
				// Update view on focal point js change
				update: function() {
					this.views.detach();
					this.model.fetch();
					this.views.render();

					return this;
				}
			});
		}

		/**
		 * Extend EditImage view
		 */
		let EditImageView = wp.media.view.EditImage.Details;
		if (EditImageView) {
			wp.media.view.EditImage.Details = EditImageView.extend({
				// Add focalPoint change listener
				initialize: function(options) {
					_view = this;
					this.model.on('change:focalPoint', this.change, this);
					wp.media.view.EditImage.prototype.initialize.apply(this, arguments);
				},
				// Init extended template
				render: function() {
					wp.media.view.EditImage.prototype.render.apply(this, arguments);
					renderView(this);
				},
				// Cancel view
				back: function() {
					this.frame.content.mode('edit-metadata');
				},
				// Re-init focal point on input change
				change: function() {
					changeView(this);
				},
				// Update view on focal point js change
				update: function() {
					this.views.detach();
					this.model.fetch();
					this.views.render();
				}
			});
		}
	});
})(jQuery);
