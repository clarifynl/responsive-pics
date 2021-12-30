(function($) {
	$(document).ready(() => {
		let $image;
		let $imageFocal;
		let $imageFocalWrapper;
		let $imageFocalPoint;
		let $imageFocalClickarea;

		const startDragFocalPoint = e => {
			$imageFocal.addClass('is-dragging');
		};

		const draggingFocalPoint = e => {
			console.log('draggingFocalPoint', e.target);
		};

		const endDragFocalPoint = e => {
			$imageFocal.removeClass('is-dragging');
		};

		const dragOverFocalPoint = e => {
			e.stopPropagation();
			e.preventDefault();
		};

		const dropFocalPoint = e => {
			e.stopPropagation();
			e.preventDefault();
			console.log('dropFocalPoint', $imageFocalPoint.position());

			// $imageFocalPoint.css({
			// 	left: `${x}%`,
			// 	top: `${y}%`,
			// 	display: 'block'
			// });
		};

		/*
		 * Init Focus Interface
		 */
		const initFocusInterface = (x, y) => {
			console.log($image, $image.width(), $image.height());

			$imageFocalWrapper.css({
				width: $image.width(),
				height: $image.height()
			});

			$imageFocalPoint.css({
				left: `${x}%`,
				top: `${y}%`,
				display: 'block'
			});

			// Drag'n drop events
			$imageFocalWrapper.on('dragover', dragOverFocalPoint);
			$imageFocalWrapper.on('drop', dropFocalPoint);
			$imageFocalPoint.on('dragstart', startDragFocalPoint);
			$imageFocalPoint.on('drag', draggingFocalPoint);
			$imageFocalPoint.on('dragend', endDragFocalPoint);
		};

		/*
		 * Init templates
		 */
		const initTemplates = element => {
			// Append focal point selector
			var selectView   = wp.media.template('attachment-select-focal-point');
			var selectParent = element.find('.thumbnail');
			var selectImage  = element.find('.details-image');

			console.log(selectImage.width(), selectImage.height());
			selectImage.on('load', e => {
				console.log(e, e.width(), e.height(), $(e.currentTarget).width(), $(e.currentTarget).height());
			});

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

		/*
		 * Init Focal Point
		 */
		const initFocalPoint = attachment => {
			const compat = attachment.get('compat');

			if (compat.item) {
				const focalPointX = $(compat.item).find('.compat-field-responsive_pics_focal_point_x input').val();
				const focalPointY = $(compat.item).find('.compat-field-responsive_pics_focal_point_y input').val();

				initFocusInterface(focalPointX, focalPointY);
			}
		};

		/*
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
					initFocalPoint(this.model);
				}

				return this;
			},
			change: function() {
				// Re-init focal point for images
				const { type } = this.model.attributes;
				if (type === 'image') {
					initFocalPoint(this.model);
				}
			}
		});
	});
})(jQuery);
