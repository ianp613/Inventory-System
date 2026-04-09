<!-- Ellipsis -->
<div class="modal fade" id="ellipsis_menu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <button hidden id="ff_select_btn" class="btn btn-light mb-2 w-100 text-start"><span class="fa fa-check"></span> Select</button>
                <button id="ff_new_folder_btn" class="btn btn-light mb-2 w-100 text-start"><span class="fa fa-plus"></span> New Folder</button>
                <button id="ff_delete_btn" class="btn btn-light w-100 text-start"><span class="fa fa-upload"></span> Upload</button>
            </div>
        </div>
    </div>
</div>


<!-- Rename -->
<div class="modal fade" id="ff_rename" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <input type="text" name="" id="ff_rename_input" class="form-control">
                <div class="d-flex gap-2 mt-2">
                    <button id="ff_rename_cancel" class="btn btn-light text-start flex-fill text-center"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_rename_save" class="btn btn-light text-start flex-fill text-center"><span class="fa fa-save"></span> Save</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- New Folder -->
<div class="modal fade" id="ff_new_folder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <input type="text" name="" id="ff_new_folder_input" class="form-control">
                <div class="d-flex gap-2 mt-2">
                    <button id="ff_new_folder_cancel" class="btn btn-light text-start flex-fill text-center"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_new_folder_create" class="btn btn-light text-start flex-fill text-center"><span class="fa fa-save"></span> Create</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div class="modal fade" id="ff_delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h6 class="text-center">You are going to delete this <span id="item_count">item</span>. Do you wish to proceed?</h6>
                <div class="d-flex gap-2 mt-3">
                    <button id="ff_delete_cancel" class="btn btn-light text-start flex-fill text-center"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_delete_proceed" class="btn btn-danger text-start flex-fill text-center"><span class="fa fa-trash"></span> Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>