
@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'services'])
    </div>
    <services-component></services-component>
@endsection
