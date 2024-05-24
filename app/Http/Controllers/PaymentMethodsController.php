<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\DeletionService;

class PaymentMethodsController extends Controller
{
    protected $workspace;
    protected $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->workspace = Workspace::find(session()->get('workspace_id'));
            $this->user = getAuthenticatedUser();
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $payment_methods = $this->workspace->payment_methods();
        $payment_methods = $payment_methods->count();
        return view('payment_methods.list', ['payment_methods' => $payment_methods]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:payment_methods,title', // Validate the title
        ]);
        $formFields['workspace_id'] = $this->workspace->id;

        if (PaymentMethod::create($formFields)) {
            return response()->json(['error' => false,'message'=>'Payment method created successfully.']);
        } else {
            return response()->json(['error' => true, 'message' => 'Payment method couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $payment_methods = $this->workspace->payment_methods();
        if ($search) {
            $payment_methods = $payment_methods->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $payment_methods->count();
        $payment_methods = $payment_methods->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(
                fn ($payment_method) => [
                    'id' => $payment_method->id,
                    'title' => $payment_method->title
                ]
            );

        return response()->json([
            "rows" => $payment_methods->items(),
            "total" => $total,
        ]);
    }

    public function get($id)
    {
        $pm = PaymentMethod::findOrFail($id);
        return response()->json(['pm' => $pm]);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'title' => 'required|unique:payment_methods,title,' . $request->id,
        ]);
        $pm = PaymentMethod::findOrFail($request->id);

        if ($pm->update($formFields)) {
            return response()->json(['error' => false,'message'=>'Payment method updated successfully.']);
        } else {
            return response()->json(['error' => true, 'message' => 'Payment method couldn\'t updated.']);
        }
    }

    public function destroy($id)
    {
        $pm = PaymentMethod::findOrFail($id);
        $pm->payslips()->update(['payment_method_id' => 0]);
        $response = DeletionService::delete(PaymentMethod::class, $id, 'Payment method');
        return $response;
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:payment_methods,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $pm = PaymentMethod::findOrFail($id);
            $pm->payslips()->update(['payment_method_id' => 0]);
            DeletionService::delete(PaymentMethod::class, $id, 'Payment method');
        }

        return response()->json(['error' => false, 'message' => 'Payment method(s) deleted successfully.']);
    }
}
