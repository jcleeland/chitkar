<?php 
$baseUrl=Yii::app()->baseUrl; 
/**
* Required statistics
* 
* $baseUrl
* $statistics[date]['sent']
* $thisweek['sent']
* $thismonth['sent']
* $forever['sent']
* $recentpub
*/
?>
                    <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton4', 'onClick'=>'$("#sentbox").toggle();$("#queuedbox").fadeToggle("slow");', 'title'=>'View queued emails')); ?>
                    <h3>Emails Sent</h3>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td class='datatd'><?php echo number_format($statistics[date("Y-m-d")]['sent']); ?></td>
                        </tr>
                        <tr>
                            <td>This Week :</td>
                            <td class='datatd'><?php echo number_format($thisweek['sent']); ?></td>
                        </tr>
                        <tr>
                            <td>This Month :</td>
                            <td class='datatd'><?php echo number_format($thismonth['sent']); ?></td>
                        </tr>
                        <tr>
                            <td>Total : </td>
                            <td class='datatd'><?php echo number_format($forever['sent']); ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Published:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            
                            <?php foreach($recentpub as $rn) {
                                echo "<p><b>".date("H:i (d M)", strtotime($rn->sendDate))."</b><br />".CHtml::link($rn->title, array('newsletters/view', 'id'=>$rn->id)). "</p>\n";
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
