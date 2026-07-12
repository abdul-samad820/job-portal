@extends('layouts.error')

@section('title', '401 - Unauthorized')
@section('code', '401')
@section('color', 'text-warning')
@section('icon', 'fas fa-lock text-warning')
@section('heading', 'Unauthorized')
@section('message', 'You need to be logged in to access this page.')

@section('extra')
<a href="{{ str_contains(url()->previous(), '/admin') ? route('admin.login') : route('user.login') }}" class="btn btn-warning btn-block">
    <i class="fas fa-sign-in-alt mr-2"></i> Login
</a>
@endsection
