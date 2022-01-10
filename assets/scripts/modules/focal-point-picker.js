/**
 Focal Point Picker module
**/
const FocalPointPicker = {
	init: view => {
		const focalPoint = view.model.get('focalPoint');

		FocalPointPicker.view     = view;
		FocalPointPicker.wrapper  = view.$el.find('.image-focal__wrapper');
		FocalPointPicker.point    = view.$el.find('.image-focal__point');
		FocalPointPicker.picker   = view.$el.find('.image-focal__clickarea');
		FocalPointPicker.image    = FocalPointPicker.wrapper.find('img');
		FocalPointPicker.position = focalPoint;

		FocalPointPicker.positionFocalPoint(focalPoint);
		FocalPointPicker.setEventListeners();
	},
	setEventListeners: () => {
		// On layout change
		$(window).on('resize', FocalPointPicker.updateInterface);
		$image.on('load', e => {
			FocalPointPicker.updateInterface();
		});

		// On click
		FocalPointPicker.picker.on('click', FocalPointPicker.setFocalPoint);

		// On drag
		if (typeof($.ui.draggable) === 'function') {
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
		console.log('updateInterface', FocalPointPicker.image);
		FocalPointPicker.wrapper.css({
			width: `${FocalPointPicker.image.width()}px`
		});
	},
	positionFocalPoint: position => {
		FocalPointPicker.point.css({
			left: `${position.x}%`,
			top: `${position.y}%`,
			position: 'absolute'
		});
		FocalPointPicker.view.model.set('focalPoint', position);
		// saveFocalPoint(FocalPointPicker.view.model);
	},
	setFocalPoint: e => {
		const pointYOffset = e.offsetY - FocalPointPicker.point.height() / 2;
		const pointXOffset = e.offsetX - FocalPointPicker.point.width() / 2;

		// Convert absolute coordinates to percentages
		FocalPointPicker.position.x = Number(pointXOffset / FocalPointPicker.picker.width() * 100).toFixed(2);
		FocalPointPicker.position.y = Number(pointYOffset / FocalPointPicker.picker.height() * 100).toFixed(2);

		FocalPointPicker.positionFocalPoint(FocalPointPicker.position);
	},
	startDrag: e => {
		$('body').addClass('focal-point-dragging');
	},
	dragging: e => {
		FocalPointPicker.position.x = Number(e.target.offsetLeft / FocalPointPicker.picker.width() * 100).toFixed(2);
		FocalPointPicker.position.y = Number(e.target.offsetTop / FocalPointPicker.picker.height() * 100).toFixed(2);
	},
	stopDrag: e => {
		$('body').removeClass('focal-point-dragging');
		FocalPointPicker.positionFocalPoint(FocalPointPicker.position);
	}
};

export default FocalPointPicker;