<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;
use DB;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Device;
use App\Models\Playlist;
use App\Models\PlaylistMultiDNS;
use App\Models\DevicePlaylist;
use Carbon\Carbon;
use Illuminate\Support\Str; 
use Session;

class DashboardController extends Controller
{
  // Dashboard - Ecommerce
  public function dashboard(Request $request)
  {
    $filteredData = [];
    $flag = Session::get('flag');
    // if($flag == 3){
    //   return redirect()->to('/thank-you');
    // }
    $response['data'] = [];
    if(Auth::user()->user_type == '1')
    {
      $query = UserSubscription::with('user')->where('status','!=',3)->whereBetween('expiry_date',[Carbon::today()->format('Y-m-d h:i:s'),Carbon::today()->addDays(30)->format('Y-m-d h:i:s')]);

      if (!empty($request->status)) {
        if ($request->status == 4) {
            $request->status = 0;
        }
        $query->where('status',$request->status);
      }else{
        $query->where('status',1);
      }
    
      //$response = UserSubscription::getAllSubscription();
   
      // if (!empty($request->status)) {
      //   if($request->status == 2){
      //       $request->status = 0;
      //   }
      //     $query->where('status', $request->status);
      // }
      $filteredData = $query->latest()->get();
      $response = UserSubscription::getAllPlan();
      // echo "<pre>";print_r($response);die;
      // $startdate = strtotime(Carbon::today()->format('Y-m-d'));  
      // $enddate = strtotime(Carbon::today()->addDays(30)->format('Y-m-d'));  
      // $filteredData = [];
      // dd($response['data']);
      // if($response['status'] == 'success'){
      //   if($startdate && $enddate){
      
      //     if($response['data']){
      //       $filteredData = array_filter($response['data'], function($var) use ($startdate, $enddate) {  
      //         $evtime = strtotime(Carbon::parse($var['expiry_date'])->setTimezone('Asia/Kolkata')->format('Y-m-d'));
      //         return $evtime <= $enddate && $evtime >= $startdate;  
      //       });
      //     }

      //   }else{
      //     $filteredData = $response['data'];
      //   }
      // }
      

      if ($request->ajax()) {
        return DataTables::of($filteredData)
        ->editColumn('email', function ($row) {
          if($row->user){
            return '<div class="d-flex flex-column"><h6 class="user-name text-truncate mb-0">' . $row->user->first_name." ".$row->user->last_name . '</h6><small class="text-truncate text-muted">' .$row->user->email_id . '</small></div>';
          }
        })
        ->editColumn('created_at', function ($row) {
          return Carbon::parse($row['created_at'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
        ->editColumn('starts_at', function ($row) {
          return Carbon::parse($row['starts_at'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
        ->editColumn('activation_date', function ($row) {
          return Carbon::parse($row['activation_date'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
            ->editColumn('amount', function ($row) use ($response) {
              $plan_id = $row->plan_id;
              $data = array_filter($response['data'], function ($element) use ($plan_id) {
                return $element['id'] == $plan_id;
              });
              $data = array_values($data);
              if($data){
                $data =  $data[0];
                $amount = '₹' . $data['price'];
              }else{
                $amount = '-';
              }
          
              return $amount;
            })
            ->editColumn('expiry_date', function ($row) {
              
              $expiry_date =  $row->expiry_date ? Carbon::parse($row->expiry_date)->setTimezone('Asia/Kolkata')->format('d-m-Y') : "-";

              return $expiry_date;
            })
            ->editColumn('status', function ($row) {

              if ($row['status'] == 1) {
                $status = "<span class='badge bg-light-success rounded-pill'>Active</span>";
              } elseif ($row['status'] == 2) {
                $status = "<span class='badge bg-light-info rounded-pill'>Upcoming</span>";
              }  elseif ($row['status'] == 3) {
                $status = "<span class='badge bg-light-warning rounded-pill'>Cancelled</span>";
              } else {
                $status = "<span class='badge bg-light-danger rounded-pill'>Expired</span>";
              }
              return $status;
            })
            ->editColumn('device_id', function ($row) {
              return ($row->device && $row->device->device_code) ? $row->device->device_code->code : '-';
              
            })
            ->editColumn('plan_name', function ($row) use ($response) {

              $plan_id = $row->plan_id;
              $data = array_filter($response['data'], function ($element) use ($plan_id) {
                return $element['id'] == $plan_id;
              });
              $data = array_values($data);
              if($data){
                $data =  $data[0];
                $plan_name = $data['plan_name'];
              }else{
                $plan_name = '-';
              }
              return $plan_name;
            })
            ->addColumn('action', function ($row) {
              $action ="";
              $userSubscription = UserSubscription::with('device')->latest()->first();
              if($userSubscription){
                $action = '
                  <a href="' . route('user-view-account', $userSubscription->user->user_id) . '" class="btn btn-icon btn-info waves-effect waves-float waves-light" title="View Account">
                  <i data-feather="eye"></i> </a>';

                return $action;
              }
              
            })

            // ->filter(function ($instance) use ($request) {
            //   if ($request->get('status') !== 'all') {
            //     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
            //       return Str::contains($row['status'], $request->get('status')) ? true : false;
            //     });
            //   }
            // })

          ->rawColumns(['created_at','starts_at','activation_date', 'email', 'plan_name', 'device_id', 'amount','expiry_date', 'status', 'action'])
            ->make(true);
      }
    }

    if(Auth::user()->user_type != '1'){
      if(empty(Auth::user()->customer_id)){
        $createCustomer = UserSubscription::createCustomer(Auth::user());
        if($createCustomer['status'] == 'success'){
            $user = User::where('user_id', Auth::user()->user_id)->first();
            $user->customer_id = $createCustomer['data']['id'];
            $user->save();
            UserSubscription::where('user_id', Auth::user()->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
        }else{
            $getCustomer = UserSubscription::getCustomerByEmail(Auth::user()->email);
            if($getCustomer['status'] == 'success'){
                $user = User::where('user_id', Auth::user()->user_id)->first();
                $user->customer_id = $getCustomer['data']['id'];
                $user->save();
                UserSubscription::where('user_id', Auth::user()->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
            }
        }
      }else{
          $getCustomer = UserSubscription::getCustomerByEmail(Auth::user()->email);
          if($getCustomer['status'] == 'success'){
              $user = User::where('user_id', Auth::user()->user_id)->first();
              $user->customer_id = $getCustomer['data']['id'];
              $user->save();
              UserSubscription::where('user_id', Auth::user()->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
          }else{
              $createCustomer = UserSubscription::createCustomer(Auth::user());
              if($createCustomer['status'] == 'success'){
                  $user = User::where('user_id', Auth::user()->user_id)->first();
                  $user->customer_id = $createCustomer['data']['id'];
                  $user->save();
                  UserSubscription::where('user_id', Auth::user()->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
              }
          }
      } 
    }
    /* Device chart*/
    $deviceResultLable = $deviceResultValue = array();
    $deviceMaxValue = "";
    $today = Carbon::today();
    $labels = Device::selectRaw('date(created_at) as year')
      ->selectRaw("count(date(created_at)) as a_count")
      ->where('created_at', '>', $today->subDays(7))
      ->groupByRaw('date(created_at)')
      ->orderByRaw('date(created_at)')
      ->get();
    foreach ($labels as $key => $value) {
      $deviceResultLable[] = date("d-m-Y", strtotime($value->year));
      $deviceResultValue[] = (int)$value->a_count;
      $deviceMaxValue = max($deviceResultValue);
    }
   
     /* User chart*/
     $userResultLable = $userResultValue = array();
     $userMaxValue = "";
     $today = Carbon::today();
     $labels = User::selectRaw('date(created_at) as year')
       ->selectRaw("count(date(created_at)) as a_count")
       ->where('user_type','!=','1')
       ->where('created_at', '>', $today->subDays(7))
       ->groupByRaw('date(created_at)')
       ->orderByRaw('date(created_at)')
       ->get();
     foreach ($labels as $key => $value) {
       $userResultLable[] = date("d-m-Y", strtotime($value->year));
       $userResultValue[] = (int)$value->a_count;
       $userMaxValue = max($userResultValue);
     }
     //  print_r(json_encode($resultLable));
     //  print_r(json_encode($maxValue));
     //  exit();
    $breadcrumbs = [['name' => "Home"]];
    if (Auth::user()->user_type == 1) {
      $diviceCount=Device::leftJoin('device_code as DC', function ($join) {
        $join->on('devices.id','=','DC.device_id');
    })->where('DC.status',1)->count();
      $playlist=Playlist::where('status',1)->count();
      $multiplaylist = PlaylistMultiDNS::groupBy('unique_id')->where('status',1)->count();
      $playlistCount = $playlist+$multiplaylist;
      $userCount=User::where('user_type','!=','1')->where('status',1)->count();
      return view('/content/dashboard/admin-dashboard', compact('diviceCount', 'userCount', 'playlistCount'),['breadcrumbs' => $breadcrumbs])
      ->with('user_label', json_encode($userResultLable))
        ->with('user_values', json_encode($userResultValue))
        ->with('user_max', json_encode($userMaxValue))
        ->with('label', json_encode($deviceResultLable))
        ->with('device', $deviceResultValue)
        ->with('values', json_encode($deviceResultValue))
        ->with('max', json_encode($deviceMaxValue));

    } else {

      
    /* Device chart*/
    $deviceResultLable = $deviceResultValue = array();
    $deviceMaxValue = "";
    $today = Carbon::today();
    $labels = Device::selectRaw('date(created_at) as year')
      ->selectRaw("count(date(created_at)) as a_count")
      ->where('created_at', '>', $today->subDays(7))
      ->where('user_id', Auth::user()->user_id)
      ->groupByRaw('date(created_at)')
      ->orderByRaw('date(created_at)')
      ->get();
    foreach ($labels as $key => $value) {
      $deviceResultLable[] = date("d-m-Y", strtotime($value->year));
      $deviceResultValue[] = (int)$value->a_count;
      $deviceMaxValue = max($deviceResultValue);
    }
   
    $diviceCount=Device::where('devices.user_id', Auth::user()->user_id)->leftJoin('device_code as DC', function ($join) {
      $join->on('devices.id','=','DC.device_id');
  })->where('DC.status',1)->count();
    $playlist=Playlist::where('user_id', Auth::user()->user_id)->where('status',1)->count();
    $multiplaylist = PlaylistMultiDNS::where('user_id', Auth::user()->user_id)->where('status',1)->groupBy('unique_id')->count();
    $playlistCount = $playlist+$multiplaylist;
      return view('/content/dashboard/user-dashboard', compact('diviceCount','playlistCount'),['breadcrumbs' => $breadcrumbs])
      ->with('user_label', json_encode($userResultLable))
      ->with('user_values', json_encode($userResultValue))
      ->with('user_max', json_encode($userMaxValue))
      ->with('label', json_encode($deviceResultLable))
      ->with('device', $deviceResultValue)
      ->with('values', json_encode($deviceResultValue))
      ->with('max', json_encode($deviceMaxValue));
    }
  }
}
