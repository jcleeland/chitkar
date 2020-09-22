<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/siteindex.js');

$basedir=Yii::app()->basePath;
$dbfail=$basedir."/../tmp/dbfailure.ctk";

if(!isset($thismonth)) $thismonth=array();
if(!isset($thisweek)) $thisweek=array();
if(!isset($statistics)) $thisweek=array();

if(file_exists($dbfail) && !Yii::app()->user->isGuest) {
    ?>
    <div style='text-align: center; '>
        <span style='color: red; font-size: 14pt'>--== Email Distribution Currently Suspended ==--</span><br />
        Visit <?php echo CHtml::link('Admin', array('site/admin')) ?> to view reason and restart.
    </div>
    <?php
}
if(!Yii::app()->user->isGuest) {
?>
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
                echo CHtml::link($top->users->firstname." ".$top->users->lastname, array('newsletters/index', 'userId'=>$top->usersId));
                echo ": ".$top->countNewsletters;
                echo "</p>\n";
            }
        ?>
        </div>
        <h1 style='margin-top: 8px'>Last 7 days</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
            <?php
                foreach($topwchitter as $top) {
                    echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ";
                    echo CHtml::link($top->users->firstname." ".$top->users->lastname, array('newsletters/index', 'userId'=>$top->usersId));
                    echo ": ".$top->countNewsletters;
                    echo "</p>\n";
                }        
            ?>
        </div>
        <h1 style='margin-top: 8px'>Of all time</h1>
        <div style='margin-left: 10px; font-size: 8pt'>
            <?php
                foreach($topfchitter as $top) {
                    echo "<p style='margin-bottom: 8px; text-indent: -8px'>&#187; ";
                    echo CHtml::link($top->users->firstname." ".$top->users->lastname, array('newsletters/index', 'userId'=>$top->usersId));
                    echo ": ".$top->countNewsletters;
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
<div class="gbox-title" style="width: 80%; float: left">
	<h1>Metrics</h1>
	<div class="gbox" style='background-color: white'>

        <div id="hourschart_div" style="width: 850px; height: 150px; margin-left: auto; margin-right: auto; cursor: pointer" onClick="$('#summarychart_div').slideToggle('slow')"></div>
        <div id="summarychart_div" style="width: 850px; height: <?php echo count($readsummary)*15+35 ?>px; margin-left: auto; margin-right: auto; background-color: white; display: none; position: absolute; z-index: 9999"></div>
        <div id="chart_div" style="width: 850px; height: 200px; margin-left: auto; margin-right: auto;"></div>
        <div id="chart_stats"  style='width: 880px; margin-left: auto; margin-right: auto;'>
            <div class="gbox-float-title">
                <h3>Newsletters</h3>
                <?php 
                //Only show all of this if there is actually something to show
                if(count($statistics) > 0) { 
                ?>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td><?php if(!empty($statistics[date("Y-m-d")]['newsletters'])) echo $statistics[date("Y-m-d")]['newsletters']; ?></td>
                        </tr>
                        <tr>
                            <td>This Week : </td>
                            <td><?php if(!empty($thisweek['newsletters'])) echo $thisweek['newsletters']; ?></td>
                        </tr>
                        <tr>
                            <td>This Month : </td>
                            <td><?php if(!empty($thismonth['newsletters'])) echo $thismonth['newsletters']; ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td><?php if(!empty($forever['newsletters'])) echo $forever['newsletters']; ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recent Newsletters:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php 
                            if(!empty($recentnews)) {
                                foreach($recentnews as $rn) {
                                    echo "<p>".CHtml::link($rn->title, array('newsletters/view', 'id'=>$rn->id)). " [".date("H:i, d M", strtotime($rn->created))."]</p>\n";
                                }
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php 
                }
                ?>
            </div>

            <div class="gbox-float-title" id="queuedbox">
                <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton3')); ?>
                <h3>Emails Queued</h3>
                <?php
                    //Only show all of this if there is something to show
                    if(count($statistics) > 0) {
                ?>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td><?php if(!empty($statistics[date("Y-m-d")]['queued'])) echo $statistics[date("Y-m-d")]['queued']; ?></td>
                        </tr>
                        <tr>
                            <td>This Week :</td>
                            <td><?php if(!empty($thisweek['queued'])) echo $thisweek['queued']; ?></td>
                        </tr>
                        <tr>
                            <td>This Month :</td>
                            <td><?php if(!empty($thismonth['queued'])) echo $thismonth['queued']; ?></td>
                        </tr>
                        <tr>
                            <td>Total : </td>
                            <td><?php if(!empty($forever['queued'])) echo $forever['queued']; ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Published:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            
                            <?php 
                            if(!empty($recentpub)) {
                                foreach($recentpub as $rn) {
                                    echo "<p>".CHtml::link($rn->title, array('newsletters/view', 'id'=>$rn->id)). " [".date("H:i, d M", strtotime($rn->sendDate))."]</p>\n";
                                }
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                    }
                ?>
            </div>
            
            <div class="gbox-float-title" id="sentbox" style='display: none'>
                    <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton4')); ?>
                    <h3>Emails Sent</h3>
                <?php
                    //Only show all of this if there is something to show
                    if(count($statistics) > 0) {
                ?>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td><?php if(!empty($statistics[date("Y-m-d")]['sent'])) echo $statistics[date("Y-m-d")]['sent']; ?></td>
                        </tr>
                        <tr>
                            <td>This Week :</td>
                            <td><?php if(!empty($thisweek['sent'])) echo $thisweek['sent']; ?></td>
                        </tr>
                        <tr>
                            <td>This Month :</td>
                            <td><?php if(!empty($thismonth['sent'])) echo $thismonth['sent']; ?></td>
                        </tr>
                        <tr>
                            <td>Total : </td>
                            <td><?php if(!empty($forever['sent'])) echo $forever['sent']; ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Published:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            
                            <?php 
                            if(!empty($recentpub)) {
                                foreach($recentpub as $rn) {
                                    echo "<p>".CHtml::link($rn->title, array('newsletters/view', 'id'=>$rn->id)). " [".date("H:i, d M", strtotime($rn->sendDate))."]</p>\n";
                                }
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                    }
                ?>
            </div>

            <div class="gbox-float-title" id='readsbox'>
                <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton1')); ?><h3>Reads</h3>
                <?php
                    //Only show all of this if there is something to show
                    if(count($statistics) > 0) {
                ?>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today : </td>
                            <td><?php if(!empty($statistics[date("Y-m-d")]['read'])) echo $statistics[date("Y-m-d")]['read']; ?></td>
                        </tr>
                        <tr>
                            <td>This Week : </td>
                            <td><?php if(!empty($thisweek['read'])) echo $thisweek['read'] ?></td>
                        </tr>
                        <tr>
                            <td>This Month : </td>
                            <td><?php if(!empty($thismonth['read'])) echo $thismonth['read']; ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td><?php if(!empty($forever['read'])) echo $forever['read']; ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Read:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php 
                            if(!empty($recentread)) {
                                foreach($recentread as $rn) {
                                    echo "<p>".CHtml::link($rn->newsletters->title, array('newsletters/view', 'id'=>$rn->newslettersId)). " [".date("H:i, d M", strtotime($rn->readTime))."]</p>\n";
                                }
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <?php
                    }
                ?>
            </div>        

            <div class="gbox-float-title" style='display: none' id='linksbox'>
                <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton2')); ?><h3>Links</h3>
                <?php
                    //Only show all of this if there is something to show
                    if(count($statistics) > 0) {
                ?>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today : </td>
                            <td><?php if(!empty($statistics[date("Y-m-d")]['linkused'])) echo $statistics[date("Y-m-d")]['linkused']; ?></td>
                        </tr>
                        <tr>
                            <td>This Week : </td>
                            <td><?php if(!empty($thisweek['linkused'])) echo $thisweek['linkused']; ?></td>
                        </tr>
                        <tr>
                            <td>This Month : </td>
                            <td><?php if(!empty($thismonth['linkused'])) echo $thismonth['linkused']; ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td><?php if(!empty($forever['linkused'])) echo $forever['linkused']; ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Clicked:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php 
                            if(!empty($recentlick)) {
                                foreach($recentclick as $rn) {
                                    echo "<p>".CHtml::link($rn->newsletters->title, array('newsletters/view', 'id'=>$rn->newslettersId)). " [".date("H:i, d M", strtotime($rn->linkUsedTime))."]</p>\n";
                                }
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <?php
                    }
                ?>
            </div> 
            
        </div>
                         
    </div><!--.gbox-->
</div>
<!-- SELECT title, count(*) as count 
FROM outgoings, newsletters 
WHERE outgoings.newslettersId=newsletters.id 
AND readTime >='<?php echo date("Y-m-d H:i:s", $summarysince) ?>'
GROUP BY title ORDER BY count DESC  -->

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
                    echo "['".$rs['title']."', ".$rs['count']."],\n";
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

