<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class PropertyAssignment extends Model
{
    use BelongsToCountry;

    use HasFactory;
    use HasFilamentComments;

    protected $fillable = [
        'user_id',
        'property_info',
        'organization_id',
        'property_observations',
        'property_images',
        'property_assignment_status',
        'rejection_reason',
    ];

    protected $casts = [
        'property_images' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }
}
