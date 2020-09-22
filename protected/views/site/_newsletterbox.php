<?php 
$baseUrl=Yii::app()->baseUrl; 

/**
* Required variables
* 
* $statistics[date]['newsletters']
* $thisweek['newsletters']
* $thismonth['newsletters']
* $forever['newsletters']
* $recentnews
*/

?>
                <h3>Newsletters</h3>
                <div class="gbox-float" id="newslettersdisplay">
                <!--<script type='text/javascript'>
                    $(document).ready(function (){
                        <?php
                        echo (CHtml::ajax(array(
                            'url'=>array('site/newslettersdisplay'),
                            'update'=>'#newslettersdisplay',
                        )));
                        ?>
                    });
                </script>-->                
                    <table>
                        <tr>
                            <td>Today :</td>
                            <td class='datatd'><?php echo number_format($statistics[date("Y-m-d")]['newsletters']); ?></td>
                        </tr>
                        <tr>
                            <td>This Week : </td>
                            <td class='datatd'><?php echo number_format($thisweek['newsletters']); ?></td>
                        </tr>
                        <tr>
                            <td>This Month : </td>
                            <td class='datatd'><?php echo number_format($thismonth['newsletters']); ?></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td class='datatd'><?php echo number_format($forever['newsletters']); ?></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b>Most Recent Newsletters:</b></td>
                        </tr>
                        <tr>
                            <td style='vertical-align: top; font-size: 8pt; border-top: 1px solid #111; background-color: white; padding: 0' colspan='2'>
                                <div class='recentlist'>
                            <?php foreach($recentnews as $rn) {
                                echo "<p><b>".date("H:i (d M)", strtotime($rn->created))."</b><br />".CHtml::link(htmlentities($rn->title, ENT_QUOTES), array('newsletters/view', 'id'=>$rn->id)). "</p>\n";
                            }
                            ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
