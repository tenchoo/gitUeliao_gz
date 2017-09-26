<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<table class="table table-condensed table-bordered">
    <thead>
    <tr class="list-hd">
        <td colspan="4">
            <span class="first">客户：<?php echo $room->member->nickName;?></span>
            <span><?php echo date('Y-m-d H:i:s', $room->lastTime)?></span>
        </td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td width="70"><?php echo CHtml::tag('img',['src'=>$this->showImage($room->product->mainPic,50)]); ?></td>
        <td colspan="3"><?php echo $room->product->title;?><br /><?php echo $room->product->serialNumber;?></td>
    </tr>
    </tbody>
</table>
<br />

<form method="post">
    <table class="table table-condensed table-bordered">
        <tbody>
        <tr>
            <td width="70"><?php if($dataList->mark!=='member') echo "回复";?></td>
            <td>
                <div><?php echo date('Y-m-d H:i:s', $dataList->createTime);?></div>
                <div><?php
                    switch($dataList->mime) {
                        case "voice":
                            echo $dataList->showVoice();
                            break;

                        case "image":
                            echo $dataList->showImage();
                            break;

                        default:
                            if ($dataList->id == $cid) {
                                echo CHtml::textArea('content', $dataList->content);
                                echo CHtml::submitButton('保存');
                            } else {
                                echo  urldecode( $dataList->content ) ;
                            }
                    }
                    ?></div>
            </td>
            <td width="100">&nbsp;</td>
        </tr>
        </tbody>
    </table>
    <br/>
</form>
<script>
  seajs.use('libs/emoji/1.0.0/emoji.js',function(){
    $('.content-wrap').emoji();
  });
</script>