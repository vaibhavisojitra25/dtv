<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Device;
use App\Models\DeviceCode;
use App\Models\Playlist;
use App\Models\DNS;
use App\Models\PlaylistMultiDNS;
use App\Models\DevicePlaylist;
use App\Models\CreditHistory;
use App\Models\Setting;
use App\Mail\DeviceCodeMail;
use DataTables;
use App\Helper\Helper;
use Auth;
use File;
use DB;
use Storage;
use Carbon\Carbon;
use Validator;
use App\Models\VerifyUser;
use App\Mail\VerifyEmail;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$device_id)
    {
        if ($request->ajax()) {

            $query = DeviceCode::select('devices.*','users.user_type','users.email','users.first_name','users.last_name','device_code.code','device_code.status','device_code.is_code_auto_renew')
            ->join('devices', function ($join) {
                $join->on('devices.id','=','device_code.device_id');
            })->join('users', function ($join) {
                $join->on('users.user_id','=','devices.user_id');
            })
            ->whereRaw('device_code.id IN (SELECT MAX(device_code.id) FROM device_code GROUP BY device_id)')
            ;
            if (Auth::user()->user_type == 1){
                if ($request->added_by) {
                  $query->where('devices.user_id',$request->added_by);
                }
                if (isset($request->status)) {
                    $query->where('device_code.status',$request->status);
                }
            }else{
                $query->where('devices.user_id',Auth::user()->user_id);
            }
            $device = $query->orderBy('device_code.created_at','DESC');
            // $device = $device->unique();
            return DataTables::eloquent($device)
                ->addColumn('code', function ($row) {
                    return $row->code ? $row->code : "";
                })
                ->editColumn('checkbox', function ($row) {
                    return '<input type="checkbox" name="device_checkbox[]" class="device_checkbox" value="'.$row->id.'" />';
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->setTimezone('Asia/Kolkata')->format('Y-m-d h:i:s');
                })
                ->addColumn('expire_date', function ($row) {
                    $userSubscription = UserSubscription::where('device_id',$row->id)->first();
                    if($userSubscription){
                        if(($userSubscription && $userSubscription->status == 1) && ($row->status == 1)){
                            return '<span class="text-success">'.$userSubscription->expiry_date.'</span>';
                        }else  if($row->status == 2){
                            return '<span class="text-warning">'.$userSubscription->expiry_date.'</span>';
                        }else if(($userSubscription && $userSubscription->status == 3)){
                            return '<span class="text-info">'.$userSubscription->expiry_date.'</span>';
                        }else{
                            return '<span class="text-danger">'.$userSubscription->expiry_date.'</span>';
                        }
                    }

                })
                ->addColumn('added_by', function ($row) {
                    if($row->user_type){
                        if($row->user_type == 2){
                            $user_type = 'User';
                        }else if($row->user_type == 3){
                            $user_type = 'Reseller';
                        }else if($row->user_type == 4){
                            $user_type = 'Sub Reseller';
                        }
                        $username = $row->first_name.' '.$row->last_name.'<br><span class="badge bg-light-success rounded-pill">'.$user_type.'</s>';
                    }else{
                        $username = '-';
                    }
                    return $username;
                })

                ->addColumn('status', function ($row) {
                    if (Auth::user()->user_type == 1){
                        if($row->code){
                            if($row->status == 1){
                                $checked='checked';
                            }else if($row->status == 2){
                                $checked='';
                            }
                            if($row->status == 1 || $row->status == 2){
                                $status = '<span class="btnOn">
                                    <label class="switch">
                                        <input type="checkbox" '.$checked.' value="' . $row->id . '"
                                            class="changeDeviceCodeStatus" />
                                        <span class="slider round"></span>
                                    </label>
                                </span>';
                            }else{
                                $status = '<span class="text-danger">Expired</span>';
                            }
                        }else{
                            $status = '-';
                        }

                    }else{
                        if($row->code){
                            $userSubscription = UserSubscription::where('device_id',$row->id)->first();
                            if(($userSubscription && $userSubscription->status == 1) && $row->status == 1){
                                $status = '<span class="text-success">Active</span>';
                            }else if($row->status == 2){
                                $status = '<span class="text-warning">InActive</span>';
                            }else if(($userSubscription && $userSubscription->status == 3)){
                                $status = '<span class="text-info">Cancelled</span>';
                            }else{
                                $status = '<span class="text-danger">Expired</span>';
                            }
                        }else{
                            $status = '-';
                        }
                    }


                    return $status;
                })
                ->addColumn('is_active', function ($row) {
                    $is_active = '';
                    if (Auth::user()->user_type == 1){
                        if($row->is_active == 1){
                            $checked='checked';
                        }else{
                            $checked='';
                        }
                        $is_active = '<span class="btnOn">
                            <label class="switch">
                                <input type="checkbox" '.$checked.' value="' . $row->id . '"
                                    class="activeDeactiveDevice" />
                                <span class="slider round"></span>
                            </label>
                        </span>';
                    }else{
                        if($row->is_active == 1){
                            $is_active = '<span class="text-success">Active</span>';
                        }else{
                            $is_active = '<span class="text-danger">Deactive</span>';
                        }
                    }

                    return $is_active;
                })
                ->editColumn('is_cloud_sync', function ($row) {
                    if ($row->is_cloud_sync == 1) {
                        $checked='checked';
                    }else{
                        $checked='';
                    }

                    $is_cloud_sync = '<span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" '.$checked.' value="' . $row->id . '"
                                class="changeCloudStatus" />
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    return $is_cloud_sync;
                })
                ->editColumn('is_code_auto_renew', function ($row) {
                    if ($row->is_code_auto_renew == 1) {
                        $checked='checked';
                    }else{
                        $checked='';
                    }

                    $is_code_auto_renew = '<span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" '.$checked.' value="' . $row->id . '"
                                class="changeCodeAutoRenew" />
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    return $is_code_auto_renew;
                })
                ->addColumn('action', function ($row) {
                    $disable = '';
                    if (Auth::user()->user_type != 1){
                        if($row->is_active == 0){
                            $disable = 'disabled';
                        }else{
                            $disable = '';
                        }
                    }

                    $userSubscription = UserSubscription::where('device_id',$row->id)->first();
                    $renew = '';
                    if($userSubscription && $userSubscription->status != 1){
                       $renew = ' <a href="'.route('subscription/plan', $row->id).'" class="btn btn-icon btn-warning waves-effect waves-float waves-light me-1" title="Renew Device Code"' .$disable.'>
                        <span data-feather="credit-card"></span>
                        </a>';
                    }
                    if($userSubscription && $userSubscription->status == 1){
                        if ($row->status == 2){
                            $renew = ' <a href="javascript:void(0);" class="btn btn-icon btn-warning waves-effect waves-float waves-light me-1 '.$disable.'" title="Reset Device"  onclick="renewCode(\'' . route('device/renew', $row->id) . '\', \'' . csrf_token() . '\')">
                                <span data-feather="refresh-ccw"></span>
                            </a>';
                        }
                    }
                    $action = '<div class="d-flex justify-content-start">
                            <a href="' . route('device/codes/history', $row->id) . '" class="btn btn-icon btn-info waves-effect waves-float waves-light me-1" title="Device Code History">
                                <span data-feather="eye"></span>
                            </a>
                            <a href="' . route('device.edit', $row->id) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light me-1 '.$disable.'" title="Edit Device">
                                <span data-feather="edit"></span>
                            </a> '
                           .$renew.
                            ' <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light me-1 '.$disable.'" onclick="handleConfirmation(\'' . route('device.destroy', $row->id) . '\', \'' . csrf_token() . '\')" title="Delete Device">
                            <span data-feather="trash"></span>
                        </a></div>';
                    return $action;
                })
                ->rawColumns(['code','checkbox','created_at','expire_date','added_by','status','is_active','is_cloud_sync','is_code_auto_renew','action'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $serchTerms = $request->search['value'];
                        $query->where(function($q) use($serchTerms) {
                            $q->where('code', 'like', "%{$serchTerms}%")
                                ->orWhere('platform', 'like', "%{$serchTerms}%")
                                ->orWhere('app_name', 'like', "%{$serchTerms}%")
                                ->orWhere('device_title', 'like', "%{$serchTerms}%")
                                ->orWhere('ip_address', 'like', "%{$serchTerms}%")
                                ->orWhere('mac_address', 'like', "%{$serchTerms}%")
                                ->orWhere('device_type', 'like', "%{$serchTerms}%")
                                ->orWhere('email', 'like', "%{$serchTerms}%")
                                ->orWhere(DB::raw('CONCAT(first_name, " ",last_name)'), 'LIKE', '%' . $serchTerms . '%')
                                ->orWhere('first_name', 'like', "%{$serchTerms}%")
                                ->orWhere('last_name', 'like', "%{$serchTerms}%");
                        });
                    }
                })
                ->toJson();
        }
        $users = User::where('user_type','!=',1)->where('is_verified',1)->where('status',1)->get();
        $breadcrumbs = [['link' => "/", 'name' => "Home"], [ 'name' => "Device List"]];
        if (Auth::user()->user_type != 1){
            $checkDevice = [];
            if($device_id){
                $checkDevice = Device::where('user_id',Auth::user()->user_id)->where('id',$device_id)->first();
            }
            if($checkDevice){
                return view('/content/device/index', ['breadcrumbs' => $breadcrumbs,'users'=>$users,'device_id'=>$device_id]);
            }else{
                return view('/content/device/index', ['breadcrumbs' => $breadcrumbs,'users'=>$users]);
            }
        }else{
            return view('/content/device/index', ['breadcrumbs' => $breadcrumbs,'users'=>$users]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "device/list", 'name' => "Device List"], ['name' => "Add Device"]];
        $current_date = Carbon::now();
        $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
        $device = Device::where('user_id',Auth::user()->user_id)->with('device_code')->first();
        $planData = UserSubscription::getAllPlan();
        $plan_data = array_filter($planData['data'],function($element) {
            return ($element['plan_active'] == 'true' && $element['meta_data']['is_credit']==0);
        });
        if (Auth::user()->user_type == 3){
            $plan_data = array_filter($plan_data,function($element) {
                return (isset($element['meta_data']['is_reseller_credit']) && $element['meta_data']['is_reseller_credit']==1);
            });
        }else{
            $plan_data = array_filter($plan_data, function ($plan) {
                return !isset($plan['meta_data']['is_reseller_credit']) || $plan['meta_data']['is_reseller_credit'] != 1;
            });
        }
        $plan_data = array_values($plan_data);
        $setting = Setting::where("id", '1')->first();
        if (Auth::user()->user_type == 1){
            $playlist = Playlist::where('user_id',$device->user_id)->where('status',1)->latest()->get()->toArray();
            $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',$device->user_id)->where('status',1)->groupBy('unique_id')->latest()->get()->toArray();
            $playlist = array_merge($playlist,$multiplaylist);
            $device_playlist =  DevicePlaylist::where('user_id',$device->user_id)->pluck('playlist_id')->all();
            $free_devices = UserSubscription::where('user_id',$device->user_id)->where('plan_code','free')->count();

            $playlistAll1 = Playlist::where('user_id',$device->user_id)->latest()->get()->toArray();
            $multiplaylistAll = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',$device->user_id)->groupBy('unique_id')->latest()->get()->toArray();
            $playlistAll = array_merge($playlistAll1,$multiplaylistAll);

            $total_credit = $setting->playlist_limit;
            if(Auth::user()->user_type == 3){
                $total_credit = round(Auth::user()->credits/4);
            }
            if(count($playlistAll) >= $total_credit){
                $is_limit = 0;
            }else{
                $is_limit = 1;
            }

        }else{
            $playlist = Playlist::where('user_id',Auth::user()->user_id)->where('status',1)->latest()->get()->toArray();
            $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->where('status',1)->groupBy('unique_id')->latest()->get()->toArray();
            $playlist = array_merge($playlist,$multiplaylist);
            $device_playlist =  DevicePlaylist::where('user_id',Auth::user()->user_id)->pluck('playlist_id')->all();
            $free_devices = UserSubscription::where('user_id',Auth::user()->user_id)->where('plan_code','free')->count();

            $playlistAll1 = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
            $multiplaylistAll = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
            $playlistAll = array_merge($playlistAll1,$multiplaylistAll);
            $total_credit = $setting->playlist_limit;
            if(Auth::user()->user_type == 3){
                $total_credit = round(Auth::user()->credits/4);
            }
            if(count($playlistAll) >= $total_credit){
                $is_limit = 0;
            }else{
                $is_limit = 1;
            }
        }
        $user = User::where('user_id',Auth::user()->user_id)->first();
        $dns = DNS::where('user_id',Auth::user()->user_id)->latest()->get();
// echo "<pre>"; print_r($plan_data); die;
        return view('/content/device/create', ['breadcrumbs' => $breadcrumbs,'flag'=>0,'plan_data' => $plan_data,'playlist'=>$playlist,'user'=>$user,'setting'=>$setting,'free_devices'=>$free_devices,'is_limit'=>$is_limit,'dns'=>$dns]);
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
            'device_title' => ['required', Rule::unique('devices')->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
            // 'duration' => 'required',
            'plan_id' => 'required',
            'playlist_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        if(Auth::user()->user_type != 1){
            $checkPlaylist = Playlist::where('id',$request->playlist_id)->where('user_id',Auth::user()->user_id)->first();
            if(empty($checkPlaylist)){
                $response['success'] = 0;
                $response['message'] = "You don't have playlist access";
                return response()->json($response, 404);
            }
        }
        // $current_date = Carbon::now();
        // $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
        // $expire_date = Carbon::parse($current_date)->addMinutes(72*60)->format('Y-m-d h:i:s');

        $device = new Device;
        $device->user_id = Auth::user()->user_id;
        $device->device_title = $request->device_title;
        $device->mac_id = $request->mac_id;
        $device->mac_key = $request->mac_key;
        $device->note = $request->note;
        $device->save();

        $deviceCode = new DeviceCode;
        $code =  $deviceCode->generate_code();
        $deviceCode->device_id = $device->id;
        $deviceCode->code = $code;
        $deviceCode->user_id = Auth::user()->user_id;
        // $deviceCode->duration = 72;
        // $deviceCode->expire_date = $expire_date;
        if($request->status){
            $deviceCode->status = $request->status;
        }
        $deviceCode->is_code_auto_renew = isset($request->is_code_auto_renew) ? 1 : 0;

        $deviceCode->save();
        if($request->playlist_id){
            DevicePlaylist::where('device_id',$device->id)->delete();
            foreach($request->playlist_id as $value){
                $device_playlist = new DevicePlaylist;
                $device_playlist->user_id = Auth::user()->user_id;
                $device_playlist->playlist_id = $value;
                $device_playlist->device_id = $device->id;
                $device_playlist->save();
            }
        }
        // CreditHistory::where('id',$request->credit_history_id)->update(['device_id'=>$device->id]);

        $userSubscription = UserSubscription::where('device_id',$device->id)->where('status',1)->first();

        // \Mail::to(Auth::user()->email)->send(new DeviceCodeMail(Auth::user(),$device,$deviceCode));
        // if (\Mail::failures()) {
        //     $response['success'] = 0;
        //     $response['message'] = 'Unable to Send Mail.';
        //     return response()->json($response, 200);
        // }
        return response()->json(['success' => 1,'device_id'=>$device->id,'code' => $code]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $device = Device::findOrFail($id);
        $setting = Setting::where("id", '1')->first();

        if (Auth::user()->user_type == 1){
            $playlist = Playlist::where('user_id',$device->user_id)->where('status',1)->latest()->get()->toArray();
            $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',$device->user_id)->where('status',1)->groupBy('unique_id')->latest()->get()->toArray();
            $playlist = array_merge($playlist,$multiplaylist);
            $device_playlist =  DevicePlaylist::where('user_id',$device->user_id)->where('device_id',$id)->pluck('playlist_id')->all();
            $free_devices = UserSubscription::where('user_id',$device->user_id)->where('plan_code','free')->count();
            $playlistAll1 = Playlist::where('user_id',$device->user_id)->latest()->get()->toArray();
            $multiplaylistAll = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',$device->user_id)->groupBy('unique_id')->latest()->get()->toArray();
            $playlistAll = array_merge($playlistAll1,$multiplaylistAll);
            $total_credit = $setting->playlist_limit;
            if(Auth::user()->user_type == 3){
                $total_credit = round(Auth::user()->credits/4);
            }
            if(count($playlistAll) >= $total_credit){
                $is_limit = 0;
            }else{
                $is_limit = 1;
            }
            $dns = DNS::where('user_id',$device->user_id)->latest()->get();
            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "device/list", 'name' => "Device List"], ['name' => "Edit Device"]];
            return view('/content/device/edit', ['breadcrumbs' => $breadcrumbs,'device' => $device,'playlist'=>$playlist,'device_playlist'=>$device_playlist,'setting'=>$setting,'free_devices'=>$free_devices,'is_limit'=>$is_limit,'dns'=>$dns]);

        }else{
            $chkdevice = Device::where('id',$id)->where('user_id',Auth::user()->user_id)->first();
            if( $chkdevice){
                $playlist = Playlist::where('user_id',Auth::user()->user_id)->where('status',1)->latest()->get()->toArray();
                $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->where('status',1)->groupBy('unique_id')->latest()->get()->toArray();
                $playlist = array_merge($playlist,$multiplaylist);
                $device_playlist =  DevicePlaylist::where('user_id',Auth::user()->user_id)->where('device_id',$id)->pluck('playlist_id')->all();
                $free_devices = UserSubscription::where('user_id',Auth::user()->user_id)->where('plan_code','free')->count();
                $playlistAll1 = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
                $multiplaylistAll = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
                $playlistAll = array_merge($playlistAll1,$multiplaylistAll);
                $total_credit = $setting->playlist_limit;
                if(Auth::user()->user_type == 3){
                    $total_credit = round(Auth::user()->credits/4);
                }
                if(count($playlistAll) >= $total_credit){
                    $is_limit = 0;
                }else{
                    $is_limit = 1;
                }
                $dns = DNS::where('user_id',Auth::user()->user_id)->latest()->get();
                $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "device/list", 'name' => "Device List"], ['name' => "Edit Device"]];

                return view('/content/device/edit', ['breadcrumbs' => $breadcrumbs,'device' => $device,'playlist'=>$playlist,'device_playlist'=>$device_playlist,'setting'=>$setting,'free_devices'=>$free_devices,'is_limit'=>$is_limit,'dns'=>$dns]);
            }else{
                return redirect()->to('/dashboard');
            }
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
        $id =  $request->device_id;
        $validator = Validator::make($request->all(), [
            'device_title' => ['required', Rule::unique('devices')->ignore($id)->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
        ]);
        if ($validator->fails()) {
            $response['success'] = 0;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        if(Auth::user()->user_type != 1){
            $checkPlaylist = Playlist::where('id',$request->playlist_id)->where('user_id',Auth::user()->user_id)->first();
            if(empty($checkPlaylist)){
                $response['success'] = 0;
                $response['message'] = "You don't have playlist access";
                return response()->json($response, 404);
            }
        }
        $device = Device::findOrFail($id);
        $device->user_id = $device->user_id;
        $device->device_title = $request->device_title;
        $device->mac_id = $request->mac_id;
        $device->mac_key = $request->mac_key;
        $device->note = $request->note;
        // $device->is_code_auto_renew = isset($request->is_code_auto_renew) ? 1 : 0;
        $device->save();
        if(Auth::user()->user_type == 1 && $request->device_code){
            DeviceCode::where('code',$request->device_code)->update(['status'=>$request->status]);
        }
        if($request->playlist_id){
            DevicePlaylist::where('device_id',$id)->delete();
            foreach($request->playlist_id as $value){
                $device_playlist = new DevicePlaylist;
                $device_playlist->user_id = $device->user_id;
                $device_playlist->playlist_id = $value;
                $device_playlist->device_id = $device->id;
                $device_playlist->save();
            }
        }

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
        $device = Device::findOrFail($id);
        $device->delete();
        DeviceCode::where('device_id',$id)->delete();
        DevicePlaylist::where('device_id',$id)->delete();
        return response()->json(['success' => 1]);
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        Device::whereIn('id',$ids)->delete();
        DeviceCode::whereIn('device_id',$ids)->delete();
        DevicePlaylist::whereIn('device_id',$ids)->delete();
        return response()->json(['success' => 1]);
    }

    public function changeDeviceStatus($id)
    {
        $obj = DeviceCode::where('device_id',$id)->latest()->first();
        if ($obj->status == 2 || $obj->status == 0) {
            $obj->status = 1;
            $obj->save();
            return response()->json(['enable' => 'Enable successfully.']);
        } else {
            $obj->status = 2;
            $obj->save();
            return response()->json(['disable' => 'Disable successfully.']);
        }
    }

    public function activeDeactiveDevice($id)
    {
        $obj = Device::where('id',$id)->latest()->first();
        if ($obj->is_active == 0) {
            $obj->is_active = 1;
            $obj->save();
            return response()->json(['enable' => 'Activate successfully.']);
        } else {
            $obj->is_active = 0;
            $obj->save();
            return response()->json(['disable' => 'Deactivate successfully.']);
        }
    }

    public function changeCloudStatus($id)
    {
        $obj = Device::find($id);
        if ($obj->is_cloud_sync == 0) {
            $obj->is_cloud_sync = 1;
            $obj->save();
            return response()->json(['enable' => 'Enable successfully.']);
        } else {
            $obj->is_cloud_sync = 0;
            $obj->save();
            return response()->json(['disable' => 'Disable successfully.']);
        }
    }

    public function changeCodeAutoRenew($id)
    {
        $obj = DeviceCode::where('device_id',$id)->first();
        if ($obj->is_code_auto_renew == 0) {
            $obj->is_code_auto_renew = 1;
            $obj->save();
            return response()->json(['enable' => 'Enable successfully.']);
        } else {
            $obj->is_code_auto_renew = 0;
            $obj->save();
            return response()->json(['disable' => 'Disable successfully.']);
        }
    }

    public function renew($id)
    {
        DeviceCode::where('device_id',$id)->where('status',1)->update(['status'=>2]);

        $deviceCode = new DeviceCode;
        $code =  $deviceCode->generate_code();
        $deviceCode->device_id = $id;
        $deviceCode->code = $code;
        $deviceCode->user_id = Auth::user()->user_id;
        $deviceCode->save();
        return response()->json(['success' => 1]);
    }

    public function getDeviceCode($id)
    {
        $device = DeviceCode::where('device_id',$id)->first();
        if ($device) {
            $userSubscription = UserSubscription::where('device_id',$id)->first();
            $response['code'] = $device->code;
            $response['expire_date'] = Carbon::parse($userSubscription->expiry_date)->format('Y-m-d h:i:s');
            return response()->json(['data' => $response]);
        }
    }

    public function removeSession($id)
    {
        Session::forget('device_id');
        if (empty(Session::get('device_id'))) {
            return response()->json(['success' => 1]);
        }
    }

    public function codeHistory(Request $request,$id)
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "device/list", 'name' => "Device List"], ['name' => "List"]];

        $device = DeviceCode::where('device_id',$id)->where('user_id',Auth::user()->user_id)->first();
        if(Auth::user()->user_type == 1 || $device){
            return view('/content/device/code_history', ['breadcrumbs' => $breadcrumbs,'device_id'=>$id]);
        }else {
            return redirect()->to('/dashboard');
        }

    }

    public function showCodeHistory(Request $request)
    {
        if ($request->ajax()) {
            $device = DeviceCode::where('device_id',$request->device_id)->latest()->get();

            return DataTables::of($device)
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                ->editColumn('created_date', function ($row) {
                    return $row->created_at;
                })
                ->editColumn('expire_date', function ($row) {
                    $userSubscription = UserSubscription::where('device_id',$row->device_id)->first();
                    if($userSubscription){
                        if(($userSubscription && $userSubscription->status == 1) && ($row->status == 1)){
                            return '<span class="text-success">'.$userSubscription->expiry_date.'</span>';
                        }else  if($row->status == 2){
                            return '<span class="text-warning">'.$userSubscription->expiry_date.'</span>';
                        }else  if(($userSubscription && $userSubscription->status == 3)){
                            return '<span class="text-info">'.$userSubscription->expiry_date.'</span>';
                        }else{
                            return '<span class="text-danger">'.$userSubscription->expiry_date.'</span>';
                        }
                    }
                })
                ->editColumn('status', function ($row) {
                    $userSubscription = UserSubscription::where('device_id',$row->device_id)->first();
                    if(($userSubscription && $userSubscription->status == 1) && $row->status == 1){
                        $status = '<span class="text-success">Active</span>';
                    }else if($row->status == 2){
                        $status = '<span class="text-warning">InActive</span>';
                    }else  if(($userSubscription && $userSubscription->status == 3)){
                        $status = '<span class="text-info">Cancelled</span>';
                    }else{
                        $status = '<span class="text-danger">Expired</span>';
                    }

                    return $status;
                })
                // ->addColumn('action', function ($row) {
                //     $action = '
                //             <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('device.destroy', $row->id) . '\', \'' . csrf_token() . '\')">
                //             <span data-feather="trash"></span>
                //         </a>';
                //     return $action;
                // })
                ->rawColumns(['code','created_date','expire_date','status'])
                ->make(true);
        }
    }

}
