<div id="routers">
    <div class="row">
        <div class="col-md-8">
            <table id="router_table" class="table border table-hover">
                <thead class="fwt-5">
                    <tr class="tr_exclude">
                        <td class="text-start">ID</td>
                        <td class="text-start">Name</td>
                        <td class="text-start">IP</td>
                        <td class="text-start">Subnet Mask</td>
                        <td style="width: 100px; !important">Action</td>
                    </tr>
                </thead>
                <tbody>
                    <!-- Entry Here -->
                </tbody>
            </table>        
        </div>
        <div class="col-md-4">
            <table id="router_isp_table" class="table border">
                <thead class="fwt-5">
                    <tr>
                        <td class="text-start">
                            WAN Settings   
                            <select class="form-control mt-2" name="" id="active_wan">
                                <option disabled selected value="-">-- Select Active WAN --</option>
                            </select>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <!-- Entry Here -->
                </tbody>
            </table> 
            <div class="w-100 text-end">
                <button hidden id="save_active_wan" class="save_active_wan btn btn-sm btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>