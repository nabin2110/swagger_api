<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calender Js</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    </head>
</head>
<body>
    <div class="container">
        <div class="card mt-5">
            <h3 class="card-header p-3">Laravel 11 FullCalender Tutorial Example - ItSolutionStuff.com</h3>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        var pusher = new Pusher('4516ffb03a4b682d9de4', {
          cluster: 'ap2'
        });

        var channel = pusher.subscribe('meeting-name');
channel.bind('meeting-event', function(data) {
    var event = data; // Assuming broadcasted event is the first element
    switch (event.type) {
        case 'add':
            $('#calendar').fullCalendar('renderEvent', {
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                allDay: false
            }, true);
            break;
        case 'update':
            var existingEvent = $('#calendar').fullCalendar('clientEvents', event.id);
            if (existingEvent.length > 0) {
                existingEvent[0].title = event.title;
                existingEvent[0].start = event.start;
                existingEvent[0].end = event.end;
                $('#calendar').fullCalendar('updateEvent', existingEvent[0]);
            }
            break;
        case 'delete':
            $('#calendar').fullCalendar('removeEvents', event.id);
            break;
    }
});

      </script>
    <script>
        $(function(){
            var calendar = $('#calendar').fullCalendar({
                    editable: true,
                    events: "/calender/create",
                    displayEventTime: true,
                    editable: true,
                    eventRender: function (event, element, view) {
                        if (event.allDay === 'true') {
                                event.allDay = true;
                        } else {
                                event.allDay = false;
                        }
                    },
                    selectable: true,
                    selectHelper: true,
                    select: function (start, end, allDay) {
                        var title = prompt('Event Title:');
                        if (title) {
                            var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
                            var end = $.fullCalendar.formatDate(end, "Y-MM-DD");
                            $.ajax({
                                url: "{{ route('calendar.store') }}",
                                data: {
                                    title: title,
                                    start: start,
                                    end: end,
                                    type: 'add',
                                    '_token':"{{ csrf_token() }}"
                                },
                                type: "POST",
                                success: function (data) {
                                    displayMessage("Event Created Successfully");
                                    calendar.fullCalendar('renderEvent',
                                        {
                                            id: data.id,
                                            title: title,
                                            start: start,
                                            end: end,
                                            allDay: allDay
                                        },true);

                                    calendar.fullCalendar('unselect');
                                }
                            });
                        }
                    },
                    eventDrop: function (event, delta) {
                        var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                        var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

                        $.ajax({
                            url: "{{ route('calendar.store') }}",
                            data: {
                                title: event.title,
                                start: start,
                                end: end,
                                id: event.id,
                                type: 'update',
                                '_token':"{{ csrf_token() }}"
                            },
                            type: "POST",
                            success: function (response) {
                                displayMessage("Event Updated Successfully");
                            }
                        });
                    },
                    eventClick: function (event) {
                        var deleteMsg = confirm("Do you really want to delete?");
                        if (deleteMsg) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('calendar.store') }}",
                                data: {
                                        id: event.id,
                                        type: 'delete',
                                        '_token':"{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    calendar.fullCalendar('removeEvents', event.id);
                                    displayMessage("Event Deleted Successfully");
                                }
                            });
                        }
                    }

                });
        })
        function displayMessage(message) {
        toastr.success(message, 'Event');
    }
    </script>
</body>
</html>
