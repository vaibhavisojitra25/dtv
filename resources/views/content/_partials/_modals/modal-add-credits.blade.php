<!-- upgrade your plan Modal -->
<div class="modal fade" id="addCreditsMdl" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-upgrade-plan">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-5 pb-2">
        <div class="text-center mb-2">
          <h1 class="mb-1">Add Credits</h1>
        </div>
        <form id="addCredits" class="row pt-50">
          <div class="col-sm-12">
            <label class="form-label" for="plan_id">Select Plan</label>
            <select id="plan_id" class="form-select" name="plan_id" required>
                <option value="">--Select--</option>
                @if($plan_data)
                @if(Auth::user()->user_type == 3)
                  @foreach(array_reverse($plan_data) as $value)
                  <option value="{{$value['id']}}" data-amount="{{$value['price']}}" data-is_inlimited="@if(isset($value['meta_data']['is_credit_unlimited'])){{$value['meta_data']['is_credit_unlimited']}}@else{{0}}@endif" data-credit_amount="@if($value['meta_data']['is_credit'] == 1 && (isset($value['meta_data']['credit_amount']))){{$value['meta_data']['credit_amount']}}@elseif($value['meta_data']['is_credit'] == 1 && (isset($value['meta_data']['is_credit_unlimited'])) && $value['meta_data']['is_credit_unlimited'] == 1){{'Unlimited'}}@endif">{{$value['plan_name']}} - (@if($value['meta_data']['is_credit'] == 1 && (isset($value['meta_data']['credit_amount']))){{$value['meta_data']['credit_amount']}}@elseif($value['meta_data']['is_credit'] == 1 && (isset($value['meta_data']['is_credit_unlimited'])) && $value['meta_data']['is_credit_unlimited'] == 1){{'Unlimited'}}@endif/{{env('CURRENCY')}}{{$value['price']}})</option>
                  @endforeach
                @else
                  @foreach(array_reverse($plan_data) as $value)
                    @if(!isset($value['meta_data']['is_credit_unlimited']) || $value['meta_data']['is_credit_unlimited'] == 0)
                      <option value="{{$value['id']}}" data-amount="{{$value['price']}}" data-credit_amount="{{$value['meta_data']['credit_amount']}}">{{$value['plan_name']}} - ({{$value['meta_data']['credit_amount']}}/{{env('CURRENCY')}}{{$value['price']}})</option>
                    @endif
                  @endforeach
                @endif
                @endif

            </select>
          </div>
          <div class="col-12 text-center">
            <input type="hidden" name="customer_id" value="{{Auth::user()->customer_id}}" id="customer_id">
            <button type="submit" class="btn btn-primary me-1 mt-1">Submit</button>
            <button type="reset" class="btn btn-outline-secondary mt-1" data-bs-dismiss="modal" aria-label="Close">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ upgrade your plan Modal -->
