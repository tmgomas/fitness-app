<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Create Exercise Category</h2>
                <a href="{{ route('exercise-categories.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            <form action="{{ route('exercise-categories.store') }}" method="POST">
                @csrf

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Measurement Type -->
                        <div>
                            <label for="measurement_type" class="block text-sm font-medium text-gray-700">Measurement
                                Type</label>
                            <input type="text" name="measurement_type" id="measurement_type"
                                value="{{ old('measurement_type') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('measurement_type') border-red-500 @enderror">
                            @error('measurement_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>