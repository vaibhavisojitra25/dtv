<!-- add new card modal  -->
<div class="modal fade" id="upgradePlanMdl" tabindex="-1" aria-labelledby="upgradePlanMdlTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-sm-5 mx-50 pb-5">
        <h1 class="text-center mb-1" id="upgradePlanMdlTitle">Upgrade Plan</h1>
        <!-- form -->
        <form id="upgradePlanForm" class="row gy-1 gx-2 mt-75" method="post">
        {{ csrf_field() }}
          <div class="credits_div p-0"></div>
          <div class="col-12 text-center">
              <h4>Subscribe using below Link</h4>
              <p class="checkout_link" style="word-break: break-all;"></p>
          </div>
          <hr>
          <p class="text-center"> OR </p>
          <div class="col-12 text-center">
              <h4>Subscribe using Coupon</h4>
              <input type="text" id="coupon_code" name="coupon_code" class="form-control"  data-msg="Please enter coupon code"  placeholder="Enter Coupon" required/>
          </div>
          <div class="col-12 text-center">
            <input type="hidden" name="user_id" value="" class="user_id">
            <input type="hidden" name="plan_id" value="" class="plan_id">
            <input type="hidden" name="device_id" value="" class="device_id">
            <input type="hidden" name="customer_id" value="" class="customer_id">
            <input type="hidden" name="price" value="" class="price">
            <input type="hidden" name="trial_amount" value="" class="trial_amount">
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
<!--/ add new card modal  -->



<!-- add new card modal  -->
<div class="modal fade" id="upgradePlanMdlOld" tabindex="-1" aria-labelledby="upgradePlanMdlTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-sm-5 mx-50 pb-5">
        <h1 class="text-center mb-1" id="upgradePlanMdlTitle">Upgrade Plan</h1>
        <p class="text-center">Add detail for upgrade</p>

        <!-- form -->
        <form id="upgratePlanForm" class="row gy-1 gx-2 mt-75" action="{{ route('upgradePlan') }}" method="post">
        {{ csrf_field() }}
            <div class="col-6">
              <label class="form-label" for="first_name">First Name</label>
              <div class="input-group input-group-merge">
                <input
                  id="first_name"
                  name="first_name"
                  class="form-control add-credit-card-mask"
                  type="text"
                  placeholder="First name"
                  aria-describedby="modalAddCard2"
                  value="{{Auth::user()->first_name}}"
                  readonly
                />
                <span class="input-group-text cursor-pointer p-25" id="modalAddCard2">
                  <span class="add-card-type"></span>
                </span>
              </div>
            </div>
            <div class="col-6">
              <label class="form-label" for="last_name">Last Name</label>
              <div class="input-group input-group-merge">
                <input
                  id="last_name"
                  name="last_name"
                  class="form-control add-credit-card-mask"
                  type="text"
                  placeholder="Last name"
                  aria-describedby="modalAddCard2"
                  value="{{Auth::user()->last_name}}"
                  readonly
                />
                <span class="input-group-text cursor-pointer p-25" id="modalAddCard2">
                  <span class="add-card-type"></span>
                </span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label" for="email">Email</label>
              <div class="input-group input-group-merge">
                <input
                  id="email"
                  name="email"
                  class="form-control add-credit-card-mask"
                  type="email"
                  placeholder="Email"
                  aria-describedby="modalAddCard2"
                  data-msg="Please enter email"
                  value="{{Auth::user()->email}}"
                  readonly
                />
                <span class="input-group-text cursor-pointer p-25" id="modalAddCard2">
                  <span class="add-card-type"></span>
                </span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label" for="card_number">Card Number</label>
              <div class="input-group input-group-merge">
                <input
                  id="card_number"
                  name="card_number"
                  class="form-control add-credit-card-mask"
                  type="text"
                  placeholder="1356 3215 6548 7898"
                  aria-describedby="modalAddCard2"
                  data-msg="Please enter your credit card number"
                />
                <span class="input-group-text cursor-pointer p-25" id="modalAddCard2">
                  <span class="add-card-type"></span>
                </span>
              </div>
            </div>

          <div class="col-6 col-md-4">
            <label class="form-label" for="month">Month</label>
            <select
              id="month"
              name="month"
              class="form-control add-expiry-date-mask"
              >
              <option value="01">01</option>
              <option value="02">02</option>
              <option value="03">03</option>
              <option value="04">04</option>
              <option value="05">05</option>
              <option value="06">06</option>
              <option value="07">07</option>
              <option value="08">08</option>
              <option value="09">09</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>

            </select>
          </div>

          <div class="col-6 col-md-4">
            <label class="form-label" for="year">Year</label>
            <select
              id="year"
              name="year"
              class="form-control add-expiry-date-mask"
              >
              <?php $years = collect(range(0, 22))->map(function ($item) {
                      return (string) date('Y') + $item;
                  }); ?>
              @foreach ($years as $year)
              <option value="{{ $year }}">{{ $year }}</option>
              @endforeach

            </select>
          </div>


          <div class="col-6 col-md-4">
            <label class="form-label" for="cvv">CVV</label>
            <input
              type="text"
              id="cvv"
              name="cvv"
              class="form-control add-cvv-code-mask"
              maxlength="3"
              placeholder="654"
            />
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center">
              {{-- <div class="form-check form-switch form-check-primary me-25">
                <input type="checkbox" class="form-check-input" id="saveCard" checked />
                <label class="form-check-label" for="saveCard">
                  <span class="switch-icon-left"><i data-feather="check"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div> --}}
              {{-- <label class="form-check-label fw-bolder" for="saveCard">Save Card for future billing?</label> --}}
            </div>
          </div>

          <div class="col-12 text-center">
            <input type="hidden" name="plan_id" value="" id="plan_id">
            <input type="hidden" name="price" value="" id="price">
            <input type="hidden" name="trial_amount" value="" id="trial_amount">
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
<!--/ add new card modal  -->
