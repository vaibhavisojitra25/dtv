<!-- upgrade your plan Modal -->
<div class="modal fade" id="deviceCodeMdl" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-upgrade-plan">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-5 pb-2">
        <div class="text-center mb-2">
          <h1 class="mb-1">Device Code</h1>
        </div>
        <div class="row">
            <table class="table table-cart">
                <tbody valign="middle">
                    <tr>
                        <td>
                            <h4><span data-feather="lock"></span> <strong>Activation code</strong> </h4>
                            <h2><span id="activation-code"
                                    class="activation-code-active text-success">1466098827</span></h2>
                            <p id="active-message" class="activation-code-active text-success">Code
                                generated successfully.</p>
                        </td>
                        <td></td>
                    </tr>
                    <tr id="expiration-view" style="">
                        <td>
                            <h4><span data-feather="clock"></span> <strong>Valid Until</strong> <span
                                    id="expiration-date">2022-03-16 10:31 PM</span> </h4>
                            <!-- <p id="expiration-diff">12 hours left</p> -->
                        </td>
                        <td></td>
                    </tr>                    
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <a id="hideDeviceCode"  href="{{ url('device/list/0') }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back to Device List</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ upgrade your plan Modal -->
