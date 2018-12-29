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

                    
                    @can('view-contacts')
                        <ul>
                            @foreach($contacts as $contact)
                                <li>{{$contact->email}}</li>
                            @endforeach
                        </ul>
                    @endcan

                    <p>Completed modules:</p>

                    <p>
                        <ul>
                            @foreach(auth()->user()->completed_modules as $module)
                                <li>{{ $module->course_key }} - {{ $module->name }}</li>
                            @endforeach
                        </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
