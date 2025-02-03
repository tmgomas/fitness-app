<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Edit Food Item</h2>
                <a href="{{ route('food-items.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            <form action="{{ route('food-items.update', $foodItem) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" 
                            value="{{ old('name', $foodItem->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $foodItem->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serving Size -->
                    <div>
                        <label for="serving_size" class="block text-sm font-medium text-gray-700">Serving Size</label>
                        <input type="number" step="0.01" name="serving_size" id="serving_size" 
                            value="{{ old('serving_size', $foodItem->serving_size) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('serving_size') border-red-500 @enderror">
                        @error('serving_size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serving Unit -->
                    <div>
                        <label for="serving_unit" class="block text-sm font-medium text-gray-700">Serving Unit</label>
                        <input type="text" name="serving_unit" id="serving_unit" 
                            value="{{ old('serving_unit', $foodItem->serving_unit) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('serving_unit') border-red-500 @enderror">
                        @error('serving_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                        <input type="url" name="image_url" id="image_url" 
                            value="{{ old('image_url', $foodItem->image_url) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('image_url') border-red-500 @enderror">
                        @error('image_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Image Preview -->
                    @if($foodItem->image_url)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Image</label>
                        <div class="mt-2">
                            <img src="{{ $foodItem->image_url }}" 
                                 alt="{{ $foodItem->name }}" 
                                 class="h-32 w-32 object-cover rounded-md">
                        </div>
                    </div>
                    @endif

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $foodItem->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('food-items.index') }}" 
                           class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                            Update Food Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>