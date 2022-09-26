<?php

$current_user_id = $_SESSION['user_id'];
$report_status = 'status--no-reporting';

$on_reporters = $report['on_reporters'];



if ($on_reporters == null || $on_reporters == '') {
    $report_status = 'status--no-reporting';
} else {
    if (strpos($on_reporters, $current_user_id) === false) {
        $report_status = 'status--no-reporting';
    } else {
        $report_status = 'status--reporting';
    }
}


$status = ($report['status'] == null || $report['status'] == '' ||  $report_status == 'status--no-reporting') ? 'no' : $report['status'];
$status_str = getStatusString($report['status']);



?>
<div class="report-list-row status--<?php echo $status ?> <?php echo $report_status ?>" report-id="<?php echo $report['id'] ?>">
    <div class="report-list-col-action">
        <div class="report-list-action-btn">
            <i class="material-icons">more_vert</i>
        </div>
        <div class="report-list-action-popup">
            <div class="report-list-action-popup-btn report-list-action-popup-btn--reporting">
                <i class="material-icons">check_circle</i>
                <span class="report-list-action-popup-btn__text start">Start Reporting</span>
                <span class="report-list-action-popup-btn__text stop">Stop Reporting</span>
            </div>
            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['user_id'] == $report['user_id']) { ?>
                <div class="report-list-action-popup-btn report-list-action-popup-btn--duplicate">
                    <i class="material-icons">file_copy</i>
                    <span class="report-list-action-popup-btn__text">Duplicate</span>
                </div>
                <div class="report-list-action-popup-btn report-list-action-popup-btn--delete">
                    <i class="material-icons">delete</i>
                    <span class="report-list-action-popup-btn__text">Delete</span>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="report-list-col-status">
        <div class="report-list-col-status-wrap">
            <div>
                <div class="report-list-col-status-wrap__title"><?php echo $status_str['title'] ?></div>
                <div class="report-list-col-status-wrap__date"><?php echo $status_str['date'] ?></div>
            </div>
        </div>
    </div>
    <div class="report-list-col-info">
        <div class="report-list-col-info-wrap">
            <div class="report-list-col-info-row">
                <div class="report-list-col-info-col-50">
                    <div class="report-list-info-col-wrap">
                        <div class="report-list-info-input-wrap">
                            <input type="text" class="report-list-info-input" name="title" value="<?php echo $report['title'] ?>" disabled1>
                            <i class="material-icons report-list-info-edit-btn">edit</i>
                        </div>
                        <div class="report-list-info-title">Author: <?php echo $_SESSION['username']; ?></div>
                    </div>
                </div>
                <div class="report-list-col-info-col-50">
                    <div class="report-list-info-col-wrap fs-small">
                        <div class="report-list-info-title">Search terms</div>
                        <div class="report-list-info-input-wrap">
                            <input type="text" class="report-list-info-input" name="terms" value="<?php echo $report['terms'] ?>" disabled1>
                            <i class="material-icons report-list-info-edit-btn">edit</i>
                        </div>
                    </div>
                </div>

            </div>
            <div class="report-list-col-info-row">
                <div class="report-list-col-info-col-50">
                    <div class="report-list-col-info-col-50">
                        <div class="report-list-info-col-wrap fs-small">
                            <div class="report-list-info-input-wrap">
                                <select class="report-list-info-input" name="study" disabled1>
                                    <?php
                                    if (isset($studies)) {
                                        foreach ($studies as $study) {
                                    ?>
                                            <option value="<?php echo $study['value'] ?>" <?php echo $report['study'] == $study['value'] ? 'selected' : '' ?>><?php echo $study['text'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <i class="material-icons report-list-info-edit-btn">edit</i>
                            </div>
                        </div>
                    </div>
                    <div class="report-list-col-info-col-50">
                        <div class="report-list-info-col-wrap fs-small">
                            <div class="report-list-info-input-wrap">
                                <select class="report-list-info-input" name="country" disabled1>
                                    <?php
                                    if (isset($countries)) {
                                        foreach ($countries as $country) {
                                    ?>
                                            <option value="<?php echo $country['value'] ?>" <?php echo $report['country'] == $country['value'] ? 'selected' : '' ?>><?php echo $country['text'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <i class="material-icons report-list-info-edit-btn">edit</i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-list-col-info-col-50">
                    <div class="report-list-info-col-wrap fs-small">
                        <div class="report-list-info-title">Condition or disease</div>
                        <div class="report-list-info-input-wrap">
                            <input type="text" class="report-list-info-input" name="conditions" value="<?php echo $report['conditions'] ?>" disabled1>
                            <i class="material-icons report-list-info-edit-btn">edit</i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="report-list-col-btn report-week-list-col-btn">
        <div class="show-line-btn">
            Time Series
            <div class="show_line_wrap">

                <img src="<?= base_url() ?>assets/img/line.png" width="18" height="18" class="show_line_btn">
                <div class="download_list_report_btn"><i class="material-icons">file_download</i></div>
            </div>

        </div>
    </div>

    <div class="report-list-col-btn">
        <div class="report-list-download-btn">
            Download PDF
            <div class="report-list-download-btn__icon-wrap"><i class="material-icons">file_download</i></div>
        </div>
    </div>
    <div class="report-list-row-loading">
        <div class="loader"></div>
    </div>
</div>