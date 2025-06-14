<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesPerMonthExport implements FromCollection, WithHeadings
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $query = Order::query()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
            ->where('status', 'completed');

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return $query
            ->groupBy('year', 'month')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)') // ordenação correta por ano e mês
            ->get();
    }


    public function headings(): array
    {
        return ['Year', 'Month', 'Total (€)'];
    }
}
