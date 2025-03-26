<?php
namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BusinessController extends Controller
{
    /**
     * Display the business listing page.
     */
    public function index()
    {
        return view('businesses.index');
    }

    /**
     * Fetch businesses data for DataTables.
     */
    public function getData()
    {
        try {
            $businesses = Business::select(['id', 'name', 'email', 'phone', 'logo', 'created_at']);
            return DataTables::of($businesses)
                ->addColumn('logo', function ($business) {
                    return $business->logo ? '<img src="' . asset("storage/" . $business->logo) . '" width="50" height="50" class="rounded">' : 'No Logo';
                })
                ->addColumn('actions', function ($business) {
                    return '
                        <a href="#" class="btn btn-sm btn-danger delete-business" data-url="' . route('businesses.destroy', $business->id) . '">Delete</a>
                        <a href="' . route('businesses.branches', $business->id) . '" class="btn btn-sm btn-info view-branches" data-url="' . route('businesses.branches', $business->id) . '">View Branches</a>
                    ';
                })
                ->rawColumns(['logo', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - Error fetching business data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
    }

    /**
     * Show the form to create a new business.
     */
    public function create()
    {
        return view('businesses.create');
    }

    /**
     * Display branches of a specific business.
     */
    public function branches(Request $request, $id)
    {
        try {
            $business = Business::findOrFail($id);
            return view('branches.index', compact('business'));
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - Error fetching branches: ' . $e->getMessage());
            return back()->with('error', 'Failed to retrieve branches.');
        }
    }

    /**
     * Store a newly created business in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:businesses,email',
                'phone' => 'required|string|unique:businesses,phone',
                'logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create and store the business
            $business        = new Business();
            $business->name  = $request->name;
            $business->email = $request->email;
            $business->phone = $request->phone;

            // Store logo if uploaded
            if ($request->hasFile('logo')) {
                $business->logo = $request->file('logo')->store('logos', 'public');
            }

            $business->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Business added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - Error storing business: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to add business.'], 500);
        }
    }

    /**
     * Remove the specified business from storage.
     */
    public function destroy($id)
    {
        try {
            $business = Business::findOrFail($id);

            // Delete business logo if exists
            if ($business->logo) {
                \Storage::delete('public/' . $business->logo);
            }

            // Delete the business (Cascade deletes branches)
            $business->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Business deleted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - Error deleting business: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete business.'], 500);
        }
    }
}
