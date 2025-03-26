<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchTiming extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'day_of_week', 'start_time', 'end_time'];

    /**
     * Get the branch that this timing record belongs to.
     *
     * Each timing entry is associated with a specific branch,
     * defining a many-to-one (inverse) relationship.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
