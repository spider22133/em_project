<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:oas="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsg="http://WSGetAccountMovementList.ALBO.CS.ws.alfabank.ru" xmlns:wsg1="http://WSGetAccountMovementListTypes.ALBO.CS.ws.alfabank.ru" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	<soapenv:Header>
	</soapenv:Header>
	<soapenv:Body>
		<wsg:WSGetAccountMovementListStatus>
			<inCommonParms>
				<externalSystemCode><?=$params['ALFABANK_EXTERNAL_SYSTEM_CODE'];?></externalSystemCode>
				<externalUserCode><?=$params['ALFABANK_EXTERNAL_USER_CODE'];?></externalUserCode>
			</inCommonParms>
			<inParms>
				<wsg1:requestId><?=$params['REQUEST_ID'];?></wsg1:requestId>
			</inParms>
		</wsg:WSGetAccountMovementListStatus>
	</soapenv:Body>
</soapenv:Envelope>
