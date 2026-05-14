@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h3 class="text-xl mb-1">
                    Quality Master
                </h3>
                <p class="text-muted mb-0">
                    View and manage quality master data
                </p>
            </div>

            <button class="btn btn-sm btn-primary" id="addQualityBtn">
                <i class="ri-add-line me-1"></i>
                New Quality Master
            </button>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Table --}}
        @if($qualityMasters->count())

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Quality Name</th>
                                <th>Quality Code</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($qualityMasters as $i => $qualityMaster)

                            <tr>
                                <td>{{ $i + 1 }}</td>

                                <td class="fw-semibold">
                                    {{ $qualityMaster->qc_name }}
                                </td>

                                <td>
                                    @if($qualityMaster->qc_code)
                                        <span class="badge bg-info">
                                            {{ $qualityMaster->qc_code }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $qualityMaster->description ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge {{ $qualityMaster->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $qualityMaster->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-warning editQualityBtn"
                                        data-id="{{ $qualityMaster->id }}"
                                        data-name="{{ $qualityMaster->qc_name }}"
                                        data-code="{{ $qualityMaster->qc_code }}"
                                        data-description="{{ $qualityMaster->description }}"
                                        data-status="{{ $qualityMaster->is_active }}"
                                    >
                                        <i class="ri-edit-line"></i>
                                    </button>

                                    <form action="{{ route('quality-master.destroy', $qualityMaster->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Deactivate this quality master?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>

                                    </form>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

        @else

        <div class="card">
            <div class="card-body text-center py-5">

                <p class="text-muted mb-0">
                    No quality master found.
                    <a href="#" id="addQualityBtnEmpty">
                        Create one
                    </a>
                </p>

            </div>
        </div>

        @endif

    </div>
</div>
@endsection


@section('modals')

<!-- Modal -->
<div class="modal fade" id="qualityModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="qualityModalLabel">
                    Add Quality Master
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form id="qualityForm"
                  method="POST"
                  action="{{ route('quality-master.store') }}">

                @csrf

                <div class="modal-body">

                    {{-- Quality Name --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Quality Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="qc_name"
                               id="qc_name"
                               class="form-control"
                               placeholder="Enter quality name"
                               required>
                    </div>

                    {{-- Quality Code --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Quality Code
                        </label>

                        <input type="text"
                               name="qc_code"
                               id="qc_code"
                               class="form-control"
                               placeholder="Enter quality code">
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="is_active"
                                id="is_active"
                                class="form-select">

                            <option value="1">Active</option>
                            <option value="0">Inactive</option>

                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Enter description"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="qualitySubmitBtn">
                        Save Quality
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const modalEl = document.getElementById('qualityModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Add
    function openAddModal()
    {
        document.getElementById('qualityForm').reset();

        document.getElementById('qualityForm').action =
            "{{ route('quality-master.store') }}";

        document.getElementById('qualityModalLabel').textContent =
            'Add Quality Master';

        document.getElementById('qualitySubmitBtn').textContent =
            'Save Quality';

        document.getElementById('is_active').value = '1';

        const oldMethod =
            document.querySelector('#qualityForm input[name="_method"]');

        if (oldMethod)
        {
            oldMethod.remove();
        }

        modal.show();
    }

    // Add Button
    document.getElementById('addQualityBtn')
        .addEventListener('click', openAddModal);

    // Empty Add Button
    const emptyBtn = document.getElementById('addQualityBtnEmpty');

    if (emptyBtn)
    {
        emptyBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openAddModal();
        });
    }

    // Edit
    document.querySelectorAll('.editQualityBtn').forEach(button => {

        button.addEventListener('click', function () {

            const data = this.dataset;

            document.getElementById('qc_name').value =
                data.name || '';

            document.getElementById('qc_code').value =
                data.code || '';

            document.getElementById('description').value =
                data.description || '';

            document.getElementById('is_active').value =
                data.status || '1';

            document.getElementById('qualityModalLabel').textContent =
                'Edit Quality Master';

            document.getElementById('qualitySubmitBtn').textContent =
                'Update Quality';

            const form = document.getElementById('qualityForm');

            const oldMethod =
                form.querySelector('input[name="_method"]');

            if (oldMethod)
            {
                oldMethod.remove();
            }

            const method = document.createElement('input');

            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';

            form.appendChild(method);

            form.action = `/quality-master/${data.id}`;

            modal.show();
        });

    });

});

</script>

@endpush