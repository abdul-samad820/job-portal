@extends('layouts.error')

@section('title', '422 - Unprocessable Entity')
@section('code', '422')
@section('color', 'text-warning')
@section('icon', 'fas fa-exclamation-circle text-warning')
@section('heading', 'Something Doesn\'t Look Right')
@section('message', 'The data submitted could not be processed. Please check the information and try again.')

@section('extra')
<a href="{{ url()->previous() }}" class="btn btn-warning btn-block">
    <i class="fas fa-redo mr-2"></i> Go Back and Retry
</a>
@endsection
