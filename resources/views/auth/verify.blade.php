@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Verify Your Email Address</h1>
            </div>
            <div class="card-body">
                @if (session('resent'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                <p>{{ __('If you did not receive the email') }},</p>
                <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to request another</button>.
                </form>
            </div>
        </div>
    </div>
@endsection