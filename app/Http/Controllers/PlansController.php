<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Device;
use App\Models\UserSubscription;
use Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class PlansController extends Controller
{
  // pricing
  public function show(Request $request)
  {


  }

  public function create()
  {
    if (Auth::user()->user_type == 1){
      $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "/subscription/plan", 'name' => "Subscription Plan"], ['name' => "Create New Plan"]];
      return view('/content/plan/create', ['breadcrumbs' => $breadcrumbs]);
    }else{
      return redirect()->to('/dashboard');
    }
  }

  public function edit($plan_id)
  {
    $response = UserSubscription::getAllPlan();
    $data = array_filter($response['data'],function($element) use($plan_id) {
        return $element['id']==$plan_id;
    });
    // echo "<pre>"; print_r($data);die;
    $data = array_values($data);
    if (Auth::user()->user_type == 1){

      $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "/subscription/plan", 'name' => "Subscription Plan"], ['name' => "Edit Plan"]];
      return view('/content/plan/edit', ['breadcrumbs' => $breadcrumbs,'plan' => $data[0]]);
    }else{
      return redirect()->to('/dashboard');
    }
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'plan_name' => 'required',
      'amount' => 'required',
    ]);

    if ($validator->fails()) {
      flash()->error($validator->errors()->first());
      return redirect()->back();
    } else {
      $plan_code = str_replace(' ','-',$request->plan_name);
      $plan_code = str_replace('_','-',$plan_code);
      $plan_code = str_replace('&','-',$plan_code);
      $plan_code = str_replace('#','-',$plan_code);
      $plan_code = str_replace('.','-',$plan_code);
      $plan_code = str_replace('?','-',$plan_code);
      $plan_code = str_replace('/','-',$plan_code);
      $plan_code = str_replace('@','-',$plan_code);
      $plan_code = str_replace('(','-',$plan_code);
      $plan_code = str_replace(')','-',$plan_code);

      $postdata['product_id'] = env('PUBBLY_PRODUCT_ID');
      $postdata['plan_name'] = $request->plan_name;
      $postdata['plan_code'] = strtolower($plan_code);
      if($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1){
        $postdata['price'] = 0;
      }else{
        $postdata['price'] = $request->amount;
      }
      if($request->billing_cycle == 'specific'){
        $postdata['billing_cycle_num'] = $request->billing_cycle_num;
        $postdata['billing_period'] = $request->billing_period;
        $postdata['billing_period_num'] = $request->billing_period_num;
      }
      if(empty($request->billing_cycle_num)){
        $postdata['billing_cycle'] = 'lifetime';
      }else{
        $postdata['billing_cycle'] = $request->billing_cycle;
      }
      $postdata['plan_description'] =  $request->description;
      $postdata['plan_active'] = 'true';
      $postdata['plan_type'] = 'flat_fee';
      $postdata['payment_gateway'] = 'all';
      $postdata['redirect_url'] = env('APP_URL').'/thank-you';
      $postdata['specific_keep_live'] = 'live';
      $meta_data['is_credit'] = ($request->is_credit == 'on' || $request->is_credit == 1) ? 1 : 0;
      if(($request->is_credit == 'on' || $request->is_credit == 1) && ($request->is_credit_unlimited == 'off' || $request->is_credit_unlimited == 0)){
        $meta_data['credit_amount'] = $request->credit_amount;
      }
      $meta_data['is_credit_unlimited'] = ($request->is_credit_unlimited == 'on' || $request->is_credit_unlimited == 1) ? 1 : 0;

      $meta_data['is_free'] = $request->is_free == 'on' ? 1 : 0;
      if($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1){
        $meta_data['is_reseller_credit'] = ($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1) ? 1 : 0;
        $meta_data['reseller_credit'] = $request->amount;
      }
      $postdata['meta_data'] = $meta_data;
      $postdata['currency_code'] = env('CURRENCY_CODE');

      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/plan/create',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>json_encode($postdata),
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
      ),

      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $response = json_decode($response,true);
      if($response['status'] == 'success'){
        return response()->json(['success' => 1,'data' => $response]);
      }else{
        return response()->json(['success' => 0,'data' => $response]);
      }
    }
  }
  public function update(Request $request)
    {
        $id =  $request->plan_id;

        $validator = Validator::make($request->all(), [
          'plan_name' => 'required',
          'amount' => 'required',
        ]);

        if ($validator->fails()) {
          flash()->error($validator->errors()->first());
          return redirect()->back();
        } else {
          $plan_code = str_replace(' ','-',$request->plan_name);
          $plan_code = str_replace('_','-',$plan_code);
          $plan_code = str_replace('&','-',$plan_code);
          $plan_code = str_replace('#','-',$plan_code);
          $plan_code = str_replace('.','-',$plan_code);
          $plan_code = str_replace('?','-',$plan_code);
          $plan_code = str_replace('/','-',$plan_code);
          $plan_code = str_replace('@','-',$plan_code);
          $plan_code = str_replace('(','-',$plan_code);
          $plan_code = str_replace(')','-',$plan_code);

          $postdata['product_id'] = env('PUBBLY_PRODUCT_ID');
          $postdata['plan_name'] = $request->plan_name;
          $postdata['plan_code'] = strtolower($plan_code);
          if($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1){
            $postdata['price'] = 0;
          }else{
            $postdata['price'] = $request->amount;
          }
          if($request->billing_cycle == 'specific'){
            $postdata['billing_cycle_num'] = $request->billing_cycle_num;
            $postdata['billing_period'] = $request->billing_period;
            $postdata['billing_period_num'] = $request->billing_period_num;
          }
          if(empty($request->billing_cycle_num)){
            $postdata['billing_cycle'] = 'lifetime';
          }else{
            $postdata['billing_cycle'] = $request->billing_cycle;
          }
          $postdata['plan_description'] =  $request->description;
          $postdata['plan_active'] = 'true';
          $postdata['plan_type'] = 'flat_fee';
          $postdata['payment_gateway'] = 'all';
          $postdata['redirect_url'] = env('APP_URL').'/thank-you';
          $postdata['specific_keep_live'] = 'live';
          $meta_data['is_credit'] = ($request->is_credit == 'on' || $request->is_credit == 1) ? 1 : 0;
          if(($request->is_credit == 'on' || $request->is_credit == 1) && ($request->is_credit_unlimited == 'off' || $request->is_credit_unlimited == 0)){
            $meta_data['credit_amount'] = $request->credit_amount;
          }
          $meta_data['is_credit_unlimited'] = ($request->is_credit_unlimited == 'on' || $request->is_credit_unlimited == 1) ? 1 : 0;

          $meta_data['is_free'] = $request->is_free == 'on' ? 1 : 0;
          if($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1){
            $meta_data['is_reseller_credit'] = ($request->is_reseller_credit == 'on' || $request->is_reseller_credit == 1) ? 1 : 0;
            $meta_data['reseller_credit'] = $request->amount;
          }
          $postdata['meta_data'] = $meta_data;

          $curl = curl_init();

          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://payments.pabbly.com/api/v1/plan/update/'.$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>json_encode($postdata),
          CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json'
          ),

          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          $response = json_decode($response,true);
          if($response['status'] == 'success'){
            return response()->json(['success' => 1,'data' => $response]);
          }else{
            return response()->json(['success' => 0,'data' => $response]);
          }
        }
    }
  function checkFreePlan(){
    $response = UserSubscription::getAllPlan();
    $data = array_filter($response['data'],function($element) {
        return (isset($plan['meta_data']['is_free']) && $element['meta_data']['is_free']==1 && $element['plan_active']==true);
    });
    if($data){
      return response()->json(['success' => 1]);
    }else{
      return response()->json(['success' => 0]);
    }
  }
  function change_plan_status(Request $request)
  {
        if ($request->status == 0) {
            $status = 'true';
            $result['active'] = 'Plan Activate successfully.';
        } else {
            $status = 'false';
            $result['suspend'] = 'Plan Deactivated successfully.';
        }
        $id = $request->id;
	   $postdata['product_id'] = env('PUBBLY_PRODUCT_ID');
	  $postdata['plan_code'] = $id;
	  $postdata['plan_name'] = $request->name;
        $postdata['plan_active'] = $status;
        $curl = curl_init();

          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://payments.pabbly.com/api/v1/plan/update/'.$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>json_encode($postdata),
          CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json'
          ),

          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          $response = json_decode($response,true);
          if($response['status'] == 'success'){
			  $result['success'] = 1;
            return response()->json($result);
          }else{
            return response()->json(['success' => 0]);
          }
    }

    public function destroy($id)
    {
      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/plans/'.$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
      ),

      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $response = json_decode($response,true);
      if($response['status'] == 'success'){
        return response()->json(['success' => 1]);
      }else{
        return response()->json(['success' => 0]);
      }
    }

    public function subscriptionPlan(Request $request)
    {
      if(Auth::user()->user_type != 1 || (Auth::user()->user_type == 1 && isset($request->device_id))){

        $device_id = $request->device_id;
        $response = UserSubscription::getAllPlan();

        $usersSubscription = [];
        $customer_id = "";
        if($device_id){
          $device = Device::where('id', $device_id)->first();
			if($device){
				$checkDevice = Device::where('user_id',$device->user_id)->where('id',$device_id)->first();
				if($checkDevice){
				 	$User = User::where('user_id', $device->user_id)->first();
				}else{
					return redirect()->to('/dashboard');
				}
			}else{
				return redirect()->to('/dashboard');
			}
        }else{
          $User = User::where('user_id', Auth::user()->user_id)->first();
        }
        if($User){
          $customer_id = $User->customer_id;
          $usersSubscription = UserSubscription::where('device_id', $device_id)->where('status', 1)->first();
        }
        foreach($response['data'] as $key => $value){
            if($value['plan_active'] == 'true' && $value['meta_data']['is_credit'] == 0){
                if($usersSubscription && isset($request->device_id)){
                    if($usersSubscription->plan_id == $value['id']){
                        $response['data'][$key]['is_purchased'] =  1;
                    }else{
                        $response['data'][$key]['is_purchased'] =  0;
                    }
                }else{
                    $response['data'][$key]['is_purchased'] =  0;
                    // $response['data'][$key]['is_hide'] =  0;
                }
            }else{
                unset($response['data'][$key]);
            }
        }

        if (Auth::user()->user_type == 3){
          $response['data'] = array_filter($response['data'],function($element) {
              return (isset($element['meta_data']['is_reseller_credit']) && $element['meta_data']['is_reseller_credit']==1);
          });
        }else{
            $response['data'] = array_filter($response['data'], function ($plan) {
                return !isset($plan['meta_data']['is_reseller_credit']) || $plan['meta_data']['is_reseller_credit'] != 1;
            });
        }

        $planData = array_values($response['data']);
        array_multisort( array_column($planData, "price"), SORT_ASC, $planData );
        // echo "<pre>"; print_r($planData);die;
        $pageConfigs = ['pageHeader' => false];
        $setting = Setting::where("id", '1')->first();
        $free_devices = UserSubscription::where('user_id',$User->user_id)->where('plan_code','free')->count();
        return view('/content/plan/subscription-plan', ['pageConfigs' => $pageConfigs,'planData' => $planData,'device_id'=>$device_id,'customer_id'=>$customer_id,'user'=>$User,'setting'=>$setting,'free_devices'=>$free_devices ]);

      }else{

        $response = UserSubscription::getAllPlan();

        foreach($response['data'] as $key => $value){
          // if($value['meta_data']['is_credit'] == 1){
          //     unset($response['data'][$key]);
          // }
        }
      $planData = array_values($response['data']);
      $result = [];
      $i=0;
      foreach($planData as $value){
        $result[$i]['id'] = $value['id'];
        $result[$i]['plan_name'] = $value['plan_name'];
        $result[$i]['price'] = $value['price'];
        $result[$i]['billing_cycle'] = $value['billing_cycle'];
        $result[$i]['is_credit'] = $value['meta_data']['is_credit'];
        $result[$i]['is_for_reseller'] = isset($value['meta_data']['is_reseller_credit']) ? 1 : 0;
        $result[$i]['reseller_credit'] = isset($value['meta_data']['reseller_credit']) ? $value['meta_data']['reseller_credit'] : 0;
        $result[$i]['credit_amount'] = isset($value['meta_data']['credit_amount']) ? $value['meta_data']['credit_amount'] : "";
        $result[$i]['status'] = $value['plan_active'];
        $i++;
      }
      array_multisort( array_column($result, "price"), SORT_ASC, $result );

        if ($request->ajax()) {

          return DataTables::of($result)
          ->editColumn('plan_name', function ($row) {
            return $row['plan_name'];
          })
            ->editColumn('amount', function ($row) {
                if($row['reseller_credit']){
                    return $row['reseller_credit'].' Credit';
                }else{
                    return env('CURRENCY') . $row['price'];
                }
            })
            ->editColumn('billing_cycle', function ($row) {
              return ucfirst($row['billing_cycle']);
            })
            ->editColumn('is_for_reseller', function ($row) {
                if ($row['is_for_reseller'] == 1) {
                  $is_for_reseller =  "<span class='badge bg-light-success rounded-pill'>Yes</span>";
                } else {
                  $is_for_reseller = "<span class='badge bg-light-danger rounded-pill'>No</span>";

                }
                return $is_for_reseller;
              })
            ->editColumn('is_credit', function ($row) {
              if ($row['is_credit'] == 1) {
                $is_credit =  "<span class='badge bg-light-success rounded-pill'>Yes</span>";
              } else {
                $is_credit = "<span class='badge bg-light-danger rounded-pill'>No</span>";

              }
              return $is_credit;
            })
            ->editColumn('credit_amount', function ($row) {
              return ($row['is_credit'] == 1) ? $row['credit_amount'] : '-';
            })
            ->editColumn('status', function ($row) {
              if ($row['status'] == 'true') {
                $status = '<span class="btnOn">
                <label class="switch">
                    <input type="checkbox" checked value="1"
                        class="chkStatus" data-id="'. $row['id'].'" data-name="'. $row['plan_name'].'"/>
                    <span class="slider round"></span>
                </label>
            </span>';
              } else {
                $status = ' <span class="btnOn">
                <label class="switch">
                    <input type="checkbox" value="0" class="chkStatus" data-id="'. $row['id'].'" data-name="'. $row['plan_name'].'"/>
                    <span class="slider round"></span>
                </label>
            </span>';
              }
              return $status;
            })
            ->addColumn('action', function ($row) {
              $action = '<a href="' . route('plan.edit', $row['id']) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Edit Plan">
                            <i data-feather="edit"></i> </a> ';
                  // <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('plan.destroy', $row['id']) . '\', \'' . csrf_token() . '\')">  <span data-feather="trash"></span> </a>
              return $action;
            })
            ->rawColumns(['plan_name','amount','billing_cycle','is_for_reseller','is_credit','credit_amount', 'status', 'action'])
            ->make(true);
        }
        $pageConfigs = ['pageHeader' => false];

        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Plan List"]];

        return view('/content/plan/index', ['breadcrumbs' => $breadcrumbs]);
      }
    }

}
