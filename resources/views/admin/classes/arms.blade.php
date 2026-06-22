    @extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Class Levels & Arms</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLevelModal">
                            <i class="fa fa-plus me-2"></i>Add Level
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            @forelse($classLevels as $level)
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $level->name }}</h5>
                            <small class="text-muted">{{ $level->category }} Secondary • Order {{ $level->level_order }}</small>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-primary" onclick="editLevel({{ $level }})">
                                <i class="fe fe-edit"></i>
                            </button>
                            @if(!$level->classArms->where('enrollments_count', '>', 0)->count())
                            <form action="{{ route('admin.classes.levels.destroy', $level) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this level?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fe fe-trash-2"></i></button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-success" onclick="addArm({{ $level->id }})">
                                <i class="fe fe-plus"></i> Arm
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Arm</th>
                                        <th>Capacity</th>
                                        <th>Enrolled</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($level->classArms as $arm)
                                    <tr>
                                        <td><strong>{{ $arm->arm }}</strong></td>
                                        <td>{{ $arm->capacity }}</td>
                                        <td>
                                            <span class="badge {{ $arm->enrollments_count > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $arm->enrollments_count }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-light" onclick="editArm({{ $arm }})">
                                                <i class="fe fe-edit"></i>
                                            </button>
                                            @if($arm->enrollments_count == 0)
                                            <form action="{{ route('admin.classes.arms.destroy', $arm) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this arm?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><i class="fe fe-trash-2"></i></button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No arms yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">No class levels found. Run the seeder.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Level Modal -->
<div class="modal fade" id="addLevelModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Class Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.classes.levels.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. JSS1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Level Order <span class="text-danger">*</span></label>
                        <input type="number" name="level_order" class="form-control" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-control select" required>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Level</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Level Modal -->
<div class="modal fade" id="editLevelModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Class Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLevelForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editLevelName" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category" id="editLevelCategory" class="form-control select" required>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Level</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add / Edit Arm Modal -->
<div class="modal fade" id="armModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="armModalTitle">Add Class Arm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="armForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="armMethod" value="POST">
                <input type="hidden" name="class_level_id" id="armLevelId">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Arm Name <span class="text-danger">*</span></label>
                        <input type="text" name="arm" id="armName" class="form-control" placeholder="e.g. A, B, Gold" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" id="armCapacity" class="form-control" value="40" min="1" max="200" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Arm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editLevel(level) {
    document.getElementById('editLevelForm').action = '{{ url("admin/classes/levels") }}/' + level.id;
    document.getElementById('editLevelName').value = level.name;
    document.getElementById('editLevelCategory').value = level.category;
    new bootstrap.Modal(document.getElementById('editLevelModal')).show();
}

function addArm(levelId) {
    document.getElementById('armForm').action = '{{ route("admin.classes.arms.store") }}';
    document.getElementById('armMethod').value = 'POST';
    document.getElementById('armLevelId').value = levelId;
    document.getElementById('armName').value = '';
    document.getElementById('armCapacity').value = 40;
    document.getElementById('armModalTitle').innerText = 'Add Class Arm';
    new bootstrap.Modal(document.getElementById('armModal')).show();
}

function editArm(arm) {
    document.getElementById('armForm').action = '{{ url("admin/classes/arms") }}/' + arm.id;
    document.getElementById('armMethod').value = 'PUT';
    document.getElementById('armLevelId').value = arm.class_level_id;
    document.getElementById('armName').value = arm.arm;
    document.getElementById('armCapacity').value = arm.capacity;
    document.getElementById('armModalTitle').innerText = 'Edit Class Arm';
    new bootstrap.Modal(document.getElementById('armModal')).show();
}
</script>

@endsection
