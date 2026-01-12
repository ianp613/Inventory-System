<div id="accounts">
    <?php if($_SESSION["privileges"] == "Administrator"){ ?>
    <div class="d-flex mb-3">
        <div class="col-md-6">
            <div class="d-flex">
                <button data-bs-toggle="modal" data-bs-target="#add_account" class="btn btn-sm btn-dark"><span class="fa fa-plus"></span> Create Account</button> 
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-end">
            <div class="dropdown">
                <div id="group_dropdown_toggle" tabindex="0" title="Right-Click Group to Edit" class="dropdown-toggle d-inline-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">Group List</div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="groupDropdown" id="group_dropdown">
                    <!-- DROP DOWN GROUP -->
                </ul>
            </div>
            <button data-bs-target="#add_group" data-bs-toggle="modal" class="btn btn-sm btn-dark" style="margin-bottom: -5px; margin-left: 10px;"><span class="fa fa-plus"></span> Add Group</button>
        </div>
    </div>
    <?php } ?>
    <table id="accounts_table" class="table border table-hover">
        <thead class="fwt-5">
            <tr>
                <td class="text-start">ID</td>
                <td class="text-start">Name</td>
                <td class="text-start">email</td>
                <td class="text-start">User ID</td>
                <td class="text-start">Privilege</td>
                <td style="width: 100px; !important">Action</td>
            </tr>
        </thead>
        <tbody>
            <!-- Entry Here -->
        </tbody>
    </table>
</div>