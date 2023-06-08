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
                    <h3>Emails Sent</h3>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td class='datatd'><?php echo number_format($statistics[date("Y-m-d")]['sent']); ?></td>
                        </tr>
                        <tr>
                            <td>Last 7 days :</td>
                            <td class='datatd'><?php echo number_format($thisweek['sent']); ?></td>
                        </tr>
                        <tr>
                            <td>Since 1st <?php echo date("M") ?> :</td>
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
