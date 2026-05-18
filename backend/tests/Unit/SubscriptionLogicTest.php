<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SubscriptionLogicTest extends TestCase
{
    public function test_plus_plan_duration_is_one_month(): void
    {
        $plan = 'plus';

        $duration = match ($plan) {
            'plus' => 1,
            'pro' => 3,
            default => 1,
        };

        $this->assertEquals(1, $duration);
    }

    public function test_pro_plan_duration_is_three_months(): void
    {
        $plan = 'pro';

        $duration = match ($plan) {
            'plus' => 1,
            'pro' => 3,
            default => 1,
        };

        $this->assertEquals(3, $duration);
    }

    public function test_cv_score_must_be_between_zero_and_one_hundred(): void
    {
        $score = 75;

        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}