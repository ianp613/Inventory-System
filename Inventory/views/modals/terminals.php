<!-- ADD TERMINAL -->
<div class="modal fade" id="add_terminal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6>Add Terminal</h6>
            </div>
            <div class="modal-body" id="modal_body_add_terminal">

                <label for="">Terminal</label>
                <input type="text" name="" id="terminal_no" class="form-control mt-2" placeholder="Terminal No.">
                <input type="text" name="" id="cabinet_no" class="form-control mt-2" placeholder="Cabinet No.">
                <input type="text" name="" id="ip_address" class="form-control mt-2" placeholder="IP Address">

                <label for="" class="mt-2 mb-2">Location</label>
                <div class="input-group mb-2">
                    <select name="" id="terminal_add_location_building" class="form-control">
                        <option selected disabled value="">-- Select Building --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="terminal_add_location_building_others" class="form-control" placeholder="if others specify building">
                </div>
                <div class="input-group mb-2">
                    <select name="" id="terminal_add_location_room" class="form-control">
                        <option selected disabled value="">-- Select Room --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="terminal_add_location_room_others" class="form-control" placeholder="if others specify room">
                </div>
                <div class="input-group mb-2">
                    <select name="" id="terminal_add_location_project" class="form-control">
                        <option selected disabled value="">-- Select Project / Office --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="terminal_add_location_project_others" class="form-control" placeholder="if others specify project / office">
                </div>

                <label for="" class="mb-2">Remarks</label>
                <textarea name="" rows="5" id="remarks_" class="form-control mb-2" placeholder="Aa"></textarea>

                <label for="" class="mb-2">Tech Recommendation</label>
                <textarea name="" rows="5" id="tech_recommendation" class="form-control" placeholder="Aa"></textarea>





                <h6 class="alert-success p-2 mt-4 mb-0">SYSTEM UNIT</h6>
                <hr class="p-0 mt-0">

                <label for="">Unit Type</label>
                <select name="" id="unit_type" class="form-control mt-2 mb-2">
                    <option selected disabled value="">-- Select Type --</option>
                    <option value="Tower">Tower</option>
                    <option value="Mini Tower">Mini Tower</option>
                    <option value="Tiny">Tiny</option>
                </select>

                <label for="">Casing</label>
                <input type="text" name="" id="casing" class="form-control mt-2 mb-2" placeholder="Casing">

                <label for="">Motherboard</label>
                <input type="text" name="" id="motherboard_model" class="form-control mt-2" placeholder="Model">
                <input type="text" name="" id="motherboard_barcode" class="form-control mt-2 mb-2" placeholder="Barcode">

                <label for="">CPU (Central Processing Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 cpu_model" placeholder="CPU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 cpu_barcode" placeholder="CPU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="cpu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">RAM (Random Access Memory)</label>
                <input type="text" name="1" id="" class="form-control mt-2 ram_model" placeholder="RAM-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 ram_barcode" placeholder="RAM-1 Barcode">
                <div class="w-100 text-end">
                    <butto id="ram_add_field"n class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Storage Drive (HDD & SSD)</label>
                <input type="text" name="1" id="" class="form-control mt-2 storage_model" placeholder="Storage-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 storage_barcode" placeholder="Storage-1 Barcode">
                <div class="w-100 text-end">
                    <button id="storage_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">PSU (Power Supply Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 psu_model" placeholder="PSU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 psu_barcode" placeholder="PSU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="psu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">GPU (Graphical Processing Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 gpu_model" placeholder="GPU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 gpu_barcode" placeholder="GPU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="gpu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Cooling System (Fan & Liquid Cooler)</label>
                <input type="text" name="1" id="" class="form-control mt-2 cs_model" placeholder="CS-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 cs_barcode" placeholder="CS-1 Barcode">
                <div class="w-100 text-end">
                    <button id="cs_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Expansion Cards</label>
                <input type="text" name="" id="" class="form-control mt-2 ec_model" placeholder="EC-1 Model">
                <input type="text" name="" id="" class="form-control mt-2 ec_barcode" placeholder="EC-1 Barcode">
                <div class="w-100 text-end">
                    <button id="ec_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">PERIPHERALS</h6>
                <hr class="p-0 mt-0">

                <label for="">Input Devices</label>
                <h6 class="f-11 f-i text-danger">This includes Keyboard, Mouse, Scanner, Microphone, Webcam etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 id_type" placeholder="ID-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 id_model" placeholder="ID-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 id_barcode" placeholder="ID-1 Barcode">
                <div class="w-100 text-end">
                    <button id="id_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>


                <label for="">Output Devices</label>
                <h6 class="f-11 f-i text-danger">This includes Monitor, Printer, Speakers, Headphones, Projector etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 od_type" placeholder="OD-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 od_model" placeholder="OD-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 od_barcode" placeholder="OD-1 Barcode">
                <div class="w-100 text-end">
                    <button id="od_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Storage Peripherals</label>
                <h6 class="f-11 f-i text-danger">This includes External HDD/SSD, USB flash drive, Memory card reader, External DVD drive etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 sp_type" placeholder="SP-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 sp_model" placeholder="SP-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 sp_barcode" placeholder="SP-1 Barcode">
                <div class="w-100 text-end">
                    <button id="sp_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">UNINTERRUPTIBLE POWER SUPPLY (UPS)</h6>
                <hr class="p-0 mt-0">

                <label for="">Brand</label>
                <input type="text" name="" id="ups_brand" class="form-control mt-2 mb-2" placeholder="Brand">

                <label for="">Casing</label>
                <input type="text" name="" id="ups_casing_model" class="form-control mt-2" placeholder="Model">
                <input type="text" name="" id="ups_casing_barcode" class="form-control mt-2" placeholder="Barcode">

                <label class="mt-2" for="">Battery</label>
                <input type="text" name="1" id="" class="form-control mt-2 ups_battery_model" placeholder="Batt-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 ups_battery_barcode" placeholder="Batt-1 Barcode">
                <div class="w-100 text-end">
                    <button id="ups_battery_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">ADDITIONAL INFORMATION</h6>
                <hr class="p-0 mt-0">

                <label for="">UPS Status</label>
                <input type="text" name="" id="ups_status" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Kaspersky</label>
                <input type="text" name="" id="kaspersky" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Bitdefender</label>
                <input type="text" name="" id="bitdefender" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Windows Update</label>
                <input type="text" name="" id="windows_update" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Operating System</label>
                <input type="text" name="" id="operating_system" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Windows License</label>
                <input type="text" name="" id="windows_license" class="form-control mt-2 mb-2" placeholder="">
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                <button id="save_add_terminal" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>
































<!-- ADD TERMINAL -->
<div class="modal fade" id="edit_terminal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6>Edit Terminal</h6>
            </div>
            <div class="modal-body" id="modal_body_edit_terminal">

                <label for="">Terminal</label>
                <input type="text" name="" id="edit_terminal_no" class="form-control mt-2" placeholder="Terminal No.">
                <input type="text" name="" id="edit_cabinet_no" class="form-control mt-2" placeholder="Cabinet No.">
                <input type="text" name="" id="edit_ip_address" class="form-control mt-2" placeholder="IP Address">

                <label for="" class="mt-2 mb-2">Location</label>
                <div class="input-group mb-2">
                    <select name="" id="edit_terminal_add_location_building" class="form-control">
                        <option selected disabled value="">-- Select Building --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="edit_terminal_add_location_building_others" class="form-control" placeholder="if others specify building">
                </div>
                <div class="input-group mb-2">
                    <select name="" id="edit_terminal_add_location_room" class="form-control">
                        <option selected disabled value="">-- Select Room --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="edit_terminal_add_location_room_others" class="form-control" placeholder="if others specify room">
                </div>
                <div class="input-group mb-2">
                    <select name="" id="edit_terminal_add_location_project" class="form-control">
                        <option selected disabled value="">-- Select Project / Office --</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="" id="edit_terminal_add_location_project_others" class="form-control" placeholder="if others specify project / office">
                </div>

                <label for="" class="mb-2">Remarks</label>
                <textarea name="" rows="5" id="edit_remarks_" class="form-control mb-2" placeholder="Aa"></textarea>

                <label for="" class="mb-2">Tech Recommendation</label>
                <textarea name="" rows="5" id="edit_tech_recommendation" class="form-control" placeholder="Aa"></textarea>





                <h6 class="alert-success p-2 mt-4 mb-0">SYSTEM UNIT</h6>
                <hr class="p-0 mt-0">

                <label for="">Unit Type</label>
                <select name="" id="edit_unit_type" class="form-control mt-2 mb-2">
                    <option selected disabled value="">-- Select Type --</option>
                    <option value="Tower">Tower</option>
                    <option value="Mini Tower">Mini Tower</option>
                    <option value="Tiny">Tiny</option>
                </select>


                <label for="">Casing</label>
                <input type="text" name="" id="edit_casing" class="form-control mt-2 mb-2" placeholder="Casing">

                <label for="">Motherboard</label>
                <input type="text" name="" id="edit_motherboard_model" class="form-control mt-2" placeholder="Model">
                <input type="text" name="" id="edit_motherboard_barcode" class="form-control mt-2 mb-2" placeholder="Barcode">

                <label for="">CPU (Central Processing Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_cpu_model" placeholder="CPU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_cpu_barcode" placeholder="CPU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_cpu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">RAM (Random Access Memory)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_ram_model" placeholder="RAM-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_ram_barcode" placeholder="RAM-1 Barcode">
                <div class="w-100 text-end">
                    <butto id="edit_ram_add_field"n class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Storage Drive (HDD & SSD)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_storage_model" placeholder="Storage-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_storage_barcode" placeholder="Storage-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_storage_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">PSU (Power Supply Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_psu_model" placeholder="PSU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_psu_barcode" placeholder="PSU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_psu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">GPU (Graphical Processing Unit)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_gpu_model" placeholder="GPU-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_gpu_barcode" placeholder="GPU-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_gpu_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Cooling System (Fan & Liquid Cooler)</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_cs_model" placeholder="CS-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_cs_barcode" placeholder="CS-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_cs_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Expansion Cards</label>
                <input type="text" name="" id="" class="form-control mt-2 edit_ec_model" placeholder="EC-1 Model">
                <input type="text" name="" id="" class="form-control mt-2 edit_ec_barcode" placeholder="EC-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_ec_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">PERIPHERALS</h6>
                <hr class="p-0 mt-0">

                <label for="">Input Devices</label>
                <h6 class="f-11 f-i text-danger">This includes Keyboard, Mouse, Scanner, Microphone, Webcam etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 edit_id_type" placeholder="ID-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 edit_id_model" placeholder="ID-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_id_barcode" placeholder="ID-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_id_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>


                <label for="">Output Devices</label>
                <h6 class="f-11 f-i text-danger">This includes Monitor, Printer, Speakers, Headphones, Projector etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 edit_od_type" placeholder="OD-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 edit_od_model" placeholder="OD-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_od_barcode" placeholder="OD-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_od_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>

                <label for="">Storage Peripherals</label>
                <h6 class="f-11 f-i text-danger">This includes External HDD/SSD, USB flash drive, Memory card reader, External DVD drive etc.</h6>
                <input type="text" name="1" id="" class="form-control mt-2 edit_sp_type" placeholder="SP-1 Type">
                <input type="text" name="1" id="" class="form-control mt-2 edit_sp_model" placeholder="SP-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_sp_barcode" placeholder="SP-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_sp_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">UNINTERRUPTIBLE POWER SUPPLY (UPS)</h6>
                <hr class="p-0 mt-0">

                <label for="">Brand</label>
                <input type="text" name="" id="edit_ups_brand" class="form-control mt-2 mb-2" placeholder="Brand">

                <label for="">Casing</label>
                <input type="text" name="" id="edit_ups_casing_model" class="form-control mt-2" placeholder="Model">
                <input type="text" name="" id="edit_ups_casing_barcode" class="form-control mt-2" placeholder="Barcode">

                <label class="mt-2" for="">Battery</label>
                <input type="text" name="1" id="" class="form-control mt-2 edit_ups_battery_model" placeholder="Batt-1 Model">
                <input type="text" name="1" id="" class="form-control mt-2 edit_ups_battery_barcode" placeholder="Batt-1 Barcode">
                <div class="w-100 text-end">
                    <button id="edit_ups_battery_add_field" class="btn btn-sm btn-secondary alert-dark mt-2">+ Add Field</button>
                </div>





                <h6 class="alert-success p-2 mt-4 mb-0">ADDITIONAL INFORMATION</h6>
                <hr class="p-0 mt-0">

                <label for="">UPS Status</label>
                <input type="text" name="" id="edit_ups_status" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Kaspersky</label>
                <input type="text" name="" id="edit_kaspersky" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Bitdefender</label>
                <input type="text" name="" id="edit_bitdefender" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Windows Update</label>
                <input type="text" name="" id="edit_windows_update" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Operating System</label>
                <input type="text" name="" id="edit_operating_system" class="form-control mt-2 mb-2" placeholder="">

                <label for="">Windows License</label>
                <input type="text" name="" id="edit_windows_license" class="form-control mt-2 mb-2" placeholder="">
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                <button id="save_edit_terminal" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>








































<!-- DELETE TERMINAL MODAL -->
<div class="modal fade" id="delete_terminal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_terminal_title" class="modal-title fw-bolder">Delete Terminal</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete <b>"<span id="delete_terminal_name">Terminal</span>"</b>.</div>
                    <div>This action cannot be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_terminal_btn" e-id="" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>