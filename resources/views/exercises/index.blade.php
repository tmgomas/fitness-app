<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Exercises</h2>
                <a href="{{ route('exercises.create') }}"
                    class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                    Add Exercise
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="mb-6">
                <form action="{{ route('exercises.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50"
                            placeholder="Search exercises...">
                    </div>
                    <div>
                        <select name="category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Exercises Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Difficulty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($exercises as $exercise)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($exercise->image_url)
                                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $exercise->image_url }}" alt="{{ $exercise->name }}">
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $exercise->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($exercise->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $exercise->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $exercise->difficulty_level === 'Beginner' ? 'bg-green-100 text-green-800' : 
                                           ($exercise->difficulty_level === 'Intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $exercise->difficulty_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('exercises.toggle-status', $exercise) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer
                                                {{ $exercise->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $exercise->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('exercises.edit', $exercise) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('exercises.destroy', $exercise) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this exercise?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $exercises->links() }}
            </div>
        </div>
    </div>
</x-app-layout>