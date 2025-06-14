@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Statistics</h1>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @elseif(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @endif

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b">
                    <th class="pb-3">Title</th>
                    <th class="pb-3">Description</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-gray-200">

                @if(auth()->user()->type === 'board')
                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Orders by Status</td>
                        <td class="py-4">Number of orders by status (pending, completed, canceled)</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.orders-status') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Sales by Month/Year</td>
                        <td class="py-4">Total sales with "completed" status, grouped by month and year</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.sales') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Users by Type</td>
                        <td class="py-4">Total users grouped by type (member, board, employee, pending_member)</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.users-by-type') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Totals of Products and Categories</td>
                        <td class="py-4">Total number of products, categories, and products per category</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.products-and-categories') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Top Selling Categories</td>
                        <td class="py-4">Categories with the highest number of sales</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.top-categories') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Top Selling Products</td>
                        <td class="py-4">Most popular products in terms of sales</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.top-products') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Total Orders per User</td>
                        <td class="py-4">Total number of "completed" orders, grouped by user</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.total-purchases-per-user') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>
                @elseif(auth()->user()->type === 'member')
                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Orders by Month/Year</td>
                        <td class="py-4">Number of your orders grouped by month and year</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.member-orders') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Spending by Year</td>
                        <td class="py-4">Total spending on "completed" orders, by year</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.member-spending-year') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Spending by Month</td>
                        <td class="py-4">Total spending on "completed" orders, by month</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.member-spending-month') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-4">Most Purchased Products</td>
                        <td class="py-4">Products purchased the most by users, based on quantity</td>
                        <td class="py-4 text-right">
                            <a href="{{ route('statistics.member-top-products') }}" class="text-green-700 dark:text-green-300 hover:underline">View</a>
                        </td>
                    </tr>
                @else
                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td colspan="3" class="py-4 text-center text-gray-500">No statistics available for your user type.</td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>
</div>
@endsection
