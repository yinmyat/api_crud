<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = CustomerResource::collection($this->service->getAllCustomer());

        return response()->json([
            'status' => true,
            'title' =>  "Success",
            'data' => $data
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['status' => false,'message'=>'Validation Fails!Please Check your data!'],422);
        }

        DB::beginTransaction();

        try{

            $input['name'] = $request->name;

            $input['image'] =$request->file('image') ?? null;

            $data = $this->service->createCustomer($input);

            DB::commit();

        }catch(Exception $e){

            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => "Fails! Please check your data!!!" ,

            ],400);
        }

        return response()->json([
            'status' => true,
            'title' =>  "Success",
            'message' =>  "Customer Info stored successully!",
            'data' => new CustomerResource($data)
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['status' => false,'message'=>'Validation Fails!Please Check your data!'],422);
        }

        $row = $this->service->getCustomerByUuid($uuid);

        DB::beginTransaction();

        try{

            $data = $this->service->customerUpdate($request->only('name','image'),$row->id);

            DB::commit();

        }catch(Exception $e){

            DB::rollback();

            return response()->json([
                'status' => false,
                'title' => 'Fails',
                'message' => 'Cannot update info. Please check again!' ,

            ],400);
        }

        return response()->json([
            'status' => true,
            'title' => "Success",
            'message' => "Customer Info Updated Successfully.",
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $this->service->deleteCustomer($uuid);

        \DB::beginTransaction();
        try{

            \DB::commit();

        }catch(\Exception $e){

            \DB::rollback();

            return response()->json([
                'status' => false,
                'message' => "Fails! Please Check again!"
            ],400);
        }

        return response()->json([
            'status' => true,
            'title' => "Success",
            'message' => "Selected Customer Deleted Successfully!"
        ],200);
    }
}
