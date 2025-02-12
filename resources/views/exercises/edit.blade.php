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

            <form action="{{ route('exercises.update', $exercise->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-gray-50 p-4 rounded-lg">
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                                placeholder="Minimum 10 characters">{{ old('description', $exercise->description) }}</textarea>
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
                                <option value="beginner" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="expert" {{ old('difficulty_level', $exercise->difficulty_level) ==
                                    'expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                            @error('difficulty_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                            @if($exercise->image_url)
                            <div class="mt-2 mb-2">
                                <img src="{{ $exercise->image_url }}" alt="Current exercise image"
                                    class="w-32 h-32 object-cover rounded-md">
                                <p class="mt-1 text-sm text-gray-500">Current image</p>
                            </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#f84525] file:text-white hover:file:bg-red-700 @error('image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Max size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF
                            </p>
                            <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image</p>
                            @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calories per Minute -->
                        <div>
                            <label for="calories_per_minute" class="block text-sm font-medium text-gray-700">Calories
                                per Minute</label>
                            <input type="number" step="0.01" min="0" max="1000" name="calories_per_minute"
                                id="calories_per_minute"
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
                            <input type="number" step="0.01" min="0" max="1000" name="calories_per_km"
                                id="calories_per_km" value="{{ old('calories_per_km', $exercise->calories_per_km) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('calories_per_km') border-red-500 @enderror">
                            @error('calories_per_km')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recommended Intensity -->
                        <div>
                            <label for="recommended_intensity"
                                class="block text-sm font-medium text-gray-700">Recommended Intensity</label>
                            <select name="recommended_intensity" id="recommended_intensity"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('recommended_intensity') border-red-500 @enderror">
                                <option value="">Select Intensity</option>
                                <option value="low" {{ old('recommended_intensity', $exercise->recommended_intensity) ==
                                    'low' ? 'selected' : '' }}>Low</option>
                                <option value="moderate" {{ old('recommended_intensity', $exercise->
                                    recommended_intensity) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="high" {{ old('recommended_intensity', $exercise->recommended_intensity)
                                    == 'high' ? 'selected' : '' }}>High</option>
                                <option value="very_high" {{ old('recommended_intensity', $exercise->
                                    recommended_intensity) == 'very_high' ? 'selected' : '' }}>Very High</option>
                            </select>
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
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('exercises.index') }}"
                        class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Update Exercise
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>