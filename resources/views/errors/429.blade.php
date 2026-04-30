@extends('layouts.error')

@section('title', '429 - Too Many Requests')
@section('code', '429')
@section('color', 'text-warning')
@section('icon', 'fas fa-hourglass-half text-warning')

@section('heading', 'Too Many Requests')

@section('message', 'You have made too many requests in a short period of time. Please wait a moment before trying again.')

@section('extra')
    <div class="mt-3">
        <div class="alert alert-warning py-2 px-3 small">
          <i class="fas fa-hourglass-half"></i> Please wait a minute before retrying.
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">
            ← Go Back
        </a>

        <p class="text-muted small mt-3">
            If the problem continues, please try again later.
        </p>
    </div>
@endsection