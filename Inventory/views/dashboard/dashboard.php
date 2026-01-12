<div id="dashboard">
    <h6>Equipments</h6>
    <div class="row">
        <div class="col-md-3 p-2">
            <div class="border border-1 rounded">
                <div class="w-100 ht-60 p-2 d-flex flex-row justify-content-between">
                    <div class="ps-2 overflow-hidden">
                        <h5 id="inuse" class="text-success p-0 m-0">0 unit</h5>
                        <h6 class="f-14">Total In Use</h6>  
                    </div>
                    <div class="pt-2 pe-3">
                        <h1 class="f-30 fa fa-check text-secondary"></h1>
                    </div>
                </div>
                <!-- <div class="w-100 ht-5 bg-secondary rounded-bottom"></div>     -->  
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="border border-1 rounded">
                <div class="w-100 ht-60 p-2 d-flex flex-row justify-content-between">
                    <div class="ps-2 overflow-hidden">
                        <h5 id="standby" class="text-primary p-0 m-0">0 unit</h5>
                        <h6 class="f-14">Total Standby</h6>  
                    </div>
                    <div class="pt-2 pe-3">
                        <h1 class="f-30 fa fa-tasks text-secondary"></h1>
                    </div>
                </div>
                <!-- <div class="w-100 ht-5 bg-secondary rounded-bottom"></div>     -->
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="border border-1 rounded">
                <div class="w-100 ht-60 p-2 d-flex flex-row justify-content-between">
                    <div class="ps-2 overflow-hidden">
                        <h5 id="forstatus" class="text-danger p-0 m-0">0 unit</h5>
                        <h6 class="f-14">Total For Status</h6>  
                    </div>
                    <div class="pt-2 pe-3">
                        <h1 class="f-30 fa fa-exclamation-triangle text-secondary"></h1>
                    </div>
                </div>
                <!-- <div class="w-100 ht-5 bg-secondary rounded-bottom"></div>     -->  
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="border border-1 rounded">
                <div class="w-100 ht-60 p-2 d-flex flex-row justify-content-between">
                    <div class="ps-2 overflow-hidden">
                        <h5 id="pending" class="text-warning p-0 m-0">0 unit</h5>
                        <h6 class="f-14">Total Pending</h6>  
                    </div>
                    <div class="pt-2 pe-3">
                        <h1 class="f-30 fa fa-clock-o text-secondary"></h1>
                    </div>
                </div>
                <!-- <div class="w-100 ht-5 bg-secondary rounded-bottom"></div>     -->  
            </div>
        </div>
    </div>
    <h6 hidden class="mt-4" style="margin-bottom: -2px;">Current Configurations</h6>
    <div hidden class="p-3">
        <div class="row">
            <div class="col-md-6">
                <h6 class="f-14"><span class="fa fa-wifi"></span> Assigned ISP</h6>
                <div id="active_isp" class="ps-3 scroll-y">
                    <p class="f-14 mb-1">No data available</p>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="f-14"><span class="fa fa-gears"></span> Connected Routers</h6>
                    <div id="active_routers" class="ps-3 scroll-y">
                    <p class="f-14 mb-1">No data available</p>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- CHARTS --------------------------------------------------------------------------------------------------------- -->
    <div class="d-flex w-100 mt-3">
        <div class="row w-100">
            <div class="col-7">
                <div class="d-flex justify-content-between w-100">
                    <h6>Stock Deductions Over Time</h6>
                    <div class="input-group input-group-sm mb-3 wd-120" style="margin-top: -3px;">
                        <span class="input-group-text" id="inputGroup-sizing-sm">Show</span>
                        <select name="" id="show_sdot" class="form-control wd-100">
                            <option value="Daily">Daily</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Yearly">Yearly</option>
                        </select>
                    </div>
                    <div id="select_date_sdot" class="input-group input-group-sm mb-3 wd-240" style="margin-top: -3px;">
                        <span class="input-group-text" id="inputGroup-sizing-sm">Select Date</span>
                        <select class="months_number form-control" name="" id="months_sdot"></select>
                        <select class="years form-control" name="" id="years_sdot"></select>
                        <span hidden class="ms-2 me-2 mb-0" style="margin-top: 4px;" id="to_dash"> to </span>
                        <select hidden class="years form-control" name="" id="to_years_sdot"></select>
                    </div>
                </div>
                <canvas id="sdot"></canvas>
            </div>
            <div class="col-5">
                <h6 class="mt-1">Top Users By Stock Deductions</h6>
                <div id="select_date_tusd" class="input-group input-group-sm mb-1 mt-4" style="margin-top: -3px; width: 350px;">
                    <span class="input-group-text" id="inputGroup-sizing-sm" style="width: 110px;">Select Date</span>
                    <select class="months_text form-control" name="" id="months_tusd"></select>
                    <select class="years form-control" name="" id="years_tusd"></select>
                </div>
                <div id="select_date_tusd" class="input-group input-group-sm mb-3" style="width: 350px;">
                    <span class="input-group-text" id="inputGroup-sizing-sm" style="width: 110px;">Consumables</span>
                    <select class="form-control" name="" id="consumable_tusd"></select>
                </div>
                <canvas style="height: 800px !important;" id="tusd"></canvas>
            </div>
        </div>
    </div>
    <h6 <?php $_SESSION["g_type"] == "IT" ? "" : print("hidden");?> class="mt-3" style="margin-bottom: -2px;"><span class="fa fa-info-circle"></span> Your IP</h6>
    <div <?php $_SESSION["g_type"] == "IT" ? "" : print("hidden");?> class="row p-3">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <p id="wmi_ip" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">IP:</span></p>
                    <p id="wmi_isp" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">ISP:</span></p>
                    <p id="wmi_asn" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">ASN:</span></p>    
                </div>
                <div class="col-md-6">
                    <p id="wmi_city" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">City:</span></p>
                    <p id="wmi_region" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">Region:</span></p>
                    <p id="wmi_country" class="f-13 mb-1 text-primary fw-bold"><span class="fwt-5 text-dark">Country:</span></p>
                </div>
            </div>
        </div>    
    </div>
</div>