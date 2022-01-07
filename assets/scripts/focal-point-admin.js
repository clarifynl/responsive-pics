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
			$imageFocalSave.removeAttr('disabled');

			var pointYOffset = e.offsetY - Focal.point.height() / 2,
				pointXOffset = e.offsetX - Focal.point.width() / 2;

			// Convert absolute coordinates to percentages
			Focal.x = Number(pointXOffset / Focal.picker.width() * 100).toFixed(2);
			Focal.y = Number(pointYOffset / Focal.picker.height() * 100).toFixed(2);
			Focal.positionFocalPoint(Focal);
		},

		startDrag: e => {
			$('body').addClass('focal-point-dragging');
			$imageFocalSave.removeAttr('disabled');
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
		const initAttachmentDetails = (element, id) => {
			// Append focal point selector
			const selectView   = wp.media.template('attachment-details-focal-point');
			const selectParent = element.find('.thumbnail');
			const selectImage  = selectParent.find('img');

			if (selectView && selectParent.length && selectImage.length) {
				selectParent.prepend(selectView);
				// Set image focal elements
				$imageFocal          = element.find('.image-focal');
				$imageFocalWrapper   = element.find('.image-focal__wrapper');
				$imageFocalPoint     = element.find('.image-focal__point');
				$imageFocalClickarea = element.find('.image-focal__clickarea');
				selectImage.prependTo($imageFocalWrapper);
				$image               = $imageFocalWrapper.find('img');
			}

			// Append focal point save button
			const saveView   = wp.media.template('attachment-save-focal-point');
			const saveParent = element.find('.attachment-actions');
			if (saveView) {
				saveParent.append(saveView);
				$imageFocalSave = element.find('button.save-attachment-focal-point');
			}
		};

		const initImgEdit = (element, id) => {
			// Append focal point selector
			const selectView   = wp.media.template('edit-image-focal-point');
			const selectParent = element.find(`#imgedit-crop-${id}`);

			if (selectView && selectParent.length) {
				selectParent.append(selectView);
				// Set image focal elements
				$imageFocalWrapper   = selectParent;
				$imageFocalPoint     = element.find('.image-focal__point');
				$imageFocalClickarea = element.find('.image-focal__clickarea');
				$image               = $imageFocalWrapper.find('img');
			}

			// Append focal point save button
			const saveView   = wp.media.template('attachment-save-focal-point');
			const saveParent = element.find('.imgedit-submit');
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
				$imageFocalSave.attr('disabled', 'disabled');
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
			console.log('initFocusInterface', attachment, focalPoint, $image);

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
		 * Extended view changed
		 */
		const changeView = view => {
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
				},
				// Init extended template
				render: function() {
					wp.media.view.Attachment.prototype.render.apply(this, arguments);
					const id   = this.model.get('id');
					const type = this.model.get('type');

					if (type === 'image') {
						initAttachmentDetails(this.$el, id);
						initFocusInterface(this.model);
					}
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

		/**
		 * Extend EditImage view
		 */
		let EditImageView = wp.media.view.EditImage.Details;
		if (EditImageView) {
			wp.media.view.EditImage.Details = EditImageView.extend({
				// Add focalPoint change listener
				initialize: function(options) {
					_view = this;
					this.frame  = options.frame;
					wp.media.view.EditImage.prototype.initialize.apply(this, arguments);
					this.model.on('change:focalPoint', this.change, this);
				},
				// Editor loaded
				loadEditor: function() {
					wp.media.view.EditImage.prototype.loadEditor.apply(this, arguments);
					$(document).one('image-editor-ui-ready', this.imageLoaded);
				},
				// Editor image loaded
				imageLoaded: function() {
					$(document).off('image-editor-ui-ready', this.imageLoaded);
					const id   = _view.model.get('id');
					const type = _view.model.get('type');

					if (type === 'image') {
						initImgEdit(_view.$el, id);
						initFocusInterface(_view.model);
					}
				},
				// Cancel button
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