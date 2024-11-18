<!DOCTYPE html>
<html lang="en">
<head>
    <title>Calendar JS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
    <div class="card mt-5">
        <div class="card-body">
            <div id='calendar'></div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="eventForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="eventTitle">Event Title</label>
                        <input type="text" class="form-control" id="eventTitle" placeholder="Enter event title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="startTime">Start Time</label>
                        <input type="datetime-local" class="form-control" id="startTime" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="endTime">End Time</label>
                        <input type="datetime-local" class="form-control" id="endTime" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
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
    $(function () {
        var calendar = $('#calendar').fullCalendar({
            editable: true,
            events: "/calendar/create",
            displayEventTime: true,
            defaultView: 'agendaDay',
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'today,dayGridMonth,agendaWeek,agendaDay,month'
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end) {
                // Show the modal
                $('#startTime').val(moment(start).format('YYYY-MM-DDTHH:mm'));
                $('#endTime').val(moment(end).format('YYYY-MM-DDTHH:mm'));
                $('#eventModal').modal('show');
            },
            eventDrop: function (event) {
                var start = moment(event.start).format("YYYY-MM-DDTHH:mm");
                var end = moment(event.end).format("YYYY-MM-DDTHH:mm");
                console.log(start);
                $.ajax({
                    url: "{{ route('calendar.store') }}",
                    type:"post",
                    data: {
                        title: event.title,
                        start: start,
                        end: end,
                        id: event.id,
                        type: 'update',
                        '_token': "{{ csrf_token() }}"
                    },
                    type: "POST",
                    success: function () {
                        displayMessage("Event Updated Successfully");
                    }
                });
            },
            eventClick: function (event) {
                if (confirm("Do you really want to delete?")) {
                    $.ajax({
                        url: "{{ route('calendar.store') }}",
                        data: {
                            id: event.id,
                            type: 'delete',
                            '_token': "{{ csrf_token() }}"
                        },
                        type: "POST",
                        success: function () {
                            calendar.fullCalendar('removeEvents', event.id);
                            displayMessage("Event Deleted Successfully");
                        }
                    });
                }
            }
        });

        // Modal Form Submission
        $('#eventForm').on('submit', function (e) {
            e.preventDefault();
            var title = $('#eventTitle').val();
            var start = $('#startTime').val();
            var end = $('#endTime').val();

            if (title && start && end) {
                $.ajax({
                    url: "{{ route('calendar.store') }}",
                    data: {
                        title: title,
                        start: start,
                        end: end,
                        type: 'add',
                        '_token': "{{ csrf_token() }}"
                    },
                    type: "POST",
                    success: function (data) {
                        $('#eventModal').modal('hide');
                        displayMessage("Event Created Successfully");
                        calendar.fullCalendar('renderEvent', {
                            id: data.id,
                            title: title,
                            start: start,
                            end: end,
                            allDay: false
                        }, true);
                    }
                });
            }
        });
    });

    function displayMessage(message) {
        toastr.success(message, 'Event');
    }
</script>
</body>
</html>
