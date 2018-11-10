<?php

namespace App;

use Carbon\Carbon;

class BudgetService
{
    /**
     * @var IBudgetRepo
     */
    private $repo;

    /**
     * @var array
     */
    private $budget;

    /**
     * BudgetService constructor.
     *
     * @param IBudgetRepo $repo
     */
    public function __construct(IBudgetRepo $repo)
    {
        $this->repo = $repo;
        $this->budget = $this->repo->getAll();
    }

    /**
     * @param string $start
     * @param string $end
     *
     * @return double
     */
    public function totalAmount(string $start, string $end)
    {
        $total = 0;

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        do {
            // 起始日期當月的最後一天  ex: 2018-04-01的最後一天2018-04-30
            $lastMonth = $startDate->copy()->lastOfMonth();

            // 判斷當起始日期當月的最後一天是否超過起讫日期
            $isNextDate = $lastMonth->getTimestamp() < $endDate->getTimestamp();

            // 計算出某月份查詢範圍的預算
            $total += $isNextDate
                ? $this->getBudget($startDate, $lastMonth)
                : $this->getBudget($startDate, $endDate);

            // 下一個月
            $startDate = $lastMonth->add("+1 day");

        } while ($isNextDate);

        return $total;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return double
     */
    private function getBudget(Carbon $start, Carbon $end)
    {
        $diffDay = $start->diffInDays($end) + 1;
        $oneDayBudget = $this->budget[$start->format("Y-m")] / $start->lastOfMonth()->format("d");

        return $diffDay * $oneDayBudget;
    }
}
