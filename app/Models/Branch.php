<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'name', 'images'];

    protected $casts = [
        'images' => 'array', // Automatically converts JSON data to an array when accessed
    ];

    /**
     * Get the business that owns this branch.
     *
     * Defines an inverse one-to-many relationship where
     * multiple branches belong to a single business.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get all timing schedules for this branch.
     *
     * A branch can have multiple timings, defining a one-to-many relationship.
     */
    public function timings()
    {
        return $this->hasMany(BranchTiming::class);
    }

    /**
     * Get all unavailability records for this branch.
     *
     * This establishes a one-to-many relationship where
     * a branch can have multiple unavailable dates.
     */
    public function unavailabilities()
    {
        return $this->hasMany(BranchUnavailability::class);
    }
}
