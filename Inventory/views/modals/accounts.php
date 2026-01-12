<!-- CREATE ACCOUNT -->
<div class="modal fade" id="add_account" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h6 class="modal-title"><span class="fa fa-user"></span> Create Account</h6>
            </div>
            <div class="modal-body">
                <label for="add_name" class="mb-2">Name</label>
                <input required type="text" name="" id="add_name" class="form-control">
                <label for="add_email" class="mb-2 mt-2">Email <i class="f-13">(optional)</i></label>
                <input required type="text" name="" id="add_email" class="form-control">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="add_username" class="mb-2">User ID</label>
                        <input required type="text" name="" id="add_username" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="add_password" class="mb-2">Password</label>
                        <input readonly required type="text" name="" value="12345" id="add_password" class="form-control">
                    </div>
                </div>
                <label for="add_privilege" class="mb-2 mt-2">Account Privilege</label>
                <select class="form-control" name="" id="add_privilege">
                    <?php if($_SESSION["privileges"] == "Administrator"){ ?>
                    <option value="Administrator">Administrator</option>
                    <?php } ?>
                    <option value="Supervisor">Supervisor</option>
                    <option selected value="User">User</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_account_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT ACCOUNT -->
<div class="modal fade" id="edit_account" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h6 class="modal-title" id="edit_account_title"><span class="fa fa-user"></span> Edit Account: </h6>
            </div>
            <div class="modal-body">
                <label for="edit_account_name" class="mb-2">Name</label>
                <input required type="text" name="" id="edit_account_name" class="form-control">
                <label for="edit_email" class="mb-2 mt-2">Email <i class="f-13">(optional)</i></label>
                <input required type="text" name="" id="edit_email" class="form-control">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="edit_username" class="mb-2">User ID</label>
                        <input required type="text" name="" id="edit_username" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_password" class="mb-2">Password</label>
                        <input required type="text" name="" value="12345" id="edit_password" class="form-control">
                    </div>
                </div>
                <label for="edit_privilege" class="mb-2 mt-2">Account Privilege</label>
                <select class="form-control" name="" id="edit_privilege">
                    <?php if($_SESSION["privileges"] == "Administrator"){ ?>
                    <option value="Administrator">Administrator</option>
                    <?php } ?>
                    <option value="Supervisor">Supervisor</option>
                    <option selected value="User">User</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_account_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE ACCOUNT MODAL -->
<div class="modal fade" id="delete_account" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_entry_title" class="modal-title fw-bolder">Delete Account</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete the account of <b>"<span id="delete_account_name">Name</span>"</b>.</div>
                    <div>This can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_account_btn" e-id="" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT ACCOUNT -->
<div class="modal fade" id="account_edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h6 class="modal-title" id="edit_account_title"><span class="fa fa-user"></span> Account Information</h6>
                <button id="account_cancel_btn" class="btn btn-sm btn-light" data-bs-dismiss="modal"><span class="fa fa-remove"></span></button>
            </div>
            <div class="modal-body">
                <label for="account_email" class="mb-2 mt-2">Email <i class="f-13">(recommended)</i></label>
                <input required type="text" name="" id="account_email" class="form-control">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="account_new_password" class="mb-2">New Password</label>
                        <input required type="password" name="" id="account_new_password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="account_confirm_password" class="mb-2">Confirm New Password</label>
                        <input required type="password" name="" id="account_confirm_password" class="form-control">
                    </div>
                </div>
                <div hidden id="passkey_field" class="row mt-2">
                    <div class="col">
                        <label for="passkey" class="mb-2">Passkey</label>
                        <div class="d-flex">
                            <div class="col-5">
                                <input readonly required type="text" name="" id="passkey" class="form-control">
                            </div>
                            <button id="btn_generate_passkey" class="btn btn-success ms-1"><span class="fa fa-refresh"></span></button>     
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button id="account_submit_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- ADD GROUP -->
 <div class="modal fade" tabindex="-1" id="add_group">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6>ADD GROUP</h6>
            </div>
            <div class="modal-body">
                <label for="group_name" class="mb-2">Group Name / Office / Project</label>
                <input type="text" name="group_name" id="group_name" class="form-control mb-2">
                <label for="group_type" class="mb-2">Type</label>
                <select name="group_type" id="group_type" class="form-control">
                    <option value="NON-IT" selected>NON-IT</option>
                    <option value="IT">IT</option>
                </select>
                <label for="group_supervisor" class="mb-2">Supervisor</label>
                <div class="btn-group d-flex mb-2">
                    <select name="group_supervisor" id="group_supervisor" class="form-control">
                        <option value="" selected disabled>Select User</option>
                    </select>
                </div>
                <div id="supervisor_container">
                    <!-- SUPERVISORS HERE -->
                </div>
                
                <label for="group_user" class="mb-2 mt-2">Users</label>
                <div class="btn-group d-flex mb-2">
                    <select name="group_user" id="group_user" class="form-control">
                        <option value="" selected disabled>Select User</option>
                    </select>
                </div>
                <div id="user_container">
                    <!-- USERS HERE -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_group_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
 </div>

 <!-- ADD GROUP -->
 <div class="modal fade" tabindex="-1" id="edit_group">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6>EDIT GROUP</h6>
            </div>
            <div class="modal-body">
                <label for="edit_group_name" class="mb-2">Group Name / Office / Project</label>
                <input type="text" name="edit_group_name" id="edit_group_name" class="form-control mb-2">
                <label for="edit_group_type" class="mb-2">Type</label>
                <select name="edit_group_type" id="edit_group_type" class="form-control">
                    <option value="NON-IT" selected>NON-IT</option>
                    <option value="IT">IT</option>
                </select>
                <label for="edit_group_supervisor" class="mb-2">Supervisor</label>
                <div class="btn-group d-flex mb-2">
                    <select name="edit_group_supervisor" id="edit_group_supervisor" class="form-control">
                        <option value="" selected disabled>Select User</option>
                    </select>
                </div>
                <div id="edit_supervisor_container">
                    <!-- SUPERVISORS HERE -->
                </div>
                
                <label for="edit_group_user" class="mb-2 mt-2">Users</label>
                <div class="btn-group d-flex mb-2">
                    <select name="edit_group_user" id="edit_group_user" class="form-control">
                        <option value="" selected disabled>Select User</option>
                    </select>
                </div>
                <div id="edit_user_container">
                    <!-- USERS HERE -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_group_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
 </div>

 <div class="modal fade" tabindex="-1" id="operate_as">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6>Operate as Group Member</h6>
                <button hidden class="btn btn-sm btn-danger" id="exit_group_btn"><span class="fa fa-sign-out"></span> Exit Group</button>

            </div>
            <div class="modal-body">
                <label for="group_list" class="mb-2">Group List</label>
                <select name="group_list" id="group_list" class="form-control">
                    <option selected disabled value="">-- Select Group --</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button class="btn btn-sm btn-primary" id="operate_as_btn"><span class="fa fa-sign-in"></span> Proceed</button>
            </div>
        </div>
    </div>
 </div>