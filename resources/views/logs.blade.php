@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'logs'])
    </div>
    <logs-component></logs-component>

@endsection
