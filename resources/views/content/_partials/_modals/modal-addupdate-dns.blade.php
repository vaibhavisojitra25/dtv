<!-- Edit User Modal -->
<div class="modal fade" id="addUpdateDNSMdl" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-edit-user">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-5 px-sm-5 pt-50">
        <div class="text-center mb-2">
          <h1 class="mb-1" id="mdlTitle">Add DNS</h1>
        </div>
        <div class="dnsForm">
            <form class="form form-vertical" method="post" id="addUpdateDNSForm">
            {{csrf_field()}}
                <div class="row">
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="dns_url">DNS URL<span class="text-danger">*</span></label>
                            <input type="text" id="dns_url" class="form-control" placeholder="DNS URL" name="dns_url">

                        </div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" id="dns_id" class="form-control" name="dns_id" value="">
                        <button class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Edit User Modal -->
