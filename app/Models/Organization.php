<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Organization extends Model
{
    use HasFactory;
    use HasFilamentComments;
    use BelongsToCountry;

    protected $fillable = [
        'country_id',
        'organization_type',
        'organization_name',
        'asset_update_dates',
        'sugef_report',
        'offer_form',
        'catalog_or_website',
        'vehicles_page',
    ];

    public function contacts() {
        return $this->hasMany(OrganizationContact::class);
    }

    public function customers() {
        return $this->hasMany(Customer::class);
    }

    public function customerreports() {
        return $this->hasMany(CustomerReport::class);
    }

    public function properties() {
        return $this->hasMany(PropertyAssignment::class);
    }

    public function offers() {
        return $this->hasMany(Offer::class);
    }

    public function accesRequests() {
        return $this->hasMany(AccesRequest::class);
    }
}
