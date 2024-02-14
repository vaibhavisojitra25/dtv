<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Device;
use App\Models\DeviceCode;
use App\Models\Playlist;
use App\Models\DevicePlaylist;
use App\Models\Setting;
use App\Models\CreditHistory;
use App\Mail\SendInvoice;
use App\Models\Coupon;
use Illuminate\Support\Facades\Mail;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Str;
use Session;
use DB;

class InvoiceController extends Controller
{
  // pricing
  public function show(Request $request)
  {
    $invoiceData = [];
    if (Auth::user()->user_type == 1) {
      if($request->user_id){
        $userSubscription = UserSubscription::with('device')->where('user_id',$request->user_id);
      }else{
        $userSubscription = UserSubscription::with('device');
      }
    } else {
      $userSubscription = UserSubscription::with('device')->where('user_id', Auth::user()->user_id);
    }
    $startdate = strtotime($request->startdate);
    $enddate = strtotime($request->enddate);
    $filteredData = [];
    if (!empty($request->status)) {
      if ($request->status == 4) {
          $request->status = 0;
      }
      $userSubscription->where('status',$request->status);
    }
    if ($startdate && $enddate) {

      if ($userSubscription) {
        // $filteredData = $userSubscription->whereBetween('starts_at', array($request->startdate, $request->enddate));
        $filteredData = $userSubscription->whereDate('starts_at','>=' ,$request->startdate)->whereDate('starts_at','<=' ,$request->enddate)->latest();
      }
    } else {
      $filteredData = $userSubscription->latest();
    }
    $response = UserSubscription::getAllPlan();
// echo "<pre>" ;print_r($filteredData); die;
    if ($request->ajax()) {

      return DataTables::eloquent($filteredData)
        ->addColumn('email', function ($row) {
          if($row->user){
            return '<div class="d-flex flex-column"><h6 class="user-name text-truncate mb-0">' . $row->user->first_name . " " . $row->user->last_name . '</h6><small class="text-truncate text-muted">' . $row->user->email . '</small></div>';
          }
        })
        ->editColumn('starts_at', function ($row) {
          return Carbon::parse($row['starts_at'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
        ->addColumn('amount', function ($row) use ($response) {
          $plan_id = $row->plan_id;
          $data = array_filter($response['data'], function ($element) use ($plan_id) {
            return $element['id'] == $plan_id;
          });
          $data = array_values($data);
          $data =  $data ? $data[0] : "";

          $amount = $data ?  '₹' . $data['price'] : "-";
          return $amount;
        })
        ->editColumn('activation_date', function ($row) {
          return Carbon::parse($row['activation_date'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
        ->editColumn('expiry_date', function ($row) {
          return Carbon::parse($row['expiry_date'])->setTimezone('Asia/Kolkata')->format('d-m-Y');
        })
        ->editColumn('status', function ($row) {

          if ($row['status'] == 1) {
            $status = "<span class='badge bg-light-success rounded-pill'>Active</span>";
          } elseif ($row['status'] == 2) {
            $status = "<span class='badge bg-light-info rounded-pill'>Upcoming</span>";
          } else {
            $status = "<span class='badge bg-light-danger rounded-pill'>Expired</span>";
          }
          return $status;
        })
        ->editColumn('device_id', function ($row) {
          return ( $row->device && $row->device->device_code) ? $row->device->device_code->code : "";
        })
        ->editColumn('plan_id', function ($row) use ($response) {

          $plan_id = $row->plan_id;
          $data = array_filter($response['data'], function ($element) use ($plan_id) {
            return $element['id'] == $plan_id;
          });
          $data = array_values($data);
          $data =  $data ? $data[0] : "";

          return $data ? $data['plan_name'] : "-";
        })
        ->addColumn('action', function ($row) {
          if (Auth::user()->user_type == '1') {
            $action =
              '<a href="' . route('sendInvoice', $row['id']) . '"  class="btn btn-icon btn-primary waves-effect waves-float waves-light" title="Send Mail"> <i data-feather="send"></i> </a>&nbsp;<a  href="' . route('invoice/preview', $row['id']) . '" target="_blank" class="btn btn-icon btn-info waves-effect waves-float waves-light" title="Preview Invoice"> <i data-feather="eye"></i> </a>
                <a  href="' . route('invoicePDF', $row['id']) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Download Invoice" download> <i data-feather="download"></i> </a>';
          } else {
            $action = ' <a  href="' . route('invoice/preview', $row['id']) . '" target="_blank" class="btn btn-icon btn-info waves-effect waves-float waves-light" title="Preview Invoice"> <i data-feather="eye"></i> </a>
                <a  href="' . route('invoicePDF', $row['id']) . '" class="btn btn-icon btn-success waves-effect waves-float waves-light" title="Download Invoice" download> <i data-feather="download"></i> </a>';
          }

          return $action;
        })

        ->rawColumns(['starts_at', 'email', 'plan_id', 'device_id', 'amount', 'activation_date', 'expiry_date', 'status', 'action'])
        ->filter(function ($query) use ($request) {
          if ($request->has('search')) {
              $serchTerms = $request->search['value'];
              $query->where(function($q) use($serchTerms) {
                  $q->whereHas('user', function($q1) use($serchTerms){
                      $q1->where('email', 'like', "%{$serchTerms}%")
                      ->orWhere(DB::raw('CONCAT(first_name, " ",last_name)'), 'LIKE', '%' . $serchTerms . '%')
                      ->orWhere('first_name', 'like', "%{$serchTerms}%")
                      ->orWhere('last_name', 'like', "%{$serchTerms}%");
                  })->whereHas('device.device_code', function($q1) use($serchTerms){
                    $q1->where('code', 'like', "%{$serchTerms}%");
                });

              });
          }
      })
        ->toJson();
    }

    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Invoice List"]];
    if (Auth::user()->user_id) {
      return view('/content/invoice/index', ['breadcrumbs' => $breadcrumbs]);
    } else {
      return redirect()->to('/dashboard');
    }
  }

  public function sendInvoice($id,$flag=0)
  {
    $user = UserSubscription::where('id', $id)->first();
    if(!$user){
      abort(404);
    }
    $user_id = $user->user_id;
    if(Auth::user()->user_type != 1){
      if($user->user_id != Auth::user()->user_id){
        abort(404);
      }
    }
    $userData = User::where('user_id',$user_id)->first();
    if($userData->user_type != '1'){
      if(empty($userData->customer_id)){
        $createCustomer = UserSubscription::createCustomer($userData);
        if($createCustomer['status'] == 'success'){
            $user = User::where('user_id', $userData->user_id)->first();
            $user->customer_id = $createCustomer['data']['id'];
            $user->save();
            UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
        }else{
            $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
            if($getCustomer['status'] == 'success'){
                $user = User::where('user_id', $userData->user_id)->first();
                $user->customer_id = $getCustomer['data']['id'];
                $user->save();
                UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);
            }
        }
      }else{
          $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
          if($getCustomer['status'] == 'success'){
              $user = User::where('user_id', $userData->user_id)->first();
              $user->customer_id = $getCustomer['data']['id'];
              $user->save();
              UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);

          }else{
              $createCustomer = UserSubscription::createCustomer($userData);
              if($createCustomer['status'] == 'success'){
                  $user = User::where('user_id', $userData->user_id)->first();
                  $user->customer_id = $createCustomer['data']['id'];
                  $user->save();
                  UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
              }
          }
      }
    }

    $device_id=UserSubscription::where('id', $id)->first()->device_id;
    $data['response'] = UserSubscription::where('device_id', $device_id)->with('device')->first();
    $plan = UserSubscription::getAllPlan();
    $data['plan_name'] = "";
    $data['plan_description'] = "";
    $data['plan_amount'] = "";
    foreach ($plan['data'] as $key => $value) {
      if ($data['response']['plan_id'] == $value['id']) {
        $data['plan_name'] = $value['plan_name'];
        $data['plan_description'] = $value['plan_description'];
        $data['plan_amount'] = $value['price'];
      }
    }
    $user = UserSubscription::getCustomerDetailsByID($data['response']['customer_id']);
    if($user['status']=='success'){
      $user = $user['data'];
    }else{
      $user = [];
    }
    $data['products'] = UserSubscription::getProductByID();
    $data['user'] = $user;
    $data['admin'] =   User::where('user_type', '1')->first();
    $adminEmail = Setting::first();
    try{
      if($data && $data['user'] && $data['user'] && $data['user']['email_id']){
        \Mail::to($data['user']['email_id'])
        ->cc($adminEmail->admin_email)
        ->send(new SendInvoice($data,$flag));
      }else{
        flash()->error('Unable to Send Mail Because User not Found');
        return redirect()->back();
      }
    }catch (\Exception $e) {
      flash()->error('Unable to Send Mail');
      return redirect()->back();
    }

    if (\Mail::failures()) {
      flash()->error('Unable to Send Mail Because User not Found');
      return redirect()->back();

    }
    flash()->success('Invoice Send Successfully');
    return redirect()->back();
  }

  public function invoicePreview($id)
  {

    $user = UserSubscription::where('id', $id)->first();
    if(!$user){
      abort(404);
    }
    $user_id = $user->user_id;
    if(Auth::user()->user_type != 1){
      if($user->user_id != Auth::user()->user_id){
        abort(404);
      }
    }

    $userData = User::where('user_id',$user_id)->first();
    if($userData->user_type != '1'){
      if(empty($userData->customer_id)){
        $createCustomer = UserSubscription::createCustomer($userData);
        if($createCustomer['status'] == 'success'){
            $user = User::where('user_id', $userData->user_id)->first();
            $user->customer_id = $createCustomer['data']['id'];
            $user->save();
            UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
        }else{
            $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
            if($getCustomer['status'] == 'success'){
                $user = User::where('user_id', $userData->user_id)->first();
                $user->customer_id = $getCustomer['data']['id'];
                $user->save();
                UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);
            }
        }
      }else{
          $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
          if($getCustomer['status'] == 'success'){
              $user = User::where('user_id', $userData->user_id)->first();
              $user->customer_id = $getCustomer['data']['id'];
              $user->save();
              UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);

          }else{
              $createCustomer = UserSubscription::createCustomer($userData);
              if($createCustomer['status'] == 'success'){
                  $user = User::where('user_id', $userData->user_id)->first();
                  $user->customer_id = $createCustomer['data']['id'];
                  $user->save();
                  UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
              }
          }
      }
    }
    // $response = UserSubscription::getInvoiceByID($id);
    $response = UserSubscription::where('id', $id)->with('device')->first();
    $plan = UserSubscription::getAllPlan();
    $plan_name = $plan_description = $plan_amount = "";
    foreach ($plan['data'] as $key => $value) {
      if ($response->plan_id == $value['id']) {
        $plan_name = $value['plan_name'];
        $plan_description = $value['plan_description'];
        $plan_amount = $value['price'];
      }
    }
    $products = UserSubscription::getProductByID();
    $user = UserSubscription::getCustomerDetailsByID($response->customer_id);
    if($user['status']=='success'){
      $user = $user['data'];
    }else{
      $user = [];
    }
    // echo "<pre>";print_r($response);die;
    $admin = User::where('user_type', '1')->first();
    return view('/content/invoice/invoice', compact('response', 'products', 'admin', 'user', 'plan_name', 'plan_description', 'plan_amount'));
  }


  public function invoicePDF($id)
  {
    $user = UserSubscription::where('id', $id)->first();
    if(!$user){
      abort(404);
    }
    $user_id = $user->user_id;
    if(Auth::user()->user_type != 1){
      if($user->user_id != Auth::user()->user_id){
        abort(404);
      }
    }
    $userData = User::where('user_id',$user_id)->first();
    if($userData->user_type != '1'){
      if(empty($userData->customer_id)){
        $createCustomer = UserSubscription::createCustomer($userData);
        if($createCustomer['status'] == 'success'){
            $user = User::where('user_id', $userData->user_id)->first();
            $user->customer_id = $createCustomer['data']['id'];
            $user->save();
            UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
        }else{
            $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
            if($getCustomer['status'] == 'success'){
                $user = User::where('user_id', $userData->user_id)->first();
                $user->customer_id = $getCustomer['data']['id'];
                $user->save();
                UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);
            }
        }
      }else{
          $getCustomer = UserSubscription::getCustomerByEmail($userData->email);
          if($getCustomer['status'] == 'success'){
              $user = User::where('user_id', $userData->user_id)->first();
              $user->customer_id = $getCustomer['data']['id'];
              $user->save();
              UserSubscription::where('id', $id)->update(['customer_id'=>$getCustomer['data']['id']]);

          }else{
              $createCustomer = UserSubscription::createCustomer($userData);
              if($createCustomer['status'] == 'success'){
                  $user = User::where('user_id', $userData->user_id)->first();
                  $user->customer_id = $createCustomer['data']['id'];
                  $user->save();
                  UserSubscription::where('id', $id)->update(['customer_id'=>$createCustomer['data']['id']]);
              }
          }
      }
    }

    $data['response'] = UserSubscription::where('id', $id)->with('device')->first();
    $plan = UserSubscription::getAllPlan();
    $plan_name = $plan_description = $plan_amount = "";
    foreach ($plan['data'] as $key => $value) {
      if ($data['response']['plan_id'] == $value['id']) {
        $plan_name = $value['plan_name'];
        $plan_description = $value['plan_description'];
        $plan_amount = $value['price'];
      }
    }
    $user = UserSubscription::getCustomerDetailsByID($data['response']['customer_id']);
    if($user['status']=='success'){
      $user = $user['data'];
    }else{
      $user = [];
    }

    $data['plan_name'] = $plan_name;
    $data['plan_description'] = $plan_description;
    $data['plan_amount'] = $plan_amount;
    $data['products'] = UserSubscription::getProductByID();
    $data['admin'] = User::where('user_type', '1')->first();
    $data['user'] = $user;
    $data['invoice'] =  $data['response'];
    $pdf = PDF::loadView('/content/invoice/download_invoice', $data);

    return $pdf->download('invoice_' . date('Y-m-dhis') . '.pdf');
  }
  //   public function downloadInvoice()
  //   {
  //       return view('/content/invoice/download_invoice');
  //   }

  public function subscription(Request $request)
  {
    // $invoice = UserSubscription::query();


    // if (!empty($request->status)) {
    //     if($request->status == 4){
    //         $request->status = 0;
    //     }
    //     $invoice->where('status', $request->status);
    // }

    // if (!empty($request->user_id)) {
    //     $invoice->where('user_id', $request->user_id);
    // }
    // $invoices = $invoice->where('status', '!=' , '2')->latest()->get();

    // if ($request->user_id) {
    //     $userSubscription = UserSubscription::where('user_id',$request->user_id)->where('status',1)->latest()->first();
    //     $response = UserSubscription::getSubscriptionByCustomerID($userSubscription->customer_id);
    // }else{
    //     $response = UserSubscription::getAllSubscription();
    // }
    $response = UserSubscription::getAllSubscription();
    // echo "<pre>";print_r($response);die;

    $startdate = strtotime($request->startdate);
    $enddate = strtotime($request->enddate);
    $filteredData = [];
    if ($response['status'] == 'success') {
      if ($startdate && $enddate) {

        if ($response['data']) {
          $filteredData = array_filter($response['data'], function ($var) use ($startdate, $enddate) {
            $evtime = strtotime(Carbon::parse($var['starts_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d'));
            return $evtime <= $enddate && $evtime >= $startdate;
          });
        }
      } else {
        $filteredData = $response['data'];
      }
    }

    if ($request->ajax()) {

      return DataTables::of($filteredData)
        ->editColumn('email', function ($row) {

          return '<div class="d-flex flex-column"><h6 class="user-name text-truncate mb-0">' . $row['customer']['first_name'] . " " . $row['customer']['last_name'] . '</h6><small class="text-truncate text-muted">' . $row['customer']['email_id'] . '</small></div>';
        })
        ->editColumn('revenu', function ($row) {
          $amount = $row['currency_symbol'] . $row['amount'];
          return $amount;
        })
        ->editColumn('status', function ($row) {
          if ($row['status'] == 'live') {
            $status = "<span class='badge bg-light-success rounded-pill'>Live</span>";
          } elseif ($row['status'] == 'trial') {
            $status = "<span class='badge bg-light-info rounded-pill'>Trial</span>";
          } elseif ($row['status'] == 'cancelled') {
            $status = "<span class='badge bg-light-warning rounded-pill'>Cancelled</span>";
          } elseif ($row['status'] == 'expired') {
            $status = "<span class='badge bg-danger rounded-pill'>Expired</span>";
          } else {
            $status = "<span class='badge bg-light-primary rounded-pill'>Pending</span>";
          }
          return $status;
        })
        ->editColumn('date', function ($row) {
          $starts_at = $row['starts_at'];
          return Carbon::parse($starts_at)->setTimezone('Asia/Kolkata')->format('Y-m-d h:i:s');
        })

        ->editColumn('billing_date', function ($row) {

          $next_billing_date =  $row['next_billing_date'] ? Carbon::parse($row['next_billing_date'])->setTimezone('Asia/Kolkata')->format('M d, Y') : "-";

          return '<div class="d-flex flex-column"><h6 class="user-name text-truncate mb-0">' . Carbon::parse($row['last_billing_date'])->setTimezone('Asia/Kolkata')->format('M d, Y') . '</h6><small class="text-truncate text-muted">' . $next_billing_date . '</small></div>';
        })

        ->editColumn('plan_name', function ($row) {
          return $row['plan']['plan_name'];
        })
        ->filter(function ($instance) use ($request) {
          if ($request->get('status') !== 'all') {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              return Str::contains($row['status'], $request->get('status')) ? true : false;
            });
          }
        })
        ->rawColumns(['date', 'email', 'plan_name', 'billing_date', 'revenu', 'status'])
        ->make(true);
    }

    $breadcrumbs = [['link' => "/", 'name' => "Home"], [ 'name' => "Subscription List"]];
    return view('/content/invoice/subscription', ['breadcrumbs' => $breadcrumbs]);
  }

  public function showMyOrder(Request $request)
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], [ 'name' => "My Order List"]];

    return view('/content/invoice/index', ['breadcrumbs' => $breadcrumbs]);
  }

  public function getCheckoutLink(Request $request)
  {
    $product_id = env('PUBBLY_PRODUCT_ID');
    $plan_id = $request->plan_id;
    $device_id = $request->device_id;
    $flag = $request->flag ? $request->flag : 0;

    $curl2 = curl_init();

    curl_setopt_array($curl2, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/checkoutpage/' . $product_id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),

      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY') . ':' . env('PUBBLY_STRIPE_KEY'),
    ));
    $response = curl_exec($curl2);

    curl_close($curl2);
    $response = json_decode($response, true);

    if ($response['status'] == 'success') {
      $data = array_filter($response['data'], function ($element) use ($plan_id) {
        return $element['id'] == $plan_id;
      });
    }
    if($flag == 0){
      $device_title = $request->device_title;
      // $duration = $request->duration;
      $mac_id = $request->mac_id;
      $mac_key = $request->mac_key;
      $playlist_id = $request->playlist_id;
      $note = $request->note;

      Session::put('device_title', $device_title);
      // Session::put('duration', $duration);
      Session::put('mac_id', $mac_id);
      Session::put('mac_key', $mac_key);
      Session::put('playlist_id', $playlist_id);
      Session::put('note', $note);
    }else if($flag == 1){
      Session::put('device_id', $device_id);
    }else if($flag == 2){
      Session::put('plan_id', $plan_id);
    }
    Session::put('flag', $flag);
    $data = array_values($data);
    return response()->json(['success' => 1, 'data' => $data]);
  }

  public function getThankYou(Request $request)
  {
    $product_id = env('PUBBLY_PRODUCT_ID');
    // print_r(Auth::user());
     $flag = Session::get('flag');
    //echo $flag die;
    if($flag == 0){
      $device_title = Session::get('device_title');
      // $duration = Session::get('duration');
      $mac_id = Session::get('mac_id');
      $mac_key = Session::get('mac_key');
      $playlist_id = Session::get('playlist_id');
      $note = Session::get('note');
    }else if($flag == 1){
      $device_id = Session::get('device_id');
    }else if($flag == 2){
      $plan_id = Session::get('plan_id');
    }else if($flag == 3){
      $mac_id = Session::get('mac_id');
      $mac_key = Session::get('mac_key');
    }

    $hostedpage = $request->hostedpage;
    $curl2 = curl_init();

    // $postdata['device_id'] = $device_id;
    $postdata['hostedpage'] = $hostedpage;

    curl_setopt_array($curl2, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/verifyhosted',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($postdata),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),

      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY') . ':' . env('PUBBLY_STRIPE_KEY'),
    ));
    $response = curl_exec($curl2);

    curl_close($curl2);
    $response = json_decode($response, true);

    if ($response['status'] == 'success') {
      $curl2 = curl_init();

      $postdata['hostedpage'] = $hostedpage;

      curl_setopt_array($curl2, array(
        CURLOPT_URL => 'https://payments.pabbly.com/api/v1/hostedpage',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postdata),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),

        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => env('PUBBLY_API_KEY') . ':' . env('PUBBLY_STRIPE_KEY'),
      ));
      $response1 = curl_exec($curl2);

      curl_close($curl2);
      $response1 = json_decode($response1, true);
      // echo "<pre>"; print_r($response1); die;

      if ($response1['status'] == 'error' || $response1['status'] == 'failed') {
        flash()->error(str_replace('"', '', $response1['message']));
        return redirect()->back();
      }
      $user_id = Auth::user()->user_id;


      $current_date = Carbon::now();
      $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
      $expire_date = Carbon::parse($current_date)->addMinutes(72*60)->format('Y-m-d h:i:s');
      if($flag == 0){
        $device = new Device;
        $device->user_id = Auth::user()->user_id;
        $device->device_title = $device_title;
        $device->mac_id = $mac_id;
        $device->mac_key = $mac_key;
        $device->note = $note;
        $device->save();
      }
      if($flag == 0){
        $deviceCode = new DeviceCode;
        $code =  $deviceCode->generate_code();
        $deviceCode->device_id = $device->id;
        $deviceCode->code = $code;
        $deviceCode->user_id = Auth::user()->user_id;
        // $deviceCode->duration = 72;
        // $deviceCode->expire_date = $expire_date;
        $deviceCode->save();
        if($playlist_id){
          DevicePlaylist::where('device_id',$device->id)->delete();
          foreach($playlist_id as $value){
              $device_playlist = new DevicePlaylist;
              $device_playlist->user_id = Auth::user()->user_id;
              $device_playlist->playlist_id = $value;
              $device_playlist->device_id = $device->id;
              $device_playlist->save();
          }
        }
        $device_id = $device->id;
      }else if($flag == 1){
        DeviceCode::where('device_id',$device_id)->update(['status'=>1]);
        $device_id = $device_id;
      }else if($flag == 3){

        $device = new Device;
        $device->user_id = $user_id;
        $device->device_title = Auth::user()->first_name.' Device';
        $device->mac_id = $mac_id;
        $device->mac_key = $mac_key;
        $device->note = "";
        $device->save();

        $device_id = $device->id;

        $deviceCode = new DeviceCode;
        $code =  $deviceCode->generate_code();
        $deviceCode->device_id = $device->id;
        $deviceCode->code = $code;
        $deviceCode->user_id = $user_id;
        $deviceCode->save();
      }

    if($flag == 0 || $flag == 1 || $flag == 3){
      $usersSubscription = UserSubscription::where('device_id', $device_id)->where('status', 1)->first();

      if (empty($usersSubscription)) {
        $usersSubscription = UserSubscription::where('device_id', $device_id)->where('status', 0)->latest()->first();
      }
      if (empty($usersSubscription)) {
        $usersSubscription = new UserSubscription;
        $usersSubscription->user_id = $user_id;
        $usersSubscription->device_id = $device_id;
        $usersSubscription->customer_id = $response1['data']['customer']['id'];
        $usersSubscription->product_id = $response1['data']['product']['id'];
        $usersSubscription->plan_id = $response1['data']['plan']['id'];
        $usersSubscription->credits = isset($response1['data']['plan']['meta_data']['credit_amount']) ? $response1['data']['plan']['meta_data']['credit_amount'] : 0;
        $usersSubscription->starts_at = Carbon::parse($response1['data']['subscription']['starts_at'])->format('Y-m-d h:i:s');
        $usersSubscription->activation_date = Carbon::parse($response1['data']['subscription']['activation_date'])->format('Y-m-d h:i:s');
        $usersSubscription->expiry_date = Carbon::parse($response1['data']['subscription']['expiry_date'])->format('Y-m-d h:i:s');
        $usersSubscription->trial_days = $response1['data']['subscription']['trial_days'];
        $usersSubscription->trial_expiry_date = $response1['data']['subscription']['trial_expiry_date'] ? $response1['data']['subscription']['trial_expiry_date'] : NULL;
        $usersSubscription->next_billing_date = $response1['data']['subscription']['next_billing_date'];
        $usersSubscription->last_billing_date = Carbon::parse($response1['data']['subscription']['activation_date'])->format('Y-m-d h:i:s');
        $usersSubscription->billing_period = $response1['data']['subscription']['plan']['billing_period'];
        $usersSubscription->billing_period_num = $response1['data']['subscription']['plan']['billing_period_num'];
        $usersSubscription->billing_cycle = $response1['data']['subscription']['plan']['billing_cycle'];
        $usersSubscription->billing_cycle_num = $response1['data']['subscription']['plan']['billing_cycle_num'] ? $response1['data']['subscription']['plan']['billing_cycle_num'] : 0;
        $usersSubscription->plan_code = $response1['data']['plan']['plan_code'];

        $usersSubscription->subscription_id = $response1['data']['subscription']['id'];
        $usersSubscription->status = 1;
        $usersSubscription->save();
      } else {
        $usersSubscriptionLast = UserSubscription::where('device_id', $device_id)->where('status', 0)->first();
        // if (empty($usersSubscriptionLast)) {
        //   $usersSubscriptionLast = UserSubscription::where('device_id', $device_id)->where('status', 2)->latest()->first();
        // }
        if (empty($usersSubscriptionLast)) {
          $usersSubscriptionLast = new UserSubscription;
        }
        $status = 1;
        // if ($usersSubscriptionLast->status == 0) {
        //   $status = 1;
        // } else {
        //   $status = 2;
        // }
        $usersSubscriptionLast->user_id = $user_id;
        $usersSubscriptionLast->device_id = $device_id;
        $usersSubscriptionLast->customer_id = $response1['data']['customer']['id'];
        $usersSubscriptionLast->product_id = $response1['data']['product']['id'];
        $usersSubscriptionLast->plan_id = $response1['data']['plan']['id'];
        $usersSubscriptionLast->credits = isset($response1['data']['plan']['meta_data']['credit_amount']) ? $response1['data']['plan']['meta_data']['credit_amount'] : 0;
        $usersSubscriptionLast->starts_at = Carbon::parse($response1['data']['subscription']['starts_at'])->format('Y-m-d h:i:s');
        $usersSubscriptionLast->activation_date = Carbon::parse($response1['data']['subscription']['activation_date'])->format('Y-m-d h:i:s');
        $usersSubscriptionLast->expiry_date = Carbon::parse($response1['data']['subscription']['expiry_date'])->format('Y-m-d h:i:s');
        $usersSubscriptionLast->trial_days = $response1['data']['subscription']['trial_days'];
        $usersSubscriptionLast->trial_expiry_date = $response1['data']['subscription']['trial_expiry_date'] ? $response1['data']['subscription']['trial_expiry_date'] : NULL;
        $usersSubscriptionLast->next_billing_date = $response1['data']['subscription']['next_billing_date'];
        $usersSubscriptionLast->last_billing_date = Carbon::parse($response1['data']['subscription']['expiry_date'])->format('Y-m-d h:i:s');
        $usersSubscriptionLast->billing_period = $response1['data']['subscription']['plan']['billing_period'];
        $usersSubscriptionLast->billing_period_num = $response1['data']['subscription']['plan']['billing_period_num'];
        $usersSubscriptionLast->billing_cycle = $response1['data']['subscription']['plan']['billing_cycle'];
        $usersSubscriptionLast->billing_cycle_num = $response1['data']['subscription']['plan']['billing_cycle_num'] ? $response1['data']['subscription']['plan']['billing_cycle_num'] : 0;
        $usersSubscriptionLast->plan_code = $response1['data']['plan']['plan_code'];
        $usersSubscriptionLast->subscription_id = $response1['data']['subscription']['id'];
        $usersSubscriptionLast->status = $status;
        $usersSubscriptionLast->save();
      }
    }
      // echo "<pre>"; print_r($response1); die;
      if((isset($response1['data']['plan']['meta_data']['is_credit']) && $response1['data']['plan']['meta_data']['is_credit'] == 1)){
          if(isset($response1['data']['plan']['meta_data']['is_credit_unlimited']) && $response1['data']['plan']['meta_data']['is_credit_unlimited'] == 1){
            $currentDateTime = Carbon::now();
            $credit_expire_date = Carbon::now()->addMonth();
            User::where('user_id',$user_id)->update(['unlimited_credits'=> 10000000,'is_unlimited_credit'=> 1,'credit_expire_date'=>$credit_expire_date]);
          }else if(isset($response1['data']['plan']['meta_data']['credit_amount'])){
            User::where('user_id',$user_id)->increment('credits', $response1['data']['plan']['meta_data']['credit_amount']);
          }
          if($flag == 2){
            if(isset($response1['data']['plan']['meta_data']['is_credit_unlimited']) && $response1['data']['plan']['meta_data']['is_credit_unlimited'] == 1){
                $credit_history = new CreditHistory;
                $credit_history->credits = -1;
                $credit_history->user_id = $user_id;
                $credit_history->is_credited = 1;
                $credit_history->credited_to = $user_id;
                $credit_history->plan_id = $plan_id ? $plan_id : $response1['data']['plan']['id'];
                $credit_history->added_by = $user_id;
                $credit_history->save();
            }else if(isset($response1['data']['plan']['meta_data']['credit_amount'])){
              $credit_history = new CreditHistory;
              $credit_history->credits = $response1['data']['plan']['meta_data']['credit_amount'];
              $credit_history->user_id = $user_id;
              $credit_history->is_credited = 1;
              $credit_history->credited_to = $user_id;
              $credit_history->plan_id = $plan_id ? $plan_id : $response1['data']['plan']['id'];
              $credit_history->added_by = $user_id;
              $credit_history->save();
            }
          }
      }

      Session::forget('device_title');
      // Session::forget('duration');
      Session::forget('mac_id');
      Session::forget('mac_key');
      Session::forget('playlist_id');
      Session::forget('note');
      Session::forget('device_id');

      if($flag == 0){
        Session::forget('flag');
        flash()->success('Device Created.');
        return redirect()->to('/device/list/'.$device_id);
      }elseif($flag == 1){
        Session::forget('flag');
        flash()->success('Device Upgraded.');
        return redirect()->to('/device/list/0');
      }elseif($flag == 2){
        Session::forget('flag');
        flash()->success('Credits Added.');
        return redirect()->to('/credits/list');
      }elseif($flag == 3){
        Session::forget('flag');
        flash()->success('Device Added.');
        return redirect()->to('/playlist/list/'.$device_id);
      }

    } else {
      if ($response['status'] == 'error' || $response['status'] == 'failed') {
        flash()->error(str_replace('"', '', $response['message']));
        return redirect()->back();
      }
    }
  }

  public function CheckCouponCode(Request $request)
  {
    $coupon_code = $request->input('coupon_code');
    $plan_id = $request->input('plan_id');

    $checkCouponCode = Coupon::selectRaw('*')->where('code', $coupon_code)->where('is_used', 0)->where('status', 1)->first();
    if (!empty($checkCouponCode) && $checkCouponCode->parent_id != 0) {
      $checkCoupon = Coupon::selectRaw('*')->where('plan_id', $plan_id)->where('id', $checkCouponCode->parent_id)->first();
      if (!empty($checkCoupon)) {
        return json_encode(TRUE);
      } else {
        return json_encode(FALSE);
      }
    } else if (!empty($checkCouponCode) && $checkCouponCode->plan_id == $plan_id) {
      return json_encode(TRUE);
    } else {
      return json_encode(FALSE);
    }
  }

  public function upgradePlan(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'plan_id' => 'required',
        // 'coupon_code' => 'required',
        'device_id' => 'required',
        'customer_id' => 'required',
        // 'price' => 'required',
        // 'trial_amount' => 'required'
      ]);
      if ($validator->fails()) {
        $response['success'] = false;
        $response['message'] = $validator->errors()->first();
        return response()->json($response, 200);
      }
      $user_id = $request->user_id;
      $device_id = $request->device_id;
      $customer_id = $request->customer_id;
      $coupon_code = $request->coupon_code;
      $plan_id = $request->plan_id;

      $user = User::where('user_id', $user_id)->first();

      $usersSubscription = UserSubscription::where('device_id', $device_id)->where('status', 1)->first();

      if (empty($usersSubscription)) {
        $usersSubscription = UserSubscription::where('device_id', $device_id)->where('status', 0)->latest()->first();
      }
      $response = UserSubscription::getAllPlan();

      if ($response['status'] == 'success') {

        $data = array_filter($response['data'], function ($element) use ($plan_id) {
          return $element['id'] == $plan_id;
        });
        $data = array_values($data);
        $data =  $data[0];
      } else {
        $response['success'] = false;
        $response['message'] = str_replace('"', '', $response['message']);
        return response()->json($response, 200);
      }
// echo "<pre>"; print_r($data); die;
      $current_date = Carbon::now();
      $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
      $trialExpireDate = $activationDate = $expire_date = $next_billing_date = $last_billing_date = NULL;
      if ($data['trial_period'] > 0) {
        if ($data['trial_type'] == 'day') {
          $activationDate = $trialExpireDate = Carbon::parse($todayDate)->addDay($data['trial_period'])->format('Y-m-d h:i:s');
        }
        if ($data['trial_type'] == 'month') {
          $activationDate = $trialExpireDate = Carbon::parse($todayDate)->addMonth($data['trial_period'])->format('Y-m-d h:i:s');
        }
      } else {
        $activationDate = Carbon::parse($todayDate)->format('Y-m-d h:i:s');
      }

      if ($data['billing_cycle'] == 'onetime') {
        $expire_date = Carbon::parse($activationDate)->addYears('100')->format('Y-m-d h:i:s');
        $next_billing_date = Carbon::parse($activationDate)->format('Y-m-d h:i:s');
        if ($data['billing_period'] == 'm') {
          $next_billing_date = Carbon::parse($activationDate)->addMonths($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
        if ($data['billing_period'] == 'y') {
          $next_billing_date  = Carbon::parse($activationDate)->addYears($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
        if ($data['billing_period'] == 'w') {
          $next_billing_date = Carbon::parse($activationDate)->addWeeks($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
      } else {
        if ($data['billing_period'] == 'm') {
            if($data['billing_cycle_num']){
                $expire_date = Carbon::parse($activationDate)->addMonths($data['billing_cycle_num'])->format('Y-m-d h:i:s');
            }else{
                $expire_date = Carbon::parse($activationDate)->addMonths($data['billing_period_num'])->format('Y-m-d h:i:s');
            }
          $next_billing_date = Carbon::parse($activationDate)->addMonths($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
        if ($data['billing_period'] == 'y') {
            if($data['billing_cycle_num']){
                $expire_date = Carbon::parse($activationDate)->addYears($data['billing_cycle_num'])->format('Y-m-d h:i:s');
            }else{
                $expire_date = Carbon::parse($activationDate)->addYears($data['billing_period_num'])->format('Y-m-d h:i:s');
            }
          $next_billing_date = Carbon::parse($activationDate)->addYears($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
        if ($data['billing_period'] == 'w') {
            if($data['billing_cycle_num']){
                $expire_date = Carbon::parse($activationDate)->addWeeks($data['billing_cycle_num'])->format('Y-m-d h:i:s');
            }else{
                $expire_date = Carbon::parse($activationDate)->addWeeks($data['billing_period_num'])->format('Y-m-d h:i:s');
            }
          $next_billing_date = Carbon::parse($activationDate)->addWeeks($data['billing_period_num'])->format('Y-m-d h:i:s');
        }
      }
      if (empty($usersSubscription)) {
        $usersSubscription = new UserSubscription;
        $usersSubscription->user_id = $user_id;
        $usersSubscription->device_id = $device_id;
        $usersSubscription->coupon_code = $coupon_code;
        $usersSubscription->customer_id = $customer_id;
        $usersSubscription->product_id = $data['product_id'];
        $usersSubscription->plan_id = $data['id'];
        $usersSubscription->plan_code = $data['plan_code'];
        $usersSubscription->credits = isset($data['meta_data']['credit_amount']) ? $data['meta_data']['credit_amount'] : 0;
        $usersSubscription->reseller_credit_amount = isset($data['meta_data']['reseller_credit']) ? $data['meta_data']['reseller_credit'] : 0;
        $usersSubscription->starts_at = $todayDate;
        $usersSubscription->activation_date = $activationDate;
        $usersSubscription->expiry_date = $expire_date;
        $usersSubscription->trial_days = $data['trial_period'];
        $usersSubscription->trial_expiry_date = $trialExpireDate;
        $usersSubscription->next_billing_date = $next_billing_date;
        $usersSubscription->last_billing_date = $activationDate;
        $usersSubscription->billing_period = $data['billing_period'];
        $usersSubscription->billing_period_num = $data['billing_period_num'];
        $usersSubscription->billing_cycle = $data['billing_cycle'];
        $usersSubscription->billing_cycle_num =  $data['billing_cycle_num'] ? $data['billing_cycle_num'] : 0;
        $usersSubscription->subscription_id = "";
        $usersSubscription->status = 1;
        $usersSubscription->save();
        $coupon = Coupon::where('code', $coupon_code)->first();
        if($coupon){
          $coupon->is_used = 1;
          $coupon->save();
        }
        $subscription_id = $usersSubscription->id;

      } else {
        $usersSubscriptionLast = UserSubscription::where('device_id', $device_id)->where('status', 0)->first();
        // if (empty($usersSubscriptionLast)) {
        //   $usersSubscriptionLast = UserSubscription::where('device_id', $device_id)->where('status', 2)->latest()->first();
        // }
        if (empty($usersSubscriptionLast)) {
          $usersSubscriptionLast = new UserSubscription;
        }
        $status = 1;
        // if ($usersSubscriptionLast->status == 0) {
        //   $status = 1;
        // } else {
        //   $status = 2;
        // }
        $usersSubscriptionLast->user_id = $user_id;
        $usersSubscriptionLast->device_id = $device_id;
        $usersSubscriptionLast->coupon_code = $coupon_code;
        $usersSubscriptionLast->customer_id = $customer_id;
        $usersSubscriptionLast->product_id = $data['product_id'];
        $usersSubscriptionLast->plan_id = $data['id'];
        $usersSubscriptionLast->plan_code = $data['plan_code'];
        $usersSubscriptionLast->credits = isset($data['meta_data']['credit_amount']) ? $data['meta_data']['credit_amount'] : 0;
        $usersSubscriptionLast->reseller_credit_amount = isset($data['meta_data']['reseller_credit']) ? $data['meta_data']['reseller_credit'] : 0;
        $usersSubscriptionLast->starts_at = $todayDate;
        $usersSubscriptionLast->activation_date = $activationDate;
        $usersSubscriptionLast->expiry_date = $expire_date;
        $usersSubscriptionLast->billing_period = $data['billing_period'];
        $usersSubscriptionLast->billing_period_num = $data['billing_period_num'];
        $usersSubscriptionLast->billing_cycle = $data['billing_cycle'];
        $usersSubscriptionLast->billing_cycle_num = $data['billing_cycle_num'] ? $data['billing_cycle_num'] : 0;
        $usersSubscriptionLast->trial_days = $data['trial_period'];
        $usersSubscriptionLast->trial_expiry_date = $trialExpireDate;
        $usersSubscriptionLast->next_billing_date = $next_billing_date;
        $usersSubscriptionLast->last_billing_date = $activationDate;
        $usersSubscriptionLast->subscription_id = "";
        $usersSubscriptionLast->status = $status;
        $usersSubscriptionLast->save();
        $coupon = Coupon::where('code', $coupon_code)->first();
        if($coupon){
          $coupon->is_used = 1;
          $coupon->save();
        }
        $subscription_id = $usersSubscriptionLast->id;

      }
      if((isset($data['meta_data']['is_credit']) && $data['meta_data']['is_credit'] == 1) && isset($data['meta_data']['credit_amount'])){
        User::where('user_id',$user_id)->increment('credits', $data['meta_data']['credit_amount']);
      }
      $device_code = DeviceCode::where('device_id', $device_id)->orderBy('created_at','DESC')->first();
      if($device_code){
        $device_code->status = 1;
        $device_code->save();
      }
      if($request->flag && $request->flag == 1){
        Session::put('device_id',$device_id);
        $this->sendInvoice($subscription_id,1);
      }
      $response['success'] = true;
      $response['expire_date'] = $expire_date;
      $response['message'] = "Users Plan Upgrade successfully";
      return response()->json($response, 200);

    } catch (\Exception $e) {
      $response['success'] = false;
      $response['message'] = $e->getMessage();
      return response()->json($response, 200);
    }
  }

  public function canceledSubscription(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'subscription_id' => 'required',
      ]);
      if ($validator->fails()) {
        flash()->error($validator->errors()->first());
        return redirect()->back();
      }

      $user_id = Auth::user()->user_id;
      $subscription_id = $request->subscription_id;
      $postdata['cancel_at_end'] = true;
      $curl2 = curl_init();

      curl_setopt_array($curl2, array(
        CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscription/' . $subscription_id . '/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postdata),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),

        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => env('PUBBLY_API_KEY') . ':' . env('PUBBLY_STRIPE_KEY'),
      ));
      $response = curl_exec($curl2);

      curl_close($curl2);
      $response = json_decode($response, true);

      // print_r($response);die;
      if ($response['status'] == 'success') {

        $usersSubscription = UserSubscription::where('user_id', $user_id)->where('subscription_id', $subscription_id)->where('status', 1)->first();
        $usersSubscription->status = 3;
        $usersSubscription->save();

        return response()->json(['success' => 1]);
      } else {
        return response()->json(['success' => 0]);
      }
    } catch (\Exception $e) {
      flash()->error($e->getMessage());
      return redirect()->back();
    }
  }

  public function deleteSubscription(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'subscription_id' => 'required',
      ]);
      if ($validator->fails()) {
        flash()->error($validator->errors()->first());
        return redirect()->back();
      }

      $user_id = Auth::user()->user_id;
      $subscription_id = $request->subscription_id;
      UserSubscription::where('user_id', $user_id)->where('subscription_id', $subscription_id)->where('status', 2)->delete();

      return response()->json(['success' => 1]);
      // $curl2 = curl_init();

      // curl_setopt_array($curl2, array(
      // CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscriptions/'.$subscription_id,
      // CURLOPT_RETURNTRANSFER => true,
      // CURLOPT_ENCODING => '',
      // CURLOPT_MAXREDIRS => 10,
      // CURLOPT_TIMEOUT => 0,
      // CURLOPT_FOLLOWLOCATION => true,
      // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      // CURLOPT_CUSTOMREQUEST => 'DELETE',
      // CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      // CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      // ));
      // $response = curl_exec($curl2);
      // curl_close($curl2);
      // $response = json_decode($response,true);

      // if($response['status'] == 'success'){
      //     UserSubscription::where('user_id', $user_id)->where('subscription_id', $subscription_id)->where('status', 2)->delete();

      //     return response()->json(['success' => 1]);
      // }else{
      //     return response()->json(['success' => 0]);
      // }

    } catch (\Exception $e) {
      flash()->error($e->getMessage());
      return redirect()->back();
    }
  }

  public function destroy($id)
  {
    $device = UserSubscription::findOrFail($id);
    $device->delete();
    flash()->success('Invoice deleted successfully');
    return redirect()->to('/dashboard');
  }
}
