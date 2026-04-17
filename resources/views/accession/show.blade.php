@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Accession Full Details
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View accession full details data</p>
                </div>
                <a href="{{ route('accession.accession-list') }}" class="btn btn-sm btn-primary">
                    <i class="ri-arrow-left-line me-1"></i>Accession List
                </a>
            </div>

    <!-- HEADER -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Accession Name: {{ $accession->accession_name }}</h4>
                <p class="mb-0 text-muted">Accession No: {{ $accession->accession_number }}<br> 
                <span class="badge bg-success">{{ $accession->status == 1 ? 'Active' : 'Inactive' }}</span></p>
            </div>
            <div class="">
                <img style="width: 100px;" src="{{ $accession->photo_url ?? '/placeholder.png' }}" >
                <img style="width: 100px;" src="{{ asset('assets/images/barcode.png') }}" >
            </div>
            <div>
                <small class="text-success ">
    <b>Entry: {{ $accession->created_at ? $accession->created_at->format('d F Y') : '-' }}</b>
</small><br>

<small class="text-secondary">
    <b>Re-check: {{ $accession->recheck_date ? \Carbon\Carbon::parse($accession->recheck_date)->format('d F Y') : '-' }}</b>
</small><br>

<small class="text-danger">
    <b>Expired: {{ $accession->expiry_date ? \Carbon\Carbon::parse($accession->expiry_date)->format('d F Y') : '-' }}</b>
</small><br>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- LEFT SIDE -->
        <div class="col-md-6">

            <!-- 1. BASIC -->
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>1. Basic Information</strong></div>
                <div class="card-body p-1">
                    
                    <table class="table table-bordered table-striped p-0">
            <tbody>

                <!-- BASIC -->
                <tr><th style="width: 30%;">Crop</th><td>{{ $accession->crop->crop_name ?? '-' }}</td></tr>
                <tr><th>Scientific Name</th><td>{{ $accession->crop->scientific_name ?? '-' }}</td></tr>
                <tr><th>Family</th><td>{{ $accession->crop->family_name ?? '-' }}</td></tr>
                <tr><th>Genus</th><td>{{ $accession->crop->genus ?? '-' }}</td></tr>
            </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. COLLECTION -->
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>2. Collection Info</strong></div>
                <div class="card-body p-1">
                    <table class="table table-bordered table-striped p-0">
            <tbody>
                    <tr><th style="width: 30%;">Collection No</th><td>{{ $accession->collection_number ?? '-' }}</td></tr>
                <tr><th>Date</th><td>{{ $accession->collection_date ? $accession->collection_date->format('d M Y') : '-' }}</td></tr>
                <tr><th>Collector</th><td>{{ $accession->collector_name  ?? '-' }}</td></tr>
                <tr><th>Donor</th><td>{{ $accession->donor_name ?? '-' }}</td></tr>
                <tr><th>Location</th><td>{{ $accession->collection_site ?? '-' }}</td></tr>
                <tr><th>Country</th><td>{{ $accession->country->country_name ?? '-' }}</td></tr>
                <tr><th>State</th><td>{{ $accession->state->state_name ?? '-' }}</td></tr>
                <tr><th>District</th><td>{{ $accession->district->district_name ?? '-' }}</td></tr>
                <tr><th>Village</th><td>{{ $accession->city->city_village_name ?? '-' }}</td></tr>
            </tbody>
                    </table>
                </div>
            </div>

            

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-6">
            <!-- 3. BIOLOGICAL -->
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>3. Biological Info</strong></div>
                <div class="card-body p-1">
                    <table class="table table-bordered table-striped p-0">
            <tbody>
                    <tr><th style="width: 30%;">Biological Status:</th><td> {{ $accession->biological_status  ?? '-' }}</td></tr>
                    <tr><th>Sample Type:</th> <td>{{ $accession->sample_type ?? '-' }}</td></tr>
                    <tr><th>Reproductive:</th> <td>{{ $accession->reproductive_type ?? '-' }}</td></tr>
            </tbody>
                    </table>
                </div>
            </div>

            <!-- 7 passport details -->
            
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>4. Passport Data</strong></div>
                <div class="card-body p-1">
                    <table class="table table-bordered table-striped p-0">

            <tbody>
                <tr>
                    <th>Passport No</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Date</th>
                    <th>Remarks</th>
                </tr>
                    @foreach($accession->passports as $passport)
                        <tr>
                            <td>{{ $passport->passport_no ?? '-' }}</td>
                            <td>{{ $passport->sample_name ?? '-' }}</td>
                            <td>{{ $passport->remarks ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- 7. DOCUMENT -->
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>5. Documentation</strong></div>
                <div class="card-body">
                    <p><strong>Barcode:</strong> {{ $accession->barcode ?? '-' }}</p>
                    <p><strong>Notes:</strong> {{ $accession->notes ?? '-' }}</p>   
                </div>
            </div>

        </div>

    </div>
            
        </div>
</div>
@endsection
