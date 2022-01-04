(function($) {
	/**
	 Global variables
	**/
	// let _self;
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
				url: wp.ajax.settings.url,
				method: 'POST',
				data: {
					action: 'save_focal_point',
					attachment: attachment.attributes
				}
			})
			.done(data => {
				attachment.update();
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
		 * Extend Attachment view
		 */
		const TwoColumn = wp.media.view.Attachment.Details.TwoColumn;
		wp.media.view.Attachment.Details.TwoColumn = TwoColumn.extend({
			// Listen to focalPoint change
			initialize: function() {
				// _self = this;
				this.model.on('change:focalPoint', this.change, this);
			},
			// Init focal point for images
			render: function() {
				wp.media.view.Attachment.prototype.render.apply(this, arguments);
				const type = this.model.get('type');

				if (type === 'image') {
					initTemplates(this.$el);
					initFocusInterface(this.model);
				}

				return this;
			},
			// Re-init focal point for images
			change: function() {
				const type       = this.model.get('type');
				const focalPoint = this.model.get('focalPoint');

				if (type === 'image') {
					Focal.positionFocalPoint(focalPoint);
				}

				return this;
			},
			update: function() {
				console.log('update views');
				this.views.detach();
				this.model.fetch();
				this.views.render();

				return this;
			}
		});
	});
})(jQuery);
