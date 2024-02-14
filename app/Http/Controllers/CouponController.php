<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    { 
        $coupon = Coupon::where('parent_id', 0);
    
        $coupons = $coupon->latest()->get();
        if ($request->ajax()) {

        return DataTables::of($coupons)
        ->editColumn('checkbox', function ($row) {
            return '<input type="checkbox" name="coupon_checkbox[]" class="coupon_checkbox" value="'.$row->id.'" />';
        })
        ->addColumn('used', function ($row) {
            $used = $row->is_used($row->id);
            return $used;
        })
        ->editColumn('code', function ($row) {
            $code = "-";
            if(!empty($row->code)){
                $code = $row->code;
            }
            return $code;
        })
        ->editColumn('limit', function ($row) {
            $limit = $row->limit;
            if($row->limit == -1){
                $limit = "-";
            }
            return $limit;
        })
        ->editColumn('status', function ($row) {
            if ($row->status == 1) {
                $status = '<span class="btnOn">
                <label class="switch">
                    <input type="checkbox" checked value="' . $row->id . '"
                        class="chkStatus" />
                    <span class="slider round"></span>
                </label>
            </span>';
            } else {
                $status = ' <span class="btnOn">
                <label class="switch">
                    <input type="checkbox" value="' . $row->id . '" class="chkStatus" />
                    <span class="slider round"></span>
                </label>
            </span>';
            }
            return $status;
        })
            ->addColumn('action', function ($row) {
            $action = '
            <a href="' . route('couponshow', $row->id) . '" class="btn btn-icon btn-info waves-effect waves-float waves-light">
            <i data-feather="eye"></i> </a>
                            <a href="' . route('coupon.edit', $row->id) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Edit Coupon">
                            <i data-feather="edit"></i> </a> <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('coupon.destroy', $row->id) . '\', \'' . csrf_token() . '\')">
                            <span data-feather="trash"></span>
                        </a>';
            return $action;
            })
            ->rawColumns(['checkbox','code', 'used', 'status', 'action'])
            ->make(true);
        }
        $pageConfigs = ['pageHeader' => false];
    
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Coupons List"]];
        if(Auth::user()->user_type == 1){
            return view('/content/coupon/index', ['breadcrumbs' => $breadcrumbs]);
        }else{
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = UserSubscription::getAllPlan();
      // echo "<pre>";
      // print_r($response);
      // die;
      $usersSubscription = [];
      foreach($response['data'] as $key => $value){
          if($value['plan_active'] == 'true'){
              if($usersSubscription){
                  if($usersSubscription->plan_code == $value['plan_code']){
                      $response['data'][$key]['is_purchased'] =  1;
                  }else{
                      $response['data'][$key]['is_purchased'] =  0;
                  }
                  if($value['plan_code'] == 'free'){
                      unset($response['data'][$key]);
                      // $response['data'][$key]['is_hide'] =  1;
                  }else{
                      // $response['data'][$key]['is_hide'] =  0;
                  }
              }else{
                  $response['data'][$key]['is_purchased'] =  0;
                  // $response['data'][$key]['is_hide'] =  0;
              }
          }else{
              unset($response['data'][$key]);
          }
        }
        $planData = array_values($response['data']);
        if(Auth::user()->user_type == 1){
            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "coupon/list", 'name' => "Coupon List"], ['name' => "Add New Coupon"]];
            return view('/content/coupon/create',  ['breadcrumbs' => $breadcrumbs, 'planData' => $planData, 'r' => new Coupon() , 'flag' => 'create']);
        }else{
            return redirect()->to('/dashboard');
        }
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function generateCouponCode(){
        $coupon = new Coupon();
        $coupon->code = $coupon->get_random_string(); 
        return response()->json(['code' => $coupon->code]);
     }
     public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_name' => 'required',
            'plan_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        $coupon = new Coupon();
        $coupon->name = $request->coupon_name;
        $coupon->plan_id = $request->plan_id;
       
        if($request->is_limit == 0){
            $coupon->parent_id =  0;
            $coupon->limit = -1;
            $coupon->code = $request->code;
        }
        if($request->is_limit == 1){
            $coupon->parent_id =  0;
            $coupon->limit = $request->limit;
        }
        $coupon->save();
        if($request->is_limit == 1){
            for($i=1; $i<=$request->limit; $i++){
                $coupons = new Coupon();
                $coupons->parent_id =  $coupon->id;
                $coupons->code = $coupons->get_random_string();
                $coupons->save();
            }
        }
        return response()->json(['success' => 1]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $r = Coupon::find($id);
        $planData = UserSubscription::getAllPlan();
        $plan_id = $r->plan_id; 
     
        $data = array_filter($planData['data'],function($element) use($plan_id) {
            return $element['id']==$plan_id;
        });
        $data = array_values($data);
        $r->plan_code = $data ? $data[0]['plan_code'] : "";
        if(Auth::user()->user_type == 1){
            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "coupon/list", 'name' => "Coupon List"], ['name' => "Show Coupon"]];
            return view('/content/coupon/create',  ['breadcrumbs' => $breadcrumbs,'r' => $r, 'flag' => 'show']);
        }else{
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $r = Coupon::find($id);
        $response = UserSubscription::getAllPlan();
        // echo "<pre>";
        // print_r($response);
        // die;
        $usersSubscription = [];
        
        
        foreach($response['data'] as $key => $value){
            if($value['plan_active'] == 'true'){
                if($usersSubscription){
                    if($usersSubscription->plan_code == $value['plan_code']){
                        $response['data'][$key]['is_purchased'] =  1;
                    }else{
                        $response['data'][$key]['is_purchased'] =  0;
                    }
                    if($value['plan_code'] == 'free'){
                        unset($response['data'][$key]);
                        // $response['data'][$key]['is_hide'] =  1;
                    }else{
                        // $response['data'][$key]['is_hide'] =  0;
                    }
                }else{
                    $response['data'][$key]['is_purchased'] =  0;
                    // $response['data'][$key]['is_hide'] =  0;
                }
            }else{
                unset($response['data'][$key]);
            }
        }
        $planData = array_values($response['data']);
       
        if(Auth::user()->user_type == 1){
            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "coupon/list", 'name' => " Coupon List"], ['name' => "Edit"]];
            return view('/content/coupon/create',  ['breadcrumbs' => $breadcrumbs, 'planData' => $planData,'r' => $r, 'flag' => 'edit']);
        }else{
            return redirect()->to('/dashboard');
        }
    }

    public function change_coupon_status($id, Request $request)
    {
        if($request->has_limit == 1){
            $obj = Coupon::find($id);
        }else{
            $obj = Coupon::find($id);
        }
        if ($obj->status == '0') {
            $obj->status = '1';
            $obj->save();
            if($obj->limit != -1)
            {
                $obj = Coupon::where('parent_id', $id)->update(['status'=>1]);
            }
            return response()->json(['active' => 'Activated successfully.']);
        } else {
            $obj->status = '0';
            $obj->save();
            if($obj->limit != -1)
            {
                $obj = Coupon::where('parent_id', $id)->update(['status'=>0]);
            }
            return response()->json(['suspend' => 'Suspended successfully.']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'coupon_name' => 'required',
            'plan_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        $coupon = Coupon::find($id);
        $coupon->name = $request->coupon_name;
        $coupon->plan_id = $request->plan_id;
        $coupon->save();
        return response()->json(['success' => 1]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Coupon = Coupon::where('parent_id',$id)->delete();
        $Coupon1 = Coupon::findOrFail($id);
        $Coupon1->delete();
        return response()->json(['success' => 1]);
    }

    public function deleteAll(Request $request)  
    {  
        $ids = $request->ids;  
        Coupon::whereIn('id',$ids)->delete(); 
        Coupon::whereIn('parent_id',$ids)->delete();
        return response()->json(['success' => 1]);  
    }  
}
