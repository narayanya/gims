@extends('layouts.app')

@section('content')

<div class="row">
<div class="col-12">

<div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
<div>
<h3 class="text-xl font-bold">Soil Types Master</h3>
<p class="text-muted">View and manage soil types master data</p>
</div>

<button class="btn btn-sm btn-primary" id="addSoilTypeBtn">
<i class="ri-add-line me-1"></i> New Soil Type
</button>

</div>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<div class="card">
<div class="card-body">

<table class="table table-bordered align-middle">

<thead>
<tr>
<th>Name</th>
<th>Code</th>
<th>Description</th>
<th>Status</th>
<th width="150">Actions</th>
</tr>
</thead>

<tbody>

@foreach($soiltypes as $soil)

<tr>

<td>{{ $soil->name }}</td>

<td>{{ $soil->code }}</td>

<td>{{ $soil->description }}</td>

<td>
<span class="badge bg-success">
{{ $soil->status }}
</span>
</td>

<td>

<button
class="btn btn-sm btn-outline-warning  editBtn"
data-id="{{ $soil->id }}"
data-name="{{ $soil->name }}"
data-code="{{ $soil->code }}"
data-description="{{ $soil->description }}"
>
<i class="ri-edit-line"></i>
</button>


<form action="{{ route('soil-types.destroy',$soil->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button class="btn btn-outline-danger btn-sm"
onclick="return confirm('Delete Soil Type?')">
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
</div>

@endsection



@section('modals')

<div class="modal fade" id="soilModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Add Soil Type</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>


<form method="POST" id="soilForm" action="{{ route('soil-types.store') }}">

@csrf

<div class="modal-body">

<input type="hidden" id="soil_id">

<div class="mb-3">

<label class="form-label">
Soil Type Name <span class="text-danger">*</span>
</label>

<input
type="text"
name="name"
id="soilName"
class="form-control @error('name') is-invalid @enderror"
placeholder="Enter soil type name (example: Loamy Soil)"
required
>

@error('name')
<div class="invalid-feedback">
{{ $message }}
</div>
@enderror

</div>


<div class="mb-3">

<label class="form-label">Code</label>

<input
type="text"
name="code"
id="soilCode"
class="form-control"
placeholder="Enter soil code (example: ST01)"
>

</div>

<div class="mb-3">

<label class="form-label">Status</label>

<select class="form-select" id="soilStatus" name="status">
    <option value="1">Active</option>
    <option value="0">Inactive</option>
</select>

</div>


<div class="mb-3">

<label class="form-label">Description</label>

<textarea
name="description"
id="soilDescription"
class="form-control"
rows="3"
placeholder="Enter soil description (optional)">
</textarea>

</div>


</div>


<div class="modal-footer">

<button class="btn btn-light" data-bs-dismiss="modal">
Close
</button>

<button class="btn btn-primary" id="saveBtn">
Save Soil Type
</button>

</div>

</form>

</div>
</div>
</div>

@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

let soilModal = new bootstrap.Modal(document.getElementById('soilModal'));

let form = document.getElementById('soilForm');


/* ADD NEW */

document.getElementById('addSoilTypeBtn').addEventListener('click', function(){

form.reset();

form.action = "{{ route('soil-types.store') }}";

let methodInput = form.querySelector('input[name="_method"]');

if(methodInput){
methodInput.remove();
}

document.querySelector('.modal-title').innerText = "Add Soil Type";

soilModal.show();

});



/* EDIT */

document.querySelectorAll('.editBtn').forEach(btn => {

btn.addEventListener('click', function(){

let id = this.dataset.id;

document.getElementById('soilName').value = this.dataset.name;
document.getElementById('soilCode').value = this.dataset.code ?? '';
document.getElementById('soilDescription').value = this.dataset.description ?? '';

form.action = "/soil-types/" + id;


/* add PUT method */

let methodInput = form.querySelector('input[name="_method"]');

if(!methodInput){

methodInput = document.createElement('input');

methodInput.type = 'hidden';
methodInput.name = '_method';
methodInput.value = 'PUT';

form.appendChild(methodInput);

}


document.querySelector('.modal-title').innerText = "Edit Soil Type";

soilModal.show();

});

});


});

</script>

@endsection  