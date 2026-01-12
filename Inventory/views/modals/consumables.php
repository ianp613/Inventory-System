<!-- ADD CONSUMABLES -->
<div class="modal fade" id="add_consumables" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6><span class="fa fa-cubes"></span> Add Consumable</h6>
            </div>
            <div class="modal-body">
                <label id="consumable_code">Code: <b>0</b></label>
                <hr>
                <label for="consumable_description">Description</label>
                <input type="text" name="" id="consumable_description" class="form-control mt-2">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="consumable_measurement">Measurement Type</label>
                        <select name="" id="consumable_measurement" class="form-control mt-2">
                            <option selected disabled value="">-- Select Measurement --</option>
                            <option value="Length">Length</option>
                            <option value="Weight">Weight</option>
                            <option value="Volume">Volume</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="consumable_unit">Unit Type</label>
                        <select name="" id="consumable_unit" class="form-control mt-2">
                            <option value="">-- Select Unit --</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="consumable_stock">Stock</label>
                        <input type="number" min="0" value="0" name="" id="consumable_stock" class="form-control mt-2">
                    </div>
                    <div class="col-md-6">
                        <label for="consumable_restock_point">Restock Point</label>
                        <input type="number" min="0" value="0" name="" id="consumable_restock_point" class="form-control mt-2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                <button id="add_consumables_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT CONSUMABLES -->
<div class="modal fade" id="edit_consumables" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6><span class="fa fa-cubes"></span> Edit Consumable</h6>
            </div>
            <div class="modal-body">
                <label id="edit_consumable_code">Code: <b>0</b></label>
                <hr>
                <label for="edit_consumable_description">Description</label>
                <input type="text" name="" id="edit_consumable_description" class="form-control mt-2">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="edit_consumable_measurement">Measurement Type</label>
                        <select name="" id="edit_consumable_measurement" class="form-control mt-2">
                            <option selected disabled value="">-- Select Measurement --</option>
                            <option value="Length">Length</option>
                            <option value="Weight">Weight</option>
                            <option value="Volume">Volume</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_consumable_unit">Unit Type</label>
                        <select name="" id="edit_consumable_unit" class="form-control mt-2">
                            <option value="">-- Select Unit --</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="edit_consumable_stock">Stock</label>
                        <input type="number" min="0" value="0" name="" id="edit_consumable_stock" class="form-control mt-2">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_consumable_restock_point">Restock Point</label>
                        <input type="number" min="0" value="0" name="" id="edit_consumable_restock_point" class="form-control mt-2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                <button id="edit_consumables_btn" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- RESTOCK CONSUMABLES -->
<div class="modal fade" id="restock_consumables" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6><span class="fa fa-refresh"></span> Restock Consumable</h6>
            </div>
            <div class="modal-body">
                <label for="search_consumable">Search by Code or Description</label>
                <input type="text" id="search_consumable" class="form-control mt-2" placeholder="Enter code or description">
                <div id="search_results" class="mt-2"></div>
                <hr>
                <div id="restock_consumable_info">
                    <span id="consumable_badge_danger" hidden class="badge bg-danger" style="position: absolute; right: 15px;">Low Stock</span>
                    <span id="consumable_badge_success" hidden class="badge bg-success" style="position: absolute; right: 15px;">In Stock</span>
                    <label>Code: <b id="restock_consumables_code"></b></label><br>
                    <label>Description: <span id="restock_consumables_description"></span></label><br>
                    <label>Remaining Stock: <span id="restock_consumables_stock"></span></label>
                </div>
                <hr>
                <label for="restock_quantity">Restock Quantity</label>
                <input type="number" min="1" value="0" id="restock_quantity" class="form-control mt-2">
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-sm btn-secondary"><span class="fa fa-remove"></span> Cancel</button>
                <button sid="" id="restock_consumables_btn" class="btn btn-sm btn-success"><span class="fa fa-plus"></span> Restock</button>
            </div>
        </div>
    </div>
</div>

<!-- GENERATE ADD LOG LINK -->
<div class="modal fade" id="add_log_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6><span class="fa fa-external-link"></span> Add Log</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="f-15">Click the link to open the Add Log page. You can also copy and share the link with others so they can add a log.</h6>
                <h6 class="f-15"><i><a href="#" id="add_log_link">Click Generate Link</a></i></h6>
            </div>
            <div class="modal-footer" id="generate_link_controls">
                <button hidden id="regenerate_link_btn" class="btn btn-sm alert-success btn-success"><span class="fa fa-link"></span> Regenerate Link</button>
                <button hidden id="delete_link_btn" class="btn btn-sm alert-danger btn-danger"><span class="fa fa-trash"></span> Delete Link</button>
                <button id="generate_link_btn" class="btn btn-sm alert-success btn-success"><span class="fa fa-link"></span> Generate Link</button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE CONSUMABLES MODAL -->
<div class="modal fade" id="delete_consumables" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header text-center">
                <div class="w-100">
                    <span class="fa fa-exclamation-triangle text-danger h2"></span>
                    <h5 id="delete_consumables_title" class="modal-title fw-bolder">Delete Consumable</h5>    
                </div>
            </div>
            <div class="modal-header text-center">
                <div class="w-100">
                    <div>You're going to delete <b>"<span id="delete_consumables_description">consumables</span>"</b>.</div>
                    <div>All logs of this item will also be deleted.</div>
                    <div>This can't be undone. Do you wish to proceed?</div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="fa fa-remove"></span> No</button>
                <button id="delete_consumables_btn" e-id="" type="button" data-bs-dismiss="" class="btn btn-danger btn-sm"><span class="fa fa-trash-o"></span> Yes</button>
            </div>
        </div>
    </div>
</div>