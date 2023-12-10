<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
    <link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">
    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <div class="chat">

        <div class="top">
            <div>
                @if (auth()->check() && auth()->user()->id != $doctor->id)
                    <p>{{ $doctor->name }}</p>
                    <small>{{ $specialist->specialist }}</small>
                @else
                    <p>{{ $patient->name }}</p>
                    <small>Patient</small>
                @endif
            </div>
        </div>

        <div class="messages">
            @foreach($messages as $message)
                @if ($message->user_id != auth()->user()->id)
                    @include('receive', ['message' => $message->content])
                @else
                    @include('broadcast', ['message' => $message->content])
                @endif
            @endforeach
        </div>

        <div class="bottom">
            <form action="">
                <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>

    </div>

    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
    
        var pusher = new Pusher(
            '{{ config('broadcasting.connections.pusher.key') }}', 
            { cluster: 'ap1' }
        );
    
        var channel = pusher.subscribe('consultation-{{ $consultation->id }}');

        //Receive messages
        channel.bind("chat", function (data) {
            $.post("{{ route('receive', $consultation->id) }}", {
                _token:  '{{csrf_token()}}',
                message: data.message,
            })
            .done(function (res) {
                $(".messages > .message").last().after(res);
                $(document).scrollTop($(document).height());
            });
        });

        //Broadcast messages
        $("form").submit(function (event) {
            event.preventDefault();

            $.ajax({
                url:     "{{ route('broadcast', $consultation->id) }}",
                method:  'POST',
                headers: {
                    'X-Socket-Id': pusher.connection.socket_id
                },
                data:    {
                    _token:  '{{csrf_token()}}',
                    message: $("form #message").val(),
                }
            })
            .done(function (res) {
                $(".messages > .message").last().after(res);
                $("form #message").val('');
                $(document).scrollTop($(document).height());
            });
        });
    </script>
</body>

</html>