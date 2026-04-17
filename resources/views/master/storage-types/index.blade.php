@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Storage Types
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage storage type master data</p>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#typeModal" id="addTypeBtn">
                    <i class="ri-add-line me-1"></i>New Type
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($types->count())
            <div class="card">
                <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                        <tr data-id="{{ $type->id }}">
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->description }}</td>
                            <td>
                               <span class="badge {{ $type->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $type->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editTypeBtn" data-id="{{ $type->id }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('storage-types.destroy', $type) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
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
                    <p class="text-slate-500">No storage type found. <a href="#" data-bs-toggle="modal" data-bs-target="#locationModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    let modal = new bootstrap.Modal(document.getElementById('typeModal'));

    document.getElementById('addTypeBtn').addEventListener('click', function () {
        document.getElementById('typeModalLabel').textContent = 'New Storage Type';
        document.getElementById('typeForm').action = '{{ route('storage-types.store') }}';
        document.getElementById('typeFormMethod').value = 'POST';
        document.getElementById('typeName').value = '';
        document.getElementById('typeDescription').value = '';
        modal.show();
    });

    document.querySelectorAll('.editTypeBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            let id = this.dataset.id;
            fetch(`{{ url('storage-types') }}/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('typeModalLabel').textContent = 'Edit Storage Type';
                    document.getElementById('typeForm').action = `{{ url('storage-types') }}/${id}`;
                    document.getElementById('typeFormMethod').value = 'PUT';
                    document.getElementById('typeName').value = data.name;
                    document.getElementById('typeDescription').value = data.description;
                    modal.show();
                });
        });
    });
});
</script>
@endsection

@section('modals')
<!-- Modal -->
<div class="modal fade" id="typeModal" tabindex="-1" aria-labelledby="typeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="typeForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="typeModalLabel">New Storage Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" id="typeFormMethod" value="POST">
                    <div class="mb-3">
                        <label for="typeName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="typeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="typeStatus" class="form-label">Status</label>
                        <select class="form-select" id="typeStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="typeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="typeDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="typeFormSubmit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection