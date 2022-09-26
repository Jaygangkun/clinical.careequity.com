const ALERT_SUCCESS = 'success';
const ALERT_FAIL = 'fail';
const ALERT_NORMAL = 'normal';

$(document).ready(function () {

    // dashboard page
    $(document).on('click', '.report-list-row', function () {
        $('.report-list-row').removeClass('active');
        $(this).addClass('active');
    })

    // report dropdown menu hide
    document.addEventListener('click', e => {
        let clickedOutside = true;

        e.path.forEach(item => {
            if (!clickedOutside)
                return;

            if (item.className === 'report-list-action-popup' || item.className === 'report-list-action-btn')
                clickedOutside = false;
        });

        if (clickedOutside) {
            // Make an action if it's clicked outside..
            $('.report-list-row').removeClass('show-popup');
        }

    });

    // report dropdown show
    $(document).on('click', '.report-list-action-btn', function () {
        $('.report-list-row').removeClass('show-popup');
        $(this).parents('.report-list-row').addClass('show-popup');
    })

    // add button
    $(document).on('click', '#report_add_btn', function () {

        if ($('#title').val() == '') {
            showAlert("<div class='message-box'>Enter Report Title</div>");
            $('#title').focus();
            return;
        }


        if ($('#terms').val() == '') {
            showAlert("<div class='message-box'>Enter a Search Term (mandatory)</div>");
            $('#terms').focus();
            return;
        }

        $.ajax({
            url: base_url + 'admin_api/report_add',
            type: 'post',
            data: {
                'title': $('#title').val(),
                'conditions': $('#conditions').val(),
                'study': $('#study').val(),
                'country': $('#country').val(),
                'terms': $('#terms').val()
            },
            success: function (resp) {
                resp = JSON.parse(resp);
                if (resp.success) {
                    showAlert("<div class='message-box'><b>" + $('#title').val() + "</b> was successfully added to the list</div>", ALERT_SUCCESS);
                    $('#report_list').append(resp.report);
                    $('.report-add-area').removeClass('report-add-area--expand');

                    console.log("id", $('#report_list .report-list-row').last().attr("report-id"));
                    $('#report_list .report-list-row').last().find(".report-list-action-popup-btn--reporting").trigger("click");

                }
                else {
                    showAlert("<div class='message-box'><b>" + $('#title').val() + "</b> was failed to add to the list</div>", ALERT_FAIL);
                }

                $('#title').val("");
                $('#terms').val("");
                $('#conditions').val("");
                $('#study').val($("#study option:first").val());
                $('#country').val($("#country option:first").val());
            }
        })
    })

    $(document).on('click', '.report-list-info-edit-btn', function () {
        $(this).parent().find('input').removeAttr('disabled');
        $(this).parent().find('select').removeAttr('disabled');
    })

    function updateReport(report_id) {
        var report_row = $('.report-list-row[report-id="' + report_id + '"]');
        $(report_row).addClass('loading');
        $.ajax({
            url: base_url + 'admin_api/report_update',
            type: 'post',
            data: {
                'id': report_id,
                'title': $(report_row).find('[name="title"]').val(),
                'conditions': $(report_row).find('[name="conditions"]').val(),
                'study': $(report_row).find('[name="study"]').val(),
                'country': $(report_row).find('[name="country"]').val(),
                'terms': $(report_row).find('[name="terms"]').val()
            },
            success: function (resp) {
                resp = JSON.parse(resp);
                if (resp.success) {
                    showAlert("<div class='message-box'>Success</div>", ALERT_SUCCESS);
                    // updating status
                    $(report_row).removeClass('status--no');
                    $(report_row).removeClass('status--new');
                    $(report_row).removeClass('status--recent');
                    $(report_row).removeClass('status--old');
                    $(report_row).addClass("status--" + resp.status);

                    $(report_row).find('.report-list-col-status-wrap__title').text(resp.status_str.title);
                    $(report_row).find('.report-list-col-status-wrap__date').text(resp.status_str.date);
                }
                else {
                    showAlert("<div class='message-box'>Failed</div>", ALERT_FAIL);
                }

                $(report_row).removeClass('loading');
            }
        })
    }

    $(document).on('keypress', '.report-list-row input', function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            updateReport($(this).parents('.report-list-row').attr('report-id'));
        }
    })

    $(document).on('change', '.report-list-row select', function (event) {
        updateReport($(this).parents('.report-list-row').attr('report-id'));
    })

    // search input
    function searchReport(keyword, sort) {
        $.ajax({
            url: base_url + 'admin_api/report_search',
            type: 'post',
            data: {
                keyword: keyword,
                sort: sort
            },
            success: function (resp) {
                resp = JSON.parse(resp);
                if (resp.success) {
                    $('#report_list').html(resp.reports);
                }
                else {
                    $('#report_list').html('');
                }
            }
        })
    }

    $(document).on('keypress', '#report_search_input', function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            searchReport($(this).val(), $('#sort').val())
        }
    })

    // sort
    $(document).on('change', '#sort', function () {
        searchReport($('#report_search_input').val(), $(this).val());
    })

    // action popup buttons
    $(document).on('click', '.report-list-action-popup-btn--reporting', function () {
        var reporting = 1;
        var report_row = $(this).parents('.report-list-row');
        var report_id = $(this).parents('.report-list-row').attr('report-id');
        var instance = this;
        if ($(report_row).hasClass('status--no-reporting')) {
            // start reporting
            reporting = 1;
        }
        else if ($(report_row).hasClass('status--reporting')) {
            // stop reporting
            reporting = 0;
        }

        $(report_row).addClass('loading');
        $.ajax({
            url: base_url + 'admin_api/report_reporting',
            type: 'post',
            data: {
                'id': report_id,
                reporting: reporting
            },
            success: function (resp) {
                resp = JSON.parse(resp);
                if (resp.success) {

                    if ($(report_row).hasClass('status--no-reporting')) {
                        // start reporting
                        $(report_row).removeClass('status--no-reporting');
                        $(report_row).addClass('status--reporting');

                        showAlert("<div class='message-box'>Reporting Started!</div>", ALERT_SUCCESS);
                    }
                    else if ($(report_row).hasClass('status--reporting')) {
                        // stop reporting
                        $(report_row).removeClass('status--reporting');
                        $(report_row).addClass('status--no-reporting');

                        showAlert("<div class='message-box'>Reporting Stoped!</div>", ALERT_SUCCESS);
                    }

                    // updating status
                    $(report_row).removeClass('status--no');
                    $(report_row).removeClass('status--new');
                    $(report_row).removeClass('status--recent');
                    $(report_row).removeClass('status--old');
                    $(report_row).addClass("status--" + resp.status);
                    $(report_row).removeClass('show-popup');

                    $(report_row).find('.report-list-col-status-wrap__title').text(resp.status_str['title']);
                    $(report_row).find('.report-list-col-status-wrap__date').text(resp.status_str['date']);
                }

                $(report_row).removeClass('loading');
            }
        })
    })

    $(document).on('click', '.report-list-action-popup-btn--duplicate', function () {
        var report_row = $(this).parents('.report-list-row');
        var report_id = $(this).parents('.report-list-row').attr('report-id');

        $(report_row).addClass('loading');
        $.ajax({
            url: base_url + 'admin_api/report_duplicate',
            type: 'post',
            data: {
                'id': report_id,
            },
            success: function (resp) {
                resp = JSON.parse(resp);
                if (resp.success) {
                    showAlert("<div class='message-box'>Success</div>", ALERT_SUCCESS);
                    // var new_report = $('.report-list-row[report-id="' + report_id + '"]').clone();
                    // $(new_report).attr('report-id', resp.report_id);

                    // $(new_report).removeClass('active');
                    // // $(new_report).removeClass('new');
                    // // $(new_report).removeClass('recent');
                    // $(new_report).removeClass('show-popup');
                    // $(new_report).removeClass('loading');

                    $('.report-list-row[report-id="' + report_id + '"]').after(resp.report);
                    // $('#report_list').append(resp.report);

                }
                else {
                    showAlert("<div class='message-box'>Fail</div>", ALERT_FAIL);
                }
                $(report_row).removeClass('loading');
                $(report_row).removeClass("show-popup");
            }
        })
    })

    $(document).on('click', '.report-list-action-popup-btn--delete', function () {
        var instance = this;

        showConfirm({
            title: 'Are you sure to delete?',
            text: '',
            confirmButtonText: 'Yes, delete it!'
        }, function () {
            var report_row = $(instance).parents('.report-list-row');
            var report_id = $(instance).parents('.report-list-row').attr('report-id');

            $(report_row).addClass('loading');
            $.ajax({
                url: base_url + 'admin_api/report_delete',
                type: 'post',
                data: {
                    'id': report_id,
                },
                success: function (resp) {
                    resp = JSON.parse(resp);
                    $(report_row).removeClass('loading');
                    if (resp.success) {
                        swal("Deleted!", "The Report has been deleted.", "success");
                        $('.report-list-row[report-id="' + report_id + '"]').remove();
                    }
                    else {
                        swal("Failed to Delete!", "The Report has been deleted.", "fail");
                    }
                }
            })
        })
    })

    $(document).on('click', '.report-list-download-btn', function () {
        var report_id = $(this).parents('.report-list-row').attr('report-id');
        window.open(base_url + 'admin_api/rss_download?report_id=' + report_id);
    })

    $(document).on('click', '#title', function (event) {
        $(this).parents('.report-add-area').addClass('report-add-area--expand');
        event.stopPropagation();
        event.preventDefault();
        console.log('title');
    })

    $(document).on('click', '.report-add-area', function (event) {
        console.log('report-add-area:', event.target.className);
        if (event.target.className.indexOf('report-input') == -1 && event.target.className.indexOf('btn-main') == -1) {
            $(this).removeClass('report-add-area--expand');
        }
    })

    function showAlert(text, type = ALERT_NORMAL) {
        if ($('.sweet-overlay').length > 0) {
            $('.sweet-overlay').addClass('hide');
        }

        let customClass = 'custom-alert';

        if (type == ALERT_SUCCESS) {
            customClass = 'custom-alert success-alert';
        }
        else if (type == ALERT_FAIL) {
            customClass = 'custom-alert fail-alert';
        }

        swal({
            title: "",
            text: text,
            timer: 2000,
            html: true,
            showConfirmButton: false,
            animation: "slide-from-top",
            animation: "slide-from-top",
            toast: true,
            position: 'top',
            customClass: customClass,
            showCloseButton: true
        });
    }

    function showConfirm(config, cbConfirm) {
        if ($('.sweet-overlay').length > 0) {
            $('.sweet-overlay').removeClass('hide');
        }

        swal({
            title: config.title,
            text: config.text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: config.confirmButtonText,
            closeOnConfirm: false
        }, function () {
            cbConfirm();
        });
    }


    // users page
    $(document).on('click', '.btn-user-delete', function () {
        var instance = this;
        showConfirm({
            title: 'Are you sure to delete?',
            text: '',
            confirmButtonText: 'Yes, delete it!'
        }, function () {
            var user_id = $(instance).attr('user-id');

            $.ajax({
                url: base_url + 'admin_api/user_delete',
                type: 'post',
                data: {
                    'user_id': user_id,
                },
                success: function (resp) {
                    resp = JSON.parse(resp);
                    if (resp.success) {
                        swal("Deleted!", "The User has been deleted.", "success");
                    }
                    else {
                        swal("Failed to Delete!", "The Report has been deleted.", "fail");
                    }
                }
            })
        })
    })

    ///Line graphic






    $(document).on("click", ".show_line_btn", function () {


        var report_id = $(this).parents('.report-list-row').attr('report-id');
        console.log("report_id", report_id);


        //set report_id to datepikcer and datepicker csv download button
        $(".date_picker_wrap").attr("report-id", report_id);
        $(".report_date-downlaod").attr("report-id", report_id);

        $("#start_date").val("");
        $("#last_date").val("");


        $.ajax({
            url: base_url + 'admin_api/report_get_week_list',
            type: 'post',
            data: {
                'id': report_id,
            },
            success: function (resp) {
                resp = JSON.parse(resp);

                console.log("getting week_list, getting_week_report", resp.week_list, resp.week_reports);


                if (resp.week_list == null || resp.week_list == "") {

                    var week_val_obj = {};

                    $('.graph-songs').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 100,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#c3ecf7"
                    });

                } else {

                    var week_list = resp.week_list.split(",");
                    var week_reports = resp.week_reports.split(",").reverse();
                    var week_val = "";


                    var every_week = 0;
                    var every_week_val = 0;
                    var week_cumul_val = 0;

                    every_week = parseInt(week_list.length / 7);

                    for (var i = week_list.length - 1; i >= 0; i--) {

                        week_cumul_val += parseInt(week_reports[i]);
                        every_week_val += 1;

                        if (every_week_val == 8 || every_week == 1) {
                            if (week_val == "") {
                                if (every_week == 0)
                                    every_week = 1;
                                week_val = week_val + '"' + every_week + ' "' + ':' + week_cumul_val;
                            } else {
                                if (every_week == 0)
                                    every_week = 1;
                                week_val = week_val + ',' + '"' + every_week + ' "' + ':' + week_cumul_val;
                            }

                            every_week -= 1;
                            every_week_val = 1;
                            week_cumul_val = 0;
                        }


                        /*
                        if (week_val == "") {
                            week_val = week_val + '"' + week_list[i] + ' "' + ':' + week_reports[i];
                        } else {
                            week_val = week_val + ',' + '"' + week_list[i] + ' "' + ':' + week_reports[i];
                        }
                        */


                    }




                    //var week_val_obj =Object.assign(week_list, week_reports.reverse());
                    week_val = '{' + week_val + '}';
                    var week_val_obj = JSON.parse(week_val);







                    $('.graph-songs').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 200,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#000"
                    });



                    //total cumulative display




                    var week_list = resp.week_list.split(",");
                    var week_reports = resp.week_reports.split(",").reverse();
                    var week_val = "";


                    var every_week = 0;
                    var every_week_val = 0;
                    var week_cumul_val = 0;

                    every_week = parseInt(week_list.length / 7);


                    for (var i = week_list.length - 1; i >= 0; i--) {

                        week_cumul_val += parseInt(week_reports[i]);
                        every_week_val += 1;

                        if (every_week_val == 8 || every_week == 1) {
                            if (week_val == "") {
                                if (every_week == 0)
                                    every_week = 1;
                                week_val = week_val + '"' + every_week + ' "' + ':' + week_cumul_val;
                            } else {
                                if (every_week == 0)
                                    every_week = 1;
                                week_val = week_val + ',' + '"' + every_week + ' "' + ':' + week_cumul_val;
                            }

                            every_week -= 1;
                            every_week_val = 1;
                            // week_cumul_val = 0;
                        }
                    }




                    //var week_val_obj =Object.assign(week_list, week_reports.reverse());
                    week_val = '{' + week_val + '}';
                    var week_val_obj = JSON.parse(week_val);





                    $('.graph-songs_total').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 200,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#000"
                    });

                    /*
                    var week_list = resp.week_list.split(",");
                    var week_reports = resp.week_reports.split(",").reverse();
                    var week_val = "";
                    for (var i = week_list.length - 2; i >= 0; i--) {
                        if (week_val == "") {
                            week_val = week_val + '"' + week_list[i] + ' "' + ':' + week_reports[i];
                        } else {
                            week_val = week_val + ',' + '"' + week_list[i] + ' "' + ':' + week_reports[i];
                        }



                    }


                    console.log("total", week_val);

                    //var week_val_obj =Object.assign(week_list, week_reports.reverse());
                    week_val = '{' + week_val + '}';
                    var week_val_obj = JSON.parse(week_val);

                    console.log("week_val_ojb", week_val_obj);

                    $('.graph-songs').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 200,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#000"
                    });
                    */
                }

                var title = resp.title;
                $(".modal-title").text(title);

            }
        })


        //remove
        $(".graph-songs").empty();

        $(".graph-songs_total").empty();



        $(".show_modal_btn").trigger("click");
    });

    //Get all date from two date
    function getDates(startDate, endDate) {
        const dates = []
        let currentDate = startDate
        const addDays = function (days) {
            const date = new Date(this.valueOf())
            date.setDate(date.getDate() + days)
            return date
        }
        while (currentDate <= endDate) {
            dates.push(currentDate.toLocaleDateString('en-US'))
            currentDate = addDays.call(currentDate, 1)
        }
        return dates
    }


    // format table
    function set_date_to_table(start_date_num, last_date_num, start_date, last_date, report_id) {

        console.log("here_formart-table", start_date_num, last_date_num, start_date, last_date, report_id)

        const dates = getDates(new Date(start_date), new Date(last_date))
        var selected_date_length = dates.length;
        /*
        dates.forEach(function (date) {
            console.log("date", date, selected_date_length)
    
        })
        */


        $.ajax({
            url: base_url + 'admin_api/report_get_week_list',
            type: 'post',
            data: {
                'id': report_id,
            },
            success: function (resp) {
                resp = JSON.parse(resp);




                if (resp.week_list == null || resp.week_list == "") {

                    var week_val_obj = {};

                    $('.graph-songs').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 100,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#c3ecf7"
                    });

                } else {

                    var week_list = resp.week_list.split(",");
                    var week_reports = resp.week_reports.split(",").reverse();
                    var selected_dates = dates.reverse();

                    const date_reports = week_reports.slice(last_date_num, start_date_num + 1);



                    var week_val = "";
                    for (var i = selected_date_length - 1; i >= 0; i--) {
                        if (week_val == "") {
                            week_val = week_val + '"' + selected_dates[i] + ' "' + ':' + date_reports[i];
                        } else {
                            week_val = week_val + ',' + '"' + selected_dates[i] + ' "' + ':' + date_reports[i];
                        }



                    }




                    //var week_val_obj =Object.assign(week_list, week_reports.reverse());
                    week_val = '{' + week_val + '}';
                    var week_val_obj = JSON.parse(week_val);



                    $('.graph-songs').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 200,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#000"
                    });


                    //total cumulative display




                    var week_list = resp.week_list.split(",");
                    var week_reports = resp.week_reports.split(",").reverse();





                    var week_val = "";
                    var toal_reports = 0;
                    for (var i = selected_date_length - 1; i >= 0; i--) {
                        if (week_val == "") {
                            week_val = week_val + '"' + selected_dates[i] + ' "' + ':' + date_reports[i];
                            toal_reports += parseInt(date_reports[i]);
                        } else {
                            toal_reports += parseInt(date_reports[i]);
                            week_val = week_val + ',' + '"' + selected_dates[i] + ' "' + ':' + toal_reports;
                        }



                    }





                    //var week_val_obj =Object.assign(week_list, week_reports.reverse());
                    week_val = '{' + week_val + '}';
                    var week_val_obj = JSON.parse(week_val);





                    $('.graph-songs_total').graphiq({
                        data: week_val_obj,
                        fluidParent: ".col",
                        height: 200,
                        xLineCount: 10,
                        dotRadius: 4,
                        yLines: true,
                        xLines: true,
                        dots: true,
                        fillOpacity: 0.5,
                        fill: true,
                        colorUnits: "#000"
                    });



                }

                var title = resp.title;
                $(".modal-title").text(title);

            }
        })


        //remove
        $(".graph-songs").empty();
        $(".graph-songs_total").empty();





    }



    //date pikcer
    $("#start_date").datepicker({
        dateFormat: 'mm / dd / yy',
        maxDate: 0,
        onSelect: function (dateText) {

            var selected_date = new Date(this.value);

            var today = new Date();

            // To calculate the time difference of two dates
            var Difference_In_Time = today.getTime() - selected_date.getTime();

            // To calculate the no. of days between two dates
            var start_difference_in_days = parseInt(Difference_In_Time / (1000 * 3600 * 24));



            var last_difference_in_day = $("#last_date").val();
            if (last_difference_in_day == "") {
                $('#last_date').datepicker("setDate", new Date(today));
                last_difference_in_day = $("#last_date").val();
            }


            if (last_difference_in_day == "") {
                var selected_last_date = new Date();
                // To calculate the time difference of two dates
                var Last_Difference_In_Time = today.getTime() - selected_last_date.getTime();

                // To calculate the no. of days between two dates
                var last_difference_in_days = 0;
            } else {
                var selected_last_date = new Date(last_difference_in_day);
                // To calculate the time difference of two dates
                var Last_Difference_In_Time = today.getTime() - selected_last_date.getTime();

                // To calculate the no. of days between two dates
                var last_difference_in_days = parseInt(Last_Difference_In_Time / (1000 * 3600 * 24));
            }


            $('#last_date').datepicker("option", "minDate", selected_date);


            var report_id = $(".date_picker_wrap").attr("report-id");


            set_date_to_table(start_difference_in_days, last_difference_in_days, selected_date, selected_last_date, report_id);
        }

    });


    $("#last_date").datepicker(
        {
            dateFormat: 'mm / dd / yy',
            maxDate: 0,
            onSelect: function (dateText) {

                var selected_date = new Date(this.value);

                var today = new Date();

                // To calculate the time difference of two dates
                var Difference_In_Time = today.getTime() - selected_date.getTime();

                // To calculate the no. of days between two dates
                var last_difference_in_days = parseInt(Difference_In_Time / (1000 * 3600 * 24));





                var last_difference_in_day = $("#start_date").val();

                if (last_difference_in_day == "") {
                    $('#start_date').datepicker("setDate", new Date(selected_date));
                    last_difference_in_day = $("#start_date").val();
                }



                if (last_difference_in_day == "") {
                    var selected_last_date = new Date();
                    // To calculate the time difference of two dates
                    var Last_Difference_In_Time = today.getTime() - selected_last_date.getTime();

                    // To calculate the no. of days between two dates
                    var start_difference_in_days = last_difference_in_days;

                } else {
                    var selected_last_date = new Date(last_difference_in_day);
                    // To calculate the time difference of two dates
                    var Last_Difference_In_Time = today.getTime() - selected_last_date.getTime();

                    // To calculate the no. of days between two dates
                    var start_difference_in_days = parseInt(Last_Difference_In_Time / (1000 * 3600 * 24));
                }



                $('#start_date').datepicker("option", "maxDate", selected_date);


                var report_id = $(".date_picker_wrap").attr("report-id");


                set_date_to_table(start_difference_in_days, last_difference_in_days, selected_last_date, selected_date, report_id);

            }
        }
    );


    //reset 30 days reports

    $(document).on("click", ".set_third_days_btn", function () {

        var today = new Date();
        var priorDate = new Date(new Date().setDate(today.getDate() - 30));

        $('#start_date').datepicker("setDate", new Date(priorDate));
        $('#last_date').datepicker("setDate", new Date(today));

        $('#start_date').datepicker("option", "maxDate", today);
        $('#last_date').datepicker("option", "maxDate", today);

        var report_id = $(".date_picker_wrap").attr("report-id");

        set_date_to_table(30, 0, priorDate, today, report_id);
    });



    //change toal and week
    $(document).on("click", ".change_display_cumulative", function () {
        $(".graph-songs_wrap").css("display", "none");
        $(".graph-songs_total_wrap").css("display", "block");

        if ($(".change_display_daily").hasClass('active')) {
            $(".change_display_daily").removeClass('active');
            $(".change_display_cumulative").addClass('active');
        }

    });

    $(document).on("click", ".change_display_daily", function () {
        $(".graph-songs_wrap").css("display", "block");
        $(".graph-songs_total_wrap").css("display", "none");

        if ($(".change_display_cumulative").hasClass('active')) {
            $(".change_display_cumulative").removeClass('active');
            $(".change_display_daily").addClass('active');
        }
    });



    //update popup


    $(document).on("click", ".report-list-col-status", function () {

        var report_id = $(this).parents('.report-list-row').attr('report-id');
        console.log("report_id", report_id);

        var name_title = $(this).parents('.report-list-row').find("input[name='title']").val();

        $(".popup-title").text(name_title);


        $.ajax({
            url: base_url + 'admin_api/popup_update',
            type: 'post',
            data: {
                'report_id': report_id,
            },
            success: function (resp) {
                resp = JSON.parse(resp);

                console.log(resp);

                var number = 1;

                var d = new Date();
                var today = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
                var getUrl = window.location.origin;
                var img_url = getUrl + "/assets/img/link_out.png";

                var tbody_string = "";
                for (const clinic of resp.clinics) {

                    tbody_string += "<tr><td class='number_td'>" + number + "</td><td>" + clinic.guid[0] + "</td><td class='title_td'>" + clinic.title[0] + "</td><td>" + clinic.description[0] + "</td><td>" + clinic.pubDate[0] + "</td>\
                    <td class='link_td'><a href='"+ clinic.link[0] + "' target='_blank'><img src='" + img_url + "'></a></td></tr>";

                    number += 1;
                }
                $(".popup_body").html(tbody_string);

                number = number - 1;

                if (number > 1) {
                    var update_numbers = number + " updates";
                } else {
                    var update_numbers = number + " update";
                }

                $(".popup_updates_count").text(update_numbers);

            }
        })

        $(".show_popup_btn").trigger("click");

    })





    // download report

    $(document).on("click", ".download_list_report_btn", function () {


        var report_id = $(this).parents('.report-list-row').attr('report-id');
        window.open(base_url + 'admin_api/download_csv?report_id=' + report_id, '_blank');
    })


    //download dates csv
    $(document).on("click", ".report_date-downlaod", function () {

        var report_id = $(this).attr('report-id');

        var start = $("#start_date").val();
        var last = $("#last_date").val();

        var start_date = new Date(start);
        var last_date = new Date(last);
        var today = new Date();

        // To calculate the time difference of two dates
        var start_Difference_In_Time = today.getTime() - start_date.getTime();

        // To calculate the no. of days between two dates
        var start_difference = parseInt(start_Difference_In_Time / (1000 * 3600 * 24));

        // To calculate the time difference of two dates
        var Last_Difference_In_Time = today.getTime() - last_date.getTime();

        // To calculate the no. of days between two dates
        var last_difference = parseInt(Last_Difference_In_Time / (1000 * 3600 * 24));

        var last_date_day = parseInt(last_date.getMonth() + 1) + "/" + last_date.getDate() + "/" + last_date.getFullYear();
        var start_date_day = parseInt(start_date.getMonth() + 1) + "/" + start_date.getDate() + "/" + start_date.getFullYear();

        if (start == "") {
            window.open(base_url + 'admin_api/download_csv?report_id=' + report_id, '_blank');
        } else {
            window.open(base_url + 'admin_api/download_dates_csv?report_id=' + report_id + '&start_date=' + start_difference + '&last_date=' + last_difference + '&last_date_day=' + last_date_day + '&start_date_day=' + start_date_day, '_blank');
        }

    })


})