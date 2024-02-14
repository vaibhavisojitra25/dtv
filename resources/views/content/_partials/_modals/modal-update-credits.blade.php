<!-- upgrade your plan Modal -->
<div class="modal fade" id="updateCreditsMdl" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-upgrade-plan">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-5 pb-2">
        <div class="text-center mb-2">
          <h1 class="mb-1">Add/Deduct Credits</h1>
        </div>
        <form id="updateUserCredits" class="row pt-50">
          <div class="col-sm-12">
            <label class="form-label" for="credits">Credit</label>
            <input type="number" name="credits" min="0" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" max="{{Auth::user()->credits}}" class="form-control credits1" placeholder="Enter Credit" required/>
          </div>
          <div class="col-12 text-center">
            <input type="hidden" name="user_id" value="" class="user_id">
            <input type="hidden" id="auth_user_id" name="auth_user_id" value="{{Auth::user()->user_id}}">
            <input type="hidden" name="flag" value="1">
            <input type="hidden" value="{{Auth::user()->user_type}}" class="admin_user_type">
            <input type="hidden" name="max_admin_credits" value="{{Auth::user()->credits}}" id="max_admin_credits">
            <button type="submit" class="btn btn-primary me-1 mt-1" id="addCredits">Add</button>
          </div>
        </form>
        <form id="deductUserCredits" class="row pt-50">
          <div class="col-sm-12">
            <label class="form-label" for="credits">Credit</label>
            <input type="number" name="credits" min="0" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" max="{{Auth::user()->credits}}" class="form-control credits2" placeholder="Enter Credit" required/>
          </div>
          <div class="col-12 text-center">
            <input type="hidden" name="user_id" value="" class="user_id">
            <input type="hidden" id="auth_user_id" name="auth_user_id" value="{{Auth::user()->user_id}}">
            <input type="hidden" name="flag" value="2">
            <input type="hidden" value="{{Auth::user()->user_type}}" class="admin_user_type">
            <input type="hidden" name="max_deduct_credits" value="" id="max_deduct_credits">

            <button type="submit" class="btn btn-danger me-1 mt-1" id="deductCredits">Deduct</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ upgrade your plan Modal -->
