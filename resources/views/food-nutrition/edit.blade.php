<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Edit Food Nutrition</h2>
                <a href="{{ route('food-nutrition.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            <form action="{{ route('food-nutrition.update', $foodNutrition) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Food Item -->
                    <div>
                        <label for="food_id" class="block text-sm font-medium text-gray-700">Food Item</label>
                        <select name="food_id" id="food_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('food_id') border-red-500 @enderror">
                            <option value="">Select Food Item</option>
                            @foreach($foodItems as $foodItem)
                                <option value="{{ $foodItem->food_id }}" {{ old('food_id', $foodNutrition->food_id) == $foodItem->food_id ? 'selected' : '' }}>
                                    {{ $foodItem->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('food_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nutrition Type -->
                    <div>
                        <label for="nutrition_id" class="block text-sm font-medium text-gray-700">Nutrition Type</label>
                        <select name="nutrition_id" id="nutrition_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('nutrition_id') border-red-500 @enderror">
                            <option value="">Select Nutrition Type</option>
                            @foreach($nutritionTypes as $type)
                                <option value="{{ $type->nutrition_id }}" {{ old('nutrition_id', $foodNutrition->nutrition_id) == $type->nutrition_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('nutrition_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount per 100g -->
                    <div>
                        <label for="amount_per_100g" class="block text-sm font-medium text-gray-700">Amount per 100g</label>
                        <input type="number" step="0.01" name="amount_per_100g" id="amount_per_100g" 
                               value="{{ old('amount_per_100g', $foodNutrition->amount_per_100g) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('amount_per_100g') border-red-500 @enderror">
                        @error('amount_per_100g')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Measurement Unit -->
                    <div>
                        <label for="measurement_unit" class="block text-sm font-medium text-gray-700">Measurement Unit</label>
                        <input type="text" name="measurement_unit" id="measurement_unit" 
                               value="{{ old('measurement_unit', $foodNutrition->measurement_unit) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('measurement_unit') border-red-500 @enderror">
                        @error('measurement_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                            Update Food Nutrition
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>