<div class="modal fade" id="zoom_card" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="post-it-card" onclick="toggleZoom(this)">
                <div class="modal-header p-0 pb-2">
                    <h6 id="zoom_card_message">Paul Ian Dumdum</h6>
                    <button class="btn btn-sm"><span class="fa fa-edit"></span></button>
                </div>
                <div class="modal-body ps-0 pe-0">
                    <p id="zoom_card_message">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_post" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6><span class="fa fa-th-large"></span> Create Post</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="pastel_color">Select Theme</label>
                        <select name="pastel_color" id="pastel_color" class="form-control"></select>        
                    </div>
                    <div class="col-md-4">
                        <label for="">Post Duration</label>
                    </div>
                    <div class="col-md-4">
                        <label for="">Type</label>
                        <Select class="form-control">
                            <option value="">Simple</option>
                            <option value="">Information ℹ️</option>
                            <option value="">Warning ⚠️</option>
                            <option value="">Important 🚨</option>
                        </Select>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn">Discard</button>
                <button class="btn">Post</button>
            </div>
        </div>
    </div>
</div>