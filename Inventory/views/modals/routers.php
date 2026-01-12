<!-- ADD ROUTER MODAL -->
<div class="modal fade" id="add_router" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Add Router</h6>
            </div>
            <div class="modal-body">
                <label for="router_name" class="mb-2">Name</label>
                <input required type="text" name="router_name" id="router_name" class="form-control">
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="router_ip" class="mb-2">IP Address</label>
                        <input required type="text" name="router_ip" id="router_ip" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="router_subnet" class="mb-2">Subnet Mask</label>
                        <input required type="text" name="router_subnet" id="router_subnet" class="form-control">
                    </div>
                </div>
                <h6 class="mt-3 fw-bold">WAN Settings</h6>
                <hr>



                <div class="w-100 d-flex justify-content-between mb-2">
                    <label for="router_wan1" class="mb-2"><h6>WAN 1 <i>(Primary)</i></h6></label>
                    <img hidden id="router_wan1_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
                <select name="router_wan1" id="router_wan1" class="form-control">
                    <option value="0" disabled selected>-- Select WAN 1 --</option>
                </select>
                <div id="wan1_info" class="ht-160 p-2" style="overflow-y: auto; overflow-x: hidden;">
                    <!-- ISP INFO -->
                </div>



                <div class="w-100 d-flex justify-content-between mb-2">
                    <label for="router_wan2" class="mb-2"><h6>WAN 2 <i>(Secondary)</i></h6></label>
                    <img hidden id="router_wan2_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
                <select name="router_wan2" id="router_wan2" class="form-control">
                    <option value="0" disabled selected>-- Select WAN 2 --</option>
                </select>
                <div id="wan2_info" class="ht-160 p-2" style="overflow-y: auto; overflow-x: hidden;">
                    <!-- ISP INFO -->
                </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="save_router_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
            </div>     
        </div>
    </div>
</div>

<!-- EDIT ROUTER MODAL -->
<div class="modal fade" id="edit_router" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 id="edit_router_title" class="modal-title fw-bold">Edit Router: </h6>
            </div>
            <div class="modal-body">
                <label for="edit_router_name" class="mb-2">Name</label>
                <input required type="text" name="edit_router_name" id="edit_router_name" class="form-control">
                <div class="row mb-2 mt-2">
                    <div class="col-md-6">
                        <label for="edit_router_ip" class="mb-2">IP Address</label>
                        <input required type="text" name="edit_router_ip" id="edit_router_ip" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_router_subnet" class="mb-2">Subnet Mask</label>
                        <input required type="text" name="edit_router_subnet" id="edit_router_subnet" class="form-control">
                    </div>
                </div>
                <h6 class="mt-3 fw-bold">WAN Settings</h6>
                <hr>



                <div class="w-100 d-flex justify-content-between mb-2">
                    <label for="edit_router_wan1" class="mb-2"><h6>WAN 1 <i>(Primary)</i></h6></label>
                    <img hidden id="edit_router_wan1_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
                <select name="edit_router_wan1" id="edit_router_wan1" class="form-control">
                    <option value="0" disabled selected>-- Select WAN 1 --</option>
                </select>
                <div id="edit_wan1_info" class="ht-160 p-2" style="overflow-y: auto; overflow-x: hidden;">
                    <!-- ISP INFO -->
                </div>



                <div class="w-100 d-flex justify-content-between mb-2">
                    <label for="edit_router_wan2" class="mb-2"><h6>WAN 2 <i>(Secondary)</i></h6></label>
                    <img hidden id="edit_router_wan2_icon" src="../../assets/img/hero.png" class="ht-30"  style="margin-top: -5px;" alt="" srcset="">
                </div>
                <select name="edit_router_wan2" id="edit_router_wan2" class="form-control">
                    <option value="0" disabled selected>-- Select WAN 2 --</option>
                </select>
                <div id="edit_wan2_info" class="ht-160 p-2" style="overflow-y: auto; overflow-x: hidden;">
                    <!-- ISP INFO -->
                </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="update_router_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
            </div>     
        </div>
    </div>
</div>


<!-- DELETE ROUTER MODAL -->
<div class="modal fade" id="delete_router" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_router_title" class="modal-title fw-bolder">Delete Router</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete router <b>"<span id="delete_router_name">ISP</span>"</b>.</div>
                    <div id="delete_router_table_container" class="ps-4 mb-2">
                        <div>The following is the list of networks connected to this router.</div>
                        <table id="delete_router_table" class="w-100">
                            <thead>
                                <tr class="fw-bold">
                                    <td>Name</td>
                                    <td>IP From</td>
                                    <td>IP To</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SA</td>
                                    <td>SA</td>
                                    <td>SA</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                    
                    <div>This can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_router_btn" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>