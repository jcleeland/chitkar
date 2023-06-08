<?php 
$baseUrl=Yii::app()->baseUrl; 

/**
* Required variables
* 
* $statistics[date]['linkused']
* $thisweek['linkused']
* $thismonth['linkused']
* $forever['linkused']
* $recentclick
*/
?>
               <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton2', 'onClick'=>'$("#linksbox").toggle();$("#readsbox").fadeToggle("slow");', 'title'=>'View reads')); ?><h3>Links</h3>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today : </td>
                            <td class='datatd'><?php echo number_format($statistics[date("Y-m-d")]['linkused']); ?></td>
                        </tr>
                        <tr>
                            <td>Last 7 days : </td>
                            <td class='datatd'><?php echo number_format($thisweek['linkused']); ?></td>
                        </tr>
                        <tr>
                            <td>Since 1st <?php echo date("M") ?> : </td>
                            <td class='datatd'><?php echo number_format($thismonth['linkused']); ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td class='datatd'><?php echo number_format($forever['linkused']); ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Clicked:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php foreach($recentclick as $rn) {
                                echo "<p><b>".date("H:i (d M)", strtotime($rn->linkUsedTime))."</b><br />".CHtml::link($rn->newsletters->title, array('newsletters/view', 'id'=>$rn->newslettersId), array('title'=>$rn->link)). "</p>\n";
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>