<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'logo'];

    /**
     * Get the branches associated with the business.
     *
     * A business can have multiple branches.
     * This defines a one-to-many relationship.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
