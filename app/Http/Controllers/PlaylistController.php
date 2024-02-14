<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Device;
use App\Models\Playlist;
use App\Models\PlaylistMultiDNS;
use App\Models\DevicePlaylist;
use App\Models\DNS;
use App\Models\Setting;
use DataTables;
use App\Helper\Helper;
use Auth;
use File;
use DB;
use Storage;
use Carbon\Carbon;
use Validator;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$device_id)
    {
        $type = $request->type;
        $playlist_device_id = $request->playlist_device_id;

        if(Auth::user()->user_type == 3){
            $users_query = User::where('user_type','!=',1)->where('user_type',4)->where('added_by',Auth::user()->user_id)->where('is_verified',1)->where('status',1);
            $users = $users_query->get();
            $all_subreseller = $users_query->pluck('user_id');
        }else{
            $users = User::where('user_type','!=',1)->where('is_verified',1)->where('status',1)->get();
        }
        if ($request->ajax()) {
            if($playlist_device_id){
                if($type == 3){
                    $query = PlaylistMultiDNS::select('playlists_multi_dns.unique_id','playlists_multi_dns.playlist_name')->join('device_playlist','playlists_multi_dns.id','device_playlist.playlist_id');
                    if(Auth::user()->user_type != 1){
                        $query->where('device_playlist.user_id',Auth::user()->user_id);
                    }
                    $playlist = $query->where('device_playlist.device_id',$playlist_device_id)->whereNull('device_playlist.deleted_at')->groupBy('playlists_multi_dns.unique_id')->orderBy('playlists_multi_dns.created_at','DESC');
                }else{
                    $query = Playlist::select('playlists.id','playlists.playlist_name','playlists.dns','playlists.username','playlists.password','playlists.m3u_url','playlists.epg','playlists.type','playlists.user_agent','playlists.dns_id')->leftjoin('device_playlist','playlists.id','device_playlist.playlist_id');
                    if(Auth::user()->user_type != 1){
                        $query->where('device_playlist.user_id',Auth::user()->user_id);
                    }
                    $playlist = $query->where('device_playlist.device_id',$playlist_device_id)->where('playlists.type',$type)->whereNull('device_playlist.deleted_at')->orderBy('playlists.created_at','DESC');
                }
            }else{
                if($type == 3){
                    $query = PlaylistMultiDNS::select('unique_id','status', 'playlist_name');
                    if ($request->added_by) {
                        $query->where('user_id',$request->added_by);
                    }
                    if (Auth::user()->user_type == 3){
                        $query->where('user_id',Auth::user()->user_id);
                    }else if (Auth::user()->user_type != 1){
                        $query->where('user_id',Auth::user()->user_id);
                    }
                    if (isset($request->status)) {
                        $query->where('status',$request->status);
                    }
                    $playlist = $query->with('user')->groupBy('unique_id')->latest();
                }else{
                    $query = Playlist::where('type',$type);
                    if ($request->added_by) {
                        $query->where('user_id',$request->added_by);
                    }
                    if (Auth::user()->user_type == 3){

                        $query->where(function ($q) use($all_subreseller) {
                            $q->where('user_id', Auth::user()->user_id)
                                  ->orWhereIn('user_id',$all_subreseller);
                        });
                    }else if (Auth::user()->user_type != 1){
                        $query->where('user_id',Auth::user()->user_id);
                    }
                    if (isset($request->status)) {
                        $query->where('status',$request->status);
                    }
                    $playlist = $query->with('user')->with('dns_url')->latest();
                }
            }
            return DataTables::eloquent($playlist)
                ->editColumn('checkbox', function ($row) use($type) {
                    if($type == 3){
                        return '<input type="checkbox" name="playlist_checkbox[]" class="playlist_checkbox" value="'.$row->unique_id.'"/>';
                    }else{
                        return '<input type="checkbox" name="playlist_checkbox[]" class="playlist_checkbox" value="'.$row->id.'"/>';
                    }
                })
                ->editColumn('dns', function ($row) use($type){
                    if(isset($row->dns_url)){
                        $dns = $row->dns_url->dns_url;
                    }else{
                        $dns = $row->dns;
                    }

                    return $dns;
                })
                ->editColumn('status', function ($row) use($type){
                    if($type == 3){
                        $value_id = $row->unique_id;
                    }else{
                        $value_id = $row->id;
                    }
                    if($row->status == 1){
                        $status = '<span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" checked value="' . $value_id . '"
                                class="changeStatus" data-type="'.$type.'"/>
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    } else {
                        $status = ' <span class="btnOn">
                        <label class="switch">
                            <input type="checkbox" value="' . $value_id . '" class="changeStatus"  data-type="'.$type.'"/>
                            <span class="slider round"></span>
                        </label>
                    </span>';
                    }
                    return $status;

                })
                ->addColumn('added_by', function ($row) {
                    if($row->user && $row->user->user_type){
                        if($row->user->user_type == 2){
                            $user_type = 'User';
                        }else if($row->user->user_type == 3){
                            $user_type = 'Reseller';
                        }else if($row->user->user_type == 4){
                            $user_type = 'Sub Reseller';
                        }else{
                            $user_type = '-';
                        }
                        $username = $row->user->first_name.' '.$row->user->last_name.'<br><span class="badge bg-light-success rounded-pill">'.$user_type.'</s>';
                    }else{
                        $username = '-';
                    }
                    return $username;
                })

                ->addColumn('action', function ($row) use($type,$playlist_device_id) {
                    if($type == 3){
                        $action = '
                        <a href="' . route('edit', $row->unique_id) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Edit Playlist">
                            <span data-feather="edit"></span>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('delete', $row->unique_id) . '\', \'' . csrf_token() . '\')" title="Delete Playlist">
                        <span data-feather="trash"></span>
                    </a>';
                    }else{
                        $action = '
                        <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-id="'.$row->id.'" data-type="'.$row->type.'" data-playlist_name="'.$row->playlist_name.'" data-dns="'.$row->dns.'" data-dns_id="'.$row->dns_id.'" data-username="'.$row->username.'" data-password="'.$row->password.'"  data-m3u_url="'.$row->m3u_url.'"  data-epg="'.$row->epg.'"  data-user_agent="'.$row->user_agent.'" class="btn btn-icon btn-success waves-effect waves-float waves-light updatePlaylist" title="Edit Playlist">
                            <span data-feather="edit"></span>
                        </a>';
                        if($playlist_device_id){
                            $action .= ' <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light removeDeviceFromPlaylist" data-device_id="'.$playlist_device_id.'" data-playlist_id="'.$row->id.'" title="Remove From Device">
                            <span data-feather="minus"></span>
                            </a>';
                        }else{
                            $action .= ' <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('playlist.destroy', $row->id) . '\', \'' . csrf_token() . '\')" title="Delete Playlist">
                            <span data-feather="trash"></span>
                            </a>';
                        }
                    }

                    return $action;
                })
                ->rawColumns(['checkbox','dns','status','added_by','action'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $serchTerms = $request->search['value'];
                        $query->where(function($q) use($serchTerms) {
                            $q->where('playlist_name', 'like', "%{$serchTerms}%")
                                ->orWhere('dns', 'like', "%{$serchTerms}%")
                                ->orWhere('username', 'like', "%{$serchTerms}%")
                                ->orWhere('password', 'like', "%{$serchTerms}%");
                                // ->orWhere(DB::raw('CONCAT(first_name, " ",last_name)'), 'LIKE', '%' . $serchTerms . '%')
                                // ->orWhere('first_name', 'like', "%{$serchTerms}%")
                                // ->orWhere('last_name', 'like', "%{$serchTerms}%");
                        });
                    }
                })
                ->toJson();
        }
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Playlist List"]];
        $device = $device_playlist = [];
        if($device_id){
            $device = Device::where('user_id',Auth::user()->user_id)->where('id',$device_id)->first();
            $device_playlist =  DevicePlaylist::where('user_id',Auth::user()->user_id)->where('device_id',$device_id)->pluck('playlist_id')->all();
        }
        $playlist = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist = array_merge($playlist,$multiplaylist);
        $settings = Setting::first();
        $total_credit = $settings->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }

        $dns = DNS::where('user_id',Auth::user()->user_id)->latest()->get();
        return view('/content/playlist/index', ['breadcrumbs' => $breadcrumbs,'is_limit'=>$is_limit,'users'=>$users,'playlist'=>$playlist,'device'=>$device,'device_playlist'=>$device_playlist,'dns'=>$dns]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "playlist/list", 'name' => "Playlist List"], ['name' => "Add Playlist"]];
        $setting = Setting::where("id", '1')->first();
        $playlist = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist = array_merge($playlist,$multiplaylist);
        $total_credit = $setting->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }
        $type = $request->type;
        if($type == 3){
            return view('/content/playlist/create-multi', ['breadcrumbs' => $breadcrumbs,'is_limit'=>$is_limit,'type'=>$type,'playlist'=>$playlist]);
        }else{
            return view('/content/playlist/create', ['breadcrumbs' => $breadcrumbs,'is_limit'=>$is_limit,'type'=>$type,'playlist'=>$playlist]);
        }
    }

    public function checkExistUserName(Request $request)
    {
        $username = $request->input('username');
		$playlist_id = $request->input('playlist_id');

		if(!empty($playlist_id)){
			$checkPlaylist = Playlist::selectRaw('*')->where('username',$username)->where('id','!=',$playlist_id)->whereNull('deleted_at')->get();
		}else{
			$checkPlaylist = Playlist::selectRaw('*')->where('username',$username)->whereNull('deleted_at')->get();
		}

		if(count($checkPlaylist) > 0) {
            echo "true";
            die();
		}else{
            echo "false";
            die();
		}
    }

    public function checkExistUserNameForMulti(Request $request)
    {
        $username = $request->input('username');
        $dns_type = $request->input('dns_type');
		$playlist_id = $request->input('playlist_id');

		if(!empty($playlist_id)){
			$checkPlaylistMultiDNS = PlaylistMultiDNS::selectRaw('*')->where('username',$username)->where('dns_type',$dns_type)->where('id','!=',$playlist_id)->whereNull('deleted_at')->get();
		}else{
			$checkPlaylistMultiDNS = PlaylistMultiDNS::selectRaw('*')->where('username',$username)->where('dns_type',$dns_type)->whereNull('deleted_at')->get();
		}

		if(count($checkPlaylistMultiDNS) > 0) {
            echo "true";
            die();
		}else{
            echo "false";
            die();
		}
    }

    public function checkExistPlaylist(Request $request)
    {
        $playlist_name = $request->input('playlist_name');
		$playlist_id = $request->input('playlist_id');
        $flag = $request->input('flag');

		if(!empty($playlist_id)){
            if($flag == 1){
			    $checkPlaylist = Playlist::selectRaw('*')->where('playlist_name',$playlist_name)->where('user_id',Auth::user()->user_id)->where('id','!=',$playlist_id)->whereNull('deleted_at')->first();
            }else{
                $checkPlaylist = PlaylistMultiDNS::selectRaw('*')->where('playlist_name',$playlist_name)->where('user_id',Auth::user()->user_id)->where('unique_id','!=',$playlist_id)->whereNull('deleted_at')->first();
            }
		}else{
            $checkPlaylist = Playlist::selectRaw('*')->where('playlist_name',$playlist_name)->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')->first();
		}

		if(!empty($checkPlaylist)) {
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
        if($request->is_multi_dns == 1){
            $validator = Validator::make($request->all(), [
                'playlist_name' => ['required', Rule::unique('playlists_multi_dns')->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
            ]);

            if ($validator->fails()) {
                $response['success'] = false;
                $response['message'] = $validator->errors()->first();
                return response()->json($response, 200);
            }
            $type_array = ['livetv','movie','show','24_7'];
            $i=1;
            $playlistunique = new PlaylistMultiDNS;
            $unique_id = $playlistunique->get_random_string();
            foreach($type_array as $value){
                $playlist = new PlaylistMultiDNS;
                $playlist->user_id = Auth::user()->user_id;
                $playlist->unique_id = $unique_id;
                $playlist->playlist_name = $request->playlist_name;
                $playlist->dns_type = $i;
                $playlist->type = $request->get($value.'_type');
                if($request->get($value.'_type') == 1){
                    $playlist->dns = $request->get($value.'_dns');
                    $playlist->username = $request->get($value.'_username');
                    $playlist->password = $request->get($value.'_password');
                }
                if($request->get($value.'_type') == 2){
                    $playlist->m3u_url = $request->get($value.'_m3u_url');
                }
                if($i == 1){
                    $playlist->epg = $request->get($value.'_epg');
                }
                $playlist->user_agent = $request->user_agent;
                $playlist->save();
                $i++;
            }

        }else{
            $validator = Validator::make($request->all(), [
                'playlist_name' => ['required', Rule::unique('playlists')->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
                'type' => 'required',
                'username' => 'required_if:type,1',
                'password' => 'required_if:type,1',
                'm3u_url' => 'required_if:type,2',
            ]);
            if(empty($request->dns_id)){
                $validator = Validator::make($request->all(), [
                    'dns' => 'required_if:type,1',
                ]);
            }
            if(empty($request->dns)){
                $validator = Validator::make($request->all(), [
                    'dns_id' => 'required_if:type,1',
                ]);
            }
            // if($request->type == 1){
            //     $validator = Validator::make($request->all(), [
            //         'username' => ['required_if:type,1', Rule::unique('playlists')->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
            //     ]);
            // }
            if ($validator->fails()) {
                $response['success'] = false;
                $response['message'] = $validator->errors()->first();
                return response()->json($response, 200);
            }

            $playlist = new Playlist;
            $playlist->user_id = Auth::user()->user_id;
            $playlist->playlist_name = $request->playlist_name;
            $playlist->type = $request->type;
            if($request->type == 1){
                if($request->dns){
                    $playlist->dns = $request->dns;
                }
                if($request->dns_id){
                    $playlist->dns_id = $request->dns_id;
                }
                $playlist->username = $request->username;
                $playlist->password = $request->password;
            }
            if($request->type == 2){
                $playlist->m3u_url = $request->m3u_url;
            }
            $playlist->epg = $request->epg;
            $playlist->user_agent = $request->user_agent;
            $playlist->save();

            // if($request->device_id){
            //     foreach($request->device_id as $value){
            //         $device_playlist = new DevicePlaylist;
            //         $device_playlist->user_id = Auth::user()->user_id;
            //         $device_playlist->playlist_id = $playlist->id;
            //         $device_playlist->device_id = $value;
            //         $device_playlist->save();
            //     }
            // }
        }
        $settings = Setting::where("id", '1')->first();
        $playlistAll = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist1 = array_merge($playlistAll,$multiplaylist);

        $total_credit = $settings->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist1) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }

        $playlist_device_id = $request->playlist_device_id;
        if($request->playlist_device_id){
            $device_playlist = new DevicePlaylist;
            $device_playlist->user_id = Auth::user()->user_id;
            $device_playlist->playlist_id = $playlist->id;
            $device_playlist->device_id = $playlist_device_id;
            $device_playlist->save();
        }
        return response()->json(['success' => 1,'message'=>'Playlist Created','playlist'=>$playlist,'limit'=>$is_limit]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "playlist/list", 'name' => "Playlist List"], ['name' => "Edit Playlist"]];
        $playlist = Playlist::where('user_id',Auth::user()->user_id)->where('id',$id)->first();
        if( $playlist){
            return view('/content/playlist/edit', ['breadcrumbs' => $breadcrumbs,'playlist' => $playlist]);
        }else{
            return redirect()->to('/dashboard');
        }

    }

    public function editMultiDNS($id)
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "playlist/list", 'name' => "Playlist List"], ['name' => "Edit Playlist"]];
        $playlist = PlaylistMultiDNS::where('unique_id',$id)->where('user_id',Auth::user()->user_id)->get();
        if (Auth::user()->user_type == 1){
            return redirect()->to('/dashboard');
        }else{
            if( $playlist){
                return view('/content/playlist/edit-multi', ['breadcrumbs' => $breadcrumbs,'playlist' => $playlist]);
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
        if($request->is_multi_dns == 1){
            $id =  $request->hide_playlist_id;
            $validator = Validator::make($request->all(), [
                'playlist_name' => ['required', Rule::unique('playlists_multi_dns')->where('user_id',Auth::user()->user_id)->where('unique_id','!=',$id)->whereNull('deleted_at')],
            ]);

            if ($validator->fails()) {
                $response['success'] = false;
                $response['message'] = $validator->errors()->first();
                return response()->json($response, 200);
            }
            $type_array = ['livetv','movie','show','24_7'];
            $i=1;
            foreach($type_array as $value){
                $playlist = PlaylistMultiDNS::where('unique_id',$id)->where('dns_type',$i)->first();
                $playlist->user_id = Auth::user()->user_id;
                $playlist->unique_id = $id;
                $playlist->playlist_name = $request->playlist_name;
                $playlist->dns_type = $i;
                $playlist->type = $request->get($value.'_type');
                if($request->get($value.'_type') == 1){
                    $playlist->dns = $request->get($value.'_dns');
                    $playlist->username = $request->get($value.'_username');
                    $playlist->password = $request->get($value.'_password');
                }
                if($request->get($value.'_type') == 2){
                    $playlist->m3u_url = $request->get($value.'_m3u_url');
                }
                if($i == 1){
                    $playlist->epg = $request->get($value.'_epg');
                }
                $playlist->user_agent = $request->user_agent;
                $playlist->save();
                $i++;
            }

        }else{
            $id = $request->hide_playlist_id;

            $validator = Validator::make($request->all(), [
                'playlist_name' => ['required', Rule::unique('playlists')->where('user_id',Auth::user()->user_id)->ignore($id)->whereNull('deleted_at')],
                'type' => 'required',
                'username' => 'required_if:type,1',
                'password' => 'required_if:type,1',
                'm3u_url' => 'required_if:type,2',
            ]);

            if(empty($request->dns_id)){
                $validator = Validator::make($request->all(), [
                    'dns' => 'required_if:type,1',
                ]);
            }
            if(empty($request->dns)){
                $validator = Validator::make($request->all(), [
                    'dns_id' => 'required_if:type,1',
                ]);
            }

            if ($validator->fails()) {
                $response['success'] = false;
                $response['message'] = $validator->errors()->first();
                return response()->json($response, 200);
            }

            $playlist = Playlist::findOrFail($id);
            $playlist->user_id = Auth::user()->user_id;
            $playlist->playlist_name = $request->playlist_name;
            $playlist->type = $request->type;
            if($request->type == 1){
                if($request->dns){
                    $playlist->dns = $request->dns;
                }
                if($request->dns_id){
                    $playlist->dns_id = $request->dns_id;
                }
                $playlist->username = $request->username;
                $playlist->password = $request->password;
                $playlist->m3u_url = "";
            }
            if($request->type == 2){
                $playlist->dns = "";
                $playlist->dns_id = "";
                $playlist->username = "";
                $playlist->password = "";
                $playlist->m3u_url = $request->m3u_url;
            }
            $playlist->epg = $request->epg;
            $playlist->user_agent = $request->user_agent;
            $playlist->save();
        }
        // DevicePlaylist::where('user_id',Auth::user()->user_id)->where('playlist_id',$playlist->id)->delete();
        // if(!empty($request->device_id)){
        //     foreach($request->device_id as $value){
        //         $check =  DevicePlaylist::where('user_id',Auth::user()->user_id)->where('playlist_id',$playlist->id)->where('device_id', $value)->withTrashed()->first();
        //         if($check){
        //             if($check->deleted_at != null)
        //             {
        //                 $check->deleted_at = null;
        //                 $check->save();
        //             }
        //         }else{
        //             $device_playlist = new DevicePlaylist;
        //             $device_playlist->user_id = Auth::user()->user_id;
        //             $device_playlist->playlist_id = $playlist->id;
        //             $device_playlist->device_id = $value;
        //             $device_playlist->save();
        //         }
        //     }
        // }
        return response()->json(['success' => 1,'message'=>'Playlist Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->delete();
        DevicePlaylist::where('playlist_id',$id)->delete();

        $setting = Setting::where("id", '1')->first();
        $playlistAll = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist = array_merge($playlistAll,$multiplaylist);
        $total_credit = $setting->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }
        return response()->json(['success' => 1,'flag' => 1,'limit'=>$is_limit]);
    }

    public function deleteAll(Request $request)
    {
        $type = $request->type;
        $ids = $request->ids;
        if($type == 3){
            PlaylistMultiDNS::whereIn('unique_id',$ids)->delete();
        }else{
            Playlist::whereIn('id',$ids)->delete();
        }
        DevicePlaylist::whereIn('playlist_id',$ids)->delete();

        $setting = Setting::where("id", '1')->first();
        $playlistAll = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist = array_merge($playlistAll,$multiplaylist);
        $total_credit = $setting->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }
        return response()->json(['success' => 1,'flag' => 1,'limit'=>$is_limit]);
    }

    public function deleteMultiDNS(Request $request,$id)
    {
        $playlist = PlaylistMultiDNS::where('unique_id',$id);
        $playlist->delete();
        DevicePlaylist::whereIn('playlist_id',$id)->delete();

        $setting = Setting::where("id", '1')->first();
        $playlistAll = Playlist::where('user_id',Auth::user()->user_id)->latest()->get()->toArray();
        $multiplaylist = PlaylistMultiDNS::select('unique_id', 'playlist_name')->where('user_id',Auth::user()->user_id)->groupBy('unique_id')->latest()->get()->toArray();
        $playlist = array_merge($playlistAll,$multiplaylist);
        $total_credit = $setting->playlist_limit;
        if(Auth::user()->user_type == 3){
            $total_credit = round(Auth::user()->credits/4);
        }
        if(count($playlist) >= $total_credit){
            $is_limit = 0;
        }else{
            $is_limit = 1;
        }
        return response()->json(['success' => 1,'flag' => 1,'limit'=>$is_limit]);
    }

    public function changePlaylistStatus(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        if($type == 3){
            $obj = PlaylistMultiDNS::where('unique_id',$id)->first();
            if ($obj->status == 0) {
                $obj->status = 1;
                $obj->save();
                return response()->json(['active' => 'Activated successfully.']);
            } else {
                $obj->status = 0;
                $obj->save();
                return response()->json(['suspend' => 'Deactivated successfully.']);
            }
        }else{
            $obj = Playlist::find($id);
            if ($obj->status == 0) {
                $obj->status = 1;
                $obj->save();
                return response()->json(['active' => 'Activated successfully.']);
            } else {
                $obj->status = 0;
                $obj->save();
                return response()->json(['suspend' => 'Deactivated successfully.']);
            }
        }
    }

    public function assignPlaylist(Request $request)
    {
        $device_id = $request->device_id;
        if($request->assign_playlist_id){
            foreach($request->assign_playlist_id as $value){
                $device_playlist = new DevicePlaylist;
                $device_playlist->user_id = Auth::user()->user_id;
                $device_playlist->playlist_id = $value;
                $device_playlist->device_id = $device_id;
                $device_playlist->save();
            }
        }
        return response()->json(['success' => 1]);
    }

    public function removeDevicePlaylist(Request $request)
    {
        $device_id = $request->device_id;
        $playlist_id = $request->playlist_id;
        DevicePlaylist::where('device_id',$device_id)->where('playlist_id',$playlist_id)->delete();
        return response()->json(['success' => 1]);
    }

}
