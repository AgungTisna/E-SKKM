<?php
use vendor\PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../inputValidator.php';

class InputValidatorTest extends TestCase
{
    public function testValidInput()
    {
        $data = ['username' => 'john', 'email' => 'john@example.com'];
        $this->assertEquals('OK', validateInput($data));
    }

    public function testMissingUsername()
    {
        $data = ['email' => 'john@example.com'];
        $this->assertEquals('Username is required', validateInput($data));
    }

    public function testInvalidEmail()
    {
        $data = ['username' => 'john', 'email' => 'not-an-email'];
        $this->assertEquals('Invalid email', validateInput($data));
    }
}
