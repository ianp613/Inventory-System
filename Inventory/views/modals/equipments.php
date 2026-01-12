<!-- ADD EQUIPMENT MODAL -->
<div class="modal fade" id="add_equipment" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Add Equipment</h6>
            </div>
            <div class="modal-body">
                <label for="add_equipment_select" class="mb-2">Equipment Name</label>
                <select name="" id="add_equipment_select" class="form-control">
                    <option selected disabled value="">-- Select Equipment --</option>
                </select>
                <label for="add_equipment_input" class="mt-2 mb-2">Others</label>
                <input required type="text" name="name" id="add_equipment_input" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_equipment_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>
            </div>    
        </div>
    </div>
</div>

<!-- EDIT EQUIPMENT MODAL -->
<div class="modal fade" id="edit_equipment" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Equipment</h6>
                <button id="delete_equipment_btn" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span></button>
            </div>
            <div class="modal-body">
                <label for="edit_equipment_input" class="mb-2">Equipment Name</label>
                <input required type="text" name="" id="edit_equipment_input" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_equipment_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE EQUIPMENT MODAL -->
<div class="modal fade" id="delete_equipment" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_network_title" class="modal-title fw-bolder">Delete Equipment</h5>    
                </div>
            </div>
            <div class="modal-body text-center">
                <div class="w-100">
                    <div>You're going to delete equipment "<b><span id="delete_equipment_name">EQUIPMENT</span></b>".</div>
                    <div>All of the entry will also be deleted, this can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#edit_equipment"><span class="fa fa-remove"></span> No</button>
                <button id="delete_equipment_btn_proceed" type="button" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span> Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- ADD ENTRY MODAL -->
<div class="modal fade" id="add_entry" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 id="add_entry_title" class="modal-title">Add Entry to Selected Equipment</h6>    
                </div>
                
            </div>
            <div class="modal-body">
                <label for="add_entry_description_input" class="mb-2">Description</label>
                <input required type="text" name="" id="add_entry_description_input" class="form-control">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="add_entry_model_no_input" class="mb-2">Model No.</label>
                        <input required style="text-transform: uppercase" type="text" name="" id="add_entry_model_no_input" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="add_entry_barcode_input" class="mb-2">Barcode (FPOSI)</label>

                        <div class="btn-group form-control m-0 p-0 form-control-nooutline">
                            <input required style="text-transform: uppercase" type="text" name="" id="add_entry_barcode_input" class="form-control">
                            <button class="btn btn-dark" id="barcode_scanner_btn"><span class="fa fa-camera"></span></button>
                        </div>
                        
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="add_entry_specifications_input" class="mb-2">Specifications</label>
                        <input required type="text" name="" id="add_entry_specifications_input" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="add_entry_status_input" class="mb-2">Status</label>
                        <select name="" id="add_entry_status_input" class="form-control">
                            <option value="" selected disabled>-- Select Status --</option>
                            <option value="Standby">Standby</option>
                            <option value="In Use">In Use</option>
                            <option value="For Status">For Status</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                </div>
                <label for="add_entry_remarks_input" class="mb-2 mt-2">Remarks</label>
                <textarea maxlength="1000" rows="5" name="" id="add_entry_remarks_input" class="form-control" placeholder="Aa"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_entry_btn" type="button" data-bs-dismiss="" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT ENTRY MODAL -->
<div class="modal fade" id="edit_entry" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 id="edit_entry_title" class="modal-title">Edit Selected Equipment</h6>    
                </div>
                
            </div>
            <div class="modal-body">
                <label for="edit_entry_description_input" class="mb-2">Description</label>
                <input required type="text" name="" id="edit_entry_description_input" class="form-control">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="edit_entry_model_no_input" class="mb-2">Model No.</label>
                        <input required style="text-transform: uppercase" type="text" name="" id="edit_entry_model_no_input" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_entry_barcode_input" class="mb-2">Barcode (FPOSI)</label>

                        <div class="btn-group form-control m-0 p-0 form-control-nooutline">
                            <input required style="text-transform: uppercase" type="text" name="" id="edit_entry_barcode_input" class="form-control">
                            <button class="btn btn-dark" id="barcode_scanner_btn_edit"><span class="fa fa-camera"></span></button>
                        </div>
                        
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="edit_entry_specifications_input" class="mb-2">Specifications</label>
                        <input required type="text" name="" id="edit_entry_specifications_input" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_entry_status_input" class="mb-2">Status</label>
                        <select name="" id="edit_entry_status_input" class="form-control">
                            <option value="" selected disabled>-- Select Status --</option>
                            <option value="Standby">Standby</option>
                            <option value="In Use">In Use</option>
                            <option value="For Status">For Status</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                </div>
                <label for="edit_entry_remarks_input" class="mb-2 mt-2">Remarks</label>
                <textarea maxlength="1000" rows="5" name="" id="edit_entry_remarks_input" class="form-control" placeholder="Aa"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_entry_btn" type="button" data-bs-dismiss="" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE ENTRY MODAL -->
<div class="modal fade" id="delete_entry" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_entry_title" class="modal-title fw-bolder">Delete Entry</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete <b>"<span id="delete_entry_name">Equipment</span>"</b> entry.</div>
                    <div>This can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_entry_btn" e-id="" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- FOR STATUS MODAL -->
<div class="modal" id="for_status" tabindex="-1">
    <div class="modal-dialog  modal-dialog-scrollable modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">FOR STATUS</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="w-100 text-center text-danger">
                    <div class="spinner-border text-danger ht-70 wd-70 me-5" role="status"></div>
                    <span class="fw-bold" style="font-size: 100px;">
                        WORK IN PROGRESS    
                    </span>
                </div>
                <div class="w-100 text-dark">
                    <div class="ms-5 me-5 mt-2">
                        <div class="w-100 row border border-dark">
                            <div class="col-md-3 p-0 d-flex align-items-center justify-content-center">
                                <img class="w-100 ht-80" src="../../assets/img/fposi-logo.png" alt="">
                            </div>
                            <div class="col-md-7 p-0 text-center border-start border-end border-dark">
                                <div class="pt-2 f-12 ht-35 border-bottom border-dark">Information Security Management System</div>
                                <div class="p-3 ht-70 d-flex align-items-center justify-content-center">
                                    <h6>Hardware Status Report For Computer Parts</h6>
                                </div>
                            </div>
                            <div class="p-0 col-md-2 f-10 fw-bold">
                                <div class="ht-35 border-bottom border-dark">
                                    <div class="ps-1">Document Form No.</div>
                                    <div class="w-100 text-center fw-bold">ISMS-IT-FR-075</div>    
                                </div>
                                <div class="ht-25  border-bottom border-dark"></div>
                                <div class="ps-1 p-1">Record Control No.085</div>
                                <div class="ht-25  border-top border-dark"></div>
                            </div>
                        </div>    
                    </div>
                    <div class="ms-5 me-5 mt-3 fw-bold">
                        <div class="d-flex mb-1">
                            <h6 class="fw-bold f-12 wd-60 text-end mr-3">SITE:</h6>
                            <span style="margin-left: 10px; margin-top: -2px; width: 200px;" class="f-11 border-bottom border-dark text-center">FPOSI MAIN BUILDING, 2ND FLOOR</span>    
                        </div>
                        <div class="d-flex mb-1">
                            <h6 class="fw-bold f-12 wd-60 text-end mr-3">PROJECT:</h6>
                            <span style="margin-left: 10px; margin-top: -2px; width: 200px;" class="f-11 border-bottom border-dark text-center">IT DEPARTMENT</span>    
                        </div>
                        <div class="d-flex mb-1">
                            <h6 class="fw-bold f-12 wd-60 text-end mr-3">SITE:</h6>
                            <span style="margin-left: 10px; margin-top: -2px; width: 200px;" class="f-11 border-bottom border-dark text-center">04/09/2025</span>    
                        </div>
                    </div>
                    <div class="ms-5 me-5 mt-3">
                        <div class="row w-100 border pt-1 border-dark">
                            <div class="col-md-10">
                                <p class="f-9"><i>This material is intended for FPOSI use only. It must not be reproduced in whole or in part, in any form, or by any means without a formal agreement or the written consent of the Document Record Controller (DRC) or Information Security Management Representative (ISMR). Any hard copy or unprotected soft copy of this document shall be regarded as uncontrolled copy.</i></p>
                            </div>
                            <div class="col-md-1 f-9">
                                <div>Document Class:</div>
                                <div>INTERNAL</div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"><span class="fa fa-download"></span> Download</button>
                <button id="edit_equipment_btn" type="button" class="btn btn-primary btn-sm"><span class="fa fa-print"></span> Print</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="barcode_camera"  tabindex="-1">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">BARCODE SCANNER</div>
            </div>
            <div class="modal-body">
                <div class="w-100 bg-primary">
                    <div id="scanner" style="position: absolute; left: 50%; transform: translateX(-50%);"></div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark form-control" id="cancel_barcode_scanner_btn"> <span class="fa fa-remove"></span> Cancel</button>
            </div>
        </div>
    </div>
</div>