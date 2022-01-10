const FocalPointPicker = {
	/**
	 Set variables
	**/
	init: view => {
		const focalPoint = view.model.get('focalPoint');
		console.log('FocalPointPicker init', focalPoint);

		FocalPointPicker.wrapper  = $imageFocalWrapper;
		FocalPointPicker.picker   = $imageFocalClickarea;
		FocalPointPicker.point    = $imageFocalPoint;
		FocalPointPicker.position = focalPoint;
		FocalPointPicker.positionFocalPoint(focalPoint);
		FocalPointPicker.setEventListeners();
	},

	/**
	 Event Listeners
	**/
	setEventListeners: () => {
		FocalPointPicker.picker.on('click', FocalPointPicker.setFocalPoint);

		// Check if jQuery UI Draggable widget is active
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

	positionFocalPoint: position => {
		FocalPointPicker.point.css({
			left: `${position.x}%`,
			top: `${position.y}%`,
			position: 'absolute'
		});
		_view.model.set('focalPoint', position);
		// saveFocalPoint(_view.model);
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