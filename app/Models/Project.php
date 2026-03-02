<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'name',
        'user_id',
        'progress',
        'project_status',
        'priority',
        'expected_benefit',
        'request_date',
        'last_updated_at',
        'observations',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
