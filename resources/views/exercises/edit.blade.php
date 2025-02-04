<!-- resources/views/admin/exercises/edit.blade.php -->
<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Edit Exercise</h2>
                <a href="{{ route('exercises.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            <form action="{{ route('exercises.update', $exercise) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $exercise->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category_id" id="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $exercise->category_id) ==
                                    $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $exercise->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty
                                Level</label>
                            <select name="difficulty_level" id="difficulty_level"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('difficulty_level') border-red-500 @enderror">
                                <option value="">Select Difficulty</option>
                                <option value="Beginner" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'Advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('difficulty_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                            <input type="url" name="image_url" id="image_url"
                                value="{{ old('image_url', $exercise->image_url) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('image_url') border-red-500 @enderror">
                            @error('image_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calories per Minute -->
                        <div>
                            <label for="calories_per_minute" class="block text-sm font-medium text-gray-700">Calories
                                per Minute</label>
                            <input type="number" step="0.01" name="calories_per_minute" id="calories_per_minute"
                                value="{{ old('calories_per_minute', $exercise->calories_per_minute) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('calories_per_minute') border-red-500 @enderror">
                            @error('calories_per_minute')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calories per KM -->
                        <div>
                            <label for="calories_per_km" class="block text-sm font-medium text-gray-700">Calories per
                                KM</label>
                            <input type="number" step="0.01" name="calories_per_km" id="calories_per_km"
                                value="{{ old('calories_per_km', $exercise->calories_per_km) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('calories_per_km') border-red-500 @enderror">
                            @error('calories_per_km')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recommended Intensity -->
                        <div>
                            <label for="recommended_intensity"
                                class="block text-sm font-medium text-gray-700">Recommended Intensity</label>
                            <input type="text" name="recommended_intensity" id="recommended_intensity"
                                value="{{ old('recommended_intensity', $exercise->recommended_intensity) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('recommended_intensity') border-red-500 @enderror">
                            @error('recommended_intensity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Requirements -->
                        <div class="space-y-4">
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="requires_distance" value="1" {{
                                        old('requires_distance', $exercise->requires_distance) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525]
                                    focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Requires Distance Tracking</span>
                                </label>
                                @error('requires_distance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="requires_heartrate" value="1" {{
                                        old('requires_heartrate', $exercise->requires_heartrate) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525]
                                    focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Requires Heart Rate Monitoring</span>
                                </label>
                                @error('requires_heartrate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active',
                                        $exercise->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525]
                                    focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                                @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Update Exercise
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>