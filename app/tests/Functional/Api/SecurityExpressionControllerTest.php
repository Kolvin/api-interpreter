<?php

namespace Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityExpressionControllerTest extends WebTestCase
{
    public function testGetRequestNotAllowed(): void
    {
        $client = SecurityExpressionControllerTest::createClient();

        $client->request(
            'GET',
            '/api/security-expressions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testSecurityMultiplyExpressionWithCalculationScale()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '*',
                    'a' => 'shares',
                    'b' => 'sales',
                    'calculation_scale' => '2',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 200,
        );

        $this->assertEquals('40.00', $responseContent['result']['output']);
    }

    public function testSecurityDivideExpressionWithCalculationScale()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '/',
                    'a' => 'shares',
                    'b' => 'ebitda',
                    'calculation_scale' => '3',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 200,
        );

        $this->assertEquals('2.000', $responseContent['result']['output']);
    }

    public function testSecurityAddExpressionWithCalculationScale()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '+',
                    'a' => 'shares',
                    'b' => 'ebitda',
                    'calculation_scale' => '3',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 200,
        );

        $this->assertEquals('15.000', $responseContent['result']['output']);
    }

    public function testSecuritySubtractExpressionWithCalculationScale()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '-',
                    'a' => 'shares',
                    'b' => 'ebitda',
                    'calculation_scale' => '3',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 200,
        );

        $this->assertEquals('5.000', $responseContent['result']['output']);
    }

    public function testSecurityABSubExpressions()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '*',
                    'a' => [
                        'fn' => '-',
                        'a' => 'eps',
                        'b' => 'shares',
                    ],
                    'b' => [
                        'fn' => '-',
                        'a' => 'assets',
                        'b' => 'liabilities',
                    ],
                ],
                'security' => 'CDE',
            ],
            expectedStatusCode: 200
        );

        $this->assertEquals('8', $responseContent['result']['output']);
    }

    public function testInvalidOperator()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => 'oiajwgp',
                    'a' => 'shares',
                    'b' => 'ebitda',
                    'calculation_scale' => '3',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 400,
        );

        $this->assertArrayHasKey('notices', $responseContent);
        $this->assertArrayHasKey('error', $responseContent['notices']);
        $this->assertEquals('Operator Not Supported', $responseContent['notices']['error']);
    }

    public function testEmptyOperator()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '',
                    'a' => 'shares',
                    'b' => 'sales',
                ],
                'security' => 'ABC',
            ],
            expectedStatusCode: 400,
        );

        $this->assertArrayHasKey('notices', $responseContent);
        $this->assertArrayHasKey('error', $responseContent['notices']);
        $this->assertEquals('Operator Not Supported', $responseContent['notices']['error']);
    }

    public function testBadSecuritySymbol()
    {
        $responseContent = $this->testSecurityExpression(
            postData: [
                'expression' => [
                    'fn' => '/',
                    'a' => 'shares',
                    'b' => 'sales',
                ],
                'security' => 'INVALID',
            ],
            expectedStatusCode: 400,
        );

        $this->assertArrayHasKey('notices', $responseContent);
        $this->assertArrayHasKey('error', $responseContent['notices']);
        $this->assertEquals('Security Not Found', $responseContent['notices']['error']);
    }

    private function testSecurityExpression(array $postData, int $expectedStatusCode): array
    {
        $client = SecurityExpressionControllerTest::createClient();

        $client->request(
            method: 'POST',
            uri: '/api/security-expressions',
            parameters: [],
            files: [],
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($postData)
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('notices', $content);

        return $content;
    }
}
