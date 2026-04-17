@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    CoreData Synchronization
                </h3>
                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Sync master data from external API sources</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sync Configuration</h5>
                    </div>
                    <div class="card-body">
                        <form id="syncForm">
                            <div class="mb-3">
                                <label for="sync_type" class="form-label">Data Type to Sync *</label>
                                <select class="form-select" id="sync_type" name="sync_type" required>
                                    <option value="">Select Data Type</option>
                                    <option value="crops">Crops</option>
                                    <option value="varieties">Varieties</option>
                                    <option value="categories">Categories</option>
                                    <option value="units">Units</option>
                                    <option value="warehouses">Warehouses</option>
                                    <option value="crop_categories">Crop Categories</option>
                                    <option value="crop_types">Crop Types</option>
                                    <option value="variety_types">Variety Types</option>
                                    <option value="seasons">Seasons</option>
                                    <option value="seed_classes">Seed Classes</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="api_url" class="form-label">API URL *</label>
                                <input type="url" class="form-control" id="api_url" name="api_url" 
                                       placeholder="https://api.example.com/data" required>
                                <small class="text-muted">Enter the full API endpoint URL</small>
                            </div>

                            <div class="mb-3">
                                <label for="api_token" class="form-label">API Token (Optional)</label>
                                <input type="text" class="form-control" id="api_token" name="api_token" 
                                       placeholder="Bearer token if required">
                                <small class="text-muted">Leave empty if API doesn't require authentication</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="syncBtn">
                                    <i class="ri-refresh-line me-1"></i> Start Sync
                                </button>
                                <button type="button" class="btn btn-secondary" id="testBtn">
                                    <i class="ri-test-tube-line me-1"></i> Test Connection
                                </button>
                            </div>
                        </form>

                        <div id="syncResult" class="mt-4" style="display: none;">
                            <div class="alert" role="alert" id="resultAlert"></div>
                        </div>

                        <div id="syncProgress" class="mt-4" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span>Syncing data, please wait...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">API Data Format</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">The API should return JSON data in the following format:</p>
                        <pre class="bg-light p-3 rounded"><code>[
  {
    "name": "Item Name",
    "code": "CODE001",
    "description": "Description"
  },
  ...
]</code></pre>
                        <div class="mt-3">
                            <h6 class="text-sm font-semibold">Required Fields:</h6>
                            <ul class="text-muted small">
                                <li><code>name</code> - Required for all types</li>
                                <li><code>code</code> - Optional</li>
                                <li><code>description</code> - Optional</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <h6 class="text-sm font-semibold">Additional Fields:</h6>
                            <ul class="text-muted small">
                                <li><strong>Crops:</strong> scientific_name</li>
                                <li><strong>Units:</strong> symbol</li>
                                <li><strong>Warehouses:</strong> location, capacity</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sync History</h5>
                    </div>
                    <div class="card-body">
                        <div id="syncHistory">
                            <p class="text-muted small">No sync history available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const syncForm = document.getElementById('syncForm');
    const syncBtn = document.getElementById('syncBtn');
    const testBtn = document.getElementById('testBtn');
    const syncResult = document.getElementById('syncResult');
    const resultAlert = document.getElementById('resultAlert');
    const syncProgress = document.getElementById('syncProgress');

    syncForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSync();
    });

    testBtn.addEventListener('click', function() {
        testConnection();
    });

    function performSync() {
        const formData = new FormData(syncForm);
        
        syncProgress.style.display = 'block';
        syncResult.style.display = 'none';
        syncBtn.disabled = true;

        fetch('{{ route("sync.data") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            syncProgress.style.display = 'none';
            syncResult.style.display = 'block';
            syncBtn.disabled = false;

            if (data.success) {
                resultAlert.className = 'alert alert-success';
                resultAlert.innerHTML = `<i class="ri-check-line me-2"></i>${data.message}`;
                addToHistory(formData.get('sync_type'), data.synced_count, 'success');
            } else {
                resultAlert.className = 'alert alert-danger';
                resultAlert.innerHTML = `<i class="ri-error-warning-line me-2"></i>${data.message}`;
                addToHistory(formData.get('sync_type'), 0, 'failed');
            }
        })
        .catch(error => {
            syncProgress.style.display = 'none';
            syncResult.style.display = 'block';
            syncBtn.disabled = false;
            resultAlert.className = 'alert alert-danger';
            resultAlert.innerHTML = `<i class="ri-error-warning-line me-2"></i>Error: ${error.message}`;
            addToHistory(formData.get('sync_type'), 0, 'error');
        });
    }

    function testConnection() {
        const apiUrl = document.getElementById('api_url').value;
        const apiToken = document.getElementById('api_token').value;

        if (!apiUrl) {
            alert('Please enter an API URL');
            return;
        }

        syncProgress.style.display = 'block';
        syncResult.style.display = 'none';

        const headers = {
            'Accept': 'application/json',
        };

        if (apiToken) {
            headers['Authorization'] = 'Bearer ' + apiToken;
        }

        fetch(apiUrl, { headers })
            .then(response => {
                syncProgress.style.display = 'none';
                syncResult.style.display = 'block';

                if (response.ok) {
                    resultAlert.className = 'alert alert-success';
                    resultAlert.innerHTML = '<i class="ri-check-line me-2"></i>Connection successful! API is reachable.';
                } else {
                    resultAlert.className = 'alert alert-warning';
                    resultAlert.innerHTML = `<i class="ri-error-warning-line me-2"></i>Connection failed with status: ${response.status}`;
                }
            })
            .catch(error => {
                syncProgress.style.display = 'none';
                syncResult.style.display = 'block';
                resultAlert.className = 'alert alert-danger';
                resultAlert.innerHTML = `<i class="ri-error-warning-line me-2"></i>Connection error: ${error.message}`;
            });
    }

    function addToHistory(type, count, status) {
        const historyDiv = document.getElementById('syncHistory');
        const now = new Date().toLocaleString();
        const statusClass = status === 'success' ? 'text-success' : 'text-danger';
        const statusIcon = status === 'success' ? 'ri-check-line' : 'ri-close-line';
        
        const historyItem = `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between">
                    <span class="small"><strong>${type}</strong></span>
                    <span class="small ${statusClass}"><i class="${statusIcon}"></i></span>
                </div>
                <div class="small text-muted">${now}</div>
                <div class="small">Records: ${count}</div>
            </div>
        `;
        
        if (historyDiv.querySelector('p')) {
            historyDiv.innerHTML = historyItem;
        } else {
            historyDiv.insertAdjacentHTML('afterbegin', historyItem);
        }
    }
});
</script>
@endsection
