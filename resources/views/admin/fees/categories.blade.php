@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Fee Categories</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.fees.structure') }}">
                            <i class="fe fe-dollar-sign me-2"></i>Fee Structure
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            {{-- List --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table datatable mb-0">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $cat)
                                    <tr>
                                        <td>{{ $cat->display_order }}</td>
                                        <td>
                                            <strong>{{ $cat->name }}</strong>
                                            @if($cat->description)
                                                <br><small class="text-muted">{{ Str::limit($cat->description, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cat->is_compulsory)
                                                <span class="badge bg-primary">Compulsory</span>
                                            @else
                                                <span class="badge bg-secondary">Optional</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cat->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="btn delete-table me-2" href="{{ route('admin.fees.categories.edit', $cat) }}">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.fees.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn delete-table"><i class="fe fe-trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted">No categories found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add / Edit Form --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ isset($feeCategory) ? 'Edit Category' : 'Add Category' }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($feeCategory) ? route('admin.fees.categories.update', $feeCategory) : route('admin.fees.categories.store') }}" method="POST">
                            @csrf
                            @if(isset($feeCategory)) @method('PUT') @endif

                            <div class="form-group mb-3">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $feeCategory->name ?? '') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="description" rows="2" class="form-control">{{ old('description', $feeCategory->description ?? '') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Display Order <span class="text-danger">*</span></label>
                                        <input type="number" name="display_order" class="form-control"
                                               value="{{ old('display_order', $feeCategory->display_order ?? 0) }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>&nbsp;</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_compulsory" value="0">
                                            <input class="form-check-input" type="checkbox" name="is_compulsory" value="1" id="is_compulsory"
                                                {{ old('is_compulsory', $feeCategory->is_compulsory ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_compulsory">Compulsory</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                        {{ old('is_active', $feeCategory->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="btn-path">
                                @if(isset($feeCategory))
                                    <a href="{{ route('admin.fees.categories') }}" class="btn btn-cancel me-2">Cancel</a>
                                @endif
                                <button type="submit" class="btn btn-primary">{{ isset($feeCategory) ? 'Update' : 'Add' }} Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
