<?php
/* @var $this SiteController */
$thetimer[12]=microtime();
$this->pageTitle=Yii::app()->name;
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/siteindex.js');

$basedir=Yii::app()->basePath;
$dbfail=$basedir."/../tmp/dbfailure.ctk";

if(file_exists($dbfail) && !Yii::app()->user->isGuest) {
    ?>
    <div style='text-align: center; '>
        <span style='color: red; font-size: 14pt'>--== Email Distribution Currently Suspended ==--</span><br />
        Visit <?php echo CHtml::link('Admin', array('site/admin')) ?> to view reason and restart.
    </div>
    <?php
}



if(!Yii::app()->user->isGuest) {
    $thetimer[13]=microtime();
    flush(); ob_flush();
    ?>
<!-- LEFT COLUMN -->
<div style="width: 18%; float: left; margin-right: 15px">

  <div class="gbox-title">
    <h1>My Chitkar</h1>
    <div class="gbox" style='background-color: white'>
        <p>
            I have <?php echo count($userpending); ?> <?php echo CHtml::link('newsletters', array('newsletters/index', 'userId'=>Yii::app()->user->id)) ?> pending.
        </p>
        <p>I've sent out <?php echo count($usertoday); ?> <?php echo CHtml::link('newsletters', array('newsletters/index', 'userId'=>Yii::app()->user->id)) ?> today.</p>
    <h1 style='margin-top: 8px'>My Recent</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
        <?php
        foreach($userlast10 as $userlast) {
            echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ".CHtml::link($userlast->title, array('newsletters/view', 'id'=>$userlast->id))." ".date("dS M", strtotime($userlast->sendDate))." (".$userlast->recipientCount."&nbsp;recips)</p>\n";
        }    
        
        ?>
        </div>
    </div>
  </div>
  <br />
  <div class="gbox-title">
    <h1>Top Chitkarers</h1>
    <div class='gbox' style='background-color: white'>
        <h1 style='margin-top: 8px'>Today</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
            <?php
                foreach($topchitter as $top) {
                    echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ";
                    echo CHtml::link($top['firstname']." ".$top['lastname'], array('newsletters/index', 'userId'=>$top['id']));
                    echo ": ".$top['countNewsletters'];
                    echo "</p>\n";
                }
            ?>
        </div>
        <h1 style='margin-top: 8px'>Last 7 days</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
            <?php
                foreach($topwchitter as $top) {
                    echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ";
                    echo CHtml::link($top['firstname']." ".$top['lastname'], array('newsletters/index', 'userId'=>$top['id']));
                    echo ": ".$top['countNewsletters'];
                    echo "</p>\n";
                }        
            ?>
        </div>
        <h1 style='margin-top: 8px'>Of all time</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
            <?php
                foreach($topfchitter as $top) {
                    echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ";
                    //print_r($top);
					echo CHtml::link($top['firstname']." ".$top['lastname'], array('newsletters/index', 'userId'=>$top['id']));
                    echo ": ".$top['countNewsletters'];
                    echo "</p>\n";
                }        
            ?>
        </div>
    </div>
    
  </div>
</div>
<?php } else {
    echo "<div style='width: 9%; float: left; margin-right: 15px'>&nbsp;</div>";
} ?>

<!-- RIGHT COLUMN -->
<div class="gbox-title" style="width: 80%; float: left">
	<h1>Metrics</h1>
	<div class="gbox" style='background-color: white'>

        <!-- TODAY CHART -->
        <div id="hourschart_div" style="width: 850px; height: 150px; margin-left: auto; margin-right: auto; cursor: pointer" onClick="$('#summarychart_div').slideToggle('slow')"></div>
        <!-- THIS WEEK CHART -->
        <div id="summarychart_div" style="width: 850px; height: <?php echo count($readsummary)*15+35 ?>px; margin-left: auto; margin-right: auto; background-color: white; display: none; position: absolute; z-index: 9999"></div>
        <div id="chart_div" style="width: 850px; height: 200px; margin-left: auto; margin-right: auto;"></div>



        <!-- STATISTICAL SUMMARIES -->

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        <div id="chart_stats" style='width: 880px; margin-left: auto; margin-right: auto;'>

    <script type='text/javascript'>
        var xhrPool = [];
        $(document).ready(function() {
            //var boxes = ['newsletterbox', 'queuedbox', 'sentbox', 'readsbox', 'linksbox'];
            var boxes=['newsletterbox', 'sentbox', 'readsbox', 'linksbox'];
            boxes.forEach(function(box) {
                var xhr = $.ajax({
                    url: '<?php echo Yii::app()->createUrl("site/statsdisplay"); ?>',
                    data: {subset: box},
                    success: function(data) {
                        $('#' + box).html(data);
                    }
                });

                xhrPool.push(xhr);
            });

            //$('a').click(function() {
            $(document).on('click', 'a', function() {
                console.log('Aborting XHR');
                xhrPool.forEach(function(xhr) {
                    console.log('Aborting ');
                    console.log(xhr);
                    xhr.abort();
                });
            });
        });
    </script>

    <!-- NEWSLETTERS -->
    <div class="gbox-float-title" id="newsletterbox" style='height: 285px'>
        <h3>Newsletters</h3>
        <img src='<?php echo $baseUrl ?>/images/ajax-loader-ongreen.gif' title='Loading...' style='margin-top: 100px' />
    </div>

    <!-- QUEUED -->
<!--    <div class="gbox-float-title" id="queuedbox" style='display: none; height: 285px'>
        <h3>Queued</h3>
        <img src='<?php echo $baseUrl ?>/images/ajax-loader-ongreen.gif' title='Loading...' style='margin-top: 100px' />
    </div> -->
    
    <!-- SENT -->
    <div class="gbox-float-title" id="sentbox" style='display: ; height: 285px'>
        <h3>Sent</h3>
        <img src='<?php echo $baseUrl ?>/images/ajax-loader-ongreen.gif' title='Loading...' style='margin-top: 100px' />
    </div>

    <!-- READS -->
    <div class="gbox-float-title" id='readsbox' style='height: 285px'>
        <h3>Reads</h3>
        <img src='<?php echo $baseUrl ?>/images/ajax-loader-ongreen.gif' title='Loading...' style='margin-top: 100px' />
    </div>        

    <!-- LINKS -->
    <div class="gbox-float-title" style='display: none' id='linksbox' style='height: 285px'>
        <h3>Links</h3>
        <img src='<?php echo $baseUrl ?>/images/ajax-loader-ongreen.gif' title='Loading...' style='margin-top: 100px' />
    </div> 
</div>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
                         
    </div><!--.gbox-->
</div>

<!-- SELECT title, count(*) as count 
FROM outgoings, newsletters 
WHERE outgoings.newslettersId=newsletters.id 
AND readTime >='<?php echo date("Y-m-d H:i:s", $summarysince) ?>'
GROUP BY title ORDER BY count DESC  -->
    <?php $thetimer[14]=microtime(); ?>
    <!-- TIMINGS: \r\n
    <?php 
        $delay=0;
        $lasttime=0;
        foreach($thetimer as $key=>$thetime) {
            list($microseconds, $seconds) = explode(' ', $thetime);
            if($lasttime==0) {echo "ENTRY $key: START\r\n";} else {echo "ENTRY $key: ".($seconds-$lasttime)."\r\n";}
            $lasttime=$seconds;
            //echo "ENTRY $key - Seconds: ".$seconds.", Microseconds: ".$microseconds."\r\n";
        }
    ?>
    -->
        <!--.gbox-title-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawSummaryChart);
          google.setOnLoadCallback(drawChart);
          google.setOnLoadCallback(drawHoursChart);
          
          function drawSummaryChart() {
              var data=google.visualization.arrayToDataTable([
                ['Newsletter', 'Reads'],
                <?php
                foreach($readsummary as $rs) {
                    echo "['".htmlentities($rs['title'], ENT_QUOTES)."', ".$rs['count']."],\n";
                }
                ?>
              ]);
              
              var options = {
                  width: 850,
                  backgroundColor: 'white',
                  title: 'Newsletters being read since <?php echo date("ga", $summarysince) ?>',
                  vAxis: {textStyle: {fontSize: 9}},
                  chartArea: {left: '50%', top:20, width: '49%', height: '80%'},
                  legend: 'none',
                  colors: ['red'],
                  bar: {groupWidth: '95%'},
              };
              
              var chart= new google.visualization.BarChart(document.getElementById('summarychart_div'));
              chart.draw(data, options);
          }
          
          function drawChart() {
            var data = google.visualization.arrayToDataTable([

              ['Date', 'Emails', 'Reads', 'Links'],
<?php
    foreach($statistics as $key=>$stat) {
        echo "['".date("jS M", strtotime($key))."', ".$stat['sent'].", ".$stat['read'].", ".$stat['linkused']."],\n";
    }
?>
            ]);
        
            var options = {
                /* hAxis: {title: 'Date'}, */
                title: 'This Week',
                width: 850,
                legend: {position: 'bottom'},
                vAxis: {title: 'Quantity', viewWindow: {min:0}},
                fontColor: 'white',
                backgroundColor: 'none',
                curveType: 'function',
                pointSize: 6
            };
        
            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);

          }
          function drawHoursChart() {
            var data = google.visualization.arrayToDataTable([

              ['Time', 'Emails', 'Reads', 'Links'],
<?php
    foreach($hours as $key=>$stat) {
        echo "['".$stat['legend']."', ".$stat['sent'].", ".$stat['read'].", ".$stat['linkused']."],\n";
    }
?>
            ]);
        
            var options = {
                /* hAxis: {title: 'Date'}, */
                title: 'Today',
                width: 850,
                legend: 'none',
                vAxis: {title: 'Quantity', viewWindow: {min:0}},
                fontColor: 'white',
                backgroundColor: 'none',
                curveType: 'function',
                pointSize: 6
            };
        
            var chart = new google.visualization.LineChart(document.getElementById('hourschart_div'));
        chart.draw(data, options);

          }

        
        //Reload page after 10 minutes
        var loadtime=new Date().getTime();
        function reloadPage() {
            if(new Date().getTime() - loadtime > 300000)
                window.location.reload(true);
            else
                setTimeout(reloadPage, 10000);
        }
        setTimeout(reloadPage, 10000);
        </script>