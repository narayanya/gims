@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    Location Data Sync
                </h3>
                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Sync countries, states, and cities from external API</p>
            </div>
            <button type="button" class="btn btn-primary" id="syncAllBtn">
                            <i class="ri-refresh-line me-2"></i> Sync All Locations
                        </button>
                        
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="ri-global-line" style="font-size: 48px; color: #0d6efd;"></i>
                        </div>
                        <h5 class="card-title">Countries</h5>
                        <p class="text-muted small">Sync all countries from API</p>
                        <div id="countryStatus" class="mb-3">
                            <span class="badge bg-secondary">Not synced</span>
                        </div>
                        <button type="button" class="btn btn-primary" id="syncCountriesBtn">
                            <i class="ri-refresh-line me-1"></i> Sync Countries
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="ri-map-pin-line" style="font-size: 48px; color: #198754;"></i>
                        </div>
                        <h5 class="card-title">States</h5>
                        <p class="text-muted small">Sync all states from API</p>
                        <div id="stateStatus" class="mb-3">
                            <span class="badge bg-secondary">Not synced</span>
                        </div>
                        <button type="button" class="btn btn-success" id="syncStatesBtn">
                            <i class="ri-refresh-line me-1"></i> Sync States
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="ri-building-line" style="font-size: 48px; color: #ffc107;"></i>
                        </div>
                        <h5 class="card-title">Cities</h5>
                        <p class="text-muted small">Sync all cities from API</p>
                        <div id="cityStatus" class="mb-3">
                            <span class="badge bg-secondary">Not synced</span>
                        </div>
                        <button type="button" class="btn btn-warning" id="syncCitiesBtn">
                            <i class="ri-refresh-line me-1"></i> Sync Cities
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                        <p class="text-muted mb-0">Sync all location data (Countries, States, and Cities) at once</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Countries:</span>
                                <strong id="countryCount">0</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>States:</span>
                                <strong id="stateCount">0</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Cities:</span>
                                <strong id="cityCount">0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sync Log</h5>
                    </div>
                    <div class="card-body">
                        <div id="syncLog" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-muted">No sync operations yet</p>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 id="loadingText">Syncing data, please wait...</h5>
                <p class="text-muted small" id="loadingSubtext">This may take a few moments</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    
    // Load initial counts
    loadCounts();

    document.getElementById('syncCountriesBtn').addEventListener('click', function() {
        syncLocation('countries');
    });

    document.getElementById('syncStatesBtn').addEventListener('click', function() {
        syncLocation('states');
    });

    document.getElementById('syncCitiesBtn').addEventListener('click', function() {
        syncLocation('cities');
    });

    document.getElementById('syncAllBtn').addEventListener('click', function() {
        syncAll();
    });

    function syncLocation(type) {
        const loadingText = document.getElementById('loadingText');
        loadingText.textContent = `Syncing ${type}...`;
        loadingModal.show();

        fetch('{{ route("sync.location") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type: type })
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                updateStatus(type, 'success', data.count);
                addLog(type, data.count, 'success', data.message);
                loadCounts();
            } else {
                updateStatus(type, 'error', 0);
                addLog(type, 0, 'error', data.message);
            }
        })
        .catch(error => {
            loadingModal.hide();
            updateStatus(type, 'error', 0);
            addLog(type, 0, 'error', error.message);
        });
    }

    function syncAll() {
        const loadingText = document.getElementById('loadingText');
        const loadingSubtext = document.getElementById('loadingSubtext');
        loadingText.textContent = 'Syncing all locations...';
        loadingSubtext.textContent = 'This will sync countries, states, and cities';
        loadingModal.show();

        fetch('{{ route("sync.location.all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                updateStatus('countries', 'success', data.countries);
                updateStatus('states', 'success', data.states);
                updateStatus('cities', 'success', data.cities);
                addLog('all', data.countries + data.states + data.cities, 'success', 
                    `Synced ${data.countries} countries, ${data.states} states, ${data.cities} cities`);
                loadCounts();
            } else {
                addLog('all', 0, 'error', data.message);
            }
        })
        .catch(error => {
            loadingModal.hide();
            addLog('all', 0, 'error', error.message);
        });
    }

    function updateStatus(type, status, count) {
        const statusElement = document.getElementById(`${type}Status`);
        if (status === 'success') {
            statusElement.innerHTML = `<span class="badge bg-success">Synced: ${count} records</span>`;
        } else {
            statusElement.innerHTML = `<span class="badge bg-danger">Failed</span>`;
        }
    }

    function addLog(type, count, status, message) {
        const logDiv = document.getElementById('syncLog');
        const now = new Date().toLocaleString();
        const statusClass = status === 'success' ? 'success' : 'danger';
        const icon = status === 'success' ? 'ri-check-line' : 'ri-close-line';
        
        const logEntry = `
            <div class="alert alert-${statusClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>
                <strong>${type.toUpperCase()}</strong>: ${message}
                <br><small class="text-muted">${now} - ${count} records</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        if (logDiv.querySelector('p')) {
            logDiv.innerHTML = logEntry;
        } else {
            logDiv.insertAdjacentHTML('afterbegin', logEntry);
        }
    }

    function loadCounts() {
        fetch('{{ route("sync.location.counts") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('countryCount').textContent = data.countries || 0;
                document.getElementById('stateCount').textContent = data.states || 0;
                document.getElementById('cityCount').textContent = data.cities || 0;
            });
    }
});
</script>
@endsection
