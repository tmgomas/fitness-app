<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightPerServingToFoodItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->decimal('weight_per_serving', 8, 2)->nullable()->after('serving_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->dropColumn('weight_per_serving');
        });
    }
}
