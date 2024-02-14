<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
  
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_subscriptions'; 
    protected $primaryKey = 'id';
    protected $dates = ['start_date','next_billing_date','last_billing_date','invoice_due_date', 'activation_date' ,'expiry_date','created_at'];
  
    public function getStartDateFormattedAttribute()
    {
      return $this->start_date->format('d-m-Y');
    }
    public function getInvoiceDueDateFormattedAttribute()
    {
      if(!empty($this->invoice_due_date)){
      return $this->invoice_due_date->format('F d, Y h:i A');
    }else{
      return "-";
    }
    }
    public function getNextBillingDateFormattedAttribute()
    {
      if(!empty($this->next_billing_date)){
        return $this->next_billing_date->format('d-m-Y');
      }else{
        return "-";
      }
      
    }
    public function getLastBillingDateFormattedAttribute()
    {
      return $this->last_billing_date->format('d-m-Y');
    }
    public function getActivationDateFormattedAttribute()
    {
      return $this->activation_date->format('d-m-Y');
    }
    public function getExpiryDateFormattedAttribute()
    {
      return $this->expiry_date->format('d-m-Y');
    }
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d-m-Y');
    }
        public function plan()
    {
        return $this->hasOne('App\Models\Plan', 'plan_id', 'plan_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
    public function device()
    {
        return $this->hasOne('App\Models\Device', 'id', 'device_id');
    }

    public static function getAllPlan(){
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/plans/'.env('PUBBLY_PRODUCT_ID'),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $response = json_decode($response,true);

      return $response;
  }

  public static function createCustomer($user){

    $postdata['first_name'] = $user->first_name;
    $postdata['last_name'] = $user->last_name;
    $postdata['email_id'] = $user->email;
    $postdata['phone'] = $user->phone_no;

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://payments.pabbly.com/api/v1/customer',
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

    return $response;
  }

  public static function getCustomerByEmail($email){
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://payments.pabbly.com/api/v1/customer/?email='.$email,
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
    CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
    ));
    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response,true);

    return $response;
  }

  public static function getCustomerDetailsByID($customer_id){
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/customer/'.$customer_id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $response = json_decode($response,true);

      return $response;
  }

  public static function getPurchaseInfoByCustomerID($customer_id,$status){
    $curl3 = curl_init();

    curl_setopt_array($curl3, array(
    // CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscriptions/'.$customer_id.'?product_id='.env('PUBBLY_PRODUCT_ID').'&status='.$status,
    CURLOPT_URL => 'https://payments.pabbly.com/api/v1/customer/purchase-info/'.$customer_id,
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
    CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
    ));
    $response3 = curl_exec($curl3);
    
    curl_close($curl3);
    $response3 = json_decode($response3,true);

    return $response3;
}

  public static function getSubscriptionByCustomerID($customer_id){
        $curl3 = curl_init();

        curl_setopt_array($curl3, array(
        CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscriptions/'.$customer_id.'?product_id='.env('PUBBLY_PRODUCT_ID'),
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
        CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
        ));
        $response3 = curl_exec($curl3);
        
        curl_close($curl3);
        $response3 = json_decode($response3,true);

        return $response3;
    }

    public static function getInvoiceByCustomerID($customer_id){
      
      $curl3 = curl_init();
      curl_setopt_array($curl3, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/invoices/'.$customer_id,
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
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));
      $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);
      return $response3;
  }
  public static function getAllSubscription()
  {
    $curl3 = curl_init();

      curl_setopt_array($curl3, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscriptions?product_id='.env('PUBBLY_PRODUCT_ID'),
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
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));
      $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);

      return $response3;
  }

  public static function getAllInvoice($start_date="",$end_date="")
  {
    $curl3 = curl_init();
    if(!empty($start_date) && !empty($end_date)){
      $url = 'https://payments.pabbly.com/api/v1/invoices?product_id='.env('PUBBLY_PRODUCT_ID').'&start_date='.$start_date.'&end_date='.$end_date.'&page=1&per_page=5000000';
    }else{
      $url = 'https://payments.pabbly.com/api/v1/invoices?product_id='.env('PUBBLY_PRODUCT_ID').'&page=1&per_page=5000000';
    }
      curl_setopt_array($curl3, array(
      CURLOPT_URL => $url,
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
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));
      $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);

      return $response3;
  }

  public static function getScheduledSubscriptionByID($subscription_id)
  {
    $curl3 = curl_init();

        curl_setopt_array($curl3, array(
        CURLOPT_URL => 'https://payments.pabbly.com/api/v1/scheduledchanges/'.$subscription_id,
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
        CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
        ));
        $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);
      return $response3;
  }
  public static function getInvoiceByID($invoice_id){
      $curl3 = curl_init();
      curl_setopt_array($curl3, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/invoice/'.$invoice_id,
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
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));
      $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);

      return $response3;
  }

  public static function getCustomerPurchaseDetails($customer_id){
    $curl3 = curl_init();
    curl_setopt_array($curl3, array(
    CURLOPT_URL => 'https://payments.pabbly.com/api/v1/customer/purchase-info/'.$customer_id,
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
    CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
    ));
    $response3 = curl_exec($curl3);
    
    curl_close($curl3);
    $response3 = json_decode($response3,true);

    return $response3;
  }

  public static function getProductByID()
  {
    $curl3 = curl_init();

      curl_setopt_array($curl3, array(
      CURLOPT_URL => 'https://payments.pabbly.com/api/v1/product/'.env('PUBBLY_PRODUCT_ID'),
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
      CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
      ));
      $response3 = curl_exec($curl3);
      
      curl_close($curl3);
      $response3 = json_decode($response3,true);

      return $response3;
  }

}
