<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Operation;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\SalesPerMonthExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;


class StatisticsController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();

        // Total de créditos (valor total creditado no cartão do utilizador)
        $totalCredits = DB::table('operations')
            ->where('card_id', $user->id)
            ->where('type', 'credit')
            ->sum('value');

        // Total de débitos (valor total gasto pelo utilizador)
        $totalDebits = DB::table('operations')
            ->where('card_id', $user->id)
            ->where('type', 'debit')
            ->sum('value');

        // Número total de operações
        $totalOperations = DB::table('operations')
            ->where('card_id', $user->id)
            ->count();

        // Agrupamento de débitos por tipo (ex: 'order', 'membership_fee')
        $debitsByType = DB::table('operations')
            ->select('debit_type', DB::raw('SUM(value) as total'))
            ->where('card_id', $user->id)
            ->where('type', 'debit')
            ->groupBy('debit_type')
            ->get();

        // Agrupamento de créditos por tipo (ex: 'payment', 'order_cancellation')
        $creditsByType = DB::table('operations')
            ->select('credit_type', DB::raw('SUM(value) as total'))
            ->where('card_id', $user->id)
            ->where('type', 'credit')
            ->groupBy('credit_type')
            ->get();

        return view('statistics.index', [
            'totalCredits'    => $totalCredits,
            'totalDebits'     => $totalDebits,
            'totalOperations' => $totalOperations,
            'debitsByType'    => $debitsByType,
            'creditsByType'   => $creditsByType,
        ]);
    }


    public function ordersByStatus()
    {
        // Conta o número de orders por status
        $ordersByStatus = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return view('statistics.orders-status', compact('ordersByStatus'));
    }

    public function sales()
    {
        $sales = DB::table('orders')
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total) as total')
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Organiza os dados por ano para usar no gráfico
        $groupedSales = $sales->groupBy('year');

        return view('statistics.sales', compact('groupedSales'));
    }

    public function salesByPeriod()
    {
        $groupedSales = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total')
        )
        ->where('status', 'completed')
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->groupBy('year');


        return view('statistics.sales', compact('groupedSales'));
    }

    public function usersByType()
    {
        $usersByType = DB::table('users')
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        return view('statistics.users-by-type', compact('usersByType'));
    }

    public function productsAndCategories()
    {
        // Total de produtos
        $totalProducts = Product::count();

        // Total de categorias
        $totalCategories = Category::count();

        // Produtos por categoria - retorna coleção com categoria e total
        $productsPerCategory = Category::withCount('products')->get();

        return view('statistics.products-and-categories', compact('totalProducts', 'totalCategories', 'productsPerCategory'));
    }

    

    public function topCategories()
    {
        $topCategories = DB::table('items_orders')
            ->join('products', 'items_orders.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(items_orders.quantity) as total_sales'))
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return view('statistics.top-categories', compact('topCategories'));
    }

    public function topProducts()
    {
        $topProducts = DB::table('items_orders')
            ->join('products', 'items_orders.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(items_orders.quantity) as total_sales'))
            ->groupBy('products.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return view('statistics.top_products', compact('topProducts'));
    }

    public function exportOrdersByStatus()
    {
        $data = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return response()->streamDownload(function () use ($data) {
            $csv = fopen('php://output', 'w');
            fputcsv($csv, ['Status', 'Total Orders']);
            foreach ($data as $row) {
                fputcsv($csv, [$row->status, $row->total]);
            }
            fclose($csv);
        }, 'orders_by_status.csv');
    }


    public function exportProductsAndCategories()
    {
        $categories = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('COUNT(products.id) as products_count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $totalCategories = $categories->count();
        $totalProducts = $categories->sum('products_count');

        return response()->streamDownload(function () use ($categories, $totalCategories, $totalProducts) {
            $csv = fopen('php://output', 'w');
            fputcsv($csv, ['Category', 'Number of Products']);

            foreach ($categories as $category) {
                fputcsv($csv, [$category->name, $category->products_count]);
            }

            fputcsv($csv, []);
            fputcsv($csv, ['Total Categories', $totalCategories]);
            fputcsv($csv, ['Total Products', $totalProducts]);

            fclose($csv);
        }, 'products_and_categories.csv');
    }


    public function exportSales(): StreamedResponse
    {
        $sales = DB::table('orders')
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total) as total')
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sales_per_month.csv"',
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM (opcional mas útil para Excel no Windows)
            echo "\xEF\xBB\xBF";

            // Cabeçalhos da tabela
            fputcsv($file, ['Ano', 'Mês', 'Total de Vendas (€)'], ';');

            // Escrever os dados
            foreach ($sales as $row) {
                fputcsv($file, [
                    $row->year,
                    $row->month,
                    number_format($row->total, 2, ',', '') // formato PT
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    

    public function memberOrders()
    {
        $userId = Auth::id();

        $ordersByMonth = DB::table('orders')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('member_id', $userId)
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->groupBy('year');

        return view('statistics.member-orders', compact('ordersByMonth'));
    }

    public function exportMemberOrdersCsv()
    {
        $userId = Auth::id();

        $orders = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('member_id', $userId)
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $response = new StreamedResponse(function() use ($orders) {
            $handle = fopen('php://output', 'w');

            // Cabeçalho CSV
            fputcsv($handle, ['Year', 'Month', 'Total Orders']);

            // Dados
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->year,
                    \DateTime::createFromFormat('!m', $order->month)->format('F'),
                    $order->total
                ]);
            }

            fclose($handle);
        });

        $filename = 'member_orders_' . date('Y-m-d_H-i-s') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }

    public function memberSpendingByYear()
    {
        $userId = Auth::id();

        $spendingByYear = DB::table('orders')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('SUM(total) as total'))
            ->where('member_id', $userId)
            ->where('status', 'completed')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('year', 'asc')
            ->get();

        return view('statistics.member-spending-year', compact('spendingByYear'));
    }

    public function memberSpendingByMonth()
    {
        $user = Auth::user();

        $spendingByMonth = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total_spent")
            ->where('member_id', $user->id)
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderByDesc('month')  // <-- aqui, ordem descendente para datas recentes no topo
            ->get();

        return view('statistics.spending-by-month', compact('spendingByMonth'));
    }

    public function exportMemberSpendingByMonth()

    {
        $user = Auth::user();

        $spendingByMonth = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total_spent")
            ->where('member_id', $user->id)
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderByDesc('month')  // <-- aqui, ordem descendente para datas recentes no topo
            ->get();


        $csvHeader = ['Month', 'Total Spending'];
        $filename = 'spending_by_month.csv';

        $callback = function() use ($spendingByMonth, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($spendingByMonth as $entry) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($entry->month . '-01')->format('F Y'),
                    number_format($entry->total_spent, 2, '.', ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ]);
    }


    public function totalPurchasesPerUser()
    {
        $query = DB::table('users')
            ->leftJoin('orders', 'users.id', '=', 'orders.member_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw("SUM(CASE WHEN orders.status = 'completed' THEN 1 ELSE 0 END) as total_orders")
            )
            ->whereIn('users.type', ['board', 'member', 'employee'])
            ->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_orders')
            ->orderBy('users.name');

        $ordersPerUser = $query->paginate(20);

        $totalFilteredUsers = DB::table('users')
            ->whereIn('type', ['board', 'member', 'employee'])
            ->whereNull('deleted_at')
            ->count();

        return view('statistics.total_purchases_per_user', [
            'ordersPerUser' => $ordersPerUser,
            'totalFilteredUsers' => $totalFilteredUsers,
        ]);
    }


    public function exportTotalPurchasesPerUser()
    {
        $data = DB::table('users')
            ->leftJoin('orders', 'users.id', '=', 'orders.member_id')
            ->select(
                'users.name as user_name',
                DB::raw("SUM(CASE WHEN orders.status = 'completed' THEN 1 ELSE 0 END) as total_orders")
            )
            ->whereIn('users.type', ['board', 'member', 'employee'])
            ->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_orders')
            ->orderBy('users.name')
            ->get();

        $csvHeader = ['User Name', 'Total Orders'];

        $callback = function () use ($data, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($data as $row) {
                fputcsv($file, [$row->user_name, $row->total_orders]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="total_orders_per_user.csv"',
        ]);
    }

    
    public function memberTopProducts()
    {
        $userId = Auth::id();

        $topProducts = DB::table('items_orders')
            ->join('orders', 'items_orders.order_id', '=', 'orders.id')
            ->join('products', 'items_orders.product_id', '=', 'products.id')
            ->where('orders.member_id', $userId)
            ->where('orders.status', 'completed')
            ->select('products.name', DB::raw('SUM(items_orders.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();


        return view('statistics.member-top-products', compact('topProducts'));
    }



}
