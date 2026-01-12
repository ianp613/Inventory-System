<!-- SETTINGS MODAL -->
<div class="modal fade" id="settings_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h6 class="modal-title"><span class="fa fa-gears"></span> Settings</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Sound</h6>
                    <label id="switch_sound" class="switch">
                        <input id="switch_sound_check" type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <h6>Dark Theme</h6>
                    <label id="switch" class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            
            <div hidden class="users_menu modal-footer">
                <button id="export_db" class="btn btn-sm btn-success rouded-pill w-100"><span class="fa fa-database"></span> DB Export</button>
                <!-- <button class="btn btn-sm btn-danger rouded-pill">Save</button> -->
            </div>
        </div>
    </div>
</div>

<!-- CONFIRM EXPORT MODAL -->
<div class="modal" id="confirm_export_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-success">
            <div class="modal-body">
                <label for="export_password" class="text-light"><i><span class="fa fa-key"></span> Account Password: </i></label>
                <input type="password" id="export_password" class="export_password text-light form-control mt-2 bg-success">
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success rouded-pill" id="cancel_export"><span class="fa fa-close"></span> Cancel</button>
                <button class="btn btn-sm btn-success rouded-pill" id="confirm_export"><span class="fa fa-database"></span> Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- WORK IN PROGRESS MODAL -->
 <div class="modal fade" id="wip_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 text-center pt-2">
                    <code><h4>WORK IN PROGRESS</h4></code>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100 text-center">
                    <button data-bs-dismiss="modal" class="w-100 btn btn-sm btn-primary rounded-pill wd-50">OK</button>
                </div>
            </div>
        </div>
    </div>
 </div>