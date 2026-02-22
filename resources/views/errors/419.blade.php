@extends('layouts.error')

@section('title', '419 - Session Expired')
@section('code', '419')
@section('color', 'text-warning')
@section('icon', 'fas fa-clock text-warning')
@section('heading', 'Session Expired')
@section('message', 'Your session has expired due to inactivity. Please login again.')

@section('extra')
<a href="{{ route('user.login') }}" class="btn btn-warning btn-block">
    <i class="fas fa-sign-in-alt mr-2"></i> Login Again
</a>
@endsection