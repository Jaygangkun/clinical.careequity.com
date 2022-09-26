<style>
    .table-list{
        font-size: 12px;
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed
    }

    .table-list table, 
    .table-list tr, 
    .table-list td, 
    .table-list th{
        border: 1px solid #DCDCDC;
        word-break: break-all;
        word-wrap: break-word;
    }

    .table-list td{
        padding: 0px 10px;
        vertical-align: top;
        padding-top: 10px;
    }

    .data-row{
        border-bottom: 1px solid #DCDCDC;
        padding-bottom: 5px;
        margin-bottom: 5px;
    }

    .data-row:last-of-type{
        border-bottom: 0px;
    }

    thead{
        background: #DFECDF;
    }

    ul{
        padding-left: 10px;
    }
</style>
<h4 style="text-align: center; margin-bottom: 10px;"><?php echo $title;?></h4>
<table class="table-list">
    <thead>
        <tr>
            <th style="width: 3%"></th>
            <th style="width: 5%">NCT Number</th>
            <th style="width: 7%">Title</th>
            <th style="width: 7%">Other Names</th>
            <th style="width: 7%">Status</th>
            <th style="width: 7%">Conditions</th>
            <th style="width: 21%">Interventions</th>
            <th style="width: 22%">Characteristics</th>
            <th style="width: 7%">Population</th>
            <th style="width: 7%">Sponsor/Collaborators</th>
            <!-- <th style="width: 6%">Funder Type</th> -->
            <th style="width: 7%">Dates</th>
            <!-- <th style="width: 7%">Locations</th> -->
        </tr>
    </thead>
    <tbody>
        <?php
        $clinic_index = 1;
        foreach($clinics as $clinic){
            ?>
            <tr>
                <td><?php echo $clinic_index?></td>
                <td><?php echo $clinic['nct_number']?></td>
                <td>
                    <div class="data-row">
                        <a href="<?php echo $clinic['link']?>"><?php echo $clinic['title']?></a>
                    </div>
                    <div class="data-row">
                        Study Documents:
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        Title Acronym:
                    </div>
                    <div class="data-row">
                        Other Ids: <?php echo $clinic['other_ids']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['status']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['conditions']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['interventions']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        Study Type: <?php echo $clinic['study_type']?>
                    </div>
                    <div class="data-row">
                        Phase: <?php echo $clinic['phase']?>
                    </div>
                    <div class="data-row">
                        Study Design: <?php echo $clinic['study_design']?>
                    </div>
                    <div class="data-row">
                        Primary Outcome Measures: <?php echo $clinic['primary_outcome_measures']?>
                    </div>
                    <div class="data-row">
                        Secondary Outcome Measures: <?php echo $clinic['secondary_outcome_measures']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        Actual Enrollment: <?php echo $clinic['number_enrolled_actual']?>
                    </div>
                    <div class="data-row">
                        Estimated Enrollment: <?php echo $clinic['number_enrolled_estimated']?>
                    </div>
                    <div class="data-row">
                        Original Estimated Enrollment: <?php echo $clinic['number_enrolled_original']?>
                    </div>
                    <div class="data-row">
                        Age: <?php echo $clinic['age']?>
                    </div>
                    <div class="data-row">
                        Sex: <?php echo $clinic['sex']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        Study Sponsors: <?php echo $clinic['sponsor']?>
                    </div>
                    <div class="data-row">
                        Collaborators: <?php echo $clinic['collaborators']?>
                    </div>
                </td>
                <!-- <td>
                    <div class="data-row">
                        <ul>
                            <li>Other</li>
                        </ul>
                    </div>
                </td> -->
                <td>
                    <div class="data-row">
                        Study Start: <?php echo $clinic['study_start']?>
                    </div>
                    <div class="data-row">
                        Primary Completion: <?php echo $clinic['primary_completion']?>
                    </div>
                    <div class="data-row">
                        Study Completion: <?php echo $clinic['study_completion']?>
                    </div>
                    <div class="data-row">
                        First Posted: <?php echo $clinic['first_posted']?>
                    </div>
                    <div class="data-row">
                        Results First Posted: <?php echo $clinic['results_first_posted']?>
                    </div>
                    <div class="data-row">
                        Last Update Posted: <?php echo $clinic['last_update_posted']?>
                    </div>
                </td>
                <!-- <td>
                </td> -->
            </tr>
            <?php
            $clinic_index++;
        }
        ?>
    </tbody>
</table>