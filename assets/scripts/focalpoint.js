(function($) {
	/**
	 Global variables
	**/
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
			Focal.x = focalPoint.x;
			Focal.y = focalPoint.y;
			Focal.positionFocalPoint(focalPoint);
			Focal.setEventListeners();
		},

		/**
		 Event Listeners
		**/
		setEventListeners: () => {
			Focal.picker.on('click', Focal.setFocalPoint);
			Focal.point.draggable({
				cursor: 'move',
				start: Focal.startDrag,
				drag: Focal.dragging,
				stop: Focal.stopDrag,
				containment: Focal.wrapper
			});
		},

		positionFocalPoint: position => {
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
		 * Save Focal Point
		 */
		const saveFocalPoint = attachment => {
			const {id} = attachment;
			const compat = attachment.get('compat');

			if (compat.item) {
				console.log(
					$(compat.item).find('.compat-field-responsive_pics_focal_point_x input'),
					$(compat.item).find('#attachments-766-responsive_pics_focal_point_x'),
					$(compat.item).find(`input[name="attachments[${id}][responsive_pics_focal_point_x]"]`)
				);

				$(compat.item).find(`input[name="attachments[${id}][responsive_pics_focal_point_x]"]`).val(`${Focal.x}`);
				$(compat.item).find(`input[name="attachments[${id}][responsive_pics_focal_point_y]"]`).val(`${Focal.y}`);

				console.log('saveFocalPoint', Focal.x, Focal.y);
			}
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
			const focalPoint = getFocalPoint(attachment);

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
		 * Extend Attachment view
		 */
		var TwoColumn = wp.media.view.Attachment.Details.TwoColumn;
		wp.media.view.Attachment.Details.TwoColumn = TwoColumn.extend({
			// Always make sure that our content is up to date.
			initialize: function() {
				this.model.on('change:compat', this.change, this);
			},
			// Init focal point for images
			render: function() {
				wp.media.view.Attachment.prototype.render.apply(this, arguments);
				const { type } = this.model.attributes;
				if (type === 'image') {
					initTemplates(this.$el);
					initFocusInterface(this.model);
				}

				return this;
			},
			// Re-init focal point for images
			change: function() {
				const { type } = this.model.attributes;
				if (type === 'image') {
					focalPoint = getFocalPoint(this.model);
					Focal.x = focalPoint.x;
					Focal.y = focalPoint.y;
					Focal.positionFocalPoint(focalPoint);
				}
			}
		});
	});
})(jQuery);
