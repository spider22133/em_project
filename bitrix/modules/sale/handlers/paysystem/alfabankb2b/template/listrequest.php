<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:oas="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsc="http://WSCreateAccountMovementListRequest.ALBO.CS.ws.alfabank.ru" xmlns:wsc1="http://WSCreateAccountMovementListRequestTypes.ALBO.CS.ws.alfabank.ru" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	<soapenv:Header>
	</soapenv:Header>
	<soapenv:Body>
		<wsc:WSCreateAccountMovementListRequestAdd>
			<inCommonParms>
				<externalSystemCode><?=$params['ALFABANK_EXTERNAL_SYSTEM_CODE'];?></externalSystemCode>
				<externalUserCode><?=$params['ALFABANK_EXTERNAL_USER_CODE'];?></externalUserCode>
			</inCommonParms>
			<inParms>
				<wsc1:accountNumber><?=$params['SELLER_COMPANY_BANK_ACCOUNT'];?></wsc1:accountNumber>
				<wsc1:startDate><?=$params['START_DATE'];?></wsc1:startDate>
				<wsc1:endDate><?=$params['END_DATE'];?></wsc1:endDate>
			</inParms>
		</wsc:WSCreateAccountMovementListRequestAdd>
	</soapenv:Body>
</soapenv:Envelope>
