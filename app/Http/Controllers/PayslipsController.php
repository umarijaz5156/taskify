<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payslip;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\DeletionService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PayslipsController extends Controller
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
        $payslips = $this->user->hasRole('admin') ? $this->workspace->payslips() : $this->user->payslips();
        $payslips = $payslips->count();
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        return view('payslips.list', ['payslips' => $payslips, 'users' => $users, 'clients' => $clients]);
    }

    public function create(Request $request)
    {
        $users = $this->workspace->users;
        $payment_methods = $this->workspace->payment_methods;
        $allowances = $this->workspace->allowances;
        $deductions = $this->workspace->deductions;
        return view('payslips.create', ['users' => $users, 'payment_methods' => $payment_methods, 'allowances' => $allowances, 'deductions' => $deductions]);
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'user_id' => ['required'],
            'month' => ['required'],
            'basic_salary' => ['required'],
            'working_days' => ['required'],
            'lop_days' => ['required'],
            'paid_days' => ['required'],
            'bonus' => ['required'],
            'incentives' => ['required'],
            'leave_deduction' => ['required'],
            'ot_hours' => ['required'],
            'ot_rate' => ['required'],
            'ot_payment' => ['required'],
            'total_allowance' => ['required'],
            'total_deductions' => ['required'],
            'total_earnings' => ['required'],
            'net_pay' => ['required'],
            'payment_method_id' => ['nullable'],
            'payment_date' => ['nullable'],
            'status' => ['required'],
            'note' => ['nullable']
        ]);

        $payment_date = $request->input('payment_date');

        if (!empty($payment_date)) {
            $payment_date = Carbon::parse($payment_date);
            // $payment_date->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
            $formFields['payment_date'] = format_date($payment_date, null, 'Y-m-d');
        }
        $formFields['workspace_id'] = $this->workspace->id;
        $formFields['created_by'] = isClient() ? 'c_' : 'u_' . $this->user->id;
        $allowance_ids = $request->input('allowances') ?? [];
        $deduction_ids = $request->input('deductions') ?? [];
        if ($payslip = Payslip::create($formFields)) {
            $payslip->allowances()->attach($allowance_ids);
            $payslip->deductions()->attach($deduction_ids);
            Session::flash('message', 'Payslip created successfully.');
            return response()->json(['error' => false]);
        } else {
            return response()->json(['error' => true, 'message' => 'Payslip couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $status = (request('status')) ? request('status') : "";
        $user_id = (request('user_id')) ? request('user_id') : "";
        $created_by = (request('created_by')) ? request('created_by') : "";
        $month = (request('month')) ? request('month') : "";
        $where = ['payslips.workspace_id' => $this->workspace->id];

        if ($status != '') {
            $where['status'] = $status;
        }

        $payslips = Payslip::select(
            'payslips.*',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'payment_methods.title as payment_method'
        )
            ->leftJoin('users', 'payslips.user_id', '=', 'users.id')
            ->leftJoin('payment_methods', 'payslips.payment_method_id', '=', 'payment_methods.id');



        if (!$this->user->hasRole('admin')) {
            $payslips = $payslips->where('payslips.user_id', $this->user->id)
                ->orWhere('payslips.created_by', 'u_' . $this->user->id);
        }

        if ($user_id) {
            $where['user_id'] = $user_id;
        }

        if ($created_by) {
            $where['created_by'] = 'u_' . $created_by;
        }
        if ($month) {
            $where['month'] = $month;
        }
        if ($search) {
            $payslips = $payslips->where(function ($query) use ($search) {
                $query->where('payslips.id', 'like', '%' . $search . '%')
                    ->orWhere('payslips.note', 'like', '%' . $search . '%')
                    ->orWhere('payslips.payment_method', 'like', '%' . $search . '%');
            });
        }

        $payslips->where($where);
        $total = $payslips->count();

        $payslips = $payslips->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($payslip) {
                $creator = User::find(substr($payslip->created_by, 2)); // Remove the 'u_' prefix
                if ($creator !== null) {
                    $creator = $creator->first_name . ' ' . $creator->last_name;
                } else {
                    $creator = '-';
                }
                $month = Carbon::parse($payslip->month);
                $payment_date = $payslip->payment_date !== null ? Carbon::parse($payslip->payment_date) : '';
                return [
                    'id' => $payslip->id,
                    'user' => $payslip->user_name,
                    'payment_method' => $payslip->payment_method,
                    'month' => $month->format('F, Y'),
                    'working_days' => $payslip->working_days,
                    'lop_days' => $payslip->lop_days,
                    'paid_days' => $payslip->paid_days,
                    'basic_salary' => $payslip->basic_salary,
                    'leave_deduction' => $payslip->leave_deduction,
                    'ot_hours' => $payslip->ot_hours,
                    'ot_rate' => $payslip->ot_rate,
                    'ot_payment' => $payslip->ot_payment,
                    'total_allowance' => $payslip->total_allowance,
                    'incentives' => $payslip->incentives,
                    'bonus' => $payslip->bonus,
                    'total_earnings' => $payslip->total_earnings,
                    'total_deductions' => $payslip->total_deductions,
                    'net_pay' => $payslip->net_pay,
                    'payment_date' => $payment_date != '' ? format_date($payment_date) : '-',
                    'status' => $payslip->status == 1 ? '<span class="badge bg-success">' . get_label('paid', 'Paid') . '</span>' : '<span class="badge bg-danger">' . get_label('unpaid', 'Unpaid') . '</span>',
                    'note' => $payslip->note,
                    'created_by' => $creator,
                    'created_at' => format_date($payslip->created_at, 'H:i:s'),
                    'updated_at' => format_date($payslip->updated_at, 'H:i:s'),
                ];
            });


        return response()->json([
            "rows" => $payslips->items(),
            "total" => $total,
        ]);
    }

    public function edit(Request $request, $id)
    {

        $payslip = Payslip::select(
            'payslips.*',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'payment_methods.title as payment_method'
        )->where('payslips.id', '=', $id)
            ->leftJoin('users', 'payslips.user_id', '=', 'users.id')
            ->leftJoin('payment_methods', 'payslips.payment_method_id', '=', 'payment_methods.id')->first();

        $creator = User::find(substr($payslip->created_by, 2));
        if ($creator !== null) {
            $payslip->creator = $creator->first_name . ' ' . $creator->last_name;
        } else {
            $payslip->creator = ' -';
        }
        $users = $this->workspace->users;
        $payment_methods = $this->workspace->payment_methods;
        $allowances = $this->workspace->allowances;
        $deductions = $this->workspace->deductions;
        return view('payslips.update', ['payslip' => $payslip, 'users' => $users, 'payment_methods' => $payment_methods, 'allowances' => $allowances, 'deductions' => $deductions]);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'user_id' => ['required'],
            'month' => ['required'],
            'basic_salary' => ['required'],
            'working_days' => ['required'],
            'lop_days' => ['required'],
            'paid_days' => ['required'],
            'bonus' => ['required'],
            'incentives' => ['required'],
            'leave_deduction' => ['required'],
            'ot_hours' => ['required'],
            'ot_rate' => ['required'],
            'ot_payment' => ['required'],
            'total_allowance' => ['required'],
            'total_deductions' => ['required'],
            'total_earnings' => ['required'],
            'net_pay' => ['required'],
            'payment_method_id' => ['nullable'],
            'payment_date' => ['nullable'],
            'status' => ['required'],
            'note' => ['nullable']
        ]);

        $payment_date = $request->input('payment_date');

        if (!empty($payment_date)) {
            // $payment_date = Carbon::parse($payment_date);
            // $payment_date->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
            $formFields['payment_date'] = format_date($payment_date, null, 'Y-m-d');
        }

        $formFields['workspace_id'] = $this->workspace->id;
        $formFields['created_by'] = isClient() ? 'c_' : 'u_' . $this->user->id;
        $allowance_ids = $request->input('allowances') ?? [];
        $deduction_ids = $request->input('deductions') ?? [];

        // Find the Payslip by its ID
        $payslip = Payslip::findOrFail($request->input('id'));

        // Update the Payslip attributes
        $payslip->update($formFields);

        // Sync the related allowances and deductions
        if (!empty($allowance_ids)) {
            $payslip->allowances()->sync($allowance_ids);
        }
        if (!empty($deduction_ids)) {
            $payslip->deductions()->sync($deduction_ids);
        }

        Session::flash('message', 'Payslip updated successfully.');
        return response()->json(['error' => false]);
    }

    public function view(Request $request, $id)
    {
        $payslip = Payslip::select(
            'payslips.*',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'users.email as user_email',
            'payment_methods.title as payment_method'
        )->where('payslips.id', '=', $id)
            ->leftJoin('users', 'payslips.user_id', '=', 'users.id')
            ->leftJoin('payment_methods', 'payslips.payment_method_id', '=', 'payment_methods.id')->first();


        // The ID corresponds to a user
        $creator = User::find(substr($payslip->created_by, 2)); // Remove the 'u_' prefix
        if ($creator !== null) {
            $payslip->creator = $creator->first_name . ' ' . $creator->last_name;
        } else {
            $payslip->creator = ' -';
        }
        $payslip->month = Carbon::parse($payslip->month);
        $payment_date = $payslip->payment_date !== null ? Carbon::parse($payslip->payment_date) : '';
        $payment_date = $payment_date != '' ? format_date($payment_date) : '-';
        $payslip->payment_date = $payment_date;
        // dd($payslip->payment_date);
        $payslip->status = $payslip->status == 1 ? '<span class="badge bg-success">' . get_label('paid', 'Paid') . '</span>' : '<span class="badge bg-danger">' . get_label('unpaid', 'Unpaid') . '</span>';
        return view('payslips.view', compact('payslip'));
    }


    public function destroy($id)
    {
        $payslip = Payslip::findOrFail($id);
        $payslip->allowances()->detach();
        $payslip->deductions()->detach();
        $response = DeletionService::delete(Payslip::class, $id, 'Payslip');
        return $response;
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:payslips,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $payslip = Payslip::findOrFail($id);
            $payslip->allowances()->detach();
            $payslip->deductions()->detach();
            DeletionService::delete(Payslip::class, $id, 'Payslip');
        }

        return response()->json(['error' => false, 'message' => 'Payslip(s) deleted successfully.']);
    }

    public function duplicate($id)
    {
        $relatedTables = ['deductions', 'allowances']; // Include related tables as needed

        // Use the general duplicateRecord function
        $duplicate = duplicateRecord(Payslip::class, $id, $relatedTables);

        if (!$duplicate) {
            if (request()->has('reload') && request()->input('reload') === 'true') {
                Session::flash('error', 'Payslip duplication failed.');
            }
            return response()->json(['error' => true, 'message' => 'Payslip duplication failed.']);
        }
        if (request()->has('reload') && request()->input('reload') === 'true') {
            Session::flash('message', 'Payslip duplicated successfully.');
        }
        return response()->json(['error' => false, 'message' => 'Payslip duplicated successfully.']);
    }
}
