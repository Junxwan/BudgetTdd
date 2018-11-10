<?php
namespace Test;

use App\BudgetService;
use PHPUnit\Framework\TestCase;

class BudgetServiceTest extends TestCase
{
    /**
     * @var BudgetService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new BudgetService(new BudgetRepo());
    }

    // 起點:2018-04-01 終點: 2018-04-30，4月預算300
    public function test_monthly_budget()
    {
        $result = $this->service->totalAmount("2018-04-01", "2018-04-30");

        $this->assertEquals(300, $result);
    }

    // 起點:2018-05-01 終點: 2018-05-31，5月預算0
    public function test_No_budget_for_the_whole_month()
    {
        $result = $this->service->totalAmount("2018-05-01", "2018-05-31");

        $this->assertEquals(0, $result);
    }

    // 起點:2018-06-01 終點: 2018-06-10，6月預算30
    public function test_partial_budget_by_month()
    {
        $result = $this->service->totalAmount("2018-06-01", "2018-06-10");

        $this->assertEquals(10, $result);
    }

    // 起點:2018-04-01 終點: 2018-06-30，4月預算300，5月預算0，6月預算30
    public function test_cross_month_budget()
    {
        $result = $this->service->totalAmount("2018-04-01", "2018-06-20");

        $this->assertEquals(320, $result);
    }

    // 起點:2018-07-01 終點: 2018-09-15，7月預算31，8月預算62，9月預算300
    public function test_many_cross_month_budget()
    {
        $result = $this->service->totalAmount("2018-07-01", "2018-09-15");

        $this->assertEquals(243, $result);
    }
}

class BudgetRepo implements \App\IBudgetRepo
{
    /**
     * @return array
     */
    public function getAll()
    {
        return [
            "2018-04" => 300,
            "2018-05" => 0,
            "2018-06" => 30,
            "2018-07" => 31,
            "2018-08" => 62,
            "2018-09" => 300,
        ];
    }
}
