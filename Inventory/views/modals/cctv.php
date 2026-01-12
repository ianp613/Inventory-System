<!-- HOW TO -->
<div class="modal fade" id="how_to" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-success">
                <h6 class="modal-title">How to?</h6>
            </div>
            <div class="modal-body">
                <h6>View Map</h6>
                <p class="ps-2">*Click <button class="btn btn-sm btn-dark"><span class="fa fa-plus"></span> Add Map</button>, fill in the necessary info, and save it. After saving, click -- Select Map -- to display it on the screen.</p>
                <h6>Manage Camera</h6>
                <p class="ps-2">*Click <button class="btn btn-sm btn-dark"><span class="fa fa-edit"></span> Manage Camera</button> to show the list of all cameras on the selected map. You can also add, edit and remove cameras from the list.</p>
                <h6>Add Camera to Map</h6>
                <p class="ps-2">*After adding cameras to the list, right-click the map (the displayed image) to show the list of cameras that can be added to the map.</p>
                <p class="ps-2">*Click <button class="btn btn-sm btn-primary"><span class="fa fa-check"></span></button> to add it to the map, and click <button class="btn btn-sm btn-danger"><span class="fa fa-ban"></span></button> to remove it from the map.</p>
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" type="button" class="btn btn-primary btn-sm">OK</button>    
            </div>     
        </div>
    </div>
</div>


<!-- ADD CCTV SITE -->
<div class="modal fade" id="add_cctv_map" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Add CCTV Site</h6>
            </div>
            <div class="modal-body">
                <label for="map_location" class="mb-2">Location</label>
                <input required type="text" name="map_location" id="map_location" class="form-control">
                <label for="floorplan" class="mb-2 mt-2">Select Floor Plan</label>
                <input required accept="image/*" type="file" name="floorplan" id="floorplan" class="form-control">
                <label for="map_remarks" class="mb-2 mt-2">Remarks</label>
                <textarea maxlength="1000" rows="5" name="" id="map_remarks" class="form-control" placeholder="Aa"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_site_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>    
            </div>     
        </div>
    </div>
</div>

<!-- EDIT CCTV SITE -->
<div class="modal fade" id="edit_cctv_map" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit CCTV Site</h6>
                <button id="delete_cctv_map_btn" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span></button>
            </div>
            <div class="modal-body">
                <label for="edit_map_location" class="mb-2">Location</label>
                <input required type="text" name="edit_map_location" id="edit_map_location" class="form-control">
                <label for="edit_floorplan" class="mb-2 mt-2">Select Floor Plan</label>
                <input required accept="image/*" type="file" name="edit_floorplan" id="edit_floorplan" class="form-control" >
                <p class="f-13 text-danger"><i>Note: Changing the floorplan may cause some cameras to disappear from the map due to differences in position alignment or map size.</i></p>
                <label for="edit_map_remarks" class="mb-2 mt-2">Remarks</label>
                <textarea maxlength="1000" rows="5" name="" id="edit_map_remarks" class="form-control" placeholder="Aa"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_site_btn" type="button" class="btn btn-success btn-sm"><span class="fa fa-save"></span> Update</button>    
            </div>     
        </div>
    </div>
</div>

<!-- MANAGE CAMERA -->
<div class="modal fade" id="manage_camera" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h6 id="manage_camera_title" class="modal-title"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="camera_menu" id="camera_menu">
                <div class="modal-body">
                    <table  id="camera_table" class="table border table-hover">
                        <thead>
                            <tr>
                                <td class="text-start">ID</td>
                                <td class="text-start" style="width: 250px; !important">Alias / DVR or NVR Port No.</td>
                                <td class="text-start">Type</td>
                                <td class="text-start">Subtype</td>
                                <td style="width: 100px; !important">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Entry Here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div class="btn-group btn-sm m-0">
                        <button class="btn btn-sm text-end" style="width: 120px;">Set Camera Size: </button>
                        <select name="" id="camera_size" class="form-control pt-0 pb-0" style="width: 40px; height: 31px !important;">
                            <option value="25">1</option>
                            <option value="30">2</option>
                            <option value="40">3</option>
                            <option value="50">4</option>
                            <option value="60">5</option>
                        </select>    
                    </div>
                    <button id="save_size_btn" class="m-0 btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
                </div>
            </div>
            

            <div hidden class="camera_form" id="camera_form">
                <div class="modal-body">
                    <h6>Primary Information</h6>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="camera_id" class="mb-1">Alias / DVR or NVR Port No.</label>
                            <input type="text" maxlength="15" name="" id="camera_id" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="camera_type" class="mb-1">Type</label>
                            <select name="" id="camera_type" class="form-control">
                                <option disabled value="-" selected>-- Select Type --</option>
                                <option value="Bullet Type Camera">Bullet Type Camera</option>
                                <option value="Dome Camera">Dome Camera</option>
                                <option value="Wireless Camera">Wireless Camera</option>
                                <option value="Network Camera">Network Camera</option>
                                <option value="Discreet CCTV">Discreet CCTV</option>
                                <option value="Infrared Camera">Infrared Camera</option>
                                <option value="Day/Night Type">Day/Night Type</option>
                                <option value="Varifocal Camera">Varifocal Camera</option>
                                <option value="PTZ Camera">PTZ Camera</option>
                                <option value="High Definition Camera">High Definition Camera</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="camera_subtype" class="mb-1">Subtype</label>
                            <select name="" id="camera_subtype" class="form-control">
                                <option disabled selected value="-">-- Select Subtype --</option>
                                <option value="IP Camera">IP Camera</option>
                                <option value="Coaxial Camera">Coaxial Camera</option>
                                <option value="Wi-Fi Camera">Wi-Fi Camera</option>
                                <option value="Fiber Camera">Fiber Camera</option>
                            </select>
                        </div>
                    </div>
                    <div hidden id="camera_subtype_form" class="row mb-2">
                        <div class="col-md-4">
                            <label for="camera_ip_address" class="mb-1">IP Address</label>
                            <input type="text" name="" id="camera_ip_address" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="camera_port_no" class="mb-1">Port No.</label>
                            <input type="text" name="" id="camera_port_no" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="camera_username" class="mb-1">Username</label>
                            <input type="text" name="" id="camera_username" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="camera_password" class="mb-1">Password</label>
                            <input type="text" name="" id="camera_password" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="camera_angle" class="mb-2">Camera Angle <i class="f-13">(°degree)</i></label>
                            <input type="number" value="0" min="0" max="360" name="" id="camera_angle" class="form-control">
                        </div>
                        <div class="col md 4">
                            <label class="mb-2">Camera Angle Preview</label>
                            <div>
                                <img id="camera_preview" style="transform: rotate(0deg);" src="../../assets/img/camera/camera.png" alt="camera_preview" width="40px;" height="40px">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Additional Information</h6>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="camera_location" class="mb-2">Location</label>
                            <input type="text" name="" id="camera_location" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="camera_brand" class="mb-2">Brand</label>
                            <input type="text" name="" id="camera_brand" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5">
                            <label for="camera_model_no" class="mb-2">Model No.</label>
                            <input required style="text-transform: uppercase" type="text" name="" id="camera_model_no" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label for="camera_barcode" class="mb-2">Barcode (FPOSI)</label>
                            <input required style="text-transform: uppercase" type="text" name="" id="camera_barcode" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="camera_status" class="mb-2">Status</label>
                            <select name="" id="camera_status" class="form-control">
                                <option value="DOWN">DOWN</option>
                                <option value="UP">UP</option>
                            </select>
                        </div>
                    </div>
                    <label for="camera_remarks" class="mb-2">Remarks</label>
                    <textarea maxlength="1000" rows="5" name="" id="camera_remarks" class="form-control" placeholder="Aa"></textarea>
                </div>
            </div>
            <div hidden class="camera_form_control" id="camera_form_control">
                <div class="modal-footer">
                    <button id="cancel_camera_form_btn" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="save_camera_form_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
                    <button id="update_camera_form_btn" class="btn btn-sm btn-success"><span class="fa fa-save"></span> Update</button>
                </div>    
            </div>
        </div>
    </div>
</div>

<!-- CAMERA LIST -->
<div class="modal fade" id="camera_list" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="camera_list_title">Camera List: Pantry Annex</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table  id="camera_list_table" class="table border table-hover">
                    <thead>
                        <tr>
                            <td class="text-start">Preview</td>
                            <td class="text-start">Alias</td>
                            <td class="text-start">Type</td>
                            <td class="text-start">Subtype</td>
                            <td class="text-center" style="width: 80px;">Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Entry Here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="w-100 f-13 text-danger"><i>*Note: select a camera from the unassigned list to add it to the map.</i></div>
            </div>
        </div>
    </div>
</div>


<!-- DELETE CCTV MODAL -->
<div class="modal fade" id="delete_cctv_map" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_cctv_map_title" class="modal-title fw-bolder">Delete Map</h5>    
                </div>
            </div>
            <div class="modal-body text-center">
                <div class="w-100">
                    <div>You're going to delete cctv map "<b><span id="delete_cctv_map_name">MAP</span></b>".</div>
                    <div>All of the camera will also be deleted, this can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#edit_cctv_map"><span class="fa fa-remove"></span> No</button>
                <button id="delete_cctv_map_btn_proceed" type="button" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span> Yes</button>
            </div>
        </div>
    </div>
</div>