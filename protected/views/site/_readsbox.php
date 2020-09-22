<?php 
/**
* Required variables
* 
* $baseUrl
* $statistics[date]['read']
* $thisweek['read']
* $thismonth['read']
* $forever['read']
* $recentread
* @var mixed
*/
$baseUrl=Yii::app()->baseUrl; 
?>

                <?php echo CHtml::image($baseUrl.'/images/swap.png', '', array('style'=>'position: absolute; margin-left: -120px; cursor: pointer', 'id'=>'swapbutton1', 'onClick'=>'$("#readsbox").toggle();$("#linksbox").fadeToggle("slow");', 'title'=>'View link clicks')); ?><h3>Reads</h3>
                <div class="gbox-float">
                    <table>
                        <tr>
                            <td>Today : </td>
                            <td class='datatd'><?php echo number_format($statistics[date("Y-m-d")]['read']); ?></td>
                        </tr>
                        <tr>
                            <td>This Week : </td>
                            <td class='datatd'><?php echo number_format($thisweek['read']); ?></td>
                        </tr>
                        <tr>
                            <td>This Month : </td>
                            <td class='datatd'><?php echo number_format($thismonth['read']); ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td class='datatd'><?php echo number_format($forever['read']); ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recently Read:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php foreach($recentread as $rn) {
                                echo "<p><b>".date("H:i (d M)", strtotime($rn->readTime))."</b><br />".CHtml::link($rn->newsletters->title, array('newsletters/view', 'id'=>$rn->newslettersId))."</p>\n";
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>