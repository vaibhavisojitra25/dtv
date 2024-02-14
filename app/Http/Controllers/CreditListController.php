<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\CreditHistory;
use Yajra\DataTables\DataTables;
use App\Helper\Helper;
use Auth;
use File;
use DB;
use Storage;
use Carbon\Carbon;
use Validator;
use App\Mail\CreditAddDeduct;
use App\Mail\SendInvoice;

class CreditListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = CreditHistory::where('user_id',Auth::user()->user_id)->with('device_code')->with('added_by_user')->with('credited_to_user');

            $startdate = strtotime($request->startdate);
            if ($request->added_by) {
              $query->where('added_by',$request->added_by);
            }
            if ($request->credited_to) {
                $query->where('credited_to',$request->credited_to);
              }
              if (isset($request->is_credited)) {
                $query->where('is_credited',$request->is_credited);
              }
            if ($startdate) {
                $query->whereDate('created_at','=' ,$request->startdate)->latest()->get();
            }
            $device = $query->orderBy('created_at', 'desc')->get();

            $response = UserSubscription::getAllPlan();

            return DataTables::of($device)
                ->editColumn('credits', function ($row) {
                    if($row->credits == -1){
                        $credits = "Unlimited";
                    }else{
                        $credits = $row->credits;
                    }
                    return $credits;
                })
                ->editColumn('is_credited', function ($row) {
                    if($row->is_credited == 1){
                        $is_credited = "<span class='badge bg-light-success rounded-pill'>Credited</span>";
                    }else{
                        $is_credited = "<span class='badge bg-light-danger rounded-pill'>Deduct</span>";
                    }
                    return $is_credited;
                })
                ->editColumn('added_by', function ($row) {
                    if($row->added_by_user){
                        return $row->added_by_user->first_name.' '.$row->added_by_user->last_name;
                    }
                })
                ->editColumn('credited_to', function ($row) {
                    if($row->credited_to_user){
                        return $row->credited_to_user->first_name.' '.$row->credited_to_user->last_name;
                    }
                })
                ->editColumn('device_code', function ($row) {
                    return $row->device_code ? $row->device_code->code : "";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row['created_at'])->setTimezone('Asia/Kolkata')->format('d-m-Y h:i:s');
                })
                ->editColumn('plan_name', function ($row) use ($response) {

                    $plan_id = $row->plan_id;
                    $data = array_filter($response['data'], function ($element) use ($plan_id) {
                      return $element['id'] == $plan_id;
                    });
                    $data = array_values($data);
                    $data =  $data ? $data[0] : "";

                    return $data ? $data['plan_name'] : "-";
                })
                ->rawColumns(['is_credited','added_by','credited_to','device_code','plan_name','created_at'])
                ->make(true);
        }
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Credit History"]];
        $planData = UserSubscription::getAllPlan();
        $plan_data = array_filter($planData['data'],function($element) {
            return $element['meta_data']['is_credit']==1;
        });

        // echo "<pre>"; print_r($plan_data);die;
        $plan_data = array_values($plan_data);
        $total_credits = CreditHistory::where('is_credited',1)->sum('credits');
        $users = User::where('added_by',Auth::user()->user_id)->where('is_verified',1)->where('status',1)->get();
        return view('/content/credits/index', ['breadcrumbs' => $breadcrumbs,'users'=>$users,'plan_data'=>$plan_data,'total_credits'=>$total_credits]);
    }

    public function updateUserCredits(Request $request)
    {
        $user_id =  $request->user_id;
        if($request->is_unlimited_credit == 0){
            $validator = Validator::make($request->all(), [
                'credits' => 'required',
            ]);

            if ($validator->fails()) {
                $response['success'] = 0;
                $response['message'] = $validator->errors()->first();
                return response()->json($response, 200);
            }
        }

        if($request->flag==1){
            $user = User::where('user_id',$user_id)->where('user_type','!=',1)->increment('credits', $request->credits);
            User::where('user_id',$request->auth_user_id)->where('user_type','!=',1)->where('credits', '>', 0)->decrement('credits', $request->credits);
            $admin_credit_history = new CreditHistory;
            $admin_credit_history->credits = $request->credits;
            $admin_credit_history->user_id = $request->auth_user_id;
            $admin_credit_history->is_credited = 0;
            $admin_credit_history->credited_to = $user_id;
            $admin_credit_history->added_by = $request->auth_user_id;
            $admin_credit_history->save();

            $credit_history = new CreditHistory;
            $credit_history->credits = $request->credits;
            $credit_history->user_id = $user_id;
            $credit_history->is_credited = 1;
            $credit_history->added_by = $request->auth_user_id;
            $credit_history->save();

            $authuser = User::where('user_id',$user_id)->where('user_type','!=',1)->first();
            $data['auth_credit'] = User::where('user_id',$request->auth_user_id)->first()->credits;
            try{
                \Mail::to($authuser->email)->send(new CreditAddDeduct($authuser,$request->credits,1));

                if (\Mail::failures()) {
                       return response()->json(['success' => 0,'message' => 'Credit added But unable to send mail','data' => $data]);
                }
            }catch (\Exception $e) {
                   return response()->json(['success' => 0,'message' => 'Credit added But unable to send mail','data' => $data]);
            }

            return response()->json(['success' => 1,'data' => $data]);
        }elseif($request->flag==2){
            $user = User::where('user_id',$user_id)->where('user_type','!=',1)->where('credits', '>', 0)->decrement('credits', $request->credits);
            // if($request->auth_user_type != 1){
                $userData = User::where('user_id',$user_id)->first();
                User::where('user_id',$userData->added_by)->where('user_type','!=',1)->increment('credits', $request->credits);

                $admin_credit_history = new CreditHistory;
                $admin_credit_history->credits = $request->credits;
                $admin_credit_history->user_id = $userData->added_by;
                $admin_credit_history->is_credited = 1;
                $admin_credit_history->added_by = $user_id;
                $admin_credit_history->save();
            // }

            $credit_history = new CreditHistory;
            $credit_history->credits = $request->credits;
            $credit_history->user_id = $user_id;
            $credit_history->is_credited = 0;
            $credit_history->added_by = $request->auth_user_id;
            $credit_history->credited_to = $user_id;
            $credit_history->save();
            $authuser = User::where('user_id',$user_id)->where('user_type','!=',1)->first();
            try{
                \Mail::to($authuser->email)->send(new CreditAddDeduct($authuser,$request->credits,2));

                if (\Mail::failures()) {
                    return response()->json(['success' => 0,'message' => 'Credit deducted But unable to send mail']);
                }
            }catch (\Exception $e) {
                return response()->json(['success' => 0,'message' => 'Credit deducted But unable to send mail']);
            }
            return response()->json(['success' => 1]);
        }else{
            if($request->is_unlimited_credit == 1){
                $user = User::where('user_id',$user_id)->where('user_type','!=',1)->where('unlimited_credits', '>', 0)->decrement('unlimited_credits', $request->plan_amount);
            }else{
                $user = User::where('user_id',$user_id)->where('user_type','!=',1)->where('credits', '>', 0)->decrement('credits', $request->plan_amount);
            }
            if($request->plan_id){
                $credit_history = new CreditHistory;
                $credit_history->credits = $request->plan_amount;
                $credit_history->user_id = $request->user_id;
                $credit_history->device_id = $request->device_id;
                $credit_history->plan_id = $request->plan_id;
                $credit_history->is_credited = 0;
                $credit_history->credited_to = $request->user_id;
                $credit_history->added_by = $request->user_id;
                $credit_history->save();
            }

            return response()->json(['success' => 1,'credit_history_id'=>$credit_history->id]);
        }
    }
}
