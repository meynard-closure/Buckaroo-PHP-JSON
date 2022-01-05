<?php

namespace SeBuDesign\BuckarooJson\Responses;

use DateTime;
use Exception;
use SeBuDesign\BuckarooJson\Helpers\StatusCodeHelper;

/**
 * Class TransactionResponse
 *
 * @package SeBuDesign\BuckarooJson\Responses
 *
 * @method string|boolean getInvoice()
 * @method string|boolean getServiceCode()
 * @method string|boolean getCurrency()
 * @method float getAmountDebit()
 * @method float getAmountCredit()
 * @method string|boolean getTransactionType()
 * @method integer|boolean getMutationType()
 * @method string|boolean getOrder()
 * @method string|boolean getIssuingCountry()
 * @method string|boolean getCustomerName()
 * @method string|boolean getPayerHash()
 * @method string|boolean getPaymentKey()
 */
class TransactionResponse
{
    const REQUIRED_ACTION_REDIRECT = 'redirect';

    protected $aResponseData;

    /**
     * TransactionResponse constructor.
     *
     * @param array $aData The response data
     */
    public function __construct(array $aData)
    {
        $this->aResponseData = $aData;
    }

    /**
     * Get the transaction key
     *
     * @return bool|string
     */
    public function getTransactionKey()
    {
        $mTransactionKey = false;
        if (isset($this->aResponseData['Key'])) {
            $mTransactionKey = $this->aResponseData['Key'];
        }

        return $mTransactionKey;
    }

    /**
     * Get the current status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        $iCode = 0;
        if ($this->hasStatusCode()) {
            $iCode = $this->aResponseData['Status']['Code']['Code'];
        }

        return $iCode;
    }

    /**
     * Get the current status code description
     *
     * @return bool|string
     */
    public function getStatusCodeDescription()
    {
        if ($this->hasStatusCode()) {
            return $this->aResponseData['Status']['Code']['Description'];
        }

        return false;
    }

    /**
     * Get the current status code
     *
     * @return int
     */
    public function getStatusSubCode()
    {
        $iCode = 0;
        if ($this->hasStatusSubCode()) {
            $iCode = $this->aResponseData['Status']['SubCode']['Code'];
        }

        return $iCode;
    }

    /**
     * Get the current status code description
     *
     * @return bool|string
     */
    public function getStatusSubCodeDescription()
    {
        if ($this->hasStatusSubCode()) {
            return $this->aResponseData['Status']['SubCode']['Description'];
        }

        return false;
    }

    /**
     * Does the response have a status code?
     *
     * @return bool
     */
    protected function hasStatusCode(): bool
    {
        return isset($this->aResponseData['Status'], $this->aResponseData['Status']['Code'], $this->aResponseData['Status']['Code']['Code']);
    }

    /**
     * Does the response have a sub status code?
     *
     * @return bool
     */
    protected function hasStatusSubCode(): bool
    {
        return isset($this->aResponseData['Status'], $this->aResponseData['Status']['SubCode'], $this->aResponseData['Status']['SubCode']['Code']);
    }

    /**
     * Get the date and time of the last status change
     *
     * @return bool|DateTime
     * @throws Exception
     */
    public function getDateTimeOfStatusChange()
    {
        $mDateTime = false;
        if (isset($this->aResponseData['Status'], $this->aResponseData['Status']['DateTime'])) {
            $mDateTime = new DateTime($this->aResponseData['Status']['DateTime']);
        }

        return $mDateTime;
    }

    /**
     * Does the transaction have a required action
     *
     * @return bool
     */
    public function hasRequiredAction(): bool
    {
        return isset($this->aResponseData['RequiredAction']);
    }

    /**
     * Does the user has to be redirected?
     *
     * @return bool
     */
    public function hasToRedirect(): bool
    {
        $bHasToRedirect = false;

        if (
            $this->hasRequiredAction() &&
            isset($this->aResponseData['RequiredAction']['Name']) &&
            strtolower($this->aResponseData['RequiredAction']['Name']) == self::REQUIRED_ACTION_REDIRECT
        ) {
            $bHasToRedirect = true;
        }

        return $bHasToRedirect;
    }

    /**
     * Get the required action name
     *
     * @return string|boolean
     */
    public function getRequiredActionName()
    {
        $mRequiredActionName = false;

        if (
            $this->hasRequiredAction() &&
            isset($this->aResponseData['RequiredAction']['Name'])
        ) {
            $mRequiredActionName = $this->aResponseData['RequiredAction']['Name'];
        }

        return $mRequiredActionName;
    }

    /**
     * Get the redirect url
     *
     * @return bool|string
     */
    public function getRedirectUrl()
    {
        $mUrl = false;

        if ($this->hasToRedirect()) {
            $mUrl = $this->aResponseData['RequiredAction']['RedirectURL'];
        }

        return $mUrl;
    }

    /**
     * Get the requested information
     *
     * @return array
     */
    public function getRequestedInformation()
    {
        $aInformation = [];

        if ($this->hasRequiredAction() && !is_null($this->aResponseData['RequiredAction']['RequestedInformation'])) {
            $aInformation = $this->aResponseData['RequiredAction']['RequestedInformation'];
        }

        return $aInformation;
    }

    /**
     * Does this request has to pay a remainder
     *
     * @return bool
     */
    public function hasToPayRemainder(): bool
    {
        return (isset($this->aResponseData['RequiredAction']['PayRemainderDetails']) && !is_null($this->aResponseData['RequiredAction']['PayRemainderDetails']));
    }

    /**
     * Get the remainder amount
     *
     * @return float
     */
    public function getRemainderAmount(): float
    {
        $fRemainderAmount = 0.0;

        if ($this->hasToPayRemainder() && isset($this->aResponseData['RequiredAction']['PayRemainderDetails']['RemainderAmount'])) {
            $fRemainderAmount = $this->aResponseData['RequiredAction']['PayRemainderDetails']['RemainderAmount'];
        }

        return $fRemainderAmount;
    }

    /**
     * Get the remainder currency
     *
     * @return boolean|string
     */
    public function getRemainderCurrency()
    {
        return $this->getPayRemainderDetail('Currency');
    }

    /**
     * Get the remainder group transaction
     *
     * @return boolean|string
     */
    public function getRemainderGroupTransaction()
    {
        return $this->getPayRemainderDetail('GroupTransaction');
    }

    /**
     * Get item out of the PayRemainderDetails array
     *
     * @param string $sDetailKey The key of the PayRemainderDetails item to retrieve
     *
     * @return mixed
     */
    protected function getPayRemainderDetail(string $sDetailKey)
    {
        $mValue = false;

        if ($this->hasToPayRemainder() && isset($this->aResponseData['RequiredAction']['PayRemainderDetails'][$sDetailKey])) {
            $mValue = $this->aResponseData['RequiredAction']['PayRemainderDetails'][$sDetailKey];
        }

        return $mValue;
    }

    /**
     * Do we have a specific type of parameters
     *
     * @param string $sParameterType The parameter type
     * @param string $sList The list name
     *
     * @return bool
     */
    public function hasParameterType(string $sParameterType, string $sList): bool
    {
        return (isset($this->aResponseData[$sParameterType], $this->aResponseData[$sParameterType][$sList]) && is_array($this->aResponseData[$sParameterType][$sList]) && count($this->aResponseData[$sParameterType][$sList]) > 0);
    }

    /**
     * Get all the parameters from a specific type
     *
     * @param string $sParameterType The parameter type
     * @param string $sList The list name
     *
     * @return array
     */
    public function getParametersFromType(string $sParameterType, string $sList): array
    {
        $aParameters = [];

        if ($this->hasParameterType($sParameterType, $sList)) {
            foreach ($this->aResponseData[$sParameterType][$sList] as $aListData) {
                $aParameters[$aListData['Name']] = $aListData['Value'];
            }
        }

        return $aParameters;
    }

    /**
     * Get the custom parameters
     *
     * @return array
     */
    public function getCustomParameters(): array
    {
        return $this->getParametersFromType('CustomParameters', 'List');
    }

    /**
     * Get the custom parameters
     *
     * @return array
     */
    public function getAdditionalParameters(): array
    {
        return $this->getParametersFromType('AdditionalParameters', 'AdditionalParameter');
    }

    /**
     * Get the parameters of a specific service
     *
     * @param string $sName The name of the service
     *
     * @return bool|array
     */
    public function getServiceParameters(string $sName)
    {
        $mResult = false;

        $aService = $this->getService($sName);
        if ($aService !== false) {
            $mResult = $aService['Parameters'];
        }

        return $mResult;
    }

    /**
     * Get a service by name
     *
     * @param string $sName The name of the service
     *
     * @return bool|array
     */
    public function getService(string $sName)
    {
        $mResult = false;

        foreach ($this->getServices() as $aService) {
            if ($aService['Name'] == $sName) {
                $mResult = $aService;
                break;
            }
        }

        return $mResult;
    }

    /**
     * Get all the services
     *
     * @return array
     */
    public function getServices(): array
    {
        $aServices = [];

        if (isset($this->aResponseData['Services']) && is_array($this->aResponseData['Services'])) {
            foreach ($this->aResponseData['Services'] as $aService) {

                $aServiceParameters = [];
                if (is_array($aService['Parameters'])) {
                    foreach ($aService[ 'Parameters' ] as $aServiceParameter) {
                        $aServiceParameters[ $aServiceParameter[ 'Name' ] ] = $aServiceParameter[ 'Value' ];
                    }
                }

                $aService['Parameters'] = $aServiceParameters;
                $aServices[] = $aService;
            }
        }

        return $aServices;
    }

    /**
     * Is this a test transaction?
     *
     * @return boolean|null
     */
    public function isTest(): ?bool
    {
        return ($this->aResponseData['IsTest'] ?? null);
    }

    /**
     * Get the related transactions
     *
     * @return array
     */
    public function getRelatedTransactions(): array
    {
        return (!isset($this->aResponseData['RelatedTransactions']) || is_null($this->aResponseData['RelatedTransactions']) ? [] : $this->aResponseData['RelatedTransactions']);
    }

    /**
     * Is this the start of recurring payments?
     *
     * @return boolean
     */
    public function hasStartedRecurringPayment(): bool
    {
        return (!isset($this->aResponseData['StartRecurrent']) || is_null($this->aResponseData['StartRecurrent']) ? false : $this->aResponseData['StartRecurrent']);
    }

    /**
     * Is this a recurring payments?
     *
     * @return boolean
     */
    public function isRecurringPayment(): bool
    {
        return (!isset($this->aResponseData['Recurring']) || is_null($this->aResponseData['Recurring']) ? false : $this->aResponseData['Recurring']);
    }

    /**
     * Is this the transaction cancelable?
     *
     * @return boolean
     */
    public function isCancelable(): bool
    {
        return (!isset($this->aResponseData['IsCancelable']) || is_null($this->aResponseData['IsCancelable']) ? false : $this->aResponseData['IsCancelable']);
    }

    /**
     * Is there a consumer message?
     *
     * @return bool
     */
    public function hasConsumerMessage(): bool
    {
        return (!isset($this->aResponseData['ConsumerMessage']) || is_null($this->aResponseData['ConsumerMessage']) ? false : true);
    }

    /**
     * Does the client need to read the message?
     *
     * @return bool
     */
    public function hasToReadConsumerMessage(): bool
    {
        $bHasToRead = false;

        if ($this->hasConsumerMessage()) {
            $bHasToRead = (boolean) $this->aResponseData['ConsumerMessage']['MustRead'];
        }

        return $bHasToRead;
    }

    /**
     * Get the consumer message
     *
     * @return bool|array
     */
    public function getConsumerMessage()
    {
        $mConsumerMessage = false;

        if ($this->hasConsumerMessage()) {
            $mConsumerMessage = $this->aResponseData['ConsumerMessage'];
        }

        return $mConsumerMessage;
    }

    /**
     * Check if there are any errors from a specific type
     *
     * @param string $sErrorType The error type
     *
     * @return bool
     */
    public function hasErrorsFromType(string $sErrorType): bool
    {
        return (
            isset($this->aResponseData['RequestErrors'], $this->aResponseData['RequestErrors'][$sErrorType]) &&
            count($this->aResponseData['RequestErrors'][$sErrorType]) > 0
        );
    }

    /**
     * Get the errors from a specific type
     *
     * @param string $sErrorType The error type
     *
     * @return array
     */
    public function getErrorsFromType(string $sErrorType): array
    {
        $aErrors = [];

        if ($this->hasErrorsFromType($sErrorType)) {
            $aErrors = $this->aResponseData['RequestErrors'][$sErrorType];
        }

        return $aErrors;
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        $aErrors = array_merge(
            $this->getErrorsFromType('ChannelErrors'),
            $this->getErrorsFromType('ServiceErrors'),
            $this->getErrorsFromType('ActionErrors'),
            $this->getErrorsFromType('ParameterErrors'),
            $this->getErrorsFromType('CustomParameterErrors')
        );

        return $aErrors;
    }

    /**
     * Are there any errors?
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        $bErrors = false;

        if (
            count($this->getErrors()) > 0 ||
            $this->getStatusCode() == StatusCodeHelper::STATUS_VALIDATION_FAILURE ||
            $this->getStatusCode() == StatusCodeHelper::STATUS_TECHNICAL_FAILURE
        ) {
            $bErrors = true;
        }

        return $bErrors;
    }

    /**
     * Dynamically get values
     *
     * @param string $sName      The name of the called function
     * @param array $aArguments The arguments passed to the function
     *
     * @return boolean|string|$this
     */
    public function __call(string $sName, array $aArguments)
    {
        // Check if the method exists in the class
        if (!method_exists($this, $sName)) {
            if (strpos($sName, 'get') === 0) {
                // With a method that starts with get, get the data

                $sName = str_replace('get', '', $sName);

                return ($this->aResponseData[$sName] ?? false);
            }
        }
    }
}
