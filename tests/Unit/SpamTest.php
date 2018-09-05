<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Inspections\Spam;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SpamTest extends TestCase
{
    /* @test */
    public function test_it_checks_for_invalid_keywords()
    {
    	$spam = new Spam();

    	$this->assertFalse($spam->detect('Innocent reply here.'));

    	$this->expectException('Exception');

    	$spam->detect('something forbidden');
    }

    /* @test */
    public function test_it_checks_for_any_being_held_down()
    {
    	$spam = new Spam();

    	$this->expectException('Exception');

    	$spam->detect('Hello word aaaaaaaaaa');
    }
}
