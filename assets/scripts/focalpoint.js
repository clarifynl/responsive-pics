(function($) {
	/**
	 Small plugin to set the focal point of an image
	**/
	const Focal = {
		/**
		 Set variables
		**/
		init: attachment => {
			const focalPoint = Focal.getFocalPoint(attachment);

			Focal.picker = $image;
			Focal.point  = $imageFocalPoint;
			Focal.x = focalPoint.x;
			Focal.y = focalPoint.y;
			Focal.setEventListeners();
		},

		/**
		 Event Listeners
		**/
		setEventListeners: () => {
			Focal.picker.on('click', this.setFocalPoint);
			Focal.point.draggable({
				cursor: 'move',
				drag: this.dragging,
				containment: $imageFocalWrapper
			});
		},

		/**
		 * Get Focal Point from meta fields
		 */
		getFocalPoint: attachment => {
			const compat = attachment.get('compat');

			if (compat.item) {
				const focalPointX = $(compat.item).find('.compat-field-responsive_pics_focal_point_x input').val();
				const focalPointY = $(compat.item).find('.compat-field-responsive_pics_focal_point_y input').val();

				return {
					x: focalPointX,
					y: focalPointY
				};
			}

			return;
		},

		/**
		 Move the focal point
		**/
		setFocalPoint: e => {
			console.log('setFocalPoint', e);
			var pointYOffset = e.offsetY - Focal.point.height() / 2,
				pointXOffset = e.offsetX - Focal.point.width() / 2;

			Focal.point.css({
				top: pointYOffset,
				left: pointXOffset,
				display: 'block'
			});

			Focal.x = Math.round((e.pageY - $(this).offset().top) / Focal.picker.height() * 100);
			Focal.y = Math.round((e.pageX - $(this).offset().left) / Focal.picker.width() * 100);
		},


		/**
		 Move focal point and background position when dragging point
		**/
		dragging: e => {
			console.log('dragging', e);
			Focal.x = Math.round(e.target.offsetLeft / Focal.picker.width() * 100);
			Focal.y = Math.round(e.target.offsetTop / Focal.picker.height() * 100);
		},
	};

	$(document).ready(() => {
		/**
		 * Init templates
		 */
		const initTemplates = element => {
			// Append focal point selector
			var selectView   = wp.media.template('attachment-select-focal-point');
			var selectParent = element.find('.thumbnail');
			var selectImage  = element.find('.details-image');

			if (selectView) {
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
			var saveView   = wp.media.template('attachment-save-focal-point');
			var saveParent = element.find('.attachment-actions');
			if (saveView) {
				saveParent.append(saveView);
			}
		};

		/**
		 * Init Focus Interface
		 */
		const initFocusInterface = attachment => {
			// Add image/window listeners
			$image.on('load', e => Focal.init(attachment));
			// $(window).on('resize', () => updateFocusInterface($image));
		};

		/**
		 * Extend Attachment view
		 */
		var TwoColumn = wp.media.view.Attachment.Details.TwoColumn;
		wp.media.view.Attachment.Details.TwoColumn = TwoColumn.extend({
			initialize: function() {
				// Always make sure that our content is up to date.
				this.model.on('change:compat', this.change, this);
			},
			render: function() {
				// Ensure that the main view is rendered.
				wp.media.view.Attachment.prototype.render.apply(this, arguments);
				// Init focal point for images
				const { type } = this.model.attributes;
				if (type === 'image') {
					initTemplates(this.$el);
					initFocusInterface(this.model);
				}

				return this;
			},
			change: function() {
				// Re-init focal point for images
				const { type } = this.model.attributes;
				if (type === 'image') {
					focalPoint = Focal.getFocalPoint(this.model);
					Focal.x = focalPoint.x;
					Focal.y = focalPoint.y;
				}
			}
		});
	});
})(jQuery);
