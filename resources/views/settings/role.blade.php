@extends('layouts.app')

@section('content')
    <div class="">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                    <div class="items-center gap-3">
                        <h3
                            class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                            Role Management
                        </h3>
                        <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Manage application
                            settings and configurations</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <form method="POST" action="{{ route('settings.store') }}"> @csrf <!-- Role Name -->
                                    <div class="mb-3"> <label class="form-label">Role Name</label> <input type="text"
                                            name="name" class="form-control" placeholder="Enter role name" required>
                                    </div> <!-- Role Slug -->
                                    <div class="mb-3"> <label class="form-label">Role Slug</label> <input type="text"
                                            name="slug" class="form-control" placeholder="Enter role slug (e.g. admin)"
                                            required> </div> <!-- Description -->
                                    <div class="mb-3"> <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                                    </div> <!-- Submit Button -->
                                    <div class="d-grid"> <button type="submit" class="btn btn-success"> <i
                                                class="bi bi-save"></i> Save Role </button> </div>
                                </form>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th width="150">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $key => $role)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>{{ $role->slug }}</td>
                                                <td>{{ $role->description }}</td>
                                                <td>
                                                    @if ($role->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a> 
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;"> 
                                        @csrf @method('DELETE') 
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')"> Delete </button>
                                    </form> --}}
                                                </td>
                                            </tr>
                                        @empty <tr>
                                                <td colspan="6" class="text-center">No Roles Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
