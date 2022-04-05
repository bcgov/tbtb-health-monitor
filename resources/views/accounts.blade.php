@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'contacts'])
    </div>
    <accounts-component></accounts-component>
@endsection
