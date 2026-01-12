if(document.getElementById("dashboard")){
    var inuse = document.getElementById("inuse");
    var standby = document.getElementById("standby");
    var forstatus = document.getElementById("forstatus");
    var pending = document.getElementById("pending");
    var active_isp = document.getElementById("active_isp");
    var routers = document.getElementById("active_routers");

    var show_sdot = document.getElementById("show_sdot")

    var months_sdot = document.getElementById("months_sdot")
    var years_sdot = document.getElementById("years_sdot")
    var to_years_sdot = document.getElementById("to_years_sdot")
    var to_dash = document.getElementById("to_dash")

    var select_date_sdot = document.getElementById("select_date_sdot")

    var consumables = [];
    var consumables_log = [];

    setConsumables()
    function setConsumables(){
        sole.get("../../controllers/dashboard/get_consumables_log.php")
        .then(res => {
            consumables_log = res.consumables_log
        })
        sole.get("../../controllers/dashboard/get_consumables.php")
        .then(res => {
            consumables = res.consumables
        })    
    }
    

    sole.get("../../controllers/dashboard/get_entry.php")
    .then(res => load_unit_counts(res))

    sole.get("https://ipinfo.io/json")
    .then(res => {
        // var asn_isp = res.
        const [asn, ...ispParts] = res.org.split(" ");
        const isp = ispParts.join(" ");

        var wmi_ip = document.getElementById("wmi_ip")
        var wmi_isp = document.getElementById("wmi_isp")
        var wmi_asn = document.getElementById("wmi_asn")
        var wmi_city = document.getElementById("wmi_city")
        var wmi_region = document.getElementById("wmi_region")
        var wmi_country = document.getElementById("wmi_country")
        
        wmi_ip.innerHTML = wmi_ip.innerHTML + " " + res.ip
        wmi_isp.innerHTML = wmi_isp.innerHTML + " " + isp
        wmi_asn.innerHTML = wmi_asn.innerHTML + " " + asn
        wmi_city.innerHTML = wmi_city.innerHTML + " " + res.city
        wmi_region.innerHTML = wmi_region.innerHTML + " " + res.region
        wmi_country.innerHTML = wmi_country.innerHTML + " " + res.country
    })


    sole.get("../../controllers/dashboard/current_configurations.php")
    .then(res => {
        res.active_isp.length ? active_isp.innerHTML = "" : null
        res.active_isp.forEach(ai => {
            var p = document.createElement("p");
            p.setAttribute("class","f-14 mb-1")
            p.innerHTML = ai
            active_isp.appendChild(p)
        });

        res.routers.length ? routers.innerHTML = "" : null
        res.routers.forEach(ai => {
            var p = document.createElement("p");
            p.setAttribute("class","f-14 mb-1")
            p.innerHTML = ai
            routers.appendChild(p)
        });
    })

    function load_unit_counts(res){
        var count = [0,0,0,0];
        for (let i = 0; i < res.entry.length; i++) {
            if(res.entry[i]["status"] == "Standby"){
                count[1]++
            }
            if(res.entry[i]["status"] == "In Use"){
                count[0]++
            }
            if(res.entry[i]["status"] == "For Status"){
                count[2]++
            }
            if(res.entry[i]["status"] == "Pending"){
                count[3]++
            }
        }

        count[0] != 0 ? count[0] == 1 ? inuse.innerText = count[0] + " unit" : inuse.innerText = count[0] + " units" : inuse.innerText = "No record"
        count[1] != 0 ? count[1] == 1 ? standby.innerText = count[1] + " unit" : standby.innerText = count[1] + " units" : standby.innerText = "No record"
        count[2] != 0 ? count[2] == 1 ? forstatus.innerText = count[2] + " unit" : forstatus.innerText = count[2] + " units" : forstatus.innerText = "No record"
        count[3] != 0 ? count[3] == 1 ? pending.innerText = count[3] + " unit" : pending.innerText = count[3] + " units" : pending.innerText = "No record"
    }



    // CHARTS ======================================================
    // Stock Deduction Over Time

    // Predefined pastel colors
    const pastelColors = [
    '#AEC6CF', // pastel blue
    '#FFB347', // pastel orange
    '#77DD77', // pastel green
    '#F49AC2', // pastel pink
    '#CFCFC4', // pastel gray
    '#B39EB5', // pastel purple
    '#FFD1DC', // baby pink
    '#CB99C9', // pastel lavender
    // '#FDFD96', // pastel yellow
    '#B5EAD7', // mint pastel
    '#C7CEEA'  // sky pastel
    ];

    // Track used colors
    let usedColors = [];

    // Function to get a unique pastel color
    function getUniquePastelColor() {
    // Find unused color
    const available = pastelColors.filter(c => !usedColors.includes(c));

    if (available.length > 0) {
        const color = available[Math.floor(Math.random() * available.length)];
        usedColors.push(color);
        return color;
    }

    // If all colors are used, generate a random pastel
    const r = Math.floor((Math.random() * 127) + 127); // 127–255 for light tones
    const g = Math.floor((Math.random() * 127) + 127);
    const b = Math.floor((Math.random() * 127) + 127);
    const color = `rgb(${r}, ${g}, ${b})`;
    usedColors.push(color);
        return color;
    }



    const ctx = document.getElementById('sdot');

    // Start with empty labels and datasets
    const sdot = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],        // no labels yet
            datasets: []       // no datasets yet
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuad'
            },
            plugins: {
                legend: {
                    title: {
                        display: true,
                        text: 'Consumables',
                    }
                }
            }
        }
    });

    sdot.options.plugins.legend.align = 'start';
    sdot.options.plugins.legend.title.position = 'start';
    sdot.update();

    // Add a new dataset dynamically
    function addDataset(itemName,chart_) {
        const newDataset = {
            label: itemName,
            data: [], // start empty
            borderColor: getUniquePastelColor(),
            borderWidth: 2,
            tension: 0.4
        };
        chart_.data.datasets.push(newDataset);
        chart_.update();
    }

    // Add a new label and values for each dataset
    function addRecord(dateLabel, values, chart_) {
        chart_.data.labels.push(dateLabel);

        chart_.data.datasets.forEach((dataset, i) => {
        dataset.data.push(values[i]); // values is an array matching datasets
        });

        chart_.update();
    }

    // INSERT DAILY START ====================================================================
    if(show_sdot.value == "Daily"){
        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });
            insertDaily(getYear(),getMonth())
        }, 1000);
    }

    function insertDaily(year, month){
        clearAllData(sdot)
        setConsumables()
        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });    
        }, 100);
        
        setTimeout(() => {
            var daily = getDates(year, month);
            var data = [];

            // loop first the date and add all date to data
            daily.forEach(dail => {
                data.push([dail,[]]);
            })

            for (let daily_id = 0; daily_id < daily.length; daily_id++) {
                // loop all consumables
                consumables.forEach(cons => {
                    // get all consumable_log with the same id and if date exist
                    var total_value = 0;
                    
                    consumables_log.forEach(cons_log => {
                        if(cons_log.date == daily[daily_id] && cons_log.cid == cons.id){
                            total_value = parseFloat(cons_log.quantity_deduction) + total_value
                        }
                    })

                    const entry = data.find(([date]) => date === daily[daily_id]);

                    if (entry) {
                        entry[1].push(total_value);
                    }
                })
            }
            data.forEach(dat => {
                addRecord(formatDateToMonthDay(dat[0]),dat[1],sdot)
            })    
        }, 200);
        
    }

    months_sdot.addEventListener("change",e => {
        if(show_sdot.value == "Daily"){
            insertDaily(years_sdot.value,months_sdot.value)    
        }
    })
    years_sdot.addEventListener("change",e => {
        if(show_sdot.value == "Daily"){
            insertDaily(years_sdot.value,months_sdot.value)    
        }else if(show_sdot.value == "Monthly"){
            insertMonthly(years_sdot.value)
        }else if(show_sdot.value == "Yearly"){
            insertYearly(years_sdot.value,to_years_sdot.value)
        }
    })

    to_years_sdot.addEventListener("change",e => {
        if(show_sdot.value == "Daily"){
            insertDaily(years_sdot.value,months_sdot.value)    
        }else if(show_sdot.value == "Monthly"){
            insertMonthly(years_sdot.value)
        }else if(show_sdot.value == "Yearly"){
            insertYearly(years_sdot.value,to_years_sdot.value)
        }
    })

    // INSERT DAILY END ====================================================================


    // INSERT MONTHLY START ====================================================================
    if(show_sdot.value == "Monthly"){
        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });
            insertMonthly(getYear())
        }, 1000);
    }

    function insertMonthly(year){
        clearAllData(sdot)
        setConsumables()

        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });    
        }, 100);

        setTimeout(() => {
            var monthly = getAllMonths("text","short");
            var data = [];

            // loop first the months and add all month to data
            monthly.forEach(month => {
                data.push([month,[]]);
            })

            for (let monthly_id = 0; monthly_id < monthly.length; monthly_id++) {
                // loop all consumables
                consumables.forEach(cons => {
                    // get all consumable_log with the same id and if date exist
                    var total_value = 0;
                    
                    consumables_log.forEach(cons_log => {
                        var mYtext = monthly[monthly_id] + ", " + years_sdot.value
                        if(isSameMonthYear(cons_log.date,mYtext) && cons_log.cid == cons.id){
                            total_value = parseFloat(cons_log.quantity_deduction) + total_value
                        }
                    })

                    const entry = data.find(([date]) => date === monthly[monthly_id]);

                    if (entry) {
                        entry[1].push(total_value);
                    }
                })
            } 
            data.forEach(dat => {
                addRecord(dat[0],dat[1],sdot)
            }) 
        }, 200);
    }
    // INSERT MONTHLY END ====================================================================

    // INSERT YEARLY START ====================================================================
    if(show_sdot.value == "Yearly"){
        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });
            insertYearly(getYear())
        }, 1000);
    }

    function insertYearly(year,to_year){
        if(year > to_year){
            bs5.toast("warning","The starting year should be greater than the ending year.")
            return
        }
        year--
        to_year++
        
        clearAllData(sdot)
        setConsumables()

        setTimeout(() => {
            consumables.forEach(cons => {
                addDataset(cons.description,sdot)
            });    
        }, 100);

        setTimeout(() => {
            var data = [];

            // loop first the months and add all month to data
            for (let index = year; index <= to_year; index++) {
                data.push([index,[]]);
            }

            for (let index = year; index <= to_year; index++) {
                // loop all consumables
                consumables.forEach(cons => {
                    // get all consumable_log with the same id and if date exist
                    var total_value = 0;
                    
                    consumables_log.forEach(cons_log => {
                        if(isSameYear(cons_log.date,index) && cons_log.cid == cons.id){
                            total_value = parseFloat(cons_log.quantity_deduction) + total_value
                        }
                    })

                    const entry = data.find(([date]) => date === index);

                    if (entry) {
                        entry[1].push(total_value);
                    }
                })
            } 
            data.forEach(dat => {
                addRecord(dat[0],dat[1],sdot)
            }) 
        }, 200);
    }
    // INSERT YEARLY END =======================================================================








    var consumable_tusd = document.getElementById("consumable_tusd")
    
    var years_tusd = document.getElementById("years_tusd")
    var months_tusd = document.getElementById("months_tusd")
    var consumable_tusd = document.getElementById("consumable_tusd")

    var users = [];

    sole.get("../../controllers/dashboard/get_users.php")
    .then(res => {
        users = res
    })

    





    const ctx_tusd = document.getElementById('tusd');
    const tusd = new Chart(ctx_tusd, {
    type: 'bar',
    data: {
        labels: [],
        datasets: []
    },
    options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                        position: 'right', // 👈 legend on the right
                        align: 'center',   // optional: start | center | end
                        labels: {
                        padding: 16      // optional: spacing
                    }
                }
            },
            scales: {
                y: {
                    display: false
                },
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    setTimeout(() => {
        reloadtusd()
    }, 500);

    setTimeout(() => {
        consumables.forEach(cons => {
            var opt = document.createElement("option")
            opt.value = cons.id
            opt.innerText = cons.description
            consumable_tusd.appendChild(opt)
        })
    }, 200);

    consumable_tusd.addEventListener("change",e => {
        reloadtusd()
    })

    months_tusd.addEventListener("change",e => {
        reloadtusd()
    })

    years_tusd.addEventListener("change",e => {
        reloadtusd()
    })

    function reloadtusd(){
        var consumables_log = [];
        clearAllData(tusd)
        
        sole.post("../../controllers/dashboard/get_logs.php",{
            id: consumable_tusd.value
        }).then(res => {
            consumables_log = res
        })

        sole.get("../../controllers/dashboard/get_users.php")
        .then(res => {
            users = res
        })

        setTimeout(() => {
            // get all consumable log base on month and year
            var cons_log = [];
            consumables_log.forEach(clog => {
                var mYtext = months_tusd.value + ", " + years_tusd.value
                if(isSameMonthYear(clog.date,mYtext)){
                    cons_log.push(clog)
                }
            });
            consumables_log = cons_log

            // get all users with consumable logs
            var user_temp = [];
            var datas = []; 
            users.forEach(user => {
                var insert = false;
                var total = 0;
                consumables_log.forEach(clog => {
                    if(clog.uid == user.id){
                        insert = true
                        total = parseFloat(clog.quantity_deduction) + total
                    }
                });
                if(insert){
                    datas.push([user.name,total])
                }
                insert ? user_temp.push(user) : null
            })
            users = user_temp

            // get all userid
            var ids = []
            users.forEach(user => {
                if(!ids.includes(user.id)){
                    ids.push(user.id)
                }
            })

            var total = 0;
            consumables_log.forEach(clog => {
                if(!ids.includes(parseInt(clog.uid))){
                    total = parseInt(clog.quantity_deduction) + total
                }
            })

            total ? datas.push(["Others",total]) : null

            
            tusd.data.labels.push(months_tusd.value);
            tusd.update();

            const top = datas.reduce((acc, item) => {
                acc.push(item);
                acc.sort((a, b) => b[1] - a[1]);
                if (acc.length > 9) acc.pop();
                return acc;
            }, []);

            usedColors = []
            top.forEach(data => {
                tusd.data.datasets.push({
                    label: data[0],
                    data: [data[1]],
                    backgroundColor: getUniquePastelColor(), // 👈 bar color
                });
                tusd.update();
            })
        }, 1000);    
    }

    var tusd_watch = setInterval(() => {
        if(document.getElementById("consumable_tusd").children.length == 0){
            consumables.forEach(cons => {
                var opt = document.createElement("option")
                opt.value = cons.id
                opt.innerText = cons.description
                consumable_tusd.appendChild(opt)
            })
            reloadtusd()
            clearInterval(tusd_watch)
        }else{
            clearInterval(tusd_watch)
        }
    }, 1000);

    show_sdot.addEventListener("change", e => {
        if(show_sdot.value == "Daily"){
            !select_date_sdot.classList.contains("wd-240") ? select_date_sdot.classList.add("wd-240") : null
            select_date_sdot.classList.contains("wd-160") ? select_date_sdot.classList.remove("wd-160") : null
            months_sdot.hidden = false
            to_dash.hidden = true
            to_years_sdot.hidden = true
            insertDaily(years_sdot.value,months_sdot.value)
        }else if(show_sdot.value == "Monthly"){
            !select_date_sdot.classList.contains("wd-160") ? select_date_sdot.classList.add("wd-160") : null
            select_date_sdot.classList.contains("wd-240") ? select_date_sdot.classList.remove("wd-240") : null
            months_sdot.hidden = true
            to_dash.hidden = true
            to_years_sdot.hidden = true
            insertMonthly(years_sdot.value)
        }else if(show_sdot.value == "Yearly"){
            // select_date_sdot.hidden = true
            !select_date_sdot.classList.contains("wd-240") ? select_date_sdot.classList.add("wd-240") : null
            select_date_sdot.classList.contains("wd-160") ? select_date_sdot.classList.remove("wd-160") : null
            months_sdot.hidden = true
            to_dash.hidden = false
            to_years_sdot.hidden = false
            insertYearly(years_sdot.value,to_years_sdot.value)
        }else{
            bs5.toast("warning","Invalid Selection.")
        }
    })





    function clearAllData(chart_) {
        chart_.data.datasets = [];
        chart_.data.labels = [];
        chart_.data.datasets.forEach(dataset => {
            dataset.data = [];
        });
        chart_.update();
    }

}
