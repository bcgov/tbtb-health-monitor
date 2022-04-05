@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'service_contacts'])
    </div>
    <service-contacts-component></service-contacts-component>
@endsection
