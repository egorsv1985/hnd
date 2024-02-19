<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$counter = 0;
?>
<div class="news-block news-block-wrap">
    <div class="slider-wrapper">
        <div class="container">
            <div class="news-page">
                <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                    <?php
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>" class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
					<span class="news__header" <?php if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>style="background-image: url(<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>)"<?php endif ?>> </span> 
                    <span class="content">
                         <?php if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                             <span class="title"><?= $arItem["NAME"] ?></span>
                          <?php endif; ?>
                          <?php if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
                              <span class="date news-date-time"><?php echo $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
                          <?php endif ?>
                     </span>
                    </a>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>