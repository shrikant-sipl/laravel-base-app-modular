<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Customer\Entities\Customer;
use Dotenv\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct()
    {
        //Auth middleware
        $this->middleware('auth');
        //Apply input cleaning middleware before processing any request
        $this->middleware('clean-input');
    }

    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $columns = ['id',
                DB::raw('concat(first_name, " ", last_name) As full_name'),
                'email', 'gender',
                'mobile',
                'created_at'
            ];

            //Get all customers
            $data['customers'] = Customer::select($columns)->paginate(5);

            //Render the view to show customer listing
            return view('customer::index', $data);
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.index')->with('failure', $ex->getMessage());
        }
    }

    /*
     * Show the form for creating a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data['customer'] = [];

            // Get validation messages
            $data['validationMessages'] = Customer::$validationMessages;

            //Render the customer add form
            return view('customer::add', $data);
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.index')->with('failure', $ex->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            //Server side validations
            $validator = \Validator::make($data, Customer::validationRules(), Customer::$validationMessages);

            if ($validator->fails()) {
                //Redirect user back with input if server side validation fails
                return redirect()->route('manage-customer.create')->withErrors($validator)->withInput();
            }

            $customer = new Customer();
            $customer->first_name   = $request->input('first_name');
            $customer->last_name    = $request->input('last_name');
            $customer->gender       = $request->input('gender');
            $customer->email        = $request->input('email');
            $customer->mobile       = $request->input('mobile');

            //Save customer details
            if ($customer->save()) {
                return redirect()->route('manage-customer.index')->with('success', 'Customer created successfully');
            } else {
                return redirect()->route('manage-customer.index')->with('failure', config('app.messages.default_error'));
            }
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.create')->with('failure', $ex->getMessage());
        }
    }

    /**
     * Display the specified resource
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data['customer'] = Customer::findOrFail($id);
            return view('customer::show', $data);
        } catch(ModelNotFoundException $ex) {
            return redirect()->route('manage-customer.index')->with('failure', 'This customer is not available');
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.index')->with('failure', $ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Get validation messages
            $data['validationMessages'] = Customer::$validationMessages;
            $data['customer'] = Customer::findOrFail($id);
            //Render the customer edit form
            return view('customer::add', $data);
        } catch(ModelNotFoundException $ex) {
            return redirect()->route('manage-customer.index')->with('failure', 'This customer is not available');
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.index')->with('failure',  $ex->getMessage());
        }
    }

    /**
     * Update the specified resource in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();

            $validator = \Validator::make($data, Customer::validationRulesForUpdate($id), Customer::$validationMessages);

            if ($validator->fails()) {
                //Show error message
                return redirect()->route('manage-customer.edit', $id)->withErrors($validator)->withInput();
            }
            $customer = Customer::findOrFail($id);
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->gender = $request->gender;
            $customer->email = $request->email;
            $customer->mobile = $request->mobile;
            //Save customer details
            if ($customer->save()) {
                return redirect()->route('manage-customer.index')->with('success', 'Customer updated successfully');
            } else {
                return redirect()->route('manage-customer.index')->with('failure', config('app.messages.default_error'));
            }
        } catch(ModelNotFoundException $ex) {
            return redirect()->route('manage-customer.index')->with('failure', 'This customer is not available');
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.edit', $id)->with('failure', $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if(Customer::destroy($id)) {
                return redirect()->route('manage-customer.index')->with('success', 'Customer deleted successfully');
            } else {
                return redirect()->route('manage-customer.index')->with('failure', config('app.messages.default_error'));
            }
        } catch (\Exception $ex) {
            return redirect()->route('manage-customer.index')->with('failure', $ex->getMessage());
        }
    }

    /**
     * Check duplicate email
     *
     * @param Request $request
     */
    public function checkEmail(Request $request, $id) {
        if($request->ajax()) {
            try{
                $email = $request->email;

                if (!empty($email)) {
                    if(isset($id) && $id > 0) {
                        $userEmail = Customer::where('email', $email)->where('id', '!=', $id)->count();
                    } else {
                        $userEmail = Customer::where('email', $email)->count();
                    }

                    if ($userEmail > 0) {
                        abort(404);
                    }
                }

                return response(\Helpers::sendSuccessAjaxResponse('success'), 200);
            } catch (\Exception $ex) {
                return response(\Helpers::sendFailureAjaxResponse(), 500);
            }
        } else {
            return view('errors.404');
        }
    }
}
