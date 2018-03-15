$(
    function () {
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        $('#calendar-holder').fullCalendar(
            {
                header: {
                    left: 'prev, next',
                    center: 'title',
                    right: 'month, agendaWeek, agendaDay,'
                },
                lazyFetching: true,
                timeFormat: 'H:mm', // uppercase H for 24-hour clock
                views: {
                    basic: {
                        // options apply to basicWeek and basicDay views
                    },
                    agenda: {
                        // options apply to agendaWeek and agendaDay views
                        timeFormat: 'H:mm', // uppercase H for 24-hour clock
                    },
                    week: {
                        // options apply to basicWeek and agendaWeek views
                    },
                    day: {
                        // options apply to basicDay and agendaDay views
                    }
                },
                eventSources: [
                {
                    url: Routing.generate('fullcalendar_loader'),
                    type: 'POST',
                    // A way to add custom filters to your event listeners
                    data: {
                        'filter': 1
                    },
                    error: function () {
                        // alert('There was an error while fetching Google Calendar!');
                    }
                }
                ]
            }
        );
    }
);
