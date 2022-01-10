import FocalPointPicker from './modules/focal-point-picker';

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
			_view.model.set('focalPoint', position);
			// saveFocalPoint(_view.model);
		},

		setFocalPoint: e => {
			const pointYOffset = e.offsetY - Focal.point.height() / 2;
			const pointXOffset = e.offsetX - Focal.point.width() / 2;

			// Convert absolute coordinates to percentages
			Focal.position.x = Number(pointXOffset / Focal.picker.width() * 100).toFixed(2);
			Focal.position.y = Number(pointYOffset / Focal.picker.height() * 100).toFixed(2);

			Focal.positionFocalPoint(Focal.position);
		},

		startDrag: e => {
			$('body').addClass('focal-point-dragging');
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
		 * Attachment Details
		 */
		const initAttachmentDetails = (element, id) => {
			// Append focal point selector
			const selectView   = wp.media.template('attachment-details-focal-point');
			const selectParent = element.find('.thumbnail');
			const selectImage  = selectParent.find('img');

			// Set image focal elements
			if (selectView && selectParent.length && selectImage.length) {
				selectParent.prepend(selectView);
				$imageFocal          = element.find('.image-focal');
				$imageFocalWrapper   = element.find('.image-focal__wrapper');
				$imageFocalPoint     = element.find('.image-focal__point');
				$imageFocalClickarea = element.find('.image-focal__clickarea');
				selectImage.prependTo($imageFocalWrapper);
				$image               = $imageFocalWrapper.find('img');
			}
		};

		/**
		 * Save Focal Point
		 */
		const saveFocalPoint = attachment => {
			$.ajax({
				url: wp?.ajax?.settings?.url,
				method: 'POST',
				data: {
					action: 'save_focal_point',
					attachment: attachment?.attributes
				}
			})
			.done(data => {
				// Update view on succesfull save
				_view.update();
			})
			.fail((jqXHR, textStatus) => {
				console.log('save focal point error', jqXHR);
			})
			.always(() => {
				console.log(_view.controller);
				_view.controller.setState('edit-image');
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
			FocalPointPicker.init(focalPoint);

			// Layout change
			$(window).on('resize', updateFocusInterface);
			$image.on('load', e => {
				updateFocusInterface();
				FocalPointPicker.init(focalPoint);
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

		const Attachment = wp.media.view.Attachment;
		const AttachmentDetails = wp.media.view.Attachment.Details;
		const TwoColumnView = wp.media.view.Attachment.Details.TwoColumn;

		/**
		 * Extend Attachment Details TwoColumn view (Media Library Modal)
		 */
		if (TwoColumnView) {
			wp.media.view.Attachment.Details.TwoColumn = TwoColumnView.extend({
				// Add focalPoint change listener
				initialize: function() {
					_view = this;
					this.model.on('change:focalPoint', this.change, this);
				},
				// Init extended template
				render: function() {
					Attachment.prototype.render.apply(this, arguments);
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
		 * Extend Attachment Details view (Post Edit Modal)
		 */
		if (AttachmentDetails) {
			wp.media.view.Attachment.Details = AttachmentDetails.extend({
				// Add focalPoint change listener
				initialize: function() {
					_view = this;
					Attachment.prototype.initialize.apply(this, arguments);
					this.model.on('change:focalPoint', this.change, this);
				},
				// Init extended template
				render: function() {
					Attachment.prototype.render.apply(this, arguments);
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
					console.log('AttachmentDetails update', this.model);
					this.views.detach();
					this.model.fetch();
					this.views.render();
				},
				// On update image
				updateAll: function() {
					console.log('AttachmentDetails updateAll', this.model);
				}
			});
		}
	});
})(jQuery);
