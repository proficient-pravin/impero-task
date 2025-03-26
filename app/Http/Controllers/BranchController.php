<?php
namespace App\Http\Controllers;

use App\Http\Requests\SetBranchAvailabilityRequest;
use App\Models\Branch;
use App\Models\BranchTiming;
use App\Models\BranchUnavailability;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches for a business.
     */
    public function index($business_id)
    {
        $business = Business::findOrFail($business_id);
        return view('branches.index', compact('business'));
    }

    /**
     * Get branch data for DataTables.
     */
    public function getData($business_id)
    {
        $branches = Branch::where('business_id', $business_id)->select(['id', 'name', 'images', 'created_at']);
        return DataTables::of($branches)
            ->addColumn('images', function ($branch) {
                $images = json_decode($branch->images, true);
                if (! $images || empty($images)) {
                    return 'No Image';
                }
                $imageHtml = '';
                foreach ($images as $image) {
                    $imageHtml .= '<img src="' . asset("storage/" . $image) . '" width="50" height="50" class="rounded me-2">';
                }
                return $imageHtml;
            })
            ->addColumn('actions', function ($branch) {
                return '
                    <a href="' . route('branches.availability', $branch->id) . '" class="btn btn-sm btn-primary">Set Availability</a>
                    <a href="' . route('branches.availability.show', $branch->id) . '" class="btn btn-sm btn-primary">View Availability</a>
                    <a href="#" class="btn btn-sm btn-danger delete-branch" data-url="' . route('branches.destroy', $branch->id) . '">Delete</a>
                ';
            })
            ->rawColumns(['images', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create($business_id)
    {
        $business = Business::findOrFail($business_id);
        return view('branches.create', compact('business'));
    }

    /**
     * Store a newly created branch in the database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'name'        => 'required|string|max:255',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $branch              = new Branch();
        $branch->business_id = $request->business_id;
        $branch->name        = $request->name;

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('branches', 'public');
            }
            $branch->images = json_encode($imagePaths);
        }

        $branch->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Branch added successfully!',
        ]);
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        // Delete branch images if they exist
        if ($branch->images) {
            foreach (json_decode($branch->images) as $image) {
                \Storage::delete('public/' . $image);
            }
        }

        // Delete the branch
        $branch->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Branch deleted successfully!',
        ]);
    }

    /**
     * Show the form for editing branch availability.
     */
    public function editAvailability($id)
    {
        $branch       = Branch::findOrFail($id);
        $availability = $branch->availability ? json_decode($branch->availability, true) : [];

        return view('branches.availability', compact('branch', 'availability'));
    }

    /**
     * Update branch availability.
     */
    public function updateAvailability(SetBranchAvailabilityRequest $request, $id)
    {
        // Delete existing availability to avoid duplicates
        BranchTiming::where('branch_id', $id)->delete();
        foreach ($request->availability as $day => $data) {
            foreach ($data['times'] as $slot) {
                BranchTiming::create([
                    'branch_id'   => $id,
                    'day_of_week' => $day,
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }

        // Process Unavailability Dates
        BranchUnavailability::where('branch_id', $id)->delete();

        if (! empty($request->unavailability)) {
            foreach ($request->unavailability as $dateData) {
                BranchUnavailability::create([
                    'branch_id' => $id,
                    'date'      => $dateData['date'],
                ]);
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Branch availability updated successfully!']);
    }

    /**
     * Display branch availability within a given date range.
     */
    public function showAvailability(Request $request, Branch $branch)
    {
        $availability = [];
        $startDate    = $request->start_date;
        $endDate      = $request->end_date;

        if (! $startDate || ! $endDate) {
            return view('branches.show-availability', compact('branch', 'availability', 'startDate', 'endDate'));
        }

        $startDate   = Carbon::parse($startDate);
        $endDate     = Carbon::parse($endDate);
        $datePointer = $startDate->copy();

        while ($datePointer->lte($endDate)) {
            $dayOfWeek     = $datePointer->format('l');
            $date          = $datePointer->toDateString();
            $isUnavailable = BranchUnavailability::where('branch_id', $branch->id)
                ->whereDate('date', $date)
                ->exists();
            $timings = BranchTiming::where('branch_id', $branch->id)
                ->where('day_of_week', $dayOfWeek)
                ->get(['start_time', 'end_time']);
            $availability[$date] = $isUnavailable || $timings->isEmpty() ? ['status' => 'unavailable', 'times' => []] : ['status' => 'available', 'times' => $timings->toArray()];
            $datePointer->addDay();
        }

        return view('branches.show-availability', compact('branch', 'availability', 'startDate', 'endDate'));
    }
}
