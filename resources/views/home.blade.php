@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>You are logged in!</p>

                    <hr>

                    <div id="status" class="alert text-center"></div>
                    @can('view-contacts')
                        <div class="list-group">
                            @foreach($contacts as $contact)
                                <div class="list-group-item d-flex justify-content-between">{{$contact->name}}
                                    <button onclick="sendReminder('{{$contact->email}}')" type="submit" class="btn btn-sm btn-dark">Send Module Reminder</button>
                    
                                </div>

                            @endforeach
                        </div>
                    @endcan
                    
                    @if(!auth()->user()->completed_modules->isEmpty())
                        <p>Completed modules:</p>

                        <p>
                            <ul class="list-group">
                                @foreach(auth()->user()->completed_modules as $module)
                                    <li class="list-group-item">{{ $module->course_key }} - {{ $module->name }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
