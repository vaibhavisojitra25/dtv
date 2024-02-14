<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\DNS;
use App\Models\Playlist;
use DataTables;
use App\Helper\Helper;
use Auth;
use File;
use DB;
use Storage;
use Carbon\Carbon;
use Validator;

class DNSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$device_id)
    {
        $dns = DNS::where('user_id',Auth::user()->user_id);

        if ($request->ajax()) {
            return DataTables::eloquent($dns)
                ->editColumn('checkbox', function ($row) {
                    $is_playlist = 0;
                    $playlist = Playlist::where('dns_id',$row->id)->get();
                    if(count($playlist) > 0){
                        $is_playlist = 1;
                    }
                    return '<input type="checkbox" name="dns_checkbox[]" class="dns_checkbox" value="'.$row->id.'" data-is_playlist="'.$is_playlist.'"/>';
                    
                })
                ->addColumn('action', function ($row) {
                    $is_playlist = 0;
                    $playlist = Playlist::where('dns_id',$row->id)->get();
                    if(count($playlist) > 0){
                        $is_playlist = 2;
                    }

                    $action = '
                        <a data-bs-toggle="modal" data-bs-target="#addUpdateDNSMdl" data-id="'.$row->id.'" data-dns_url="'.$row->dns_url.'" class="btn btn-icon btn-success waves-effect waves-float waves-light updateDNS" title="Edit DNS">
                            <span data-feather="edit"></span>
                        </a> 
                        <a href="javascript:void(0);" class="btn btn-icon btn-danger waves-effect waves-float waves-light" onclick="handleConfirmation(\'' . route('dns.destroy', $row->id) . '\', \'' . csrf_token() . '\', '.$is_playlist.')" title="Delete DNS">
                        <span data-feather="trash"></span>
                    </a>';
                  
                    return $action;
                })
                ->rawColumns(['checkbox','action'])
                ->toJson();
        }
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "DNS List"]];
        if (Auth::user()->user_type != 1){
            return view('/content/dns/index', ['breadcrumbs' => $breadcrumbs]);
        }else{
            return redirect()->to('/dashboard');
        }
    }

    public function checkExistDNS(Request $request)
    {
		$dns_id = $request->input('dns_id');
        $dns_url = $request->input('dns_url');

		if(!empty($dns_id)){
			$checkDNS = DNS::selectRaw('*')->where('id','!=',$dns_id)->where('user_id',Auth::user()->user_id)->where('dns_url',$dns_url)->whereNull('deleted_at')->first();
		}else{
			$checkDNS = DNS::selectRaw('*')->where('dns_url',$dns_url)->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')->first();
		}
        // echo "<pre>"; print_r(count($checkDNS)); die;

		if(!empty($checkDNS)) {
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
            'dns_url' => ['required', Rule::unique('dns')->where('user_id',Auth::user()->user_id)->whereNull('deleted_at')],
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        
        $dns = new DNS;
        $dns->user_id = Auth::user()->user_id;
        $dns->dns_url = $request->dns_url;
        $dns->save();
        
        return response()->json(['success' => 1,'message'=>'DNS Created']);
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
        $id = $request->dns_id;

        $validator = Validator::make($request->all(), [
            'dns_url' => ['required', Rule::unique('dns')->where('id',$request->dns_id)->where('user_id',Auth::user()->user_id)->ignore($request->dns_id)->whereNull('deleted_at')],
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
    
        $dns = DNS::findOrFail($id);
        $dns->user_id = Auth::user()->user_id;
        $dns->dns_url = $request->dns_url;
        $dns->save();
        return response()->json(['success' => 1,'message'=>'DNS Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dns = DNS::findOrFail($id);
        $dns->delete();
        return response()->json(['success' => 1]);
    }

    public function deleteAll(Request $request)  
    {  
        $ids = $request->ids;  
        DNS::whereIn('id',$ids)->delete(); 
        return response()->json(['success' => 1]);
    }  

}
