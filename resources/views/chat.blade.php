@php
use Carbon\Carbon;
@endphp

@extends('layouts/master')

@section('title', 'Konsultasi - alohadoc')

@section('content')
<div class="chat">

    <div class="top">
        <h5>Konsultasi #{{ $consultation->id }} ({{ $consultation->consultation_date }})</h5>

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

    @if (Carbon::parse($session->end_time) > Carbon::now())
        <div class="bottom">
            <form action="">
                <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    @endif

</div>
@endsection

@section('extra-js')
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
@endsection