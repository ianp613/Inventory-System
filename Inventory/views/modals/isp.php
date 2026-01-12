<!-- ADD ISP MODAL -->
<div class="modal fade" id="add_isp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Add ISP</h6>
                <div>
                    <img id="isp_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
            </div>
            <div class="modal-body">
                <label for="name" class="mb-2">Name</label>
                <input required type="text" name="name" id="name" class="form-control">
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="isp_name" class="mb-2">ISP</label>
                        <select name="isp_name" id="isp_name" class="form-control">
                            <option value="" disabled selected>-- Select ISP --</option>
                            <option value="PLDT Inc.">PLDT Inc.</option>
                            <option value="Globe Telecom, Inc.">Globe Telecom, Inc.</option>
                            <option value="Converge ICT Solutions Inc.">Converge ICT Solutions Inc.</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="wan_ip" class="mb-2">WAN IP</label>
                        <input required type="text" name="wan_ip" id="wan_ip" class="form-control">
                    </div>
                </div>
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="subnet" class="mb-2">Subnet Mask</label>
                        <input required type="text" name="subnet" id="subnet" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="gateway" class="mb-2">Gateway</label>
                        <input required type="text" name="gateway" id="gateway" class="form-control">
                    </div>
                </div>
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="dns1" class="mb-2">DNS 1</label>
                        <input required type="text" name="dns1" id="dns1" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="dns2" class="mb-2">DNS 2</label>
                        <input required type="text" name="dns2" id="dns2" class="form-control">
                    </div>
                </div>
                <label for="isp_webmgmtpt" class="mb-2">Web Management Port</i></label>
                <input required type="text" name="isp_webmgmtpt" id="isp_webmgmtpt" class="form-control mb-2">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_isp_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
            </div>     
        </div>
    </div>
</div>


<!-- EDIT ISP MODAL -->
<div class="modal fade" id="edit_isp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="edit_isp_title">EDIT ISP: </h6>
                <div>
                    <img id="edit_isp_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
            </div>
            <div class="modal-body">
                <label for="edit_name" class="mb-2">Name</label>
                <input required type="text" name="edit_name" id="edit_name" class="form-control">
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="edit_isp_name" class="mb-2">ISP</label>
                        <select name="edit_isp_name" id="edit_isp_name" class="form-control">
                            <option value="" disabled selected>-- Select ISP --</option>
                            <option value="PLDT Inc.">PLDT Inc.</option>
                            <option value="Globe Telecom, Inc.">Globe Telecom, Inc.</option>
                            <option value="Converge ICT Solutions Inc.">Converge ICT Solutions Inc.</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_wan_ip" class="mb-2">WAN IP</label>
                        <input required type="text" name="edit_wan_ip" id="edit_wan_ip" class="form-control">
                    </div>
                </div>
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="edit_subnet" class="mb-2">Subnet Mask</label>
                        <input required type="text" name="edit_subnet" id="edit_subnet" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_gateway" class="mb-2">Gateway</label>
                        <input required type="text" name="edit_gateway" id="edit_gateway" class="form-control">
                    </div>
                </div>
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="edit_dns1" class="mb-2">DNS 1</label>
                        <input required type="text" name="edit_dns1" id="edit_dns1" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_dns2" class="mb-2">DNS 2</label>
                        <input required type="text" name="edit_dns2" id="edit_dns2" class="form-control">
                    </div>
                </div>
                <label for="edit_isp_webmgmtpt" class="mb-2">Web Management Port</i></label>
                <input required type="text" name="edit_isp_webmgmtpt" id="edit_isp_webmgmtpt" class="form-control mb-2">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_isp_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Update</button>    
            </div>     
        </div>
    </div>
</div>

<!-- DELETE ISP MODAL -->
<div class="modal fade" id="delete_isp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_isp_title" class="modal-title fw-bolder">Delete ISP</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete ISP <b>"<span id="delete_isp_name">ISP</span>"</b>.</div>
                    <div><span id="delete_isp_message">This ISP will also be removed from the router to which it is assigned, this can't be undone.</span> Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_isp_btn" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>