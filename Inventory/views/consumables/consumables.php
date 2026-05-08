<div id="consumables" class="theme-card theme-card-dark">
    <div hidden class="btn-group mb-2" id="request_menu_btn">
        <button id="other_request" class="btn btn-dark alert-dark fw-bold">Other Request</button>    
        <button id="your_request" class="btn btn-dark fw-bold">Your Request</button>
    </div>
    
    <div hidden id="cons">
        <button hidden data-bs-toggle="modal" data-bs-target="#add_consumables" class="btn btn-dark wd-120 alert-dark fw-bold"><span class="fa fa-cubes"></span><div>ADD ENTRY</div></button>
        <button hidden data-bs-toggle="modal" data-bs-target="#restock_consumables" class="btn btn-dark wd-120 alert-dark fw-bold"><span class="fa fa-refresh"></span><div>RESTOCK</div></button>
        <button id="show_logs" class="btn btn-dark wd-120 alert-dark fw-bold"><span class="fa fa-edit"></span><div>LOGS</div></button>
        <button id="add_log" class="btn btn-dark wd-150 alert-dark fw-bold"><span class="fa fa-file-text"></span><div>REQUEST FORM</div></button>
        <button id="consumable_request" class="btn btn-dark wd-150 alert-dark fw-bold"><span class="fa fa-info"></span><div>REQUESTS</div></button>
        <a target="_blank" href="https://idcsi-officesuites.com/aims2/login.php" class="btn btn-dark wd-150 alert-dark fw-bold"><span class="fa fa-external-link"></span><div>AIMS</div></a>
        <div class="mt-4"></div>
        <table id="consumables_table" class="table table-hover border">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Code</td>
                    <td>Description</td>
                    <td>Measurement</td>
                    <td>Unit</td>
                    <td>Stock</td>
                    <td>Status</td>
                    <td>Last Restock</td>
                    <td class="text-start" style="width: 50px;">Action</td>
                </tr>
            </thead>
            <tbody>
                <!-- Entry Here -->
            </tbody>
        </table>    
    </div>
    <div hidden id="cons_log">
        <table id="consumables_logs_table" class="table table-hover border">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Name</td>
                    <td>Item</td>
                    <td>Quantity Deduction</td>
                    <td>Remarks</td>
                    <td>Date</td>
                </tr>
            </thead>
            <tbody>
                <!-- Entry Here -->
            </tbody>
        </table>
    </div>
    <div hidden id="cons_request_others">
        <table id="consumables_requests_others_table" class="table table-hover border">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Requested by</td>
                    <td>Item</td>
                    <td>Quantity</td>
                    <td>Remarks</td>
                    <td>Status</td>
                    <td>Date</td>
                    <td class="wd-200">Action</td>
                </tr>
            </thead>
            <tbody>
                <!-- Entry Here -->
            </tbody>
        </table>
    </div>
    <div hidden id="cons_request">
        <h6 class="f-i text-danger alert-danger p-3 rounded-3 mb-4 blink-1">*Note: If your request is not approved after an hour, please send a message to wherever you sent your request.</h6>
        <table id="consumables_requests_table" class="table table-hover border">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Requested to</td>
                    <td>Item</td>
                    <td>Quantity</td>
                    <td>Remarks</td>
                    <td>Status</td>
                    <td>Date</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <!-- Entry Here -->
            </tbody>
        </table>
    </div>
</div>