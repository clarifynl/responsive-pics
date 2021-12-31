(function($) {
	$(document).ready(() => {
		/**
		 * Set variables
		 */
		let $image;
		let $imageFocal;
		let $imageFocalWrapper;
		let $imageFocalPoint;
		let $imageFocalClickarea;

		let imageDimensions = {
			width: 0,
			height: 0
		};

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
		 * Get Focal Point from meta fields
		 */
		const getFocalPoint = attachment => {
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
		};

		/**
		 * Calculate Focal Point by relative coordinates
		 */
		const calculateFocalPoint = position => {
			console.log(Number((position.left / imageDimensions.width) * 100).toFixed(2));

			return {
				x: Number((position.left / imageDimensions.width) * 100).toFixed(2),
				y: Number((position.top / imageDimensions.height) * 100).toFixed(2)
			};
		};

		/**
		 * Update Focal Point coordinates
		 */
		const setFocalPoint = (x, y) => {
			console.log('setFocalPoint', x, y);
			$imageFocalPoint.css({
				left: `${x}%`,
				top: `${y}%`,
				display: 'block'
			});
		};

		/**
		 * HTML5 Drag events
		 */
		const startDragFocalPoint = e => {
			$('body').addClass('focal-point-dragging');
			console.log($(e.currentTarget).position());
			// e.originalEvent.dataTransfer.setData('text', e.target.id);
			// e.originalEvent.dataTransfer.effectAllowed = 'move';
		};

		const endDragFocalPoint = e => {
			$('body').removeClass('focal-point-dragging');
		};

		const dragOverFocalPoint = e => {
			e.stopPropagation();
			e.preventDefault();
			e.originalEvent.dataTransfer.dropEffect = 'move';
		};

		const dropFocalPoint = e => {
			e.stopPropagation();
			e.preventDefault();

			// const dragId = e.originalEvent.dataTransfer.getData('text');
			// const dragEl = $(`#${dragId}`);
			// $imageFocalWrapper.prepend(dragEl);
			console.log(e.originalEvent.dataTransfer, $imageFocalPoint.position(), $imageFocalPoint.offset());
			const dragPosition = $imageFocalPoint.position();

			// const focalPoint = calculateFocalPoint(dragPosition);
			// setFocalPoint(focalPoint.x, focalPoint.y);
		};

		/**
		 * Update Focus Interface
		 */
		const updateFocusInterface = image => {
			imageDimensions = {
				width: image.width(),
				height: image.height()
			};

			$imageFocalWrapper.css({
				width: `${imageDimensions.width}px`,
				height: `${imageDimensions.height}px`
			});
		};

		/**
		 * Init Focus Interface
		 */
		const initFocusInterface = attachment => {
			// Set focal point
			const focalPoint = getFocalPoint(attachment);
			setFocalPoint(focalPoint.x, focalPoint.y);

			// Add image/window listeners
			$image.on('load', e => updateFocusInterface($(e.currentTarget)));
			$(window).on('resize', () => updateFocusInterface($image));

			// Drag'n drop events
			$imageFocalPoint.on('mousedown', startDragFocalPoint);
			// $imageFocalWrapper.on('dragover', dragOverFocalPoint);
			// $imageFocalWrapper.on('drop', dropFocalPoint);
			// $imageFocalPoint.on('dragstart', startDragFocalPoint);
			// $imageFocalPoint.on('dragend', endDragFocalPoint);
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
					const focalPoint = getFocalPoint(this.model);
					setFocalPoint(focalPoint.x, focalPoint.y);
				}
			}
		});
	});
})(jQuery);
