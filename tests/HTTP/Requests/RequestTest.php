<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\HTTP\Requests;

use Keruald\OmniTools\HTTP\Requests\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\Request::getRemoteAddress
     * @backupGlobals enabled
     */
    public function testGetRemoteAddress () : void {
        $this->assertEmpty(Request::getRemoteAddress());

        $_SERVER = [
            'REMOTE_ADDR' => '10.0.0.2',
        ];
        $this->assertEquals('10.0.0.2', Request::getRemoteAddress());

        $_SERVER += [
            'HTTP_X_FORWARDED_FOR' => '10.0.0.3',
            'HTTP_CLIENT_IP' => '10.0.0.4',
        ];
        $this->assertEquals(
            '10.0.0.3', Request::getRemoteAddress(),
            "HTTP_X_FORWARDED_FOR must be prioritized."
        );
    }

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\Request::getAcceptedLanguages
     * @backupGlobals enabled
     */
    public function testGetAcceptedLanguages () : void {
        $_SERVER = [
            'HTTP_ACCEPT_LANGUAGE' => 'fr,en-US;q=0.7,en;q=0.3',
        ];

        $this->assertEquals(
            ["fr", "en-US", "en"],
            Request::getAcceptedLanguages()
        );
    }

    /**
     * @backupGlobals enabled
     * @dataProvider provideServerURLs
     */
    public function testGetServerURL (array $server, string $url) : void {
        $_SERVER = $server;

        $this->assertEquals($url, Request::getServerURL());
    }

    ///
    /// Data providers
    ///

    public function provideServerURLs () : iterable {
        yield [[], "http://localhost"];
        yield [["UNRELATED" => "ANYTHING"], "http://localhost"];

        yield [
            [
                "SERVER_PORT" => "80",
                "SERVER_NAME" => "acme.tld",
            ],
            "http://acme.tld"
        ];

        yield [
            [
                "SERVER_PORT" => "443",
                "SERVER_NAME" => "acme.tld",
            ],
            "https://acme.tld"
        ];

        yield [
            [
                "SERVER_PORT" => "80",
                "SERVER_NAME" => "acme.tld",
                "HTTP_X_FORWARDED_PROTO" => "https",
            ],
            "https://acme.tld"
        ];

        yield [
            [
                "SERVER_PORT" => "80",
                "SERVER_NAME" => "acme.tld",
                "HTTP_FORWARDED" => "for=192.0.2.43, for=\"[2001:db8:cafe::17]\", proto=https, by=203.0.113.43",
            ],
            "https://acme.tld"
        ];

        yield [
            [
                "SERVER_PORT" => "8443",
                "SERVER_NAME" => "acme.tld",
                "HTTPS" => "on",
            ],
            "https://acme.tld:8443"
        ];


    }


}
