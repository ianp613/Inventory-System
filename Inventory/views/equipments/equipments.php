<div id="equipments">
    <div class="d-flex mb-3">
        <div class="col-md-6">
            <button id="add_entry_modal_btn" class="btn btn-sm btn-dark" style="margin-bottom: -5px;"><span class="fa fa-plus"></span> Add Entry</button>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-end">
            <div class="dropdown">
                <div id="equipment_dropdown_toggle" tabindex="0" title="Right-Click Equipment to Edit" class="dropdown-toggle d-inline-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">-- Select Equipment --</div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" id="equipment_dropdown">
                <!-- DROP DOWN EQUIPMENTS -->
                </ul>
            </div>
            <button data-bs-target="#add_equipment" data-bs-toggle="modal" class="btn btn-sm btn-dark" style="margin-bottom: -5px; margin-left: 10px;"><span class="fa fa-plus"></span> Add Equipment</button>
        </div>
    </div>
    <table id="equipment_table" class="table border table-hover">
        <thead class="fwt-5">
            <tr>
                <td class="text-start">ID</td>
                <td class="text-start">Description</td>
                <td class="text-start">Model No.</td>
                <td class="text-start">Barcode (FPOSI)</td>
                <td class="text-start">Status</td>
                <td style="width: 100px; !important">Action</td>
            </tr>
        </thead>
        <tbody>
            <!-- Entry Here -->
        </tbody>
    </table>
</div>
