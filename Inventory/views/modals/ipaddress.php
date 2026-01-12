<!-- ADD NETWORK MODAL -->
<div class="modal fade" id="add_network" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Add Network</h6>
            </div>
            <div class="modal-body">
                <label for="network_name" class="mb-2">Network Name</label>
                <input required type="text" name="network_name" id="network_name" class="form-control mb-2">
                <label for="ip_range_from" class="mb-2">IP Range <i class="f-13 text-danger">(Can't be edited after saving.)</i></label>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <input required type="text" name="ip_range_from" id="ip_range_from" class="form-control" placeholder="FROM">
                    </div>
                    <div class="col-md-6">
                        <input required type="text" name="ip_range_to" id="ip_range_to" class="form-control" placeholder="TO">
                    </div>
                </div>
                <label for="ip_subnet" class="mb-2">Subnet Mask</label>
                <input required type="text" name="ip_subnet" id="ip_subnet" class="form-control mb-2">
                <label for="ip_gateway_select" class="mb-2">Default Gateway <i class="f-13">(Router)</i></label>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <select name="ip_gateway_select" id="ip_gateway_select" class="form-control">
                            <option disabled selected value="-">-- Select Router --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input disabled required type="text" name="ip_gateway" id="ip_gateway" class="form-control mb-2">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display: flex;">
                <div id="ready_state">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="add_network_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
                </div>
                <div id="saving_state" style="display: none;">
                    <button class="btn btn-primary btn-sm" type="button">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Saving
                    </button>    
                </div>
            </div>     
        </div>
    </div>
</div>

<!-- EDIT NETWORK MODAL -->
<div class="modal fade" id="edit_network" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Network Info</h6>
                <button class="btn btn-danger btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#delete_network"><span class="fa fa-trash"></span></button>
            </div>
            <div class="modal-body">
                <label for="edit_network_name" class="mb-2">Network Name</label>
                <input required type="text" name="edit_network_name" id="edit_network_name" class="form-control mb-2">
                <label for="ip_range_from" class="mb-2">IP Range <i class="f-13 text-danger">(Can't be edited.)</i></label>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <input required disabled type="text" name="edit_ip_range_from" id="edit_ip_range_from" class="form-control" placeholder="FROM">
                    </div>
                    <div class="col-md-6">
                        <input required disabled type="text" name="edit_ip_range_to" id="edit_ip_range_to" class="form-control" placeholder="TO">
                    </div>
                </div>
                <label for="edit_ip_subnet" class="mb-2">Subnet Mask</label>
                <input required type="text" name="edit_ip_subnet" id="edit_ip_subnet" class="form-control mb-2">
                <label for="edit_ip_gateway_select" class="mb-2">Default Gateway <i class="f-13">(Router)</i></label>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <select name="edit_ip_gateway_select" id="edit_ip_gateway_select" class="form-control">
                            <option value="-">-- Select Router --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input disabled required type="text" name="edit_ip_gateway" id="edit_ip_gateway" class="form-control mb-2">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display: flex;">
                <div id="edit_ready_state">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="edit_network_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
                </div>
                <div id="edit_saving_state" style="display: none;">
                    <button class="btn btn-primary btn-sm" type="button">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Saving
                    </button>    
                </div>
            </div>     
        </div>
    </div>
</div>

<!-- DELETE NETWORK MODAL -->
<div class="modal fade" id="delete_network" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_network_title" class="modal-title fw-bolder">Delete Network</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete network <b>"<span id="delete_network_name">NETWORK</span>"</b>.</div>
                    <div>All of the recorded IP address will also be deleted, this can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <div id="delete_ready_state">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#edit_network"><span class="fa fa-remove"></span> No</button>
                    <button id="delete_network_btn" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
                </div>
                <div id="delete_saving_state" style="display: none;">
                    <button class="btn btn-danger btn-sm" type="button">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Deleting
                    </button>    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT IP MODAL -->
<div class="modal fade" id="edit_ip" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 id="edit_ip_title" class="modal-title">Edit Selected IP</h6>    
                </div>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="hostname" class="mb-2">Hostname</label>
                        <input required type="text" name="" id="hostname" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="site" class="mb-2">Site / Location</label>
                        <input required type="text" name="" id="site" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="server" class="mb-2">Server</label>
                        <input required type="text" name="" id="server" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="webmgmtpt" class="mb-2">Web Management Port</label>
                        <input required type="text" name="" id="webmgmtpt" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="username" class="mb-2">Username</label>
                        <input required type="text" name="" id="username" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="mb-2">Password</label>
                        <div class="ps-field">
                            <input type="password" id="password" class="form-control">
                            <span id="togglePassword" class="fa fa-eye-slash text-secondary"></span>
                        </div>
                    </div>
                </div>
                <label for="state" class="mb-2 mt-2">State</label>
                <select name="state" id="state" class="form-control">
                    <option value="DOWN">DOWN</option>
                    <option value="UP">UP</option>
                </select>
                <label for="remarks" class="mb-2 mt-2">Remarks</label>
                <textarea maxlength="1000" rows="5" name="" id="remarks" class="form-control" placeholder="Aa"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_ip_btn" type="button" data-bs-dismiss="" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- IMPORT MODAL -->
<div class="modal" id="import_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div id="import_message" class="w-100 text-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- UNASSIGN IP MODAL -->
<div class="modal fade" id="unassign_ip_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="unassign_ip_name"></h6>
            </div>
            <div class="modal-body pe-4 ps-4">
                <div>You are going to unassign an IP, this can't be undone do you wish to proceed?</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button class="btn btn-danger btn-sm" id="unassign_ip_btn"><span class="fa fa-ban"></span> Unassign</button>
            </div>
        </div>
    </div>
</div>