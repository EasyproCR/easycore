<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class OrganizationContact extends Model
{
    use HasFactory;
    use HasFilamentComments;
    use BelongsToCountry;

    protected $fillable = [
        'country_id',
        'organization_id',
        'contact_type',
        'contact_name',
        'contact_position',
        'contact_phone_number',
        'contact_email',
        'contact_main_method',
        'contact_remarks',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
