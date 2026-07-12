@extends('layouts.error')

@section('title', '503 - Under Maintenance')
@section('code', '503')
@section('color', 'text-secondary')
@section('icon', 'fas fa-tools text-secondary')
@section('heading', 'Down for Maintenance')
@section('message', 'Job Hub is currently undergoing scheduled maintenance. We will be back shortly — thanks for your patience.')

@section('extra')
<div class="mt-3">
    <div class="alert alert-secondary py-2 px-3 small">
        <i class="fas fa-tools mr-1"></i> Please check back again in a few minutes.
    </div>
</div>
@endsection
