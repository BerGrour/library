<?php

/** @var yii\web\View $this */
/** @var common\models\Book $edition */
?>

<style>
.cat_card table {
    border:3px solid #000;
}

.cat_card table tr td {
    border-bottom:1px solid #000;
    border-right:3px solid #000;
    font-size:18px;
    line-height: 22px;
    /* 93 */
}
</style>

<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:93px; height:22px; max-width: 93px; overflow:hidden;" align="center">
            <div style="font-weight: bold;">
				<?php if (strlen($edition->rubric->title) > 9) { ?>
                    <?= mb_substr($edition->rubric->title, 0, 9, 'utf-8');?>
                <?php } else { ?>
                    <?= $edition->rubric->title;?>
                <?php } ?>
            </div>
        </td>
        <td style="width:25px;">&nbsp;</td>
        <td style="width:361px; border-right:0px solid #000;" valign="top">
            <div style="position:relative; height:22px; top:3px;">
                <p style='text-indent: 25px; margin: 0px;'>
                    <b><?= $edition->name ?></b>
                    <?php
                    if ($edition->additionalname) {
                        echo " : " . $edition->additionalname;
                    }
                    if ($edition->response) {
                        echo " / " . $edition->response;
                    }
                    if ($edition->publishplace) echo ". &ndash; $edition->publishplace";
                    if ($edition->publishyear) echo ", $edition->publishyear";
                    if ($edition->pages) echo ". &ndash; $edition->pages c.";
                    ?>
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;" align="center"><span style="font-weight: bold;"><?= $edition->authorsign;?></span></td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;" align="right"><span style="font-weight: bold;"><?= $edition->code;?></span></td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right:0px solid #000;">&nbsp;</td>
    </tr>
    <tr>
        <td style="width:93px; height:22px; border-bottom:0px solid #000;">&nbsp;</td>
        <td style="border-bottom:0px solid #000;">&nbsp;</td>
        <td style="border-bottom:0px solid #000; border-right:0px solid #000;">&nbsp;</td>
    </tr>
</table>