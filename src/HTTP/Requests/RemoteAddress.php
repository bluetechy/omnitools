<?php
declare(strict_types=1);

namespace Keruald\OmniTools\HTTP\Requests;

class RemoteAddress {

    /**
     * @var string
     */
    private $remoteAddress;

    ///
    /// Constructor
    ///

    public function __construct (string $remoteAddress = '') {
        $this->remoteAddress = $remoteAddress;
    }

    public static function fromServer () : self {
        return new self(self::extractRemoteAddressesFromHeaders());
    }

    ///
    /// Format methods
    ///

    public function has () : bool {
        return $this->remoteAddress !== "";
    }

    public function getClientAddress () : string {
        // Header contains 'clientIP, proxyIP, anotherProxyIP'
        //              or 'clientIP proxyIP anotherProxyIP'
        // The first value is so the one to return.
        // See draft-ietf-appsawg-http-forwarded-10.
        $ips = preg_split("/[\s,]+/", $this->remoteAddress, 2);
        return trim($ips[0]);
    }

    public function getAll () : string {
        return $this->remoteAddress;
    }

    ///
    /// Helper methods to determine the remote address
    ///

    /**
     * Allows to get all the remote addresses from relevant headers
     */
    public static function extractRemoteAddressesFromHeaders () : string {
        foreach (self::listRemoteAddressHeaders() as $candidate) {
            if (isset($_SERVER[$candidate])) {
                return $_SERVER[$candidate];
            }
        }

        return "";
    }

    ///
    /// Data sources
    ///

    public static function listRemoteAddressHeaders () : array {
        return [
            // Standard header provided by draft-ietf-appsawg-http-forwarded-10
            'HTTP_X_FORWARDED_FOR',

            // Legacy headers
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_FORWARDED',

            // Default header if no proxy information could be detected
            'REMOTE_ADDR',
        ];
    }
}
