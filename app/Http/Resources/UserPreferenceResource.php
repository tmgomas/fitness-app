<?php
// app/Http/Resources/UserPreferenceResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'pref_id' => $this->pref_id,            // changed 'id' to 'pref_id'
            'user_id' => $this->user_id,            // added user_id
            'allergies' => $this->allergies,
            'dietary_restrictions' => $this->dietary_restrictions,
            'disliked_foods' => $this->disliked_foods,
            'fitness_goals' => $this->fitness_goals,
            'activity_level' => $this->activity_level,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),  // added created_at
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => $this->whenLoaded('user')      // added user relationship
        ];
    }
}
