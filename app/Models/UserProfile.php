<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'business_name',
        'rif_ci',
        'address',
        'city',
        'state',
        'postal_code',
        'description',
        'verification_status',
        'verification_documents',
        'social_media'
    ];

    protected $casts = [
        'verification_documents' => 'array',
        'social_media' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 