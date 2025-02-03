<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Edit Meal</h2>
                <a href="{{ route('meals.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            <form action="{{ route('meals.update', $meal->meal_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $meal->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $meal->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Serving Size -->
                        <div>
                            <label for="default_serving_size" class="block text-sm font-medium text-gray-700">Default Serving Size</label>
                            <input type="number" step="0.01" name="default_serving_size" id="default_serving_size" 
                                value="{{ old('default_serving_size', $meal->default_serving_size) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('default_serving_size') border-red-500 @enderror">
                            @error('default_serving_size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Serving Unit -->
                        <div>
                            <label for="serving_unit" class="block text-sm font-medium text-gray-700">Serving Unit</label>
                            <input type="text" name="serving_unit" id="serving_unit" 
                                value="{{ old('serving_unit', $meal->serving_unit) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('serving_unit') border-red-500 @enderror">
                            @error('serving_unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                            @if($meal->image_url)
                                <div class="mb-2">
                                    <img src="{{ $meal->image_url }}" alt="{{ $meal->name }}" 
                                         class="h-20 w-20 object-cover rounded-md">
                                </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#f84525] file:text-white hover:file:bg-red-700">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="is_active" class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $meal->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Food Items Section -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Food Items</h3>
                        <button type="button" id="addFoodItemBtn"
                            class="bg-gray-500 text-white px-3 py-1 rounded-md hover:bg-gray-600 text-sm">
                            Add Food Item
                        </button>
                    </div>
                    
                    <div id="food-items-container">
                        @foreach($meal->foods as $index => $mealFood)
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 items-center food-item-row">
                                <div>
                                    <select name="foods[{{ $index }}][food_id]" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50"
                                            required>
                                        <option value="">Select Food Item</option>
                                        @foreach($foodItems as $item)
                                            <option value="{{ $item->food_id }}" 
                                                {{ $mealFood->food_id == $item->food_id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="number" step="0.01" 
                                           name="foods[{{ $index }}][quantity]"
                                           value="{{ $mealFood->quantity }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50"
                                           placeholder="Quantity" required>
                                </div>
                                <div>
                                    <input type="text" 
                                           name="foods[{{ $index }}][unit]"
                                           value="{{ $mealFood->unit }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50"
                                           placeholder="Unit" required>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900 remove-food-item"
                                            {{ $index === 0 ? 'disabled' : '' }}>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Nutrition Information Section -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nutritional Information</h3>
                    <div id="nutrition-container">
                        @foreach($nutritionTypes as $index => $type)
                            @php
                                $nutritionFact = $meal->nutritionFacts
                                    ->where('nutrition_id', $type->nutrition_id)
                                    ->first();
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $type->name }}</label>
                                    <input type="hidden" name="nutrition_facts[{{ $index }}][nutrition_id]" 
                                           value="{{ $type->nutrition_id }}">
                                </div>
                                <div>
                                    <input type="number" step="0.01" 
                                           name="nutrition_facts[{{ $index }}][amount_per_100g]"
                                           value="{{ old('nutrition_facts.' . $index . '.amount_per_100g', $nutritionFact ? $nutritionFact->amount_per_100g : null) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50"
                                           placeholder="Amount per 100g">
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="nutrition_facts[{{ $index }}][measurement_unit]" 
                                           value="{{ $type->unit }}">
                                    <span class="text-sm text-gray-500">{{ $type->unit }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Update Meal
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let foodItemCount = {{ $meal->foods->count() }}; // Start from existing count
        
        // Add Food Item Button Click Handler
        document.getElementById('addFoodItemBtn').addEventListener('click', function() {
            const container = document.getElementById('food-items-container');
            const template = container.querySelector('.food-item-row').cloneNode(true);
            
            // Update the indices in the name attributes
            template.querySelectorAll('[name]').forEach(element => {
                element.name = element.name.replace(/\[\d+\]/, `[${foodItemCount}]`);
            });
            
            // Enable and setup remove button
            const removeBtn = template.querySelector('.remove-food-item');
            removeBtn.disabled = false;
            removeBtn.addEventListener('click', function() {
                template.remove();
            });
            
            // Clear values
            template.querySelectorAll('input, select').forEach(element => {
                element.value = '';
            });
            
            // Append the new row
            container.appendChild(template);
            foodItemCount++;
        });
        
        // Add click handlers to existing remove buttons
        document.querySelectorAll('.remove-food-item').forEach(button => {
            if (!button.disabled) {
                button.addEventListener('click', function() {
                    this.closest('.food-item-row').remove();
                });
            }
        });
    });
    </script>
    @endpush
</x-app-layout>