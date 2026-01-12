<div id="logs">
    <?php if($_SESSION["privileges"] == "Administrator"){  ?>
        <div id="select_log_container" style="margin-bottom: -10px !important;" class="w-100 d-flex">
            <select id="select_log" style="width: 238px; height: 30px; position: absolute;" class="select_log f-13 pt-0 pb-0 form-control">
                <option value="All" selected>Show all logs</option>
            </Select>
        </div>
    <?php } ?>
    <table id="log_table" class="table border table-hover">
        <thead class="fwt-5">
            <tr class="tr_exclude">
                <td class="text-start">ID</td>
                <td class="text-start" style="width: 70%;">Logs</td>
                <td class="text-start">Date & Time</td>
                <?php $_SESSION["privileges"] == "Administrator" ? printf("<td class=\"text-start\" style=\"width: 50px;\">Action</td>") : ""; ?>
            </tr>
        </thead>
        <tbody>
            <!-- Entry Here -->
        </tbody>
    </table>     
</div>