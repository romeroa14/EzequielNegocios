<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id',
        'reviewed_id',
        'order_id',
        'rating',
        'comment',
        'review_type',
        'is_public'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_public' => 'boolean'
    ];

    public function reviewer()
    {
        return $this->belongsTo(Person::class, 'reviewer_id');
    }

    public function reviewedPerson()
    {
        return $this->belongsTo(Person::class, 'reviewed_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
} 