<?php

namespace Tests;

trait SetupTestData
{
    // This trait can be used for additional test data setup
    // Seeders are now automatically run in TestCase::setUp()
    
    protected function setUp(): void
    {
        parent::setUp();
        // Any additional test-specific setup can go here
    }
}
