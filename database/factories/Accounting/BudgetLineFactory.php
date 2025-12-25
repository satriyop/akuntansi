<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Budget;
use App\Models\Accounting\BudgetLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetLine>
 */
class BudgetLineFactory extends Factory
{
    protected $model = BudgetLine::class;

    public function definition(): array
    {
        $monthlyAmount = $this->faker->numberBetween(1000000, 10000000);

        return [
            'budget_id' => Budget::factory(),
            'account_id' => Account::factory(),
            'jan_amount' => $monthlyAmount,
            'feb_amount' => $monthlyAmount,
            'mar_amount' => $monthlyAmount,
            'apr_amount' => $monthlyAmount,
            'may_amount' => $monthlyAmount,
            'jun_amount' => $monthlyAmount,
            'jul_amount' => $monthlyAmount,
            'aug_amount' => $monthlyAmount,
            'sep_amount' => $monthlyAmount,
            'oct_amount' => $monthlyAmount,
            'nov_amount' => $monthlyAmount,
            'dec_amount' => $monthlyAmount,
            'annual_amount' => $monthlyAmount * 12,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function forBudget(Budget $budget): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_id' => $budget->id,
        ]);
    }

    public function forAccount(Account $account): static
    {
        return $this->state(fn (array $attributes) => [
            'account_id' => $account->id,
        ]);
    }

    public function withAnnualAmount(int $annualAmount): static
    {
        $monthlyAmount = (int) floor($annualAmount / 12);
        $remainder = $annualAmount - ($monthlyAmount * 12);

        return $this->state(fn (array $attributes) => [
            'jan_amount' => $monthlyAmount,
            'feb_amount' => $monthlyAmount,
            'mar_amount' => $monthlyAmount,
            'apr_amount' => $monthlyAmount,
            'may_amount' => $monthlyAmount,
            'jun_amount' => $monthlyAmount,
            'jul_amount' => $monthlyAmount,
            'aug_amount' => $monthlyAmount,
            'sep_amount' => $monthlyAmount,
            'oct_amount' => $monthlyAmount,
            'nov_amount' => $monthlyAmount,
            'dec_amount' => $monthlyAmount + $remainder,
            'annual_amount' => $annualAmount,
        ]);
    }

    public function withMonthlyAmounts(array $amounts): static
    {
        $total = array_sum($amounts);

        return $this->state(fn (array $attributes) => [
            'jan_amount' => $amounts[1] ?? 0,
            'feb_amount' => $amounts[2] ?? 0,
            'mar_amount' => $amounts[3] ?? 0,
            'apr_amount' => $amounts[4] ?? 0,
            'may_amount' => $amounts[5] ?? 0,
            'jun_amount' => $amounts[6] ?? 0,
            'jul_amount' => $amounts[7] ?? 0,
            'aug_amount' => $amounts[8] ?? 0,
            'sep_amount' => $amounts[9] ?? 0,
            'oct_amount' => $amounts[10] ?? 0,
            'nov_amount' => $amounts[11] ?? 0,
            'dec_amount' => $amounts[12] ?? 0,
            'annual_amount' => $total,
        ]);
    }
}
