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
}
</style>

<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:93px; height:22px;" align="center">
            <span style="font-weight: bold;">
                <?= $edition->rubric->shottitle;?>
            </span>
        </td>
        <td style="width:25px;">&nbsp;</td>
        <td style="width:361px; border-right:0px solid #000;" valign="top">
            <div style="position:relative; height:22px; top:3px;">
                <b>
                    <?php if (!empty($edition->bookAuthors)) {
                        if (count($edition->getArrayAuthors()) < 4) {
                            foreach ($edition->bookAuthors as $author_rel) {
                                echo $author_rel->author->showFIO();
                                break;
                            }
                        }
                    } ?>
                </b>
                <p style='text-indent: 25px; margin: 0px;'>
                    <b><?= $edition->name ?></b>
                    <?php
                    if ($edition->additionalname) {
                        echo " : " . $edition->additionalname;
                    }
                    if ($edition->response) {
                        echo " / " . $edition->response;
                    }
                    if ($edition->bookinfo) echo ". &ndash; $edition->bookinfo";
                    if ($edition->publishplace) echo ". &ndash; $edition->publishplace";
                    if ($edition->publishhouse) echo " : $edition->publishhouse";
                    if ($edition->publishyear) echo ", $edition->publishyear";
                    if ($edition->tom) echo ". &ndash; $edition->tom";
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