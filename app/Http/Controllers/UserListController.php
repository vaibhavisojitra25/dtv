<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Device;
use App\Models\DeviceCode;
use App\Models\Playlist;
use App\Models\DevicePlaylist;
use App\Models\Plan;
use App\Mail\VerifyEmail;
use App\Models\VerifyUser;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\Rule;
use Auth;
use Validator;
use DB;

class UserListController extends Controller
{

    public function show(Request $request)
    {
        \DB::enableQueryLog();
        $query = User::where('user_type','!=','1');
        if (!empty($request->status)) {
            if ($request->status == 2) {
                $request->status = 0;
            }
            $query->where('status', $request->status);
        }
        if (!empty($request->is_verified)) {
            if ($request->is_verified == 2) {
                $request->is_verified = 0;
            }
            $query->where('is_verified', $request->is_verified);
        }
        if(Auth::user()->user_type == 1){
            if (!empty($request->user_type)) {
                $query->where('user_type', $request->user_type);
            }
        }else{
            $query->where('user_type', 4);
        }
        if(Auth::user()->user_type != 1){
            $query = $query->where('added_by',Auth::user()->user_id);
        }
        $users = $query->latest();
        // $queries = DB::getQueryLog();

        // dd($queries);

        if ($request->ajax()) {
            return DataTables::eloquent($users)
                ->editColumn('checkbox', function ($row) {
                    $is_subreseller = 0;
                    if($row->user_type == 3){
                        $subreseller = User::where('user_type',4)->where('added_by',$row->user_id)->get();
                        if(count($subreseller) > 0){
                            $is_subreseller = 1;
                        }
                    }

                    return '<input type="checkbox" name="user_checkbox[]" class="user_checkbox" value="'.$row->user_id.'" data-is_subreseller="'.$is_subreseller.'"/>';
                })
                ->editColumn('name', function ($row) {

                    if (!empty($row->profile_picture)) {
                        $profile_picture = '<img src="' . url('/uploads/profile_pictures') . '/' . $row->profile_picture . '" alt="Avatar" height="32" width="32">';
                    } else {
                        $profile_picture = '<img src="' . url('/images/portrait/small/avatar-s-11.jpg') . '" alt="Avatar" height="32" width="32">';
                    }

                    return '<div class="d-flex justify-content-left align-items-center"><div class="avatar-wrapper"><div class="avatar  me-1">' . $profile_picture . '</div></div><div class="d-flex flex-column"><a href="'. route('user-view-account', $row->user_id) . '" class="user_name text-truncate text-body"><span class="fw-bolder">' . $row->first_name . ' ' . $row->last_name . '</span></a><small class="emp_post text-muted">' . $row->email . '</small></div></div>';
                })
                ->editColumn('user_type', function ($row) {
                    if ($row->user_type == 1) {
                        $user_type = "<span class='badge bg-light-success rounded-pill'>Super Admin</span>";
                    }else
                    if ($row->user_type == 2) {
                        $user_type = "<span class='badge bg-light-success rounded-pill'>User</span>";
                    } else if ($row->user_type == 3) {
                        $user_type = "<span class='badge bg-light-warning rounded-pill'>Reseller</span>";
                    } else {
                        $user_type = "<span class='badge bg-light-info rounded-pill'>SubReseller</span>";
                    }
                    return $user_type;
                })
                ->addColumn('added_by', function ($row) {
                    $added_by_user = User::where('user_id',$row->added_by)->first();
                    $added_by = '';
                    if ($added_by_user) {
                        $added_by = '<span>' . $added_by_user->first_name . ' ' . $added_by_user->last_name . '</span>';
                    }
                    return $added_by;
                })
                ->editColumn('is_verified', function ($row) {

                    if ($row->is_verified == 1) {
                        $is_verified = "<span class='badge bg-light-success rounded-pill'>Verified</span>";
                    } else {
                        $is_verified = '<span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" value="' . $row->id . '" class="chkIsVerified" />
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    }

                    return $is_verified;
                })

                ->editColumn('status', function ($row) {
                    $is_subreseller = 0;
                    if($row->user_type == 3){
                        $subreseller = User::where('user_type',4)->where('added_by',$row->user_id)->get();
                        if(count($subreseller) > 0){
                            $is_subreseller = 1;
                        }
                    }

                    if ($row->status == 1) {
                        $status = '<span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" checked value="' . $row->id . '"
                                class="chkStatus" data-is_subreseller="'.$is_subreseller.'"/>
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    } else {
                        $status = ' <span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" value="' . $row->id . '" class="chkStatus" data-is_subreseller="'.$is_subreseller.'"/>
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $is_subreseller = 0;
                    if($row->user_type == 3){
                        $subreseller = User::where('user_type',4)->where('added_by',$row->user_id)->get();
                        if(count($subreseller) > 0){
                            $is_subreseller = 1;
                        }
                    }
                    $action = '';
                    if ($row->status == 1 && $row->is_verified == 1) {
                        $action .= '
                        <a title="Manage Credits" data-bs-toggle="modal" data-bs-target="#updateCreditsMdl" class="btn btn-icon btn-warning waves-effect waves-float waves-light updateCredits" data-user_id="'.$row->user_id.'" data-credits="'.$row->credits.'">
                        <i data-feather="dollar-sign"></i> </a>';
                    }
                    $action .= ' <a href="' . route('user-view-account', $row->user_id) . '" title="View User" class="btn   btn-icon btn-info waves-effect waves-float waves-light">
                        <i data-feather="eye"></i> </a>
                        <a href="' . route('users.edit', $row->user_id) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Edit User">
                            <span data-feather="edit"></span>
                        </a>
                        <a href="javascript:void(0);" title="Delete User" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('users.destroy', $row->id) . '\', \'' . csrf_token() . '\', '.$is_subreseller.')">
                        <span data-feather="trash"></span> </a>';

                    if (Auth::user()->user_id != $row->user_id && session('impersonated_by') == null) {
                        $action .= ' <a href="javascript:void(0);" class="btn btn-icon btn-primary waves-effect waves-float waves-light" title="View as User" onclick="takeAccess(\'' . route('impersonate', $row->id) . '\', \'' . route('users.show', $row->id) . '\', \'' . route('impersonate.leave') . '\')">
                        <span data-feather="user"></span>
                        </a>';
                    }
                    return $action;
                })
				->rawColumns(['checkbox','name','user_type','added_by', 'is_verified','device_id', 'status', 'action'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $serchTerms = $request->search['value'];
                        $query->where(function($q) use($serchTerms) {
                            $q->where('email', 'like', "%{$serchTerms}%")
                                ->orWhere(DB::raw('CONCAT(first_name, " ",last_name)'), 'LIKE', '%' . $serchTerms . '%')
                                ->orWhere('first_name', 'like', "%{$serchTerms}%")
                                ->orWhere('last_name', 'like', "%{$serchTerms}%")
                                ->orWhere('user_type', 'like', "%{$serchTerms}%")
                                ->orWhere('added_by', 'like', "%{$serchTerms}%")
                                ->orWhere('phone_no', 'like', "%{$serchTerms}%")
                                ->orWhere('credits', 'like', "%{$serchTerms}%");
                        });
                    }
                })
                ->toJson();
        }


        $total_reseller = User::where('user_type',3)->count();
        if (Auth::user()->user_type == 3){
            $total_subreseller = User::where('user_type',4)->where('added_by',Auth::user()->user_id)->count();
        }else{
            $total_subreseller = User::where('user_type',4)->count();
        }
        $total_customer = User::where('user_type',2)->count();
        $verified_user = User::where('user_type','!=','1')->where('is_verified', '1')->count();
        $active_user = User::where('user_type','!=','1')->where('status', '1')->count();
        $suspended_user = User::where('user_type','!=','1')->where('status', '0')->count();
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "User List"]];
        if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3) {
            return view('/content/user/app-user-list', compact('total_reseller','total_subreseller', 'total_customer','verified_user', 'active_user', 'suspended_user'), ['breadcrumbs' => $breadcrumbs]);
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function change_user_status($id)
    {
        $obj = User::find($id);
        if ($obj->status == '0') {
            $obj->status = '1';
            $obj->save();
            $suspended_user = User::where('status', '0')->count();
            $active_user = User::where('status', '1')->count();
            return response()->json(['active_user'=>$active_user,'suspended_user'=>$suspended_user,'active' => 'Activated successfully.']);
        } else {
            $obj->status = '0';
            $obj->save();
            $suspended_user = User::where('status', '0')->count();
            $active_user = User::where('status', '1')->count();
            return response()->json(['active_user'=>$active_user,'suspended_user'=>$suspended_user,'suspend' => 'Suspended successfully.']);
        }
    }

    public function verified_user($id)
    {
        $obj = User::find($id);
        if ($obj->is_verified == 0) {
            $obj->is_verified = 1;
            $obj->save();
            $verified_user = User::where('is_verified', '1')->count();
            return response()->json(['verified_user'=>$verified_user,'active' => 'Verfied successfully.']);
        }
    }

    // User Account Page
    public function user_view_account($user_id)
    {
        if(Auth::user()->user_type == 1){
            $user = User::where('user_id', $user_id)->first();
        }else if(Auth::user()->user_type == 3){
            $user = User::where('user_id', $user_id)->first();
            if($user && $user->added_by != Auth::user()->user_id){
                return abort(404);
            }
        }else if($user_id != Auth::user()->user_id){
            return abort(404);
        }
        if($user){
            $userCustomerData = UserSubscription::with('device')->latest()->first();
            $userUpcomingSubscription = $userCurrnetPlan = [];
            $remain_device = 1;
            $trial_remain_device = 0;
            $userUpcomingSubscription = [];
            // if ($userCustomerData) {
            //     $response3 = UserSubscription::getSubscriptionByCustomerID($userCustomerData->customer_id);
            //     if ($response3['status'] == 'success') {
            //         foreach ($response3['data'] as $value) {
            //             if ($value['status'] == 'live' || $value['status'] == 'trial') {
            //                 $userCurrnetPlan = $value;
            //             }
            //         }
            //         // $userUpcomingSubscription = UserSubscription::where('user_id',$user_id)->where('status',2)->latest()->first();
            //         $userUpcomingSubscription = UserSubscription::getScheduledSubscriptionByID($userCustomerData->subscription_id);
            //     } else {
            //         $userUpcomingSubscription = UserSubscription::where('user_id',$user_id)->where('status',2)->latest()->first();
            //     }
            //     $totalDevice = Device::where('user_id', $user_id)->count();
            //     if ($userCurrnetPlan) {
            //         $userSubscription = UserSubscription::with('device')->where('plan_id', $userCurrnetPlan['plan']['id'])->first();
            //         $userCurrnetPlan['device_limit'] = $userSubscription->device_limit;
            //         $userCurrnetPlan['trial_device_limit'] = $userSubscription->trial_device_limit;
            //         $remain_device = $userCurrnetPlan['remaining_device_limit'] = $userSubscription->remaining_device_limit;
            //         $trial_remain_device = $userCurrnetPlan['trial_remain_device_limit'] = $userSubscription->trial_remaining_device_limit;
            //     } else {
            //         if ($totalDevice > 0) {
            //             $remain_device = 0;
            //             $trial_remain_device = 0;
            //         }
            //     }
            // }
            // echo "<pre>";print_r($userCurrnetPlan);die;

            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "users/list", 'name' => "User List"], ['name' => "User View"]];
            return view('/content/user/app-user-view-account', ['breadcrumbs' => $breadcrumbs, 'user' =>  $user, 'userSubscription' => $userCurrnetPlan, 'userUpcomingSubscription' => $userUpcomingSubscription, 'remain_device' => $remain_device, 'trial_remain_device' => $trial_remain_device]);
        }else{
            return abort(404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $reseller = User::where('user_type',3)->where('status',1)->where('is_verified',1)->get();
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "users/list", 'name' => "User List"], ['name' => "Add User"]];

        if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3) {
            return view('/content/user/app-user-create', ['breadcrumbs' => $breadcrumbs,'reseller'=>$reseller]);
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function checkExistEmail(Request $request)
    {
        $email = $request->input('email');
		$user_id = $request->input('user_id');

		if(!empty($user_id)){
			$checkUser = User::selectRaw('*')->where('email',$email)->where('user_id','!=',$user_id)->whereNull('deleted_at')->first();
		}else{
			$checkUser = User::selectRaw('*')->where('email',$email)->whereNull('deleted_at')->first();
		}

		if(!empty($checkUser)) {
			return json_encode(FALSE);
		}else{
			return json_encode(TRUE);
		}
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->whereNull('deleted_at')],
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response['success'] = 0;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }

        $user = new User;
        $user_id = $user->get_random_string();
        $user->user_id = $user_id;
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->phone_no = $request->get('phone_no');
        if(Auth::user()->user_type == 1){
            if($request->get('is_reseller') == 'on'){
                $user->user_type = 3;
                $user->added_by = Auth::user()->user_id;
            }else if($request->get('is_subreseller') == 'on'){
                $user->user_type = 4;
                $user->added_by = $request->get('reseller_id');
            }else{
                $user->user_type = 2;
                $user->added_by = Auth::user()->user_id;
            }
        }else{
            $user->user_type = 4;
            $user->added_by = Auth::user()->user_id;
        }
        $user->is_multi_dns = $request->get('is_multi_dns') == 'on' ? 1 : 0;
        $user->save();

        if ($user) {
            if(empty($user->customer_id)){
                $createCustomer = UserSubscription::createCustomer($user);
                if($createCustomer['status'] == 'success'){
                    $user = User::where('user_id', $user->user_id)->first();
                    $user->customer_id = $createCustomer['data']['id'];
                    $user->save();
                    UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
                }else{
                    $getCustomer = UserSubscription::getCustomerByEmail($user->email);
                    if($getCustomer['status'] == 'success'){
                        $user = User::where('user_id', $user->user_id)->first();
                        $user->customer_id = $getCustomer['data']['id'];
                        $user->save();
                        UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
                    }
                }
            }else{
                $getCustomer = UserSubscription::getCustomerByEmail($user->email);
                if($getCustomer['status'] == 'success'){
                    $user = User::where('user_id', $user->user_id)->first();
                    $user->customer_id = $getCustomer['data']['id'];
                    $user->save();
                    UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
                }else{
                    $createCustomer = UserSubscription::createCustomer($user);
                    if($createCustomer['status'] == 'success'){
                        $user = User::where('user_id', $user->user_id)->first();
                        $user->customer_id = $createCustomer['data']['id'];
                        $user->save();
                        UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
                    }
                }
            }
            $verifyUser = VerifyUser::create([
                'user_id' => $user->user_id,
                'token' => sha1(time())
            ]);
            $user['verifyUser'] = $verifyUser;
            try{
                \Mail::to($user->email)->send(new VerifyEmail($user,2));

                if (\Mail::failures()) {
                    $response['success'] = 0;
                    $response['message'] = 'Unable to Send Verification Mail.';
                    return response()->json($response, 200);
                }
            }catch (\Exception $e) {
                $response['success'] = 0;
                $response['message'] = 'Unable to Send Verification Mail.'.$e->getMessage();
                return response()->json($response, 200);
            }
        }
        return response()->json(['success' => 1]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('user_id',$id)->first();
        $reseller = User::where('user_id','!=',$id)->where('user_type',3)->where('status',1)->where('is_verified',1)->get();
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "users/list", 'name' => "User List"], ['name' => "Edit User"]];

        if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3) {
            if($user->user_type == 4 && Auth::user()->user_type == 3){
                $checkUser = User::where('user_id',$id)->where('added_by',Auth::user()->user_id)->first();
                if($checkUser){
                    return view('/content/user/app-user-edit', ['breadcrumbs' => $breadcrumbs,'user' => $user,'reseller'=>$reseller]);
                } else {
                    return redirect()->to('/dashboard');
                }
            }
            $is_subreseller = 0;
            if($user->user_type == 3){
                $subreseller = User::where('user_type',4)->where('added_by',$id)->get();
                if(count($subreseller) > 0){
                    $is_subreseller = 1;
                }
            }

            return view('/content/user/app-user-edit', ['breadcrumbs' => $breadcrumbs,'user' => $user,'reseller'=>$reseller,'is_subreseller'=>$is_subreseller]);
        } else {
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user_id =  $request->user_id;

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->where('user_id',Auth::user()->user_id)->ignore($request->user_id)->whereNull('deleted_at')],
            // 'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response['success'] = 0;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }

        $user = User::where('user_id',$user_id)->first();
        $old_email = $user->email;
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        if($request->get('password')){
            $user->password = bcrypt($request->get('password'));
        }
        $user->phone_no = $request->get('phone_no');
        if(Auth::user()->user_type == 1){
            if($request->get('is_reseller') == 'on'){
                $user->user_type = 3;
                $user->added_by = Auth::user()->user_id;
            }else if($request->get('is_subreseller') == 'on'){
                $user->user_type = 4;
                $user->added_by = $request->get('reseller_id');
            }else {
                $user->user_type = 2;
                $user->added_by = Auth::user()->user_id;
            }
        }else {
            if($request->get('is_user') == 'on'){
                $admin = User::where('user_type', 1)->first();
                $user->user_type = 2;
                $user->added_by = $admin->user_id;
            }else {
                $user->user_type = 4;
                $user->added_by = Auth::user()->user_id;
            }
        }
        $user->is_multi_dns = $request->get('is_multi_dns') == 'on' ? 1 : 0;
        $user->save();
        if($user->user_type == 2){
            User::where('added_by',$user->user_id)->update(['added_by'=>NULL]);
        }
        if($user->email != $old_email){
            User::where('user_id',$user_id)->update(['is_verified'=>0]);
            try{
                \Mail::to($user->email)->send(new VerifyEmail($user,2));

                if (\Mail::failures()) {
                    $response['success'] = 0;
                    $response['message'] = 'Unable to Send Verification Mail.';
                    return response()->json($response, 200);
                }
            }catch (\Exception $e) {
                $response['success'] = 0;
                $response['message'] = 'Unable to Send Verification Mail.'.$e->getMessage();
                return response()->json($response, 200);
            }
        }

        return response()->json(['success' => 1]);
    }


    public function destroy($id)
    {

        $user_id = User::where('id', $id)->first()->user_id;

        $userSubscription = UserSubscription::where('user_id', $user_id);
        $userSubscription->delete();

        $device = Device::where('user_id', $user_id)->get();
        foreach ($device as $value) {
            $deviceCode = DeviceCode::where('device_id', $value->id);
            $deviceCode->delete();
        }
        Device::where('user_id', $user_id)->delete();
        $playlist = Playlist::where('user_id', $user_id);
        $playlist->delete();
        DevicePlaylist::where('user_id', $user_id)->delete();
        $User = User::findOrFail($id);
        $User->delete();
        return response()->json(['success' => 1]);
    }


    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $userSubscription = UserSubscription::whereIn('user_id', $ids);
        $userSubscription->delete();

        $device = Device::whereIn('user_id', $ids)->get();
        foreach ($device as $value) {
            $deviceCode = DeviceCode::where('device_id', $value->id);
            $deviceCode->delete();
        }
        Device::whereIn('user_id', $ids)->delete();
        $playlist = Playlist::whereIn('user_id', $ids);
        $playlist->delete();
        DevicePlaylist::whereIn('user_id', $ids)->delete();
        User::whereIn('user_id',$ids)->delete();
        return response()->json(['success' => 1]);
    }
}
