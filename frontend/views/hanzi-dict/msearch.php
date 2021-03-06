<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\HanziSet;
use common\models\search\HanziSetSearch;

?>

<style type="text/css">
    .dic-title {
        color: #1a0dab;
        font-family: 宋体;
        font-size: 18px;
        margin-top: 20px;
    }

    .hanzi-normal {
        font-family: 楷体;
        font-size: 32px;
        width: 40px;
        color: #1a0dab;
        text-align: center;
    }

    .hanzi-normal a {
        color: #1a0dab;
    }

    .param {
        color: red;
    }

    .hanzi-item a {
        color: #000;
    }

    .hanzi-item .param {
        color: red;
    }

    .param img {
        border: 1px solid red;
    }

    .hanzi-item {
        font-size: 33px;
        margin: 0px 2px;
    }

    .hanzi-img {
        margin: 1px 2px;
        padding: 0px 0px;
        width: 33px;
        height: 34px;
        vertical-align: text-top;
    }

    .variant2 {
        background-color: rgb(192, 232, 255);
    }

    .variant3 {
        background-color: rgb(255, 248, 208);
    }
</style>

<div class="hanzi-set-ids-index">
    <div class="search-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'method' => 'post',
            'enableClientScript' => false,
            'enableClientValidation' => false
        ]); ?>

        <?= $form->field($hanziSearch, 'param', ['template' => "{input}\n{hint}\n{error}", 'options' => ['style' => 'width:40%; float:left; padding-right:10px;']])->textInput(['maxlength' => true, 'placeholder' => "请输入文字或图片字编号…"]) ?>
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>

        <?php ActiveForm::end(); ?>

        <!-- 检索结果 -->
        <div class="search-result">
            <?php if (!empty($param) && empty($hanziSet) && empty($lqVariants)) {
                echo "<div style='color:#808080; margin-left: 10px; font-style: italic;' class='summary'>查询结果为空。</div>";
            } ?>

            <div class="search-result">
                <?php if (!empty($lqVariants)) : ?>
                    <div id="lq-variant">
                        <div class="dic-title">龍泉異體字字典：</div>
                        <?php foreach ($lqVariants as $normal => $variants) {
                            $class = ($param == $normal) ? 'param' : '';
                            echo "<div class='hanzi-normal'><a target='_blank' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $normal]) . "'>【<span class=$class>" . $normal . "</span>】</a></div>";
                            echo "<div class='hanzi-variants'>";
                            foreach ($variants as $variant) {
                                if (!empty($variant->word)) {
                                    $title = empty($variant->nor_var_type) ? '' : HanziSet::norVarTypes()[$variant->nor_var_type];
                                    if (!empty($variant->nor_var_type) && $variant->nor_var_type >= HanziSet::TYPE_NORMAL_WIDE) {
                                        $title = $title . '|' . $variant->belong_standard_word_code;
                                    }
                                    $class = ($param == $variant->word) ? 'param variant' . $variant->nor_var_type : 'variant' . $variant->nor_var_type;
                                    echo "<span class='hanzi-item' ><a target='_blank' class='$class' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->word]) . "'>" . $variant->word . "</a></span>";
                                } elseif (!empty($variant->ori_pic_name)) {
                                    $picPath = $variant->getLqPicturePath();
                                    $title = empty($variant->pic_name) ? preg_replace('(.jpg|.png)', '', $variant->ori_pic_name) : $variant->pic_name;
                                    if (!empty($variant->nor_var_type)) {
                                        $title = $title . '|' . HanziSet::norVarTypes()[$variant->nor_var_type];
                                    }
                                    if (!empty($variant->nor_var_type) && $variant->nor_var_type >= HanziSet::TYPE_NORMAL_WIDE) {
                                        $title = $title . '|' . $variant->belong_standard_word_code;
                                    }
                                    $class = ($param == $variant->pic_name) ? 'param variant' . $variant->nor_var_type : 'variant' . $variant->nor_var_type;
                                    echo "<span class='hanzi-item' ><a target='_blank' class='$class' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->pic_name]) . "'>" . "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'></a></span>";
                                }
                            }
                            echo "</div><br/>";
                        } ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($hanziSet[HanziSet::SOURCE_TAIWAN])) : ?>
                    <div class="dic-title">台灣異體字字典：</div>
                    <?php foreach ($hanziSet[HanziSet::SOURCE_TAIWAN] as $normal => $variants) {
                        $class = ($param == $normal) ? 'param' : '';
                        echo "<div class='hanzi-normal'><a target='_blank' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $normal]) . "'>【<span class=$class>" . $normal . "</span>】</a></div>";
                        echo "<div class='hanzi-variants'>";
                        foreach ($variants as $variant) {
                            if (!empty($variant->word)) {
                                $title = empty($variant->nor_var_type) ? '' : HanziSet::norVarTypes()[$variant->nor_var_type];
                                $class = ($param == $variant->word) ? 'param variant' . $variant->nor_var_type : 'variant' . $variant->nor_var_type;
                                echo "<span class='hanzi-item' ><a target='_blank' class='$class' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->word]) . "'>" . $variant->word . "</a></span>";
                            } elseif (!empty($variant->pic_name)) {
                                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                                $title = $variant->pic_name;
                                if (!empty($variant->nor_var_type)) {
                                    $title = $title . '|' . HanziSet::norVarTypes()[$variant->nor_var_type];
                                }
                                $class = ($param == $variant->pic_name) ? 'param variant' . $variant->nor_var_type : 'variant' . $variant->nor_var_type;
                                echo "<span class='hanzi-item' ><a target='_blank' class='$class' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->pic_name]) . "'>" . "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'></a></span>" . $variant->pic_name;
                            }
                        }
                        echo "</div><br/>";
                    } ?>
                <?php endif; ?>

                <?php if (!empty($hanziSet[HanziSet::SOURCE_GAOLI])) : ?>
                    <div class="dic-title">高麗異體字字典：</div>
                    <?php foreach ($hanziSet[HanziSet::SOURCE_GAOLI] as $normal => $variants) {
                        $class = ($param == $normal) ? 'param' : '';
                        echo "<div class='hanzi-normal'><a target='_blank' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $normal, 'a' => 'gl']) . "'>【<span class=$class >" . $normal . "</span>】</a></div>";
                        echo "<div class='hanzi-variants'>";
                        foreach ($variants as $variant) {
                            if (!empty($variant->word)) {
                                echo "<span class='hanzi-item'>" . $variant->word . "</span>";
                            } elseif (!empty($variant->pic_name)) {
                                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                                echo "<img alt='$variant->pic_name' title='$variant->pic_name' src='$picPath' class='hanzi-img'>" . $variant->pic_name;
                            }
                        }
                        echo "</div><br/>";
                    } ?>
                <?php endif; ?>

                <?php if (!empty($hanziSet[HanziSet::SOURCE_HANYU])) : ?>
                    <div>
                        <div class="dic-title">漢語大字典：</div>
                        <div><a target="_blank"
                                href=<?= Url::toRoute(['hanzi-dict/variant', 'param' => $param, 'a' => 'hy']) ?>><?php
                                $position = explode('-', $hanziSet[HanziSet::SOURCE_HANYU]);
                                echo "第" . $position[1] . "页 第" . $position[2] . "字";
                                ?></a></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($hanziSet[HanziSet::SOURCE_DUNHUANG])) : ?>
                    <div>
                        <div class="dic-title">敦煌俗字典：</div>
                        <div><a target="_blank"
                                href=<?= Url::toRoute(['hanzi-dict/variant', 'param' => $param, 'a' => 'dh']) ?>><?php
                                echo "第" . $hanziSet[HanziSet::SOURCE_DUNHUANG] . "页";
                                ?></a></div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

