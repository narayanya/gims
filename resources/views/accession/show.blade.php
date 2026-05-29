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
                <tr><th style="width: 30%;">Accession No</th><td>{{ $accession->accession_number ?? '-' }}</td></tr>
                <tr><th>Accession Name</th><td>{{ $accession->accession_name ?? '-' }}</td></tr>
                <tr><th>Source</th><td>
                    {{ ucfirst($accession->acc_source ?? '-') }}

                    @if($accession->acc_source == 'external' && $accession->ext_source)
                        <br>
                        <small class="text-muted">
                            {{ $accession->ext_source }}
                        </small>
                    @endif    
                </td></tr>
                <tr><th>Storage Time</th><td>{{ $accession->storageTime?->name ?? '-' }}</td></tr>
                <tr><th>Sample Id</th><td>{{ $accession->sample_id ?? '-' }}</td></tr>
                <tr><th>Requester Show</th><td>{{ $accession->requester_show == 'yes' ? 'Yes' : 'No' }}</td></tr>
                <tr><th> Year Of Arrival</th><td>{{ $accession->year_of_arrival ?? '-' }}</td></tr>

                <tr><th style="width: 30%;">Crop</th><td>{{ $accession->crop->crop_name ?? '-' }}</td></tr>


                <tr><th>Common Name</th><td>{{ $accession->crop->common_name ?? '-' }}</td></tr>
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
                    <td style="width: 30%;"><strong>Passport Data</strong></td>
                    <td>
                        {{ $accession->passport_file_path ?? '-' }}
                    </td>
                                
                </tr>
            </tbody>
                    </table>
            <tbody>
                </div>
            </div>

            <!-- 7. DOCUMENT -->
            <div class="card mb-3">
                <div class="card-header bg-light p-2"><strong>5. Documentation</strong></div>
                <div class="card-body">
                    <p><strong>Barcode Type:</strong> {{ $accession->barcode_type ?? '-' }}</p>
                    <p><strong>Barcode:</strong> {{ $accession->barcode ?? '-' }}</p>
                    <p><strong>Notes:</strong> {{ $accession->notes ?? '-' }}</p>   
                    <p> Created At: {{ $accession->created_at ? $accession->created_at->format('d M Y, h:i A') : '-' }}</p>
                    <p> Updated At: {{ $accession->updated_at ? $accession->updated_at->format('d M Y, h:i A') : '-' }}</p>
                    <p>Created By: {{ $accession->createdBy->name ?? '-' }}</p>
                    <p>Updated By: {{ $accession->updatedBy->name ?? '-' }}</p>
                </div>
            </div>

        </div>

    </div>
            
        </div>
</div>
@endsection
