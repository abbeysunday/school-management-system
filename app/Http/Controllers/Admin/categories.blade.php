<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fee Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded rounded-md">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <button type="button" onclick="document.getElementById('create-modal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add New Category</button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b-2 border-gray-200">
                            <tr>
                                <th class="pb-3 px-4">Name</th>
                                <th class="pb-3 px-4">Description</th>
                                <th class="pb-3 px-4">Compulsory</th>
                                <th class="pb-3 px-4">Order</th>
                                <th class="pb-3 px-4">Status</th>
                                <th class="pb-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $category->name }}</td>
                                    <td class="py-3 px-4 text-gray-500">{{ Str::limit($category->description, 50) }}</td>
                                    <td class="py-3 px-4">
                                        @if($category->is_compulsory)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">Yes</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-bold">No</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">{{ $category->display_order }}</td>
                                    <td class="py-3 px-4">
                                        @if($category->is_active)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Active</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button onclick="editCategory({{ $category->toJson() }})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                        <form action="{{ route('admin.fees.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($categories->isEmpty())
                                <tr>
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">No fee categories found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="create-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-bold mb-4">Add Fee Category</h3>
            <form action="{{ route('admin.fees.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Display Order</label>
                        <input type="number" name="display_order" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_compulsory" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2">Is Compulsory? (Applied to all students by default)</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2">Active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-bold mb-4">Edit Fee Category</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" id="edit-name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" id="edit-description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Display Order</label>
                        <input type="number" name="display_order" id="edit-display-order" min="1" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_compulsory" id="edit-is-compulsory" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2">Is Compulsory?</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2">Active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCategory(category) {
            document.getElementById('edit-form').action = '/admin/fees/categories/' + category.id;
            document.getElementById('edit-name').value = category.name;
            document.getElementById('edit-description').value = category.description;
            document.getElementById('edit-display-order').value = category.display_order;
            document.getElementById('edit-is-compulsory').checked = category.is_compulsory;
            document.getElementById('edit-is-active').checked = category.is_active;
            document.getElementById('edit-modal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
