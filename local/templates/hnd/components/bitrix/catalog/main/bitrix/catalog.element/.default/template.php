<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
//$this->addExternalCss('/bitrix/css/main/bootstrap.css');

$templateLibrary = array('popup', 'fx', 'ui.fonts.opensans');
$currencyList    = '';

if (!empty($arResult['CURRENCIES'])) {
	$templateLibrary[] = 'currency';
	$currencyList      = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
	'TEMPLATE_THEME'   => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES'       => $currencyList,
	'ITEM'             => array(
		'ID'              => $arResult['ID'],
		'IBLOCK_ID'       => $arResult['IBLOCK_ID'],
		'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED'],
		'JS_OFFERS'       => $arResult['JS_OFFERS']
	)
);
unset($currencyList, $templateLibrary);

$mainId  = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID'                    => $mainId,
	'DISCOUNT_PERCENT_ID'   => $mainId . '_dsc_pict',
	'STICKER_ID'            => $mainId . '_sticker',
	'BIG_SLIDER_ID'         => $mainId . '_big_slider',
	'BIG_IMG_CONT_ID'       => $mainId . '_bigimg_cont',
	'SLIDER_CONT_ID'        => $mainId . '_slider_cont',
	'OLD_PRICE_ID'          => $mainId . '_old_price',
	'PRICE_ID'              => $mainId . '_price',
	'DESCRIPTION_ID'        => $mainId . '_description',
	'DISCOUNT_PRICE_ID'     => $mainId . '_price_discount',
	'PRICE_TOTAL'           => $mainId . '_price_total',
	'SLIDER_CONT_OF_ID'     => $mainId . '_slider_cont_',
	'QUANTITY_ID'           => $mainId . '_quantity',
	'QUANTITY_DOWN_ID'      => $mainId . '_quant_down',
	'QUANTITY_UP_ID'        => $mainId . '_quant_up',
	'QUANTITY_MEASURE'      => $mainId . '_quant_measure',
	'QUANTITY_LIMIT'        => $mainId . '_quant_limit',
	'BUY_LINK'              => $mainId . '_buy_link',
	'ADD_BASKET_LINK'       => $mainId . '_add_basket_link',
	'BASKET_ACTIONS_ID'     => $mainId . '_basket_actions',
	'NOT_AVAILABLE_MESS'    => $mainId . '_not_avail',
	'COMPARE_LINK'          => $mainId . '_compare_link',
	'TREE_ID'               => $mainId . '_skudiv',
	'DISPLAY_PROP_DIV'      => $mainId . '_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
	'OFFER_GROUP'           => $mainId . '_set_group_',
	'BASKET_PROP_DIV'       => $mainId . '_basket_prop',
	'SUBSCRIBE_LINK'        => $mainId . '_subscribe',
	'TABS_ID'               => $mainId . '_tabs',
	'TAB_CONTAINERS_ID'     => $mainId . '_tab_containers',
	'SMALL_CARD_PANEL_ID'   => $mainId . '_small_card_panel',
	'TABS_PANEL_ID'         => $mainId . '_tabs_panel'
);
$obName  = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name  = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt   = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

$haveOffers = !empty($arResult['OFFERS']);
if ($haveOffers) {
	$actualItem         = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['MORE_PHOTO_COUNT'] > 1) {
			$showSliderControls = true;
			break;
		}
	}
} else {
	$actualItem         = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps     = array();
$price        = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y') {
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '') {
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
} else {
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn          = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName  = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn          = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe       = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);
$productType         = $arResult['PRODUCT']['TYPE'];

$arParams['MESS_BTN_BUY']           = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE) {
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
} else {
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
}

$arParams['MESS_BTN_COMPARE']            = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE']     = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB']        = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB']         = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB']           = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY']      = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW']  = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');
// $arParams['USE_COMMENTS']                = "Y";
$positionClassMap = array(
	'left'   => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right'  => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top'    => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION'])) {
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
	}
}
?>
<div class="container bx-catalog-element bx-<?= $arParams['TEMPLATE_THEME'] ?>" id="<?= $itemIds['ID'] ?>" itemscope
	itemtype="http://schema.org/Product">
	<div class="hidden" id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
		<div class="product-item-detail-slider-block
						<?= ($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '') ?>"
			data-entity="images-slider-block">
		</div>
		<div class="product-item-detail-slider-images-container" data-entity="images-container"></div>
	</div>


	<div class="container-fluid items_top">
		<div class="dealers-inner">
			<a href="/dealers/" class="bt">Найти дилера</a>
			<!-- <a href="#online" class="buy_online bt bt-stroke">Купить онлайн</a> -->
		</div>

		<?
		if (!empty($arResult['PROPERTIES']['PRICES']['VALUE'])) {
			if (!empty($arResult['PROPERTIES']['OLD_PRICES']['VALUE'])) {

				$prices          = $arResult['PROPERTIES']['PRICES']['VALUE'];
				$oldPrice        = $arResult['PROPERTIES']['OLD_PRICES']['VALUE'];
				$discountPercent        = $arResult['PROPERTIES']['DISCOUNT']['VALUE'];
				// $discountPercent = round(($prices / $oldPrice) * 100 - 100);
				
			}
			?>
			<div class="prices_container">
				<span class="vat_text">Рекомендованная цена с НДС</span>
				<div class="price">
					<?= number_format($arResult['PROPERTIES']['PRICES']['VALUE'], 0, ',', ' '); ?> ₽
					<? if (!empty($discountPercent)) { ?><span class="discount_percent">
						<?= $discountPercent; ?> %
					</span>
				<? } ?>
				</div>
				<? if (!empty($arResult['PROPERTIES']['OLD_PRICES']['VALUE'])) { ?>
					<div class="price_old">
						<?= number_format($arResult['PROPERTIES']['OLD_PRICES']['VALUE'], 0, ',', ' '); ?> ₽
					</div>
					<?
				}
				?>
			</div>
			<?
		}
		?>
	</div>
	<div class="card-gallery gallery d-flex">

		<div class="preview general-style d-flex a-center j-center">
			<div class="tools d-flex a-center">

				<div class="product-item-detail-compare-container">
					<div class="product-item-detail-compare">
						<div class="checkbox">
							<label class="bt-tool icon-comparison" id="<?= $itemIds['COMPARE_LINK'] ?>">
								<input style="visibility: hidden" type="checkbox" data-entity="compare-checkbox">
								<span data-entity="compare-title">
									<?= $arParams['MESS_BTN_COMPARE'] ?>
								</span>
							</label>
						</div>
					</div>
				</div>

				<?php
				$APPLICATION->IncludeComponent(
					'bitrix:iblock.vote',
					'hndmod',
					array(
						'CUSTOM_SITE_ID'    => $arParams['CUSTOM_SITE_ID'] ?? null,
						'IBLOCK_TYPE'       => $arParams['IBLOCK_TYPE'],
						'IBLOCK_ID'         => $arParams['IBLOCK_ID'],
						'ELEMENT_ID'        => $arResult['ID'],
						'ELEMENT_CODE'      => '',
						'MAX_VOTE'          => '5',
						'VOTE_NAMES'        => array('1', '2', '3', '4', '5'),
						'SET_STATUS_404'    => 'N',
						'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
						'CACHE_TYPE'        => $arParams['CACHE_TYPE'],
						'CACHE_TIME'        => $arParams['CACHE_TIME']
					),
					$component,
					array('HIDE_ICONS' => 'Y')
				);
				?>


				<button class="add-to-favorites bt-tool icon-heart" data-product-id="<?= $arResult['ID'] ?>"
					onclick="toggleFavorites(this);"></button>
			</div>
		</div>
		<div class="thumbnail">
			<ul class="unstyled d-flex">
				<li class="cur js_thumbnail-img-item"><a data-big="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>"
						data-full="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" data-style="opacity: 1; z-index: 2"><img
							src="<?= $arResult["PREVIEW_PICTURE"]["SRC"] ?>" alt="" /></a></li>
				<? foreach ($arResult['GALLERY'] as $gal_item): ?>
					<li class="js_thumbnail-img-item"><a data-big="<?= $gal_item['FULL_SRC'] ?>" data-full="<?= $gal_item['FULL_SRC'] ?>" data-style="opacity: 0; z-index: 1"><img
								src="<?= $gal_item['THUMBNAIL_SRC'] ?>" /></a>
					</li>
				<? endforeach; ?>
                <? foreach ($arResult['VIDEO'] as $gal_video): ?>
                    <li class="thumbnail-video-item js_thumbnail-video-item"><a data-video="<?=$gal_video['YOUTUBE_SRC']?>" data-style="opacity: 0; z-index: 1"><img
                                    src="<?=$gal_video['THUMBNAIL_SRC']?>" /><svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24"><path fill="currentColor" d="m10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9c.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83c-.25.9-.83 1.48-1.73 1.73c-.47.13-1.33.22-2.65.28c-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44c-.9-.25-1.48-.83-1.73-1.73c-.13-.47-.22-1.1-.28-1.9c-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83c.25-.9.83-1.48 1.73-1.73c.47-.13 1.33-.22 2.65-.28c1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44c.9.25 1.48.83 1.73 1.73Z"/></svg></a>
                    </li>
                <? endforeach; ?>
			</ul>
		</div>
	</div>
    <?
    if (!empty($arResult['PROPERTIES']["LINK_OZON"]["VALUE"]) || !empty($arResult['PROPERTIES']["LINK_MARKET"]["VALUE"]))
    {
        ?>
        <div class="container-fluid " id="buy_online">
            <div class="dealers_header">Для приобретения оригинальной продукции HND онлайн, <br /> пожалуйста выберите
                маркетплейс из предлагаемых вариантов:</div>
            <div class="dealers-inner">
                <? if ($arResult['PROPERTIES']["LINK_OZON"]["VALUE"]) { ?><a
                    href="<?= $arResult['PROPERTIES']["LINK_OZON"]["VALUE"]; ?>" class="ozon"><img
                                src="/local/templates/hnd/img/ozon.png" alt="ozon" /></a>
                <? } ?>
                <? if ($arResult['PROPERTIES']["LINK_MARKET"]["VALUE"]) { ?><a
                    href="<?= $arResult['PROPERTIES']["LINK_MARKET"]["VALUE"]; ?>" class="market"><img
                                src="/local/templates/hnd/img/yandex.png" alt="ozon" /></a>
                <? } ?>

            </div>
        </div>
        <?
    }
    ?>
	<div class="container-fluid">


		<div class="card-tabs general-style">
			<div class="col-sm-8 col-md-12">
				<div class="row" id="<?= $itemIds['TABS_ID'] ?>">
					<div class="col-xs-12">
						<div class="product-item-detail-tabs-container card-tabs general-style">
							<ul class="product-item-detail-tabs-list tabs d-flex">
								<?php
								if ($showDescription) {
									?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="description">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span>
												<?= $arParams['MESS_DESCRIPTION_TAB'] ?>
											</span>
										</a>
									</li>
									<?php
								}

								if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
									?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="properties">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span>
												<?= $arParams['MESS_PROPERTIES_TAB'] ?>
											</span>
										</a>
									</li>
									<?php
								}

								if ($arParams['USE_COMMENTS'] === 'Y') {
									?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="comments">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span>
												<?= $arParams['MESS_COMMENTS_TAB'] ?>
											</span>
										</a>
									</li>
									<?php
								}
								?>
								<? if ($arResult['PROPERTIES']["PRIVILIGE"]["VALUE"]): ?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="privilege">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span>Преимущества</span>
										</a>
									</li>
								<? endif; ?>

								<? if ($arResult['PROPERTIES']["GIUDE"]["VALUE"]): ?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="guide">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span>Руководства</span>
										</a>
									</li>
								<? endif; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="row" id="<?= $itemIds['TAB_CONTAINERS_ID'] ?>">
					<div class="col-xs-12">
						<?php
						if ($showDescription) {
							?>

							<div class="product-item-detail-tab-content tab-content active" data-entity="tab-container"
								data-value="description" itemprop="description" id="<?= $itemIds['DESCRIPTION_ID'] ?>">
								<div class="content">
									<?php
									if (!function_exists('mb_lcfirst')) {
										function mb_lcfirst($str) {
											$fc = mb_strtolower(mb_substr($str, 0, 1));
											return $fc . mb_substr($str, 1);
										}
									} ?>
									<h2>Описание
										<?= mb_lcfirst($arResult['NAME']) ?>
									</h2>
									<?php
									if (
										$arResult['PREVIEW_TEXT'] != ''
										&& (
											$arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
											|| ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
										)
									) {
										echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>' . $arResult['PREVIEW_TEXT'] . '</p>';
									}

									if ($arResult['DETAIL_TEXT'] != '') {
										echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>' . $arResult['DETAIL_TEXT'] . '</p>';
									}
									?>
								</div>
							</div>
							<?php
						}

						if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
							?>
							<div class="product-item-detail-tab-content tab-content" data-entity="tab-container"
								data-value="properties">
								<div class="content">
									<div class="d-flex j-between">
										<?php
										if (!empty($arResult['DISPLAY_PROPERTIES'])) {
											?>
											<div class="option-list">
												<?php

												foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
													?>
													<div class="option-item">
														<div class="label">
															<?= $property['NAME'] ?>
														</div>
														<div class="value">
															<?= (
																is_array($property['DISPLAY_VALUE'])
																? implode(' / ', $property['DISPLAY_VALUE'])
																: $property['DISPLAY_VALUE']
															) ?>
														</div>
													</div>

													<?php
												}
												unset($property);
												?>
											</div>
										</div>
										<?php
										}

										if ($arResult['SHOW_OFFERS_PROPS']) {
											?>
										<dl class="product-item-detail-properties" id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>">
										</dl>
										<?php
										}
										?>
								</div>
							</div>
							<?php
						}

						if ($arParams['USE_COMMENTS'] === 'Y') {
							?>
							<div class="product-item-detail-tab-content tab-content " data-entity="tab-container"
								data-value="comments">
								<?php
								$componentCommentsParams = array(
									'ELEMENT_ID'       => $arResult['ID'],
									'ELEMENT_CODE'     => '',
									'IBLOCK_ID'        => $arParams['IBLOCK_ID'],
									'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
									'URL_TO_COMMENT'   => '',
									'WIDTH'            => '',
									'COMMENTS_COUNT'   => '5',
									'BLOG_USE'         => $arParams['BLOG_USE'],
									'FB_USE'           => $arParams['FB_USE'],
									'FB_APP_ID'        => $arParams['FB_APP_ID'],
									'VK_USE'           => $arParams['VK_USE'],
									'VK_API_ID'        => $arParams['VK_API_ID'],
									'CACHE_TYPE'       => $arParams['CACHE_TYPE'],
									'CACHE_TIME'       => $arParams['CACHE_TIME'],
									'CACHE_GROUPS'     => $arParams['CACHE_GROUPS'],
									'BLOG_TITLE'       => '',
									'BLOG_URL'         => $arParams['BLOG_URL'],
									'PATH_TO_SMILE'    => '',
									'EMAIL_NOTIFY'     => $arParams['BLOG_EMAIL_NOTIFY'],
									'AJAX_POST'        => 'Y',
									'SHOW_SPAM'        => 'Y',
									'SHOW_RATING'      => 'N',
									'FB_TITLE'         => '',
									'FB_USER_ADMIN_ID' => '',
									'FB_COLORSCHEME'   => 'light',
									'FB_ORDER_BY'      => 'reverse_time',
									'VK_TITLE'         => '',
									'TEMPLATE_THEME'   => $arParams['~TEMPLATE_THEME']
								);
								if (isset($arParams["USER_CONSENT"]))
									$componentCommentsParams["USER_CONSENT"] = $arParams["USER_CONSENT"];
								if (isset($arParams["USER_CONSENT_ID"]))
									$componentCommentsParams["USER_CONSENT_ID"] = $arParams["USER_CONSENT_ID"];
								if (isset($arParams["USER_CONSENT_IS_CHECKED"]))
									$componentCommentsParams["USER_CONSENT_IS_CHECKED"] = $arParams["USER_CONSENT_IS_CHECKED"];
								if (isset($arParams["USER_CONSENT_IS_LOADED"]))
									$componentCommentsParams["USER_CONSENT_IS_LOADED"] = $arParams["USER_CONSENT_IS_LOADED"];
								$APPLICATION->IncludeComponent(
									'bitrix:catalog.comments',
									'',
									$componentCommentsParams,
									$component,
									array('HIDE_ICONS' => 'Y')
								);
								?>
							</div>
							<?php
						}
						?>
						<? //echo "<pre>"; print_r($arResult['PROPERTIES']["PRIVILIGE"]["VALUE"]); echo "</pre>";?>
						<?
						if ($arResult['PROPERTIES']["PRIVILIGE"]["VALUE"]) { ?>
							<style>
								.label:first-letter {
									text-transform: capitalize
								}
							</style>
							<div class="product-item-detail-tab-content tab-content " data-entity="tab-container"
								data-value="privilege">
								<div class="content">
									<div class="d-flex j-between">
										<div class="option-list">
											<?php
											$re  = '/<li>(.|\n)*?<\/li>/m';
											$str = $arResult['PROPERTIES']["PRIVILIGE"]["~VALUE"]['TEXT'];

											preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

											// echo "<pre>";
											// var_dump($matches);
											// echo "</pre>";
										
											foreach ($matches as $key => $value) {
												echo '<div class="option-item"><div class="label">';
												echo trim(strip_tags($value[0]));
												echo '</div></div>';
											}
											?>

										</div>
									</div>
								</div>
							</div>
						<? } ?>
						<div class="product-item-detail-tab-content tab-content " data-entity="tab-container"
							data-value="guide">
							<div class="content">
								<?
								$arFile            = CFile::GetFileArray($arResult['PROPERTIES']["GIUDE"]["VALUE"]);
								$fileSizeMegabytes = $arFile['FILE_SIZE'] / (1024 * 1024);
								$fileSizeMegabytes = round($fileSizeMegabytes, 2);
								?>
								<ul class="listfile">
									<li>
										<a href="<?= $arFile['SRC'] ?>">
											<span>
												<?= $arFile["ORIGINAL_NAME"] ?>
											</span>
											<span>
												<?= $fileSizeMegabytes ?> мб
											</span>
										</a>
									</li>

								</ul>

							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="row">
			<div class="col-xs-12">
				<?php
				if ($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
					$APPLICATION->IncludeComponent(
						'bitrix:sale.prediction.product.detail',
						'.default',
						array(
							'BUTTON_ID'                => $showBuyBtn ? $itemIds['BUY_LINK'] : $itemIds['ADD_BASKET_LINK'],
							'CUSTOM_SITE_ID'           => $arParams['CUSTOM_SITE_ID'] ?? null,
							'POTENTIAL_PRODUCT_TO_BUY' => array(
								'ID'                     => $arResult['ID'] ?? null,
								'MODULE'                 => $arResult['MODULE'] ?? 'catalog',
								'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
								'QUANTITY'               => $arResult['QUANTITY'] ?? null,
								'IBLOCK_ID'              => $arResult['IBLOCK_ID'] ?? null,

								'PRIMARY_OFFER_ID'       => $arResult['OFFERS'][0]['ID'] ?? null,
								'SECTION'                => array(
									'ID'           => $arResult['SECTION']['ID'] ?? null,
									'IBLOCK_ID'    => $arResult['SECTION']['IBLOCK_ID'] ?? null,
									'LEFT_MARGIN'  => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
									'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
								),
							)
						),
						$component,
						array('HIDE_ICONS' => 'Y')
					);
				}

				if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
					?>
					<div data-entity="parent-container">
						<?php
						if (!isset($arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y') {
							?>
							<div class="catalog-block-header" data-entity="header" data-showed="false"
								style="display: none; opacity: 0;">
								<?= ($arParams['GIFTS_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFT_BLOCK_TITLE_DEFAULT')) ?>
							</div>
							<?php
						}

						CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');
						$APPLICATION->IncludeComponent(
							'bitrix:sale.products.gift',
							'.default',
							array(
								'CUSTOM_SITE_ID'                                     => $arParams['CUSTOM_SITE_ID'] ?? null,
								'PRODUCT_ID_VARIABLE'                                => $arParams['PRODUCT_ID_VARIABLE'],
								'ACTION_VARIABLE'                                    => $arParams['ACTION_VARIABLE'],

								'PRODUCT_ROW_VARIANTS'                               => "",
								'PAGE_ELEMENT_COUNT'                                 => 0,
								'DEFERRED_PRODUCT_ROW_VARIANTS'                      => \Bitrix\Main\Web\Json::encode(
									SaleProductsGiftComponent::predictRowVariants(
										$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
										$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
									)
								),
								'DEFERRED_PAGE_ELEMENT_COUNT'                        => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

								'SHOW_DISCOUNT_PERCENT'                              => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
								'DISCOUNT_PERCENT_POSITION'                          => $arParams['DISCOUNT_PERCENT_POSITION'],
								'SHOW_OLD_PRICE'                                     => $arParams['GIFTS_SHOW_OLD_PRICE'],
								'PRODUCT_DISPLAY_MODE'                               => 'Y',
								'PRODUCT_BLOCKS_ORDER'                               => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
								'SHOW_SLIDER'                                        => $arParams['GIFTS_SHOW_SLIDER'],
								'SLIDER_INTERVAL'                                    => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
								'SLIDER_PROGRESS'                                    => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

								'TEXT_LABEL_GIFT'                                    => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

								'LABEL_PROP_' . $arParams['IBLOCK_ID'] => array(),
								'LABEL_PROP_MOBILE_' . $arParams['IBLOCK_ID'] => array(),
								'LABEL_PROP_POSITION'                                => $arParams['LABEL_PROP_POSITION'],

								'ADD_TO_BASKET_ACTION'                               => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
								'MESS_BTN_BUY'                                       => $arParams['~GIFTS_MESS_BTN_BUY'],
								'MESS_BTN_ADD_TO_BASKET'                             => $arParams['~GIFTS_MESS_BTN_BUY'],
								'MESS_BTN_DETAIL'                                    => $arParams['~MESS_BTN_DETAIL'],
								'MESS_BTN_SUBSCRIBE'                                 => $arParams['~MESS_BTN_SUBSCRIBE'],

								'SHOW_PRODUCTS_' . $arParams['IBLOCK_ID'] => 'Y',
								'PROPERTY_CODE_' . $arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
								'PROPERTY_CODE_MOBILE' . $arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE_MOBILE'],
								'PROPERTY_CODE_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
								'OFFER_TREE_PROPS_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
								'CART_PROPERTIES_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
								'ADDITIONAL_PICT_PROP_' . $arParams['IBLOCK_ID'] => ($arParams['ADD_PICT_PROP'] ?? ''),
								'ADDITIONAL_PICT_PROP_' . $arResult['OFFERS_IBLOCK'] => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),

								'HIDE_NOT_AVAILABLE'                                 => 'Y',
								'HIDE_NOT_AVAILABLE_OFFERS'                          => 'Y',
								'PRODUCT_SUBSCRIPTION'                               => $arParams['PRODUCT_SUBSCRIPTION'],
								'TEMPLATE_THEME'                                     => $arParams['TEMPLATE_THEME'],
								'PRICE_CODE'                                         => $arParams['PRICE_CODE'],
								'SHOW_PRICE_COUNT'                                   => $arParams['SHOW_PRICE_COUNT'],
								'PRICE_VAT_INCLUDE'                                  => $arParams['PRICE_VAT_INCLUDE'],
								'CONVERT_CURRENCY'                                   => $arParams['CONVERT_CURRENCY'],
								'BASKET_URL'                                         => $arParams['BASKET_URL'],
								'ADD_PROPERTIES_TO_BASKET'                           => $arParams['ADD_PROPERTIES_TO_BASKET'],
								'PRODUCT_PROPS_VARIABLE'                             => $arParams['PRODUCT_PROPS_VARIABLE'],
								'PARTIAL_PRODUCT_PROPERTIES'                         => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
								'USE_PRODUCT_QUANTITY'                               => 'N',
								'PRODUCT_QUANTITY_VARIABLE'                          => $arParams['PRODUCT_QUANTITY_VARIABLE'],
								'CACHE_GROUPS'                                       => $arParams['CACHE_GROUPS'],
								'POTENTIAL_PRODUCT_TO_BUY'                           => array(
									'ID'                     => $arResult['ID'] ?? null,
									'MODULE'                 => $arResult['MODULE'] ?? 'catalog',
									'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
									'QUANTITY'               => $arResult['QUANTITY'] ?? null,
									'IBLOCK_ID'              => $arResult['IBLOCK_ID'] ?? null,

									'PRIMARY_OFFER_ID'       => $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?? null,
									'SECTION'                => array(
										'ID'           => $arResult['SECTION']['ID'] ?? null,
										'IBLOCK_ID'    => $arResult['SECTION']['IBLOCK_ID'] ?? null,
										'LEFT_MARGIN'  => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
										'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
									),
								),

								'USE_ENHANCED_ECOMMERCE'                             => $arParams['USE_ENHANCED_ECOMMERCE'],
								'DATA_LAYER_NAME'                                    => $arParams['DATA_LAYER_NAME'],
								'BRAND_PROPERTY'                                     => $arParams['BRAND_PROPERTY']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
					<?php
				}

				if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
					?>
					<div data-entity="parent-container">
						<?php
						if (!isset($arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y') {
							?>
							<div class="catalog-block-header" data-entity="header" data-showed="false"
								style="display: none; opacity: 0;">
								<?= ($arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFTS_MAIN_BLOCK_TITLE_DEFAULT')) ?>
							</div>
							<?php
						}

						$APPLICATION->IncludeComponent(
							'bitrix:sale.gift.main.products',
							'.default',
							array(
								'CUSTOM_SITE_ID'            => $arParams['CUSTOM_SITE_ID'] ?? null,
								'PAGE_ELEMENT_COUNT'        => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
								'LINE_ELEMENT_COUNT'        => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
								'HIDE_BLOCK_TITLE'          => 'Y',
								'BLOCK_TITLE'               => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

								'OFFERS_FIELD_CODE'         => $arParams['OFFERS_FIELD_CODE'],
								'OFFERS_PROPERTY_CODE'      => $arParams['OFFERS_PROPERTY_CODE'],

								'AJAX_MODE'                 => $arParams['AJAX_MODE'],
								'IBLOCK_TYPE'               => $arParams['IBLOCK_TYPE'],
								'IBLOCK_ID'                 => $arParams['IBLOCK_ID'],

								'ELEMENT_SORT_FIELD'        => 'ID',
								'ELEMENT_SORT_ORDER'        => 'DESC',
								'FILTER_NAME'               => 'searchFilter',
								'SECTION_URL'               => $arParams['SECTION_URL'],
								'DETAIL_URL'                => $arParams['DETAIL_URL'],
								'BASKET_URL'                => $arParams['BASKET_URL'],
								'ACTION_VARIABLE'           => $arParams['ACTION_VARIABLE'],
								'PRODUCT_ID_VARIABLE'       => $arParams['PRODUCT_ID_VARIABLE'],
								'SECTION_ID_VARIABLE'       => $arParams['SECTION_ID_VARIABLE'],

								'CACHE_TYPE'                => $arParams['CACHE_TYPE'],
								'CACHE_TIME'                => $arParams['CACHE_TIME'],

								'CACHE_GROUPS'              => $arParams['CACHE_GROUPS'],
								'SET_TITLE'                 => $arParams['SET_TITLE'],
								'PROPERTY_CODE'             => $arParams['PROPERTY_CODE'],
								'PRICE_CODE'                => $arParams['PRICE_CODE'],
								'USE_PRICE_COUNT'           => $arParams['USE_PRICE_COUNT'],
								'SHOW_PRICE_COUNT'          => $arParams['SHOW_PRICE_COUNT'],

								'PRICE_VAT_INCLUDE'         => $arParams['PRICE_VAT_INCLUDE'],
								'CONVERT_CURRENCY'          => $arParams['CONVERT_CURRENCY'],
								'CURRENCY_ID'               => $arParams['CURRENCY_ID'],
								'HIDE_NOT_AVAILABLE'        => 'Y',
								'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
								'TEMPLATE_THEME'            => ($arParams['TEMPLATE_THEME'] ?? ''),
								'PRODUCT_BLOCKS_ORDER'      => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],

								'SHOW_SLIDER'               => $arParams['GIFTS_SHOW_SLIDER'],
								'SLIDER_INTERVAL'           => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
								'SLIDER_PROGRESS'           => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

								'ADD_PICT_PROP'             => ($arParams['ADD_PICT_PROP'] ?? ''),
								'LABEL_PROP'                => ($arParams['LABEL_PROP'] ?? ''),
								'LABEL_PROP_MOBILE'         => ($arParams['LABEL_PROP_MOBILE'] ?? ''),
								'LABEL_PROP_POSITION'       => ($arParams['LABEL_PROP_POSITION'] ?? ''),
								'OFFER_ADD_PICT_PROP'       => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),
								'OFFER_TREE_PROPS'          => ($arParams['OFFER_TREE_PROPS'] ?? ''),
								'SHOW_DISCOUNT_PERCENT'     => ($arParams['SHOW_DISCOUNT_PERCENT'] ?? ''),
								'DISCOUNT_PERCENT_POSITION' => ($arParams['DISCOUNT_PERCENT_POSITION'] ?? ''),
								'SHOW_OLD_PRICE'            => ($arParams['SHOW_OLD_PRICE'] ?? ''),
								'MESS_BTN_BUY'              => ($arParams['~MESS_BTN_BUY'] ?? ''),
								'MESS_BTN_ADD_TO_BASKET'    => ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? ''),
								'MESS_BTN_DETAIL'           => ($arParams['~MESS_BTN_DETAIL'] ?? ''),
								'MESS_NOT_AVAILABLE'        => ($arParams['~MESS_NOT_AVAILABLE'] ?? ''),
								'ADD_TO_BASKET_ACTION'      => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
								'SHOW_CLOSE_POPUP'          => ($arParams['SHOW_CLOSE_POPUP'] ?? ''),
								'DISPLAY_COMPARE'           => ($arParams['DISPLAY_COMPARE'] ?? ''),
								'COMPARE_PATH'              => ($arParams['COMPARE_PATH'] ?? ''),
							)
							+ array(
								'OFFER_ID'               => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
								? $arResult['ID']
								: $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
								'SECTION_ID'             => $arResult['SECTION']['ID'],
								'ELEMENT_ID'             => $arResult['ID'],

								'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
								'DATA_LAYER_NAME'        => $arParams['DATA_LAYER_NAME'],
								'BRAND_PROPERTY'         => $arParams['BRAND_PROPERTY']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>

	<!--Top tabs-->
	<div class="product-item-detail-tabs-container-fixed hidden-xs" id="<?= $itemIds['TABS_PANEL_ID'] ?>">
		<ul class="product-item-detail-tabs-list">
			<?php
			if ($showDescription) {
				?>
				<li class="product-item-detail-tab" data-entity="tab" data-value="description">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span>
							<?= $arParams['MESS_DESCRIPTION_TAB'] ?>
						</span>
					</a>
				</li>
				<?php
			}

			if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
				?>
				<li class="product-item-detail-tab" data-entity="tab" data-value="properties">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span>
							<?= $arParams['MESS_PROPERTIES_TAB'] ?>
						</span>
					</a>
				</li>
				<?php
			}

			if ($arParams['USE_COMMENTS'] === 'Y') {
				?>
				<li class="product-item-detail-tab" data-entity="tab" data-value="comments">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span>
							<?= $arParams['MESS_COMMENTS_TAB'] ?>
						</span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<meta itemprop="name" content="<?= $name ?>" />
	<meta itemprop="category" content="<?= $arResult['CATEGORY_PATH'] ?>" />
	<?php
	if ($haveOffers) {
		foreach ($arResult['JS_OFFERS'] as $offer) {
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE'])) {
				foreach ($offer['TREE'] as $propName => $skuId) {
					$propId = (int) mb_substr($propName, 5);

					foreach ($skuProps as $prop) {
						if ($prop['ID'] == $propId) {
							foreach ($prop['VALUES'] as $propId => $propValue) {
								if ($propId == $skuId) {
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
			?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?>" />
				<meta itemprop="price" content="<?= $offerPrice['RATIO_PRICE'] ?>" />
				<meta itemprop="priceCurrency" content="<?= $offerPrice['CURRENCY'] ?>" />
				<link itemprop="availability" href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
			</span>
			<?php
		}

		unset($offerPrice, $currentOffersList);
	} else {
		?>
	<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>" />
		<meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>" />
		<link itemprop="availability"
			href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
	</span>
	<?php
	}
	?>
</div>

<div class="row">
	<? if ($arResult["PROPERTIES"]["ACCESSORIES_CN"]["VALUE"]): ?>
		<? $GLOBALS['exludeFiltter'] = array('ID' => $arResult["PROPERTIES"]["ACCESSORIES_CN"]["VALUE"]); ?>
		<? $APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"product_slider",
			array(
				"ACTIVE_DATE_FORMAT"              => "d.m.Y",
				"ADD_SECTIONS_CHAIN"              => "N",
				"AJAX_MODE"                       => "N",
				"AJAX_OPTION_ADDITIONAL"          => "",
				"AJAX_OPTION_HISTORY"             => "N",
				"AJAX_OPTION_JUMP"                => "N",
				"AJAX_OPTION_STYLE"               => "Y",
				"CACHE_FILTER"                    => "N",
				"CACHE_GROUPS"                    => "Y",
				"CACHE_TIME"                      => "36000000",
				"CACHE_TYPE"                      => "A",
				"CHECK_DATES"                     => "Y",
				"DETAIL_URL"                      => "",
				"DISPLAY_BOTTOM_PAGER"            => "N",
				"DISPLAY_DATE"                    => "N",
				"DISPLAY_NAME"                    => "Y",
				"DISPLAY_PICTURE"                 => "Y",
				"DISPLAY_PREVIEW_TEXT"            => "Y",
				"DISPLAY_TOP_PAGER"               => "N",
				"FIELD_CODE"                      => array("", ""),
				"FILTER_NAME"                     => "exludeFiltter",
				"HIDE_LINK_WHEN_NO_DETAIL"        => "N",
				"IBLOCK_ID"                       => "2",
				"IBLOCK_TYPE"                     => "hnd_catalog",
				"INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
				"INCLUDE_SUBSECTIONS"             => "N",
				"MESSAGE_404"                     => "",
				"NEWS_COUNT"                      => "20",
				"PAGER_BASE_LINK_ENABLE"          => "N",
				"PAGER_DESC_NUMBERING"            => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL"                  => "N",
				"PAGER_SHOW_ALWAYS"               => "N",
				"PAGER_TEMPLATE"                  => ".default",
				"PAGER_TITLE"                     => "Аксессуары и запчасти",
				"PARENT_SECTION"                  => "",
				"PARENT_SECTION_CODE"             => "",
				"PREVIEW_TRUNCATE_LEN"            => "",
				"PROPERTY_CODE"                   => array("MAXPOWER", "TYPESNOWBLOW", "TYPEGEENERATOR", "MOVTYPE", "GRIPWIDTH", "GRIPHEIGHT", "vote_count", "rating", "vote_sum", "EJECTIONRAGE", "LINK_DILLER", "LINK_BUY", "BLOG_POST_ID", "BLOG_COMMENTS_CNT", ""),
				"SET_BROWSER_TITLE"               => "N",
				"SET_LAST_MODIFIED"               => "N",
				"SET_META_DESCRIPTION"            => "N",
				"SET_META_KEYWORDS"               => "N",
				"SET_STATUS_404"                  => "N",
				"SET_TITLE"                       => "N",
				"SHOW_404"                        => "N",
				"SORT_BY1"                        => "ACTIVE_FROM",
				"SORT_BY2"                        => "SORT",
				"SORT_ORDER1"                     => "DESC",
				"SORT_ORDER2"                     => "ASC",
				"STRICT_SECTION_CHECK"            => "N"
			)
		); ?>
	<? endif; ?>
	<br>
	<? if ($arResult["PROPERTIES"]["FEATURED_CN"]["VALUE"]): ?>
		<? $GLOBALS['exludeFiltter'] = array('ID' => $arResult["PROPERTIES"]["FEATURED_CN"]["VALUE"]); ?>
		<? $APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"product_slider",
			array(
				"ACTIVE_DATE_FORMAT"              => "d.m.Y",
				"ADD_SECTIONS_CHAIN"              => "N",
				"AJAX_MODE"                       => "N",
				"AJAX_OPTION_ADDITIONAL"          => "",
				"AJAX_OPTION_HISTORY"             => "N",
				"AJAX_OPTION_JUMP"                => "N",
				"AJAX_OPTION_STYLE"               => "Y",
				"CACHE_FILTER"                    => "N",
				"CACHE_GROUPS"                    => "Y",
				"CACHE_TIME"                      => "36000000",
				"CACHE_TYPE"                      => "A",
				"CHECK_DATES"                     => "Y",
				"DETAIL_URL"                      => "",
				"DISPLAY_BOTTOM_PAGER"            => "N",
				"DISPLAY_DATE"                    => "N",
				"DISPLAY_NAME"                    => "Y",
				"DISPLAY_PICTURE"                 => "Y",
				"DISPLAY_PREVIEW_TEXT"            => "Y",
				"DISPLAY_TOP_PAGER"               => "N",
				"FIELD_CODE"                      => array("", ""),
				"FILTER_NAME"                     => "exludeFiltter",
				"HIDE_LINK_WHEN_NO_DETAIL"        => "N",
				"IBLOCK_ID"                       => "2",
				"IBLOCK_TYPE"                     => "hnd_catalog",
				"INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
				"INCLUDE_SUBSECTIONS"             => "N",
				"MESSAGE_404"                     => "",
				"NEWS_COUNT"                      => "20",
				"PAGER_BASE_LINK_ENABLE"          => "N",
				"PAGER_DESC_NUMBERING"            => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL"                  => "N",
				"PAGER_SHOW_ALWAYS"               => "N",
				"PAGER_TEMPLATE"                  => ".default",
				"PAGER_TITLE"                     => "ПОХОЖИЕ ТОВАРЫ",
				"PARENT_SECTION"                  => "",
				"PARENT_SECTION_CODE"             => "",
				"PREVIEW_TRUNCATE_LEN"            => "",
				"PROPERTY_CODE"                   => array("MAXPOWER", "TYPESNOWBLOW", "TYPEGEENERATOR", "MOVTYPE", "GRIPWIDTH", "GRIPHEIGHT", "vote_count", "rating", "vote_sum", "EJECTIONRAGE", "LINK_DILLER", "LINK_BUY", "BLOG_POST_ID", "BLOG_COMMENTS_CNT", ""),
				"SET_BROWSER_TITLE"               => "N",
				"SET_LAST_MODIFIED"               => "N",
				"SET_META_DESCRIPTION"            => "N",
				"SET_META_KEYWORDS"               => "N",
				"SET_STATUS_404"                  => "N",
				"SET_TITLE"                       => "N",
				"SHOW_404"                        => "N",
				"SORT_BY1"                        => "ACTIVE_FROM",
				"SORT_BY2"                        => "SORT",
				"SORT_ORDER1"                     => "DESC",
				"SORT_ORDER2"                     => "ASC",
				"STRICT_SECTION_CHECK"            => "N"
			)
		); ?>
	<? endif; ?>
</div>
<?php
if ($haveOffers) {
	$offerIds   = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer) {
		$offerIds[]   = (int) $jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer   = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps         = '';
		$strMainProps        = '';
		$strPriceRangesRatio = '';
		$strPriceRanges      = '';

		if ($arResult['SHOW_OFFERS_PROPS']) {
			if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property) {
					$current     = '<dt>' . $property['NAME'] . '</dt><dd>' . (
						is_array($property['VALUE'])
						? implode(' / ', $property['VALUE'])
						: $property['VALUE']
					) . '</dd>';
					$strAllProps .= $current;

					if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']])) {
						$strMainProps .= $current;
					}
				}

				unset($current);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1) {
			$strPriceRangesRatio = '(' . Loc::getMessage(
				'CT_BCE_CATALOG_RATIO_PRICE',
				array('#RATIO#' => ($useRatio
					? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
					: '1'
				) . ' ' . $measureName)
			) . ')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range) {
				if ($range['HASH'] !== 'ZERO-INF') {
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice) {
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
							break;
						}
					}

					if ($itemPrice) {
						$strPriceRanges .= '<dt>' . Loc::getMessage(
							'CT_BCE_CATALOG_RANGE_FROM',
							array('#FROM#' => $range['SORT_FROM'] . ' ' . $measureName)
						) . ' ';

						if (is_infinite($range['SORT_TO'])) {
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						} else {
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'] . ' ' . $measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>' . ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) . '</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES']            = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML']       = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML']             = $strPriceRanges;
	}

	$templateData['OFFER_IDS']   = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG'          => array(
			'USE_CATALOG'               => $arResult['CATALOG'],
			'SHOW_QUANTITY'             => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE'                => true,
			'SHOW_DISCOUNT_PERCENT'     => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE'            => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT'           => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE'           => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS'            => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP'               => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE'         => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION'      => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP'          => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY'         => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR'  => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME'            => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS'              => true,
			'USE_SUBSCRIBE'             => $showSubscribe,
			'SHOW_SLIDER'               => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL'           => $arParams['SLIDER_INTERVAL'],
			'ALT'                       => $alt,
			'TITLE'                     => $title,
			'MAGNIFIER_ZOOM_PERCENT'    => 200,
			'USE_ENHANCED_ECOMMERCE'    => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME'           => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY'            => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
			? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
			: null,
			'SHOW_SKU_DESCRIPTION'      => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE'    => $arResult['PRODUCT']['TYPE'],
		'VISUAL'          => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE'  => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT'         => array(
			'ID'                => $arResult['ID'],
			'ACTIVE'            => $arResult['ACTIVE'],
			'NAME'              => $arResult['~NAME'],
			'CATEGORY'          => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT'       => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE'  => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT'      => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET'          => array(
			'QUANTITY'         => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL'       => $arParams['BASKET_URL'],
			'SKU_PROPS'        => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS'          => $arResult['JS_OFFERS'],
		'OFFER_SELECTED'  => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS'      => $skuProps
	);
} else {
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties) {
		?>
		<div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
			<?php
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) {
					?>
					<input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
						value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
					<?php
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				}
			}

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties) {
				?>
			<table>
				<?php
				foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo) {
					?>
					<tr>
						<td>
							<?= $arResult['PROPERTIES'][$propId]['NAME'] ?>
						</td>
						<td>
							<?php
							if (
								$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
								&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
							) {
								foreach ($propInfo['VALUES'] as $valueId => $value) {
									?>
									<label>
										<input type="radio" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
											value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? '"checked"' : '') ?>>
										<?= $value ?>
									</label>
									<br>
									<?php
								}
							} else {
								?>
							<select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]">
								<?php
								foreach ($propInfo['VALUES'] as $valueId => $value) {
									?>
									<option value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? '"selected"' : '') ?>>
										<?= $value ?>
									</option>
									<?php
								}
								?>
							</select>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
			}
			?>
		</div>


		<?php
	}

	$jsParams = array(
		'CONFIG'       => array(
			'USE_CATALOG'              => $arResult['CATALOG'],
			'SHOW_QUANTITY'            => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE'               => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT'    => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE'           => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT'          => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE'          => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE'        => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION'     => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP'         => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY'        => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME'           => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS'             => true,
			'USE_SUBSCRIBE'            => $showSubscribe,
			'SHOW_SLIDER'              => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL'          => $arParams['SLIDER_INTERVAL'],
			'ALT'                      => $alt,
			'TITLE'                    => $title,
			'MAGNIFIER_ZOOM_PERCENT'   => 200,
			'USE_ENHANCED_ECOMMERCE'   => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME'          => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY'           => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
			? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
			: null
		),
		'VISUAL'       => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT'      => array(
			'ID'                           => $arResult['ID'],
			'ACTIVE'                       => $arResult['ACTIVE'],
			'PICT'                         => reset($arResult['MORE_PHOTO']),
			'NAME'                         => $arResult['~NAME'],
			'SUBSCRIPTION'                 => true,
			'ITEM_PRICE_MODE'              => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES'                  => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED'          => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES'         => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS'          => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED'  => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT'                 => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER'                       => $arResult['MORE_PHOTO'],
			'CAN_BUY'                      => $arResult['CAN_BUY'],
			'CHECK_QUANTITY'               => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT'               => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY'                 => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY'                => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY'                     => $arResult['CATEGORY_PATH']
		),
		'BASKET'       => array(
			'ADD_PROPS'        => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY'         => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS'            => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS'      => $emptyProductProperties,
			'BASKET_URL'       => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE']) {
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE'        => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH'                => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
;

?>
<script>
	document.addEventListener("DOMContentLoaded", function () {
		let tabs = document.querySelectorAll(".product-item-detail-tab");
		let tabContents = document.querySelectorAll(".product-item-detail-tab-content");
		const mediaQuery = window.matchMedia('(max-width: 991px)')

		tabs.forEach((tab) => {
			tab.addEventListener("click", function () {
				let targetValue = this.getAttribute("data-value");

				// Удалить активный класс у всех вкладок и контейнеров
				tabs.forEach((t) => t.classList.remove("active"));
				tabContents.forEach((content) => content.classList.remove("active"));

				// Добавить активный класс к выбранной вкладке и контейнеру
				this.classList.add("active");
				let targetContent = document.querySelector(
					`.product-item-detail-tab-content[data-value="${targetValue}"]`
				);
				targetContent.classList.add("active");
			});
		});

		// Создаем медиа условие, проверяющее viewports на ширину не менее 991 пикселей.
		if (mediaQuery.matches) {
			tabs.forEach((tab) => {
				tab.classList.remove("active");
			});

			const panel = document.querySelector('#<?= $itemIds['TABS_PANEL_ID'] ?>');
			panel.parentNode.removeChild(panel);

			const tab = document.querySelector('.product-item-detail-tab-content[data-value=properties]');
			if (tab) {
				tab.classList.add('active');
				tab.querySelector('.content').setAttribute('style', 'display: block;');
			}

		}
	});


	BX.message({
		ECONOMY_INFO_MESSAGE: '<?= GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2') ?>',
		TITLE_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
		TITLE_BASKET_PROPS: '<?= GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
		BASKET_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		BTN_SEND_PROPS: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS') ?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
		BTN_MESSAGE_CLOSE: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP') ?>',
		TITLE_SUCCESSFUL: '<?= GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK') ?>',
		COMPARE_MESSAGE_OK: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
		COMPARE_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
		COMPARE_TITLE: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
		PRODUCT_GIFT_LABEL: '<?= GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL') ?>',
		PRICE_TOTAL_PREFIX: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX') ?>',
		RELATIVE_QUANTITY_MANY: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY']) ?>',
		RELATIVE_QUANTITY_FEW: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW']) ?>',
		SITE_ID: '<?= CUtil::JSEscape($component->getSiteId()) ?>'
	});

	var <?= $obName ?> = new JCCatalogElement(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
</script>
<?php
unset($actualItem, $itemIds, $jsParams);

// Аксессуары
if (!empty($arResult["PROPERTIES"]["G_MODEL"]["VALUE"]))
{
    ?>
    <div class="access-block">
        <?php
        $GLOBALS['accessFilter'] = array('PROPERTY_PRIM' => $arResult["PROPERTIES"]["G_MODEL"]["VALUE"]);

        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "access",
            array(
                "ACTION_VARIABLE" => "action",
                "ADD_PROPERTIES_TO_BASKET" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "BACKGROUND_IMAGE" => "-",
                "BASKET_URL" => "/personal/basket.php",
                "BROWSER_TITLE" => "-",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "N",
                "CACHE_TIME" => "36000000",
                "CACHE_TYPE" => "A",
                "COMPATIBLE_MODE" => "N",
                "DETAIL_URL" => "",
                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "DISPLAY_COMPARE" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_SORT_FIELD" => "sort",
                "ELEMENT_SORT_FIELD2" => "id",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_SORT_ORDER2" => "desc",
                "ENLARGE_PRODUCT" => "STRICT",
                "FILTER_NAME" => "accessFilter",
                "IBLOCK_ID" => "2",
                "IBLOCK_TYPE" => "hnd_catalog",
                "INCLUDE_SUBSECTIONS" => "Y",
                "LAZY_LOAD" => "N",
                "LINE_ELEMENT_COUNT" => "3",
                "LOAD_ON_SCROLL" => "N",
                "MESSAGE_404" => "",
                "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_BTN_LAZY_LOAD" => "Показать ещё",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "MESS_NOT_AVAILABLE" => "Нет в наличии",
                "MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
                "META_DESCRIPTION" => "-",
                "META_KEYWORDS" => "-",
                "OFFERS_LIMIT" => "5",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => ".default",
                "PAGER_TITLE" => "Товары",
                "PAGE_ELEMENT_COUNT" => "18",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRICE_CODE" => array(
                    0 => "PRICES",
                    1 => "OLD_PRICES",
                ),
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                "RCM_TYPE" => "personal",
                "SECTION_CODE" => "",
                "PROPERTY_CODE" => array(
                    0 => "MAXPOWER",
                    1 => "TYPESNOWBLOW",
                    2 => "TYPEGEENERATOR",
                    3 => "MOVTYPE",
                    4 => "GRIPWIDTH",
                    5 => "GRIPHEIGHT",
                    6 => "MASSA",
                    7 => "FUEL_IN",
                    8 => "VFUEL",
                    9 => "TIP_MODELI",
                    10 => "PEREDAYSHIY_VAL",
                    11 => "RYKOJATKA",
                    12 => "RABOCHAY_DLINA_SHTANGI",
                    13 => "REJYSHAYA_NASADKA",
                    14 => "NARYJNIY_DIAMETR",
                    15 => "MODEL_DVS",
                    16 => "OBYEM_DVS",
                    17 => "MPSHNOST_KVT",
                    18 => "EMKOST_TOPLIVNOGO_BAKA",
                    19 => "ZAPYSK",
                    20 => "GABARITY_DSHV",
                    21 => "SYXAYA_MASSA_KG",
                    22 => "DLINA_SHUNY_DM",
                    23 => "MAX_RABOCHA_SKOROST",
                    24 => "MIN_SKOROST_VRASHNIYA",
                    25 => "MIN_SKOROST_DSKLYCHENIYA",
                    26 => "POLEZNAYA_MOSHNOST_DVS",
                    27 => "SISTEMA_PYSKA_DVS",
                    28 => "STEPEN_SJATIYA",
                    29 => "MAKSIMALNAYA_MOSHCHNOST_KVA",
                    30 => "NOMINALNAYA_MOSHCHNOST_KVA",
                    31 => "NOMINALNOE_VYHODNOE_NAPRYAZHENIE_V",
                    32 => "NOMINALNAYA_CHASTOTA_PEREMENNOGO_TOKA_GC",
                    33 => "EHKONOMICHNYJ_REZHIM",
                ),
                "SECTION_ID" => "",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SECTION_URL" => "",
                "SECTION_USER_FIELDS" => array(
                    0 => "",
                    1 => "",
                ),
                "SEF_MODE" => "N",
                "SET_BROWSER_TITLE" => "N",
                "SET_LAST_MODIFIED" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_META_KEYWORDS" => "N",
                "SET_STATUS_404" => "N",
                "SET_TITLE" => "N",
                "SHOW_404" => "N",
                "SHOW_ALL_WO_SECTION" => "Y",
                "SHOW_FROM_SECTION" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "SHOW_SLIDER" => "N",
                "TEMPLATE_THEME" => "blue",
                "USE_ENHANCED_ECOMMERCE" => "N",
                "USE_MAIN_ELEMENT_SECTION" => "N",
                "USE_PRICE_COUNT" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "COMPONENT_TEMPLATE" => "access",
                "PROPERTY_CODE_MOBILE" => array(
                ),
                "ADD_PICT_PROP" => "-",
                "LABEL_PROP" => array(
                )
            ),
            false
        ); ?>
        </div>
    </div>
    <?
}
