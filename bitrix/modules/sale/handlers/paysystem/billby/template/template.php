<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?=Loc::getMessage('SALE_HPS_BILLBY_TITLE')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
<style type="text/css">
	table { border-collapse: collapse; }
	table.acc td { border: 1pt solid #000000; padding: 0 3pt; line-height: 21pt; }
	table.it td { border: 1pt solid #000000; padding: 0 3pt; }
	table.sign td { vertical-align: bottom; }
	table.header td { padding: 0; vertical-align: top; }
</style>
</head>

<?

if ($_REQUEST['BLANK'] == 'Y')
	$blank = true;

$pageWidth  = 595.28;
$pageHeight = 841.89;

$background = '#ffffff';
if ($params['BILLBY_BACKGROUND'])
{
	$path = $params['BILLBY_BACKGROUND'];
	if (intval($path) > 0)
	{
		if ($arFile = CFile::GetFileArray($path))
			$path = $arFile['SRC'];
	}

	$backgroundStyle = $params['BILLBY_BACKGROUND_STYLE'];
	if (!in_array($backgroundStyle, array('none', 'tile', 'stretch')))
		$backgroundStyle = 'none';

	if ($path)
	{
		switch ($backgroundStyle)
		{
			case 'none':
				$background = "url('" . $path . "') 0 0 no-repeat";
				break;
			case 'tile':
				$background = "url('" . $path . "') 0 0 repeat";
				break;
			case 'stretch':
				$background = sprintf(
					"url('%s') 0 0 repeat-y; background-size: %.02fpt %.02fpt",
					$path, $pageWidth, $pageHeight
				);
				break;
		}
	}
}

$margin = array(
	'top' => intval($params['BILLBY_MARGIN_TOP'] ?: 15) * 72/25.4,
	'right' => intval($params['BILLBY_MARGIN_RIGHT'] ?: 15) * 72/25.4,
	'bottom' => intval($params['BILLBY_MARGIN_BOTTOM'] ?: 15) * 72/25.4,
	'left' => intval($params['BILLBY_MARGIN_LEFT'] ?: 20) * 72/25.4
);

$width = $pageWidth - $margin['left'] - $margin['right'];

?>

<body style="margin: 0; padding: 0; background: <?=$background; ?>"<? if ($_REQUEST['PRINT'] == 'Y') { ?> onload="setTimeout(window.print, 0);"<? } ?>>

<div style="margin: 0; padding: <?=join('pt ', $margin); ?>pt; width: <?=$width; ?>pt; background: <?=$background; ?>">

<?if ($params['BILLBY_HEADER_SHOW'] == 'Y'):?>
	<?
	// region Seller info
	$sellerInfo = '';
	$sellerInfoRows = array();
	$sellerInfoName = '';
	if ($params["SELLER_COMPANY_NAME"])
	{
		$sellerInfoName .= $params["SELLER_COMPANY_NAME"];
		if (!empty($sellerInfoName))
			$sellerInfoRows[] = $sellerInfoName;
	}
	unset($sellerInfoName);
	$sellerInfoTaxId = '';
	if ($params['SELLER_COMPANY_INN'])
	{
		$sellerInfoTaxId .= Loc::getMessage('SALE_HPS_BILLBY_INN').': '.$params['SELLER_COMPANY_INN'];
		if (!empty($sellerInfoTaxId))
			$sellerInfoRows[] = $sellerInfoTaxId;
	}
	unset($sellerInfoTaxId);
	$sellerInfoBank = '';
	$sellerBank = '';
	$sellerRs = '';
	if ($params["SELLER_COMPANY_BANK_NAME"])
	{
		$sellerBankCity = '';
		if ($params["SELLER_COMPANY_BANK_CITY"])
		{
			$sellerBankCity = $params["SELLER_COMPANY_BANK_CITY"];
			if (is_array($sellerBankCity))
				$sellerBankCity = implode(', ', $sellerBankCity);
			else
				$sellerBankCity = str_replace(array("\r\n", "\n", "\r"), ', ', strval($sellerBankCity));
		}
		$sellerBank = sprintf(
			"%s %s",
			$params["SELLER_COMPANY_BANK_NAME"],
			$sellerBankCity
		);
		$sellerRs = $params["SELLER_COMPANY_BANK_ACCOUNT"];
	}
	else
	{
		$rsPattern = '/\s*\d{10,100}\s*/';

		$sellerBank = trim(preg_replace($rsPattern, ' ', $params["SELLER_COMPANY_BANK_ACCOUNT"]));

		preg_match($rsPattern, $params["SELLER_COMPANY_BANK_ACCOUNT"], $matches);
		$sellerRs = trim($matches[0]);
	}
	if (!empty($sellerRs))
	{
		$sellerRsPrefix = Loc::getMessage('SALE_HPS_BILLBY_SELLER_ACC_ABBR');
		if (!empty($sellerRsPrefix))
			$sellerRs = $sellerRsPrefix.' '.$sellerRs;
		unset($sellerRsPrefix);
		$sellerInfoBank .= $sellerRs;
	}
	unset($sellerRs);
	if (!empty($sellerBank))
	{
		if (!empty($sellerInfoBank))
			$sellerInfoBank .= ', ';
		$sellerInfoBank .= $sellerBank;
	}
	unset($sellerBank);
	if (!empty($params['SELLER_COMPANY_BANK_BIC']))
	{
		if (!empty($sellerInfoBank))
			$sellerInfoBank .= ', ';
		$sellerInfoBank .= Loc::getMessage('SALE_HPS_BILLBY_SELLER_BANK_BIK').' '.$params['SELLER_COMPANY_BANK_BIC'];
	}
	if (!empty($sellerInfoBank))
		$sellerInfoRows[] = $sellerInfoBank;
	unset($sellerInfoBank);
	$sellerInfoAddr = '';
	if ($params['SELLER_COMPANY_ADDRESS'])
	{
		$sellerAddr = $params['SELLER_COMPANY_ADDRESS'];
		if (is_array($sellerAddr))
			$sellerAddr = implode(', ', $sellerAddr);
		else
			$sellerAddr = str_replace(array("\r\n", "\n", "\r"), ', ', strval($sellerAddr));
		if (!empty($sellerAddr))
			$sellerInfoAddr .= Loc::getMessage('SALE_HPS_BILLBY_ADDR_TITLE').': '.$sellerAddr;
	}
	if ($params["SELLER_COMPANY_PHONE"])
	{
		if (!empty($sellerInfoAddr))
			$sellerInfoAddr .= ', ';
		$phoneTitle = Loc::getMessage('SALE_HPS_BILLBY_PHONE_TITLE');
		if (!empty($phoneTitle))
			$sellerInfoAddr .= $phoneTitle.' ';
		$sellerInfoAddr .= $params["SELLER_COMPANY_PHONE"];
	}
	if (!empty($sellerInfoAddr))
		$sellerInfoRows[] = $sellerInfoAddr;
	unset($sellerInfoAddr);
	if (!empty($sellerInfoRows))
		$sellerInfo = implode('<br>', $sellerInfoRows);
	unset($sellerInfoRows);
	// endregion Seller info
	?>
	<table class="header">
		<tr>
			<td><?= $sellerInfo ?></td>
			<? if ($params["BILLBY_PATH_TO_LOGO"]) { ?>
			<td style="padding-left: 5pt; padding-bottom: 5pt; ">
				<? $imgParams = CFile::_GetImgParams($params['BILLBY_PATH_TO_LOGO']); ?>
				<? $imgWidth = $imgParams['WIDTH'] * 96 / (intval($params['BILLBY_LOGO_DPI']) ?: 96); ?>
				<img src="<?=$imgParams['SRC']; ?>" width="<?=$imgWidth; ?>" />
			</td>
			<? } ?>
		</tr>
	</table>
<?endif;?>
<br>
<br>

<table width="100%">
	<colgroup>
		<col width="50%">
		<col width="0">
		<col width="50%">
	</colgroup>
<?if ($params['BILLBY_HEADER']):?>
	<?
	$dateValue = $params["PAYMENT_DATE_INSERT"];
	if ($dateValue instanceof \Bitrix\Main\Type\Date || $dateValue instanceof \Bitrix\Main\Type\DateTime)
	{
		$dateValue = ToLower(FormatDate('d F Y', $dateValue->getTimestamp()));
		$yearPostfix = Loc::getMessage('SALE_HPS_BILLBY_YEAR_POSTFIX');
		if (!empty($yearPostfix))
			$dateValue .= $yearPostfix;
		unset($yearPostfix);
	}
	else if (is_string($dateValue))
	{
		$timeStampValue = MakeTimeStamp($dateValue);
		if ($timeStampValue !== false)
			$dateValue = ToLower(FormatDate('d F Y', $timeStampValue));
		unset($timeStampValue);
	}
	?>
	<tr>
		<td></td>
		<td style="font-size: 1.6em; font-weight: bold; text-align: center">
			<nobr><?=$params['BILLBY_HEADER'];?> <?=Loc::getMessage('SALE_HPS_BILLBY_SELLER_TITLE', array('#PAYMENT_NUM#' => $params["ACCOUNT_NUMBER"], '#PAYMENT_DATE#' => $dateValue));?>
			</nobr>
		</td>
		<td></td>
	</tr>
<?endif;?>
<? if ($params["BILLBY_ORDER_SUBJECT"]) { ?>
	<tr>
		<td></td>
		<td><?=$params["BILLBY_ORDER_SUBJECT"]; ?></td>
		<td></td>
	</tr>
<? } ?>
<? if ($params["PAYMENT_DATE_PAY_BEFORE"]) { ?>
	<tr>
		<td></td>
		<td>
			<?=Loc::getMessage('SALE_HPS_BILLBY_SELLER_DATE_END', array('#PAYMENT_DATE_END#' => ConvertDateTime($params["PAYMENT_DATE_PAY_BEFORE"], FORMAT_DATE) ?: $params["PAYMENT_DATE_PAY_BEFORE"]));?>
		</td>
		<td></td>
	</tr>
<? } ?>
</table>

<br>
<?

if ($params['BILLBY_PAYER_SHOW'] == 'Y')
{
	$buyerInfo = '';
	$buyerInfoRows = array();
	if ($params['BUYER_PERSON_COMPANY_DOGOVOR'])
	{
		$buyerInfoRows[] =
			Loc::getMessage('SALE_HPS_BILLBY_BUYER_DOGOVOR').': '.$params['BUYER_PERSON_COMPANY_DOGOVOR'].'<br>';
	}
	$buyerInfoName = Loc::getMessage('SALE_HPS_BILLBY_BUYER_TITLE').':';
	if ($params["BUYER_PERSON_COMPANY_NAME"])
	{
		if (!empty($buyerInfoName))
			$buyerInfoName .= ' ';
		$buyerInfoName .= $params["BUYER_PERSON_COMPANY_NAME"];
	}
	if (!empty($buyerInfoName))
		$buyerInfoRows[] = $buyerInfoName;
	unset($buyerInfoName);
	$buyerInfoTaxId = '';
	if ($params['BUYER_PERSON_COMPANY_INN'])
	{
		$buyerInfoTaxId .= Loc::getMessage('SALE_HPS_BILLBY_INN').': '.$params['BUYER_PERSON_COMPANY_INN'];
		if (!empty($buyerInfoTaxId))
			$buyerInfoRows[] = $buyerInfoTaxId;
	}
	unset($buyerInfoTaxId);
	$buyerInfoBank = '';
	$buyerBank = '';
	$buyerRs = '';
	if ($params["BUYER_PERSON_COMPANY_BANK_NAME"])
	{
		$buyerBankCity = '';
		if ($params["BUYER_PERSON_COMPANY_BANK_CITY"])
		{
			$buyerBankCity = $params["BUYER_PERSON_COMPANY_BANK_CITY"];
			if (is_array($buyerBankCity))
				$buyerBankCity = implode(', ', $buyerBankCity);
			else
				$buyerBankCity = str_replace(array("\r\n", "\n", "\r"), ', ', strval($buyerBankCity));
		}
		$buyerBank = sprintf(
			"%s %s",
			$params["BUYER_PERSON_COMPANY_BANK_NAME"],
			$buyerBankCity
		);
		$buyerRs = $params["BUYER_PERSON_COMPANY_BANK_ACCOUNT"];
	}
	else
	{
		$rsPattern = '/\s*\d{10,100}\s*/';

		$buyerBank = trim(preg_replace($rsPattern, ' ', $params["BUYER_PERSON_COMPANY_BANK_ACCOUNT"]));

		preg_match($rsPattern, $params["BUYER_PERSON_COMPANY_BANK_ACCOUNT"], $matches);
		$buyerRs = trim($matches[0]);
	}
	if (!empty($buyerRs))
	{
		$buyerRsPrefix = Loc::getMessage('SALE_HPS_BILLBY_SELLER_ACC_ABBR');
		if (!empty($buyerRsPrefix))
			$buyerRs = $buyerRsPrefix.' '.$buyerRs;
		unset($buyerRsPrefix);
		$buyerInfoBank .= $buyerRs;
	}
	unset($buyerRs);
	if (!empty($buyerBank))
	{
		if (!empty($buyerInfoBank))
			$buyerInfoBank .= ', ';
		$buyerInfoBank .= $buyerBank;
	}
	unset($buyerBank);
	if (!empty($params['BUYER_PERSON_COMPANY_BANK_BIC']))
	{
		if (!empty($buyerInfoBank))
			$buyerInfoBank .= ', ';
		$buyerInfoBank .= Loc::getMessage('SALE_HPS_BILLBY_SELLER_BANK_BIK').' '.$params['BUYER_PERSON_COMPANY_BANK_BIC'];
	}
	if (!empty($buyerInfoBank))
		$buyerInfoRows[] = $buyerInfoBank;
	unset($buyerInfoBank);
	$buyerInfoAddr = '';
	if ($params['BUYER_PERSON_COMPANY_ADDRESS'])
	{
		$buyerAddr = $params['BUYER_PERSON_COMPANY_ADDRESS'];
		if (is_array($buyerAddr))
			$buyerAddr = implode(', ', $buyerAddr);
		else
			$buyerAddr = str_replace(array("\r\n", "\n", "\r"), ', ', strval($buyerAddr));
		if (!empty($buyerAddr))
			$buyerInfoAddr .= Loc::getMessage('SALE_HPS_BILLBY_ADDR_TITLE').': '.$buyerAddr;
	}
	if ($params["BUYER_PERSON_COMPANY_PHONE"])
	{
		if (!empty($buyerInfoAddr))
			$buyerInfoAddr .= ', ';
		$phoneTitle = Loc::getMessage('SALE_HPS_BILLBY_PHONE_TITLE');
		if (!empty($phoneTitle))
			$buyerInfoAddr .= $phoneTitle.' ';
		$buyerInfoAddr .= $params["BUYER_PERSON_COMPANY_PHONE"];
	}
	if ($params["BUYER_PERSON_COMPANY_FAX"])
	{
		if (!empty($buyerInfoAddr))
			$buyerInfoAddr .= ', ';
		$phoneTitle = Loc::getMessage('SALE_HPS_BILLBY_FAX_TITLE');
		if (!empty($phoneTitle))
			$buyerInfoAddr .= $phoneTitle.' ';
		$buyerInfoAddr .= $params["BUYER_PERSON_COMPANY_FAX"];
	}
	if ($params["BUYER_PERSON_COMPANY_NAME_CONTACT"])
	{
		if (!empty($buyerInfoAddr))
			$buyerInfoAddr .= ', ';
		$buyerInfoAddr .= $params["BUYER_PERSON_COMPANY_NAME_CONTACT"];
	}
	if (!empty($buyerInfoAddr))
		$buyerInfoRows[] = $buyerInfoAddr;
	unset($buyerInfoAddr);
	if (!empty($buyerInfoRows))
	{
		$buyerInfo = implode('<br>', $buyerInfoRows);
		echo $buyerInfo;
	}
	unset($buyerInfoRows);
}
?>

<br>
<br>

<?
$arCurFormat = CCurrencyLang::GetCurrencyFormat($params['CURRENCY']);
$currency = preg_replace('/(^|[^&])#/', '${1}', $arCurFormat['FORMAT_STRING']);

$cells = array();
$props = array();

$n = 0;
$sum = 0.00;
$vat = 0;
$cntBasketItem = 0;

$columnList = array('NUMBER', 'NAME', 'QUANTITY', 'MEASURE', 'PRICE', 'VAT_RATE', 'SUM');
$arCols = array();
$vatRateColumn = 0;
foreach ($columnList as $column)
{
	if ($params['BILLBY_COLUMN_'.$column.'_SHOW'] == 'Y')
	{
		$caption = $params['BILLBY_COLUMN_'.$column.'_TITLE'];
		if (in_array($column, array('PRICE', 'SUM')))
			$caption .= ', '.$currency;

		$arCols[$column] = array(
			'NAME' => htmlspecialcharsbx($caption),
			'SORT' => $params['BILLBY_COLUMN_'.$column.'_SORT']
		);
	}
}
if ($params['USER_COLUMNS'])
{
	$columnList = array_merge($columnList, array_keys($params['USER_COLUMNS']));
	foreach ($params['USER_COLUMNS'] as $id => $val)
	{
		$arCols[$id] = array(
			'NAME' => htmlspecialcharsbx($val['NAME']),
			'SORT' => $val['SORT']
		);
	}
}

uasort($arCols, function ($a, $b) {return ($a['SORT'] < $b['SORT']) ? -1 : 1;});

$arColumnKeys = array_keys($arCols);
$columnCount = count($arColumnKeys);

foreach ($params['BASKET_ITEMS'] as $basketItem)
{
	$productName = $basketItem["NAME"];
	if ($productName == "OrderDelivery")
		$productName = Loc::getMessage('SALE_HPS_BILLBY_DELIVERY');
	else if ($productName == "OrderDiscount")
		$productName = Loc::getMessage('SALE_HPS_BILLBY_DISCOUNT');

	if ($basketItem['IS_VAT_IN_PRICE'])
		$basketItemPrice = $basketItem['PRICE'];
	else
		$basketItemPrice = $basketItem['PRICE']*(1 + $basketItem['VAT_RATE']);

	$cells[++$n] = array();
	foreach ($arCols as $columnId => $col)
	{
		$data = null;

		switch ($columnId)
		{
			case 'NUMBER':
				$data = $n;
				break;
			case 'NAME':
				$data = htmlspecialcharsbx($productName);
				break;
			case 'QUANTITY':
				$data = roundEx($basketItem['QUANTITY'], SALE_VALUE_PRECISION);
				break;
			case 'MEASURE':
				$data = $basketItem["MEASURE_NAME"] ? htmlspecialcharsbx($basketItem["MEASURE_NAME"]) : Loc::getMessage('SALE_HPS_BILLBY_BASKET_MEASURE_DEFAULT');
				break;
			case 'PRICE':
				$data = SaleFormatCurrency($basketItem['PRICE'], $basketItem['CURRENCY'], true);
				break;
			case 'VAT_RATE':
				$data = roundEx($basketItem['VAT_RATE'] * 100, SALE_VALUE_PRECISION)."%";
				break;
			case 'SUM':
				$data = SaleFormatCurrency($basketItemPrice * $basketItem['QUANTITY'], $basketItem['CURRENCY'], true);
				break;
			default :
				$data = ($basketItem[$columnId]) ?: '';
		}

		if ($data !== null)
			$cells[$n][$columnId] = $data;
	}
	$props[$n] = array();
	if ($basketItem['PROPS'])
	{
		foreach ($basketItem['PROPS'] as $basketPropertyItem)
		{
			if ($basketPropertyItem['CODE'] == 'CATALOG.XML_ID' || $basketPropertyItem['CODE'] == 'PRODUCT.XML_ID')
				continue;
			$props[$n][] = htmlspecialcharsbx(sprintf("%s: %s", $basketPropertyItem["NAME"], $basketPropertyItem["VALUE"]));
		}
	}
	$sum += doubleval($basketItem['PRICE'] * $basketItem['QUANTITY']);
	$vat = max($vat, $basketItem['VAT_RATE']);
}

if ($vat <= 0)
{
	unset($arCols['VAT_RATE']);
	$columnCount = count($arCols);
	$arColumnKeys = array_keys($arCols);
	foreach ($cells as $i => $cell)
		unset($cells[$i]['VAT_RATE']);
}

if ($params['DELIVERY_PRICE'] > 0)
{
	$deliveryItem = Loc::getMessage('SALE_HPS_BILLBY_DELIVERY');

	if ($params['DELIVERY_NAME'])
		$deliveryItem .= sprintf(" (%s)", $params['DELIVERY_NAME']);
	$cells[++$n] = array();
	foreach ($arCols as $columnId => $caption)
	{
		$data = null;

		switch ($columnId)
		{
			case 'NUMBER':
				$data = $n;
				break;
			case 'NAME':
				$data = htmlspecialcharsbx($deliveryItem);
				break;
			case 'QUANTITY':
				$data = 1;
				break;
			case 'MEASURE':
				$data = '';
				break;
			case 'PRICE':
				$data = SaleFormatCurrency($params['DELIVERY_PRICE'], $params['CURRENCY'], true);
				break;
			case 'VAT_RATE':
				$data = roundEx($vat * 100, SALE_VALUE_PRECISION)."%";
				break;
			case 'SUM':
				$data = SaleFormatCurrency($params['DELIVERY_PRICE'], $params['CURRENCY'], true);
				break;
		}

		if ($data !== null)
			$cells[$n][$columnId] = $data;
	}
	$sum += doubleval($params['DELIVERY_PRICE']);
}

if ($params['BILLBY_TOTAL_SHOW'] == 'Y')
{
	$cntBasketItem = $n;
	if ($sum < $params['SUM'])
	{
		$cells[++$n] = array();
		for ($i = 0; $i < $columnCount; $i++)
			$cells[$n][$arColumnKeys[$i]] = null;

		$cells[$n][$arColumnKeys[$columnCount-2]] = htmlspecialcharsbx(Loc::getMessage('SALE_HPS_BILLBY_SUBTOTAL'));
		$cells[$n][$arColumnKeys[$columnCount-1]] = SaleFormatCurrency($sum, $params['CURRENCY'], true);
	}

	if ($params['TAXES'])
	{
		foreach ($params['TAXES'] as $tax)
		{
			$cells[++$n] = array();
			for ($i = 0; $i < $columnCount; $i++)
				$cells[$n][$arColumnKeys[$i]] = null;

			$cells[$n][$arColumnKeys[$columnCount-2]] = htmlspecialcharsbx(sprintf(
					"%s%s%s:",
					($tax["IS_IN_PRICE"] == "Y") ? Loc::getMessage('SALE_HPS_BILLBY_INCLUDING') : "",
					$tax["TAX_NAME"],
					($vat <= 0 && $tax["IS_PERCENT"] == "Y")
							? sprintf(' (%s%%)', roundEx($tax["VALUE"], SALE_VALUE_PRECISION))
							: ""
			));
			$cells[$n][$arColumnKeys[$columnCount-1]] = SaleFormatCurrency($tax["VALUE_MONEY"], $params['CURRENCY'], true);
		}
	}

	if (!$params['TAXES'])
	{
		$cells[++$n] = array();
		for ($i = 0; $i < $columnCount; $i++)
			$cells[$n][$i] = null;

		$cells[$n][$arColumnKeys[$columnCount-2]] = htmlspecialcharsbx(Loc::getMessage('SALE_HPS_BILLBY_TOTAL_VAT_RATE'));
		$cells[$n][$arColumnKeys[$columnCount-1]] = htmlspecialcharsbx(Loc::getMessage('SALE_HPS_BILLBY_TOTAL_VAT_RATE_NO'));
	}
	if ($params['SUM_PAID'] > 0)
	{
		$cells[++$n] = array();
		for ($i = 0; $i < $columnCount; $i++)
			$cells[$n][$arColumnKeys[$i]] = null;

		$cells[$n][$arColumnKeys[$columnCount-2]] = Loc::getMessage('SALE_HPS_BILLBY_TOTAL_PAID');
		$cells[$n][$arColumnKeys[$columnCount-1]] = SaleFormatCurrency($params['SUM_PAID'], $params['CURRENCY'], true);
	}
	if ($params['DISCOUNT_PRICE'] > 0)
	{
		$cells[++$n] = array();
		for ($i = 0; $i < $columnCount; $i++)
			$cells[$n][$arColumnKeys[$i]] = null;

		$cells[$n][$arColumnKeys[$columnCount-2]] = Loc::getMessage('SALE_HPS_BILLBY_TOTAL_DISCOUNT');
		$cells[$n][$arColumnKeys[$columnCount-1]] = SaleFormatCurrency($params['DISCOUNT_PRICE'], $params['CURRENCY'], true);
	}

	$cells[++$n] = array();
	for ($i = 0; $i < $columnCount; $i++)
		$cells[$n][$arColumnKeys[$i]] = null;

	$cells[$n][$arColumnKeys[$columnCount-2]] = Loc::getMessage('SALE_HPS_BILLBY_TOTAL_SUM');
	$cells[$n][$arColumnKeys[$columnCount-1]] = SaleFormatCurrency($params['SUM'], $params['CURRENCY'], true);
}
?>
<table class="it" width="100%">
	<tr>
		<?foreach ($arCols as $columnId => $col):?>
			<td><?=$col['NAME'];?></td>
		<?endforeach;?>
	</tr>
<?

$rowsCnt = count($cells);
for ($n = 1; $n <= $rowsCnt; $n++):

	$accumulated = 0;
?>
	<tr valign="top">
		<?foreach ($arCols as $columnId => $col):?>
		<?
			if (!is_null($cells[$n][$columnId]))
			{
				if ($columnId === 'NUMBER')
				{?>
					<td align="center"><?=$cells[$n][$columnId];?></td>
				<?}
				elseif ($columnId === 'NAME')
				{
				?>
					<td align="<?=($n > $cntBasketItem) ? 'right' : 'left';?>"
						style="word-break: break-word; word-wrap: break-word; <? if ($accumulated) {?>border-width: 0pt 1pt 0pt 0pt; <? } ?>"
						<? if ($accumulated) { ?>colspan="<?=($accumulated+1); ?>"<? $accumulated = 0; } ?>>
						<?=$cells[$n][$columnId]; ?>
						<? if (isset($props[$n]) && is_array($props[$n])) { ?>
						<? foreach ($props[$n] as $property) { ?>
						<br>
						<small><?=$property; ?></small>
						<? } ?>
						<? } ?>
					</td>
				<?}
				else
				{
					if (!is_null($cells[$n][$columnId]))
					{
						if ($columnId != 'VAT_RATE' || $vat > 0 || is_null($cells[$n][$columnId]) || $n > $cntBasketItem)
						{ ?>
							<td align="right"
								<? if ($accumulated) { ?>
								style="border-width: 0pt 1pt 0pt 0pt"
								colspan="<?=(($columnId == 'VAT_RATE' && $vat <= 0) ? $accumulated : $accumulated+1); ?>"
								<? $accumulated = 0; } ?>>
								<?if ($columnId == 'SUM' || $columnId == 'PRICE'):?>
									<nobr><?=$cells[$n][$columnId];?></nobr>
								<?else:?>
									<?=$cells[$n][$columnId]; ?>
								<?endif;?>
							</td>
						<? }
					}
					else
					{
						$accumulated++;
					}
				}
			}
			else
			{
				$accumulated++;
			}
		?>
	<?endforeach;?>
	</tr>

<?endfor;?>
</table>
<br>

<?if ($params['BILLBY_TOTAL_SHOW'] == 'Y'):?>
	<?=Loc::getMessage(
			'SALE_HPS_BILLBY_BASKET_TOTAL',
			array(
					'#BASKET_COUNT#' => $cntBasketItem,
					'#BASKET_PRICE#' => SaleFormatCurrency($params['SUM'], $params['CURRENCY'], false)
			)
	);?>
	<br>

	<b>
	<?

	if (in_array($params['CURRENCY'], array("RUR", "RUB", "UAH", "KZT", "BYN")))
	{
		echo Number2Word_Rus($params['SUM'], "Y", $params['CURRENCY']);
	}
	else
	{
		echo SaleFormatCurrency(
			$params['SUM'],
			$params['CURRENCY'],
			false
		);
	}

	?>
	</b>
<?endif;?>
<br>
<br>

<? if ($params["BILLBY_COMMENT1"] || $params["BILLBY_COMMENT2"]) { ?>
<br>
	<? if ($params["BILLBY_COMMENT1"]) { ?>
	<?=nl2br(HTMLToTxt(preg_replace(
		array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
		htmlspecialcharsback($params["BILLBY_COMMENT1"])
	), '', array(), 0)); ?>
	<br>
	<br>
	<? } ?>
	<? if ($params["BILLBY_COMMENT2"]) { ?>
	<?=nl2br(HTMLToTxt(preg_replace(
		array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
		htmlspecialcharsback($params["BILLBY_COMMENT2"])
	), '', array(), 0)); ?>
	<br>
	<br>
	<? } ?>
<? } ?>

<br>
<br>

<?if ($params['BILLBY_SIGN_SHOW'] == 'Y'):?>
	<? if (!$blank) { ?>
	<div style="position: relative; "><?=CFile::ShowImage(
			$params["BILLBY_PATH_TO_STAMP"],
		160, 160,
		'style="position: absolute; left: 40pt; "'
	); ?></div>
	<? } ?>

	<div style="position: relative">
		<table class="sign">
			<? if ($params["SELLER_COMPANY_DIRECTOR_POSITION"]) { ?>
			<tr>
				<td style="width: 150pt; font-weight: bold;"><?= $params["SELLER_COMPANY_DIRECTOR_POSITION"] ?></td>
				<td style="width: 160pt; border-bottom: 1pt solid #000000; text-align: center; ">
					<? if (!$blank) { ?>
					<?=CFile::ShowImage($params["SELLER_COMPANY_DIR_SIGN"], 200, 50); ?>
					<? } ?>
				</td>
				<td>
					<? if ($params["SELLER_COMPANY_DIRECTOR_NAME"]) { ?>
					(<?=$params["SELLER_COMPANY_DIRECTOR_NAME"]; ?>)
					<? } ?>
				</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<? } ?>
			<? if ($params["SELLER_COMPANY_ACCOUNTANT_POSITION"]) { ?>
			<tr>
				<td style="width: 150pt; font-weight: bold;"><?= $params["SELLER_COMPANY_ACCOUNTANT_POSITION"] ?></td>
				<td style="width: 160pt; border-bottom: 1pt solid #000000; text-align: center; ">
					<? if (!$blank) { ?>
					<?=CFile::ShowImage($params["SELLER_COMPANY_ACC_SIGN"], 200, 50); ?>
					<? } ?>
				</td>
				<td>
					<? if ($params["SELLER_COMPANY_ACCOUNTANT_NAME"]) { ?>
					(<?=$params["SELLER_COMPANY_ACCOUNTANT_NAME"]; ?>)
					<? } ?>
				</td>
			</tr>
			<? } ?>
		</table>
	</div>
<?endif;?>

</div>

</body>
</html>