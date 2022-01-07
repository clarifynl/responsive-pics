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
			Focal.wrapper  = $imageFocalWrapper;
			Focal.picker   = $imageFocalClickarea;
			Focal.point    = $imageFocalPoint;
			Focal.position = focalPoint;
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
			Focal.point.css({
				left: `${position.x}%`,
				top: `${position.y}%`,
				position: 'absolute'
			});
		},

		setFocalPoint: e => {
			$imageFocalSave.removeAttr('disabled');

			const pointYOffset = e.offsetY - Focal.point.height() / 2;
			const pointXOffset = e.offsetX - Focal.point.width() / 2;

			// Convert absolute coordinates to percentages
			Focal.position.x = Number(pointXOffset / Focal.picker.width() * 100).toFixed(2);
			Focal.position.y = Number(pointYOffset / Focal.picker.height() * 100).toFixed(2);

			Focal.positionFocalPoint(Focal.position);
		},

		startDrag: e => {
			$('body').addClass('focal-point-dragging');
			$imageFocalSave.removeAttr('disabled');
		},

		dragging: e => {
			Focal.position.x = Number(e.target.offsetLeft / Focal.picker.width() * 100).toFixed(2);
			Focal.position.y = Number(e.target.offsetTop / Focal.picker.height() * 100).toFixed(2);
		},

		stopDrag: e => {
			$('body').removeClass('focal-point-dragging');
			Focal.positionFocalPoint(Focal.position);
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
			const selectView   = wp.media.template('image-edit-focal-point');
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
				x: Focal.position.x,
				y: Focal.position.y
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
			Focal.init(focalPoint);

			// Layout change
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
				Focal.position = focalPoint;
				Focal.positionFocalPoint(focalPoint);
			}
		};

		/**
		 * Extend Attachment Details TwoColumn view
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

		let EditAttachments = wp.media.view.MediaFrame.EditAttachments;
		let EditImageDetailsView = wp.media.view.EditImage.Details;

		/**
		 * Extend MediaFrame ImageDetails view
		 */
		if (EditAttachments) {
			wp.media.view.MediaFrame.EditAttachments = EditAttachments.extend({
				initialize: function(options) {
					console.log('EditAttachments initialize');
					wp.media.view.Frame.prototype.initialize.apply(this, arguments);
				},
				editImageMode: function(contentRegion) {
					console.log('EditAttachments editImageMode');
				},
				editImageModeRender: function( view ) {
					console.log('EditAttachments editImageModeRender');
				}
			});
		}

		/**
		 * Extend EditImage Details view
		 */
		if (EditImageDetailsView) {
			wp.media.view.EditImage.Details = EditImageDetailsView.extend({
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
