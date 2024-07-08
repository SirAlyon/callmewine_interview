@extends('layouts.app')
@section('title', 'Cryptocurrencies')
@section('content')
    <div class="container-sm">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nome
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Prezzo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cap. di Mercato
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($alert))
                        @include('alert', ['alert' => $alert])
                    @endif
                    @foreach ($data as $crypto)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $crypto['rank'] }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $crypto['name'] }} ( {{ $crypto['symbol'] }})
                            </td>
                            <td class="px-6 py-4">
                                {{ number_format($crypto['price'], 2) }} $
                            </td>
                            <td class="px-6 py-4">
                                {{ number_format($crypto['market_cap'], 2) }} $
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
