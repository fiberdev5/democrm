{{-- resources/views/frontend/secure/support/index.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Debugging Support Tickets</h1>
        @if(isset($tickets))
            <p>Number of tickets: {{ $tickets->total() }}</p>
            @foreach($tickets as $ticket)
                <p>Ticket #{{ $ticket->ticket_number }} - {{ $ticket->subject }}</p>
            @endforeach
        @else
            <p>The $tickets variable is not set.</p>
        @endif
    </div>
</div>
@endsection