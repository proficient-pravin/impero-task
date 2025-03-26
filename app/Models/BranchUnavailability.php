<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchUnavailability extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'date', 'status'];

    /**
     * Get the branch that this unavailability record belongs to.
     *
     * Each unavailability record is linked to a specific branch.
     * This defines a many-to-one (inverse) relationship.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
