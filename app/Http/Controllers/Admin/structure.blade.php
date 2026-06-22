<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fee Structure') }}
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

            <!-- Term Selector -->
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg flex items-center justify-between">
                <form action="{{ route('admin.fees.structure') }}" method="GET" class="flex items-center gap-4">
                    <label class="font-medium text-sm">Select Term:</label>
                    <select name="term_id" class="border-gray-300 rounded-md shadow-sm text-sm py-1" onchange="this.form.submit()">
                        <option value="">-- Select Term --</option>
                        @foreach($terms as $t)
                            <option value="{{ $t->id }}" {{ ($term && $t->id == $term->id) ? 'selected' : '' }}>
                                {{ $t->session->name }} - {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($term)
                    <form action="{{ route('admin.fees.structure.copy') }}" method="POST" class="flex items-center gap-4" onsubmit="return confirm('This will copy fee structures to the currently selected term. Proceed?');">
                        @csrf
                        <input type="hidden" name="to_term_id" value="{{ $term->id }}">
                        <label class="text-sm font-medium">Copy From:</label>
                        <select name="from_term_id" class="border-gray-300 rounded-md shadow-sm text-sm py-1" required>
                            <option value="">-- Select Source Term --</option>
                            @foreach($terms as $t)
                                @if($t->id != $term->id)
                                    <option value="{{ $t->id }}">{{ $t->session->name }} - {{ $t->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">Copy</button>
                    </form>
                @endif
            </div>

            @if($term)
                <form action="{{ route('admin.fees.structure.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="term_id" value="{{ $term->id }}">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 overflow-x-auto">
                            <h3 class="font-bold text-lg mb-4">Fee Structure for {{ $term->session->name }} - {{ $term->name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">Leave amount blank for N/A.</p>

                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border p-2 min-w-[200px] sticky left-0 bg-gray-100 z-10 shadow-sm">Fee Category \ Class Level</th>
                                        @foreach($classLevels as $level)
                                            <th class="border p-2 text-center">{{ $level->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td class="border p-2 sticky left-0 bg-white z-10 shadow-sm font-medium">
                                                {{ $category->name }}
                                                @if($category->is_compulsory)
                                                    <span class="text-red-500 text-xs" title="Compulsory">*</span>
                                                @endif
                                            </td>
                                            @foreach($classLevels as $level)
                                                @php
                                                    $key = $category->id . '-' . $level->id;
                                                    $amount = isset($structures[$key]) ? rtrim(rtrim($structures[$key]->amount, '0'), '.') : '';
                                                @endphp
                                                <td class="border p-1">
                                                    <input type="number"
                                                           step="0.01"
                                                           min="0"
                                                           name="amounts[{{ $category->id }}][{{ $level->id }}]"
                                                           value="{{ old('amounts.'.$category->id.'.'.$level->id, $amount) }}"
                                                           class="w-full text-right border-gray-300 rounded text-sm px-2 py-1 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                           placeholder="-">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    @if($categories->isEmpty())
                                        <tr>
                                            <td colspan="{{ count($classLevels) + 1 }}" class="p-4 text-center text-gray-500 border">No active fee categories found. Add categories first.</td>
                                        </tr>
                                    @endif
                                    @if($classLevels->isEmpty())
                                        <tr>
                                            <td colspan="1" class="p-4 text-center text-gray-500 border">No class levels found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">Save Fee Structure</button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center text-gray-500">
                    Please select a term to view or edit its fee structure.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
