@extends('layouts.app')

@section('content')
<script>
    window.location.href = "{{ route('storage-management.index') }}";
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Redirecting...</div>

                <div class="card-body">
                    <p>You are being redirected to the Storage Management page.</p>
                    <p>If you are not redirected automatically, <a href="{{ route('storage-management.index') }}">click here</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
