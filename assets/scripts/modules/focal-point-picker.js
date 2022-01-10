/**
 * Focal Point Picker module
 */
const FocalPointPicker = {
	init: view => {
		// Set properties
		FocalPointPicker.view     = view;
		FocalPointPicker.position = view.model.get('focalPoint');
		FocalPointPicker.wrapper  = view.$el.find('.image-focal__wrapper');
		FocalPointPicker.point    = view.$el.find('.image-focal__point');
		FocalPointPicker.picker   = view.$el.find('.image-focal__clickarea');
		FocalPointPicker.image    = FocalPointPicker.wrapper.find('img');
		// Add event listners
		FocalPointPicker.setEventListeners();
		console.log(view.model);
		FocalPointPicker.positionFocalPoint(FocalPointPicker.position);
	},
	setEventListeners: () => {
		// On layout change
		jQuery(window).on('resize', FocalPointPicker.updateInterface);
		FocalPointPicker.image.on('load', e => {
			FocalPointPicker.updateInterface();
		});

		// On click
		FocalPointPicker.picker.on('click', FocalPointPicker.setFocalPoint);

		// On drag
		if (typeof(jQuery.ui.draggable) === 'function') {
			FocalPointPicker.point.draggable({
				cursor: 'move',
				start: FocalPointPicker.startDrag,
				drag: FocalPointPicker.dragging,
				stop: FocalPointPicker.stopDrag,
				containment: FocalPointPicker.wrapper
			});
		}
	},
	updateInterface: () => {
		FocalPointPicker.wrapper.css({
			width: `${FocalPointPicker.image.width()}px`
		});
	},
	positionFocalPoint: position => {
		FocalPointPicker.view.model.set('focalPoint', position);
		FocalPointPicker.point.css({
			left: `${position?.x}%`,
			top: `${position?.y}%`,
			position: 'absolute'
		});
	},
	setFocalPoint: e => {
		const pointYOffset = e.offsetY - FocalPointPicker.point.height() / 2;
		const pointXOffset = e.offsetX - FocalPointPicker.point.width() / 2;

		// Convert absolute coordinates to percentages
		FocalPointPicker.position.x = Math.round(pointXOffset / FocalPointPicker.picker.width() * 100);
		FocalPointPicker.position.y = Math.round(pointYOffset / FocalPointPicker.picker.height() * 100);

		FocalPointPicker.positionFocalPoint(FocalPointPicker.position);
		FocalPointPicker.saveFocalPoint(FocalPointPicker.view.model);
	},
	saveFocalPoint: attachment => {
		let request;
		if (request) {
			request.abort();
		}

		request = jQuery.ajax({
			url: wp?.ajax?.settings?.url,
			method: 'POST',
			data: {
				action: 'save_focal_point',
				attachment: attachment?.attributes
			},
			beforeSend: () => {
				FocalPointPicker.view.updateSave('waiting');
			}
		})
		.done(data => {
			FocalPointPicker.view.update();
		})
		.fail((jqXHR, textStatus) => {
			console.error('ResponsivePics error while saving focal point', jqXHR.statusText);
		})
		.always(() => {
			FocalPointPicker.view.updateSave('ready');
			request = null;
		});
	},
	startDrag: e => {
		jQuery('body').addClass('focal-point-dragging');
	},
	dragging: e => {
		FocalPointPicker.position.x = Math.round(e.target.offsetLeft / FocalPointPicker.picker.width() * 100);
		FocalPointPicker.position.y = Math.round(e.target.offsetTop / FocalPointPicker.picker.height() * 100);
	},
	stopDrag: e => {
		jQuery('body').removeClass('focal-point-dragging');
		FocalPointPicker.positionFocalPoint(FocalPointPicker.position);
		FocalPointPicker.saveFocalPoint(FocalPointPicker.view.model);
	}
};

export default FocalPointPicker;