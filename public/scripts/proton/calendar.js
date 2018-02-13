$(document).ready(function() {
	!verboseBuild || console.log('-- starting proton.calendar build');
    
    proton.calendar.build();
});

proton.calendar = {
	build: function () {
		// Initiate calendar events
		proton.calendar.events();
		proton.calendar.makeCalendar();
		proton.calendar.bindDragEvent();

		!verboseBuild || console.log('            proton.calendar build DONE');

	},
	events: function () {
		!verboseBuild || console.log('            proton.calendar binding events');

	},
	makeCalendar: function (template) {
		!verboseBuild || console.log('            proton.calendar.makeCalendar()');
		
		// fullcalendar
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();

		$('.calendar').each(function() {
			$(this).fullCalendar({
				header: {
					left: 'prev,next',
					center: 'title',
					right: 'today,month,agendaWeek,agendaDay'
				},
				editable: true,
				droppable: true, 
				drop: function(date, allDay) { // this function is called when something is dropped
					
						// retrieve the dropped element's stored Event Object
						var originalEventObject = $(this).data('eventObject');
						
						// we need to copy it, so that multiple events don't have a reference to the same object
						var copiedEventObject = $.extend({}, originalEventObject);
						
						// assign it the date that was reported
						copiedEventObject.start = date;
						copiedEventObject.allDay = allDay;
						
						// render the event on the calendar
						// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
						$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
						
						// is the "remove after drop" checkbox checked?
						if ($('#drop-remove').is(':checked')) {
							// if so, remove the element from the "Draggable Events" list
							$(this).remove();
						}
						
					}
				,
				events: [
					{
						title: 'All Day Event',
						start: new Date(y, m, 1)
					},
					{
						title: 'Long Event',
						start: new Date(y, m, d-5),
						end: new Date(y, m, d-2),
						className:'bg-primary'
					},
					{
						id: 999,
						title: 'Repeating Event',
						start: new Date(y, m, d-3, 16, 0),
						allDay: false
					},
					{
						id: 999,
						title: 'Repeating Event',
						start: new Date(y, m, d+4, 16, 0),
						allDay: false
					},
					{
						title: 'Meeting',
						start: new Date(y, m, d, 10, 30),
						allDay: false
					},
					{
						title: 'Lunch',
						start: new Date(y, m, d, 12, 0),
						end: new Date(y, m, d, 14, 0),
						allDay: false
					},
					{
						title: 'Birthday Party',
						start: new Date(y, m, d+1, 19, 0),
						end: new Date(y, m, d+1, 22, 30),
						allDay: false
					},
					{
						title: 'Click for Google',
						start: new Date(y, m, 28),
						end: new Date(y, m, 29),
						url: 'http://google.com/'
					}
				]
			});
		});
		$('.calendar .fc-button').addClass('btn').addClass('btn-info').addClass('btn-xs');
	},
	addDragEvent: function($this){
		// Documentation here:
		// http://arshaw.com/fullcalendar/docs/event_data/Event_Object/
		var eventObject = {
			title: $.trim($this.text()), // use the element's text as the event title
		};
		
		$this.data('eventObject', eventObject);
		
		$this.draggable({
			zIndex: 999,
			revert: true,
			revertDuration: 0  
		});
	},
	bindDragEvent: function () {
		$('.custom-events .bootstrap-tagsinput span').each(function() {
			proton.calendar.addDragEvent($(this));
		});
		$('.custom-events .bootstrap-tagsinput input').on('keyup', function(event, element) {
			// console.log('change: ' + element.val());
		});
	}
}