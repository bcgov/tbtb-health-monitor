
@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'home'])
    </div>
    <main-component></main-component>
@endsection
