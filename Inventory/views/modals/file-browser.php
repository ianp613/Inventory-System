<!-- Ellipsis -->
<div class="modal fade" id="ellipsis_menu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ff-menu">
                    <button hidden id="ff_select_btn" class="ff-menu-item"><span class="fa fa-check-square-o"></span> Select</button>
                    <button id="ff_new_folder_btn" class="ff-menu-item"><span class="fa fa-folder-o"></span> New Folder</button>
                    <button id="ff_upload_btn" class="ff-menu-item"><span class="fa fa-upload"></span> Upload</button>
                    <div class="ff-menu-divider"></div>
                    <button id="ff_logout_btn" class="ff-menu-item ff-menu-item-danger"><span class="fa fa-sign-out"></span> Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Rename -->
<div class="modal fade" id="ff_rename" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h6 class="ff-modal-title"><span class="fa fa-i-cursor"></span> Rename</h6>
                <input type="text" name="" id="ff_rename_input" class="form-control" placeholder="New name" autocomplete="off">
                <div class="d-flex gap-2 mt-3">
                    <button id="ff_rename_cancel" class="btn btn-secondary p-2 text-center flex-fill"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_rename_save" class="btn btn-primary p-2 text-center flex-fill"><span class="fa fa-save"></span> Save</button>
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
                <h6 class="ff-modal-title"><span class="fa fa-folder-o"></span> New Folder</h6>
                <input type="text" name="" id="ff_new_folder_input" class="form-control" placeholder="Folder name" autocomplete="off">
                <div class="d-flex gap-2 mt-3">
                    <button id="ff_new_folder_cancel" class="btn btn-secondary p-2 text-center flex-fill"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_new_folder_create" class="btn btn-primary p-2 text-center flex-fill"><span class="fa fa-save"></span> Create</button>
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
                <div class="ff-modal-confirm">
                    <div class="ff-modal-icon ff-modal-icon-danger"><span class="fa fa-trash"></span></div>
                    <h6 class="text-center">You are going to delete this <span id="item_count">item</span>. Do you wish to proceed?</h6>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button id="ff_delete_cancel" class="btn btn-secondary p-2 text-center flex-fill"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="ff_delete_proceed" class="btn btn-danger p-2 text-center flex-fill"><span class="fa fa-trash"></span> Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload -->
 <div class="modal fade" id="ff_upload" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card_upload">
                    <div class="d-flex justify-content-between">
                        <h1>Upload Files &amp; Folders</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="drop-zone" id="dropZone">
                        <div class="icon">&#128228;</div>
                        <div class="label">Drop files or folders here</div>
                        <div class="sub">or use the buttons below</div>
                    </div>

                    <input type="file" id="fileInput" multiple />
                    <input type="file" id="folderInput" webkitdirectory multiple />

                    <div class="btn_upload-row">
                        <button class="btn_upload" onclick="document.getElementById('fileInput').click()">+ Add files</button>
                        <button class="btn_upload" onclick="document.getElementById('folderInput').click()">+ Add folder</button>
                        <button class="btn_upload primary_upload" id="uploadBtn" disabled onclick="uploadAll()">Upload all</button>
                    </div>

                    <div class="queue" id="queue"></div>
                    <div class="summary" id="summary"></div>
                    <div class="log" id="log"></div>
                </div>
            </div>
        </div>
    </div>
</div>