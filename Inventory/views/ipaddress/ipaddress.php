<div id="ipaddress">
    <div class="d-flex mb-3">
        <div class="col-md-6">
            <div class="d-flex">
                <h6 id="used_ip">Used IP: 0</h6><span class="ms-3 me-3" style="margin-top: -4px;">|</span>
                <h6 id="available_ip">Available IP: 0</h6>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-end">
            <div class="dropdown">
                <div id="network_dropdown_toggle" tabindex="0" title="Right-Click Network to Edit" class="dropdown-toggle d-inline-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">-- Select Network --</div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" id="network_dropdown">
                <!-- DROP DOWN NETWORK -->
                </ul>
            </div>
            <button data-bs-target="#add_network" data-bs-toggle="modal" class="btn btn-sm btn-dark" style="margin-bottom: -5px; margin-left: 10px;"><span class="fa fa-plus"></span> Add Network</button>
        </div>
    </div>
    <input hidden type="file" name="" id="ip_import_input"  accept=".xls, .xlsx">
    <table id="network_table" class="table border table-hover">
        <thead class="fwt-5">
            <tr>
                <td class="text-start">ID</td>
                <td class="text-start">IP</td>
                <td class="text-start">Hostname</td>
                <td class="text-start">Site/Location</td>
                <td class="text-start">Server</td>
                <td class="text-start">Status</td>
                <td class="text-start">State</td>
                <td class="text-start">Web Mgmt. Port</td>
                <td class="text-start">Credentials</td>
                <td style="width: 100px !important;">Action</td>
            </tr>
        </thead>
        <tbody>
            <!-- Entry Here -->
        </tbody>
    </table>
</div>
