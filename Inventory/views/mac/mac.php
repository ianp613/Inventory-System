<div id="mac">
    <div class="d-flex mb-3">
        <div class="col-md-6">
            <div class="d-flex">
                <h6 id="mac_record">MAC Address: 0</h6>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-end">
            <div class="dropdown">
                <div id="wifi_dropdown_toggle" tabindex="0" title="Right-Click Wifi to Edit" class="dropdown-toggle d-inline-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">-- Select Wifi --</div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" id="wifi_dropdown">
                <!-- DROP DOWN SSID -->
                </ul>
            </div>
            <button data-bs-target="#add_wifi" data-bs-toggle="modal" class="btn btn-sm btn-dark" style="margin-bottom: -5px; margin-left: 10px;"><span class="fa fa-plus"></span> Add Wifi</button>
        </div>
    </div>
    <input hidden type="file" name="" id="ip_import_input"  accept=".xls, .xlsx">
    <table id="wifi_table" class="table border table-hover">
        <thead class="fwt-5">
            <tr>
                <td class="text-start">ID</td>
                <td class="text-start">MAC</td>
                <td class="text-start">Name</td>
                <td class="text-start">Tagged Device</td>
                <td class="text-start">Project / Office</td>
                <td class="text-start">Location</td>
                <td style="width: 100px !important;">Action</td>
            </tr>
        </thead>
        <tbody>
            <!-- Entry Here -->
        </tbody>
    </table>
</div>
